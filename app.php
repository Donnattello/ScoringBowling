<?php
    class Round 
    {
        public $roll1 =1;
        public $roll2 = 0;
        
        public $score;

        public function __construct($roll1, $roll2, $score)
        {
            $this->roll1 = $roll1;
            $this->roll2 = $roll2;
            $this->score = $score;
        }
        
        public function count()
        {
            $this->score = $this->roll1 + $this->roll2;
        }

        public function GetNumPins1()
        {
            do {
                echo "First roll: ";
                $input = trim(fgets(STDIN));

                if (filter_var($input, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 10]]) !== false) {
                    $this->roll1 = (int) $input;
                    break;
                } 
                else {
                    echo "Błędna wartość! Podaj liczbę od 0 do 10.\n";
                }
            } while (true); 
            
        }
        public function GetNumPins2()
        {
            do {
                echo "Second roll: ";
                $input = trim(fgets(STDIN));

                if (filter_var($input, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0, "max_range" => 10]]) !== false) {
                    if ($input <= 10 - $this->roll1)
                    {
                        $this->roll2 = (int) $input;
                        break;
                    }
                    else {
                        echo "You have only : " . 10 - $this->roll1 . "pins left. You can`t knocked down more then you have!\n";
                    }
                } 
                else {
                    echo "Invalid value! Please enter a number from 0 to 10.\n";
                }
            } while (true);
        }
    }
    class Game 
    {
        public $rounds;
        public $TotalScore = 0;
        private $spare = false;
        private $strike = false;
        public $N = 0;

        public function __construct()
        {
            $this->rounds = array_map(fn() => new Round(0,0,0,0), range(1, 11));
        }

        # metoda wyświetla liczbę punktów z bieżącej rundy, liczbę punktów z poprzedniej i łączną ilość punktów 
        private function getScore($i)
        {
            echo "--------------------------\n";
            echo "this round score: " . $this->rounds[$i]->score . "\n";
            if ($i > 0)
            {
                echo "last round score: " . $this->rounds[$i - 1]->score . "\n";
            }
            echo "--------------------------\n";
            echo "total score: " . $this->TotalScore . "\n";
            echo "--------------------------\n";
        }

        # ta metoda wywołuje się jeśli w 10 rundzie wychodzi strike lub spare
        private function ExtraRound($i)
        {
            echo  "--------EXTRA ROUND-------\n";
            $this->rounds[$i + 1]->GetNumPins1();
            $this->TotalScore += $this->rounds[$i + 1]->roll1;
            echo "total score: " . $this->TotalScore . "\n";
        }

        # ta metoda pobiera i liczy punkty za rundę 
        public function roll ($i)
        {
            if ($i < 10)
            {
                echo "\nRound " . $i + 1 . "\n";
                $this->rounds[$i]->GetNumPins1();
                #sprawdza czy w poprzedniej rundzie nie było spare
                if ($this->spare == true)
                {
                    $this->rounds[$i - 1]->score += $this->rounds[$i]->roll1;
                    $this->TotalScore += $this->rounds[$i]->roll1;
                }
                # jeśli w pierwszym rzucie zbito 10 kręgli to drugi rzut automatycznie ustawia się na 0 i strike = true
                if ($this->rounds[$i]->roll1 == 10)
                {
                    $this->rounds[$i]->roll2 = 0;
                    if ($this->strike == true)
                    {
                        $this->rounds[$i - 1]->score += $this->rounds[$i]->roll1 + $this->rounds[$i]->roll2;
                        $this->TotalScore += $this->rounds[$i]->roll1 + $this->rounds[$i]->roll2;
                    }
                    $this->strike = true;
                }
                else
                {
                    $this->rounds[$i]->GetNumPins2();
                    #sprawdzanie czy nie było spare w poprzedniej rundzie
                    if ($this->strike == true)
                    {
                        $this->rounds[$i - 1]->score += $this->rounds[$i]->roll1 + $this->rounds[$i]->roll2;
                        $this->TotalScore += $this->rounds[$i]->roll1 + $this->rounds[$i]->roll2;
                    } 
                    $this->strike = false;
                }


                $this->rounds[$i]->count();
                
                # sprawdzanie czy był spare w tej rundzie 
                if ($this->rounds[$i]->score == 10 && $this->rounds[$i]->roll1 != 10)
                {    
                    $this->spare = true;}
                else $this->spare = false;

                $this->TotalScore += $this->rounds[$i]->score;
                
                $this->getScore($i);
                # wywołanie metody extra raundu w razie striku lub spare w 10 rundzie 
                if ($i==9 && ($this->spare == true || $this->strike == true))
                {
                    $this->ExtraRound($i);
                }
            }
        }

        
    }
    
    $f = new Game ();
    for ($i = 0; $i < 10; $i++)
    {
        $f->roll($i);
    }
    
?>