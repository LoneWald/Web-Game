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

    function __construct($playerField, $fieldWidth, $fieldHeight, $difficult)
    {
        $this->playerField = $playerField;
        $this->fieldWidth = $fieldWidth;
        $this->fieldHeight = $fieldHeight;
        $this->difficult = $difficult;
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
            return true;
        } else if ($countEnemy == $this->SHIPS_CELLS_TO_WIN) {
            $this->winner = 2;
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
    public function GetDifficult(){
        return $this->difficult;
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
    private $freeFieldMask;
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
        rsort($this->shipsArray);
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
            $this->freeFieldMask[$y] = array();
            $this->playebleField[$y] = array();
            for ($x = 0; $x < $this->fieldWidth; $x++) {
                $this->freeFieldMask[$y][$x] = false;
                $this->playebleField[$y][$x] = false;
            }
        }
        $this->fieldMask = $this->playebleField;
        $this->InsertShips(0, $this->fieldMask);
        // if($this->InsertShip(0, $this->fieldMask)){
        //     return $this->playebleField;
        // }
        // else
        //     return array();

    }
    // делает точку и все вокруг нее в маске true
    private function AddToMask($field, $startY, $startX, $endY, $endX){
        $currentField = $field;
        for ($y = $startY; $y <= $endY; $y++) {
            for ($x = $startX; $x <= $endX; $x++) {
                $currentField[$y][$x] = true;
            }
        }
        return $currentField;
    }

    private function DeleteFromMask($field, $startY, $startX, $endY, $endX){
        $currentField = $field;
        for ($y = $startY; $y <= $endY; $y++) {
            for ($x = $startX; $x <= $endX; $x++) {
                $currentField[$y][$x] = false;
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

    private function InsertShips($shipNumber, $field)
    {
        $firstMask = $field;
        $ship = $shipNumber;
        $shipLenght = $this->shipsArray[$ship];
        $countOfCorrectPositions = 0;
        $secondMask = array();
        // Заполняет secondMask массивом точек, которые могут стать стартовыми для текущего корабля
        print_r("Высота ".$this->fieldHeight."\n");
        print_r("Ширина ".$this->fieldWidth."\n");
        print_r("Field Start \n");
        for ($y = 0; $y < $this->fieldHeight; $y++) {
            $secondMask[$y] = array();
            for ($x = 0; $x < $this->fieldWidth; $x++) {
                if (
                    $this->fieldMask[$y][$x] == false
                    && (($this->checker->CheckFreeLine($this->fieldMask, $y, $x, $y + ($shipLenght-1), $x) && $y + $shipLenght <= $this->fieldHeight) 
                        || ($this->checker->CheckFreeLine($this->fieldMask, $y, $x, $y, $x + ($shipLenght-1)) && $x + $shipLenght <= $this->fieldWidth))
                ) {
                    print_r("   true\n");
                    $secondMask[$y][$x] = false;
                } else {
                    print_r("   false\n");
                    $secondMask[$y][$x] = true;
                }
            }
        }
        print_r("Field End \n");
        $this->checker->PrintTest($secondMask);
        // Рекурсивный цикл запонения кораблей
        //print_r($this->FreeMaskToArray($secondMask));
        $result = false;
        //print_r($this->shipsArray);
        do {
            $countOfCorrectPositions = count($this->FreeMaskToArray($secondMask));
            print_r("\nВозможных позиций - ".$countOfCorrectPositions."\n");
            //print_r($this->FreeMaskToArray($secondMask));
            if ($countOfCorrectPositions > 0) {
                $buf = $this->FreeMaskToArray($secondMask);
                $randPos = rand(0, count($buf) - 1);
                $startY = $buf[$randPos][0];
                $startX = $buf[$randPos][1];
                print_r("Выбранная позиция: ".$randPos."\n");
                print_r("Координаты точки (".$startY.";".$startX.")\n");
                // Вставляет в горизонтальном направлении и меняет маску
                $choice = 0;
                // Выбор вертикально или горизонтально. Если не влезает то в маске закрыть эту позицию
                if (
                    $this->checker->CheckFreeLine($firstMask, $startY, $startX, $startY, $startX + ($shipLenght-1)) && $startX + ($shipLenght) <= $this->fieldWidth
                    && $this->checker->CheckFreeLine($firstMask, $startY, $startX, $startY + ($shipLenght-1), $startX) && $startY + ($shipLenght) <= $this->fieldHeight
                ) {
                    $choice = 3;
                } else if ($this->checker->CheckFreeLine($firstMask, $startY, $startX, $startY, $startX + ($shipLenght-1)) && $startX + ($shipLenght) <= $this->fieldWidth) {
                    $choice = 2;
                } else if ($this->checker->CheckFreeLine($firstMask, $startY, $startX, $startY + ($shipLenght-1), $startX) && $startY + ($shipLenght) <= $this->fieldHeight) {
                    $choice = 1;
                }
                print_r("\nВыбор расстановки - ".$choice."\n");
            switch ($choice) {
                case 3:
                    $rand = rand(0, 1);
                    if ($rand == 1){
                        $choice = 1;
                        $this->AddOneShip($startY, $startX, $startY, $startX + ($shipLenght-1));
                    }
                    else{
                        $choice = 2;
                        $this->AddOneShip($startY, $startX, $startY + ($shipLenght-1), $startX);
                    }
                    break;
                case 2:
                    $this->AddOneShip($startY, $startX, $startY, $startX + ($shipLenght-1));
                    break;
                case 1:
                    $this->AddOneShip($startY, $startX, $startY + ($shipLenght-1), $startX);
                    break;
                case 0:
                    $secondMask[$startY][$startX] = true;
                    break;
            }
            //print_r($secondMask);
            if ($choice > 0) {
                if ($ship < count($this->shipsArray) - 1) {
                    $boo = $this->InsertShips($ship + 1, $firstMask);
                    if ($boo == true) {
                        $result = true;
                        break;
                    } else {
                        $result = false;
                    }
                } else {
                    $result = true;
                    break;
                }
            } else {
                $result = false;
            }
        }
        } while ($countOfCorrectPositions > 0);
        return $result;
    }
    

    private function AddOneShip($startY, $startX, $endY, $endX)
    {
        for ($y = $startY; $y <= $endY; $y++) {
            for ($x = $startX; $x <= $endX; $x++) {
                if ($this->playebleField[$y][$x] != 0 || $this->playebleField[$y][$x] != null)
                    return false;
                $this->playebleField[$y][$x] = 1;
                $this->fieldMask = $this->AddToMask($this->fieldMask, $y-1, $x-1, $y+1, $x+1);
            }
        }
        return true;
    }

    private function DeleteOneShip($startY, $startX, $endY, $endX){
        for ($y = $startY; $y <= $endY; $y++) {
            for ($x = $startX; $x <= $endX; $x++) {
                if($this->playebleField[$y][$x] != 1)
                    return false;
                $this->playebleField[$y][$x] = 0;
                $this->fieldMask = $this->AddToMask($this->fieldMask, $y-1, $x-1, $y+1, $x+1);
            }
        }
        return true;
    }

    private function UpdateFieldMask(){
        for ($y = 0; $y < $this->fieldWidth; $y++) {
            for ($x = 0; $x < $this->fieldWidth; $x++) {
                if($this->playebleField[$y][$x] == true){
                    for ($y1 = $y-1; $y1 < $y+1; $y1++) {
                        for ($x1 = $x-1; $x1 <= $y+1; $x1++) {
                            $this->fieldMask = true;
                        }
                    }

                }
            }
        }
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
    // Проверяет, чтобы соседние клетки шли либо горизонтально, либо вертикально, и не было по диагонали
    // а так же заносит в shipsArray корабль и ставит метку в check, что эти клетки проверены
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
    // Проверяет, есть ли корабли, на диагонали от текущей клетки
    // Если есть - добавляет к errors + 1
    private function CheckDiagonal($field, $y, $x)
    {
        if ($field[$y + 1][$x + 1] == 1 || $field[$y - 1][$x + 1] == 1 || $field[$y - 1][$x - 1] == 1 || $field[$y + 1][$x - 1] == 1)
            $this->errors++;
    }
    // Проверяет нет ли припятствий на линии
    public function CheckFreeLine($field, $startY, $startX, $endY, $endX)
    {
        $count = 0;
        for ($y = $startY; $y <= $endY; $y++) {
            for ($x = $startX; $x <= $endX; $x++) {
                print_r("\n(".$y.";".$x.") => ".($field[$y][$x]? "1" : "0"));
                if ($field[$y][$x] == true) {
                    $count++;
                }

            }
        }
        print_r("\nОриентация: = ".($startX==$endX? "Y" : "X")."");
        return $count == 0;
    }

    public function PrintTest($field){
        print("\n--------------");
        for ($y = 0; $y < count($field); $y++) {
            print("\n");
            for ($x = 0; $x < count($field[$y]); $x++) {
                if($field[$y][$x] == false)
                print_r("0");
                else 
                print_r("1");
                print(" ");
            }
        }
        print("\n--------------");
    }

    public function GetReady()
    {
        return $this->isReady;
    }

    public function GetShipsArray(){
        return $this->shipsArray;
    }

    public function Print1(){
        print_r("Hui");
    }
}