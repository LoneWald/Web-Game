<?php
// 0 - пусто
// 1 - Есть корабль
// 2 - Мимо
// 3 - Корабль подбит
class SeaBattle
{
    private $bot;
    private $fieldWidth;
    private $fieldHeight;
    private $difficult = "Easy";
    private $shotField = array();
    private $enemyField = array(
        array(1, 0, 0, 0, 0),
        array(0, 1, 1, 0, 0),
        array(0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0)
    );
    private $playerField;
    private $SHIPS_CELLS_TO_WIN = 3;
    private $field = array();
    private $winner = null; // после окончания игры будет содержать 1 или 2.

    function __construct($playerField, $fieldWidth, $fieldHeight)
    {
        $this->playerField = $playerField;
        $this->fieldWidth = $fieldWidth;
        $this->fieldHeight = $fieldHeight;
        $this->SetShipsToWin();
        $this->bot = new Bot("Easy", $this->fieldWidth, $this->fieldHeight, $playerField);
        $this->bot->CreatePlayField();
        $this->enemyField = $this->bot->GetPlayebleField();
        //print_r($this->bot->GetPlayebleField());
    }

    public function makeShot($y, $x)
    {
        if (
            $x >= 0 && $x < $this->fieldWidth
            &&
            $y >= 0 && $y < $this->fieldHeight
            &&
            empty($this->shotField[$y][$x])
        ) {
            if ($this->enemyField[$y][$x] == 0) {
                $this->shotField[$y][$x] = 2;
            } else {
                $this->shotField[$y][$x] = 3;
            }
            if (!$this->checkWinner()) {
                // Ход компьютера
                $this->playerField = $this->bot->EasyShot($this->playerField);
                $this->checkWinner();
            }
        }
    }

    private function checkWinner()
    {
        $countPlayer = 0;
        $countEnemy = 0;
        for ($y = 0; $y < $this->fieldHeight; $y++) {
            for ($x = 0; $x < $this->fieldWidth; $x++) {
                if ($this->shotField[$y][$x] == 3)
                    $countPlayer++;
                if ($this->playerField[$y][$x] == 3)
                    $countEnemy++;
            }
        }
        if ($countPlayer == $this->SHIPS_CELLS_TO_WIN) {
            $this->winner = 1;
            $this->currentPlayer = null;
            return true;
        } else if ($countEnemy == $this->SHIPS_CELLS_TO_WIN) {
            $this->winner = 2;
            $this->currentPlayer = null;
            return true;
        } else
            return false;
    }
    public function getWinner()
    {
        return $this->winner;
    }
    public function getField()
    {
        return $this->shotField;
    }
    public function getFieldWidth()
    {
        return $this->fieldWidth;
    }
    public function getFieldHeight()
    {
        return $this->fieldHeight;
    }
    public function getPlayerField()
    {
        return $this->playerField;
    }

    private function SetShipsToWin(){
        $this->SHIPS_CELLS_TO_WIN = 0;
        for ($y = 0; $y < $this->fieldHeight; $y++) {
            for ($x = 0; $x < $this->fieldWidth; $x++) {
                if($this->playerField[$y][$x] == true || $this->playerField[$y][$x] == 1){
                    $this->SHIPS_CELLS_TO_WIN++;
                }
            }
        }
    }
}

class Bot
{
    private $playerField;
    private $playebleField;
    private $checker;
    private $shipsArray;
    private $difficulty;
    private $fieldWidth;
    private $fieldHeight;
    private $fieldMask;
    function __construct($diff, $fieldWidth, $fieldHeight, $playerField)
    {
        $this->playerField = $playerField;
        $this->checker = new Checker();
        $this->shipsArray = $this->checker->CheckShipsArrangement($this->playerField);
        $this->shipsArray = $this->checker->getShipsArray();
        switch ($diff) {
            case "Easy":
                $this->difficulty = 0;
                break;
            case "Hard":
                $this->difficulty = 1;
                break;
            default:
                $this->difficulty = 0;
        }
        $this->fieldWidth = $fieldWidth;
        $this->fieldHeight = $fieldHeight;
    }

    // Выбирает случайное ячейку из тех, что не были под огнем 
    // и возвращает поле с измененной ячейкой
    public function EasyShot(array $playerField)
    {
        $field = $playerField;
        $array = array();
        for ($y = 0; $y < $this->fieldHeight; $y++) {
            for ($x = 0; $x < $this->fieldWidth; $x++) {
                if ($field[$y][$x] != 2 && $field[$y][$x] != 3) {
                    array_push($array, array($y, $x));
                }
            }
        }
        $rand = rand(0, count($array) - 1);
        $fieldX = $array[$rand][1];
        $fieldY = $array[$rand][0];
        // $fieldX = rand(0, $this->fieldWidth - 1);
        // $fieldY = rand(0, $this->fieldHeight - 1);
        // while ($field[$fieldX][$fieldY] == 2 || $field[$fieldX][$fieldY] == 3){
        //     $fieldX = rand(0, $this->fieldWidth - 1);
        //     $fieldY = rand(0, $this->fieldHeight - 1);
        // }
        if ($field[$fieldY][$fieldX] == 0 || $field[$fieldY][$fieldX] == null) {
            $field[$fieldY][$fieldX] = 2;
        } else {
            $field[$fieldY][$fieldX] = 3;
        }
        return $field;
    }

    public function CreatePlayField(){
        $this->playebleField = array();
        for ($y = 0; $y < $this->fieldHeight; $y++) {
            $this->playebleField[$y] = array();
            for ($x = 0; $x < $this->fieldWidth; $x++) {
                $this->playebleField[$y][$x] = false;
            }
        }
        $this->fieldMask = $this->playebleField;
        $this->InsertShip(0, $this->fieldMask);
        // if($this->InsertShip(0, $this->fieldMask)){
        //     return $this->playebleField;
        // }
        // else
        //     return array();

    }

    private function UpdateMask($field, $startY, $startX, $endY, $endX){
        $currentField = $field;
        for ($y = $startY; $y <= $endY; $y++) {
            for ($x = $startX; $x <= $endX; $x++) {
                $currentField[$y][$x] = true;
            }
        }
        return $currentField;
    }

    private function FreeMaskToArray($mask){
        $array = array();
        for ($y = 0; $y < $this->fieldHeight; $y++) {
            for ($x = 0; $x < $this->fieldWidth; $x++) {
                if($mask[$y][$x] == false){
                    array_push($array, array($y, $x));
                }
            }
        }
        return $array;
    }

    private function InsertShip($shipNumber, $parentMask)
    {
        $ship = $shipNumber;
        $shipLenght = $this->shipsArray[$ship];
        $countOfCorrectPositions = 0;
        $secondMask = array();
        // Заполняет secondMask массивом точек, которые могут стать стартовыми для текущего корабля
        for ($y = 0; $y < $this->fieldHeight; $y++) {
            $secondMask[$y] = array();
            for ($x = 0; $x < $this->fieldWidth; $x++) {
                if (
                    $parentMask[$y][$x] == false
                    && ($this->checker->CheckFreeLine($parentMask, $y, $x, $y + $shipLenght, $x)
                        || $this->checker->CheckFreeLine($parentMask, $y, $x, $y, $x + $shipLenght))
                ) {
                    $secondMask[$y][$x] = false;
                } else {
                    $secondMask[$y][$x] = true;
                }
                echo $secondMask[$y][$x] ? 'true' : 'false';
            }
            ?>
            <br><?php
        }
        // Рекурсивный цикл запонения кораблей
        do {
            $countOfCorrectPositions = count($this->FreeMaskToArray($secondMask));
            if ($countOfCorrectPositions > 0) {
                $buf = $this->FreeMaskToArray($secondMask);
                $randPos = rand(0, count($buf) - 1);
                $startY = $buf[$randPos][0];
                $startX = $buf[$randPos][1];
                // Вставляет в горизонтальном направлении и меняет маску
                if ($this->checker->CheckFreeLine($secondMask, $startY, $startX, $startY, $startX + $shipLenght)) {
                    for ($y = $startY; $y <= $startY + $shipLenght; $y++) {
                        for ($x = $startX; $x <= $startX; $x++) {
                            $this->playebleField[$y][$x] = 1;
                            $secondMask = $this->UpdateMask($secondMask, $y-1, $x-1, $y+1, $x+1);
                        }
                    }
                }
                else if ($this->checker->CheckFreeLine($secondMask, $startY, $startX, $startY + $shipLenght, $startX)) {
                    for ($y = $startY; $y <= $startY; $y++) {
                        for ($x = $startX; $x <= $startX + $shipLenght; $x++) {
                            $this->playebleField[$y][$x] = 1;
                            $secondMask = $this->UpdateMask($secondMask, $y-1, $x-1, $y+1, $x+1);
                        }
                    }
                }
                $boo = $this->InsertShip($ship+1, $secondMask);
                if($boo == true){
                    return true;
                }
            }
            else {
                return false;
            }
        } while ($countOfCorrectPositions > 0);
    }

    public function GetPlayebleField(){
        return $this->playebleField;
    }
}

class Checker
{
    private $isReady = false;
    private $errors = 0;
    private $shipsArray = array();
    private $check = array();
    private $shipsCount = 0;

    function CheckShipsArrangement($field)
    {
        $this->shipsArray = array();
        $this->errors = 0;
        $this->shipsCount = 0;
        $height = count($field);
        $width = count($field[0]);
        // Массив прорверок клеток
        for ($y = 0; $y < $height; $y++) {
            $this->check[$y] = array();
            for ($x = 0; $x < $width; $x++) {
                array_push($this->check[$y], false);
            }
        }

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ($this->check[$y][$x] == false) {
                    $this->CheckCell($field, $y, $x);
                }
            }
        }
        // rsort($this->shipsArray);
        // print_r($this->shipsArray);
        if ($this->errors > 0) {
            $this->isReady = false;
            return false;
        } else {
            if (count($this->shipsArray) > 0)
                $this->isReady = true;
            else $this->isReady = false;
            return true;
        }
    }

    private function CheckCell($field, $y, $x)
    {
        $this->check[$y][$x] = true;
        if ($field[$y][$x] == 1) {
            $this->CheckDiagonal($field, $y, $x);
            $shipLength = 1;
            if ($field[$y][$x + 1] == 1 && $field[$y + 1][$x] == 0 && $field[$y - 1][$x] == 0) {
                $z = $x;
                while ($field[$y][$z + 1] == 1) {
                    $this->CheckDiagonal($field, $y, $z + 1);
                    $this->check[$y][$z + 1] = true;
                    $z++;
                    $shipLength++;
                    if ($field[$y + 1][$z] == 1 || $field[$y - 1][$z] == 1) {
                        $this->errors++;
                    }
                }
            } else if ($field[$y + 1][$x] == 1 && $field[$y][$x + 1] == 0 && $field[$y][$x - 1] == 0) {
                $z = $y;
                while ($field[$z + 1][$x] == 1) {
                    $this->CheckDiagonal($field, $z + 1, $x);
                    $this->check[$z + 1][$x] = true;;
                    $z++;
                    $shipLength++;
                    if ($field[$z][$x + 1] == 1 || $field[$z][$x - 1] == 1) {
                        $this->errors++;
                    }
                }
            } else if ($field[$y][$x + 1] == 1 && $field[$y + 1][$x] == 1)
                $this->errors++;
            array_push($this->shipsArray, $shipLength);
        }
    }

    private function CheckDiagonal($field, $y, $x)
    {
        if ($field[$y + 1][$x + 1] == 1 || $field[$y - 1][$x + 1] == 1 || $field[$y - 1][$x - 1] == 1 || $field[$y + 1][$x - 1] == 1)
            $this->errors++;
    }
    // Проверяет нет ли припятствий на линии
    public function CheckFreeLine($field, $startY, $startX, $endY, $endX)
    {
        $count = 0;
        for ($y = $startY; $y < $endY; $y++) {
            for ($x = $startX; $x < $endX; $x++) {
                if ($field[$y][$x] == true) {
                    $count++;
                }
            }
        }
        if ($count = 0)
            return true;
        else
            return false;
    }

    public function GetReady()
    {
        return $this->isReady;
    }

    public function GetShipsArray(){
        return $this->shipsArray;
    }
}
