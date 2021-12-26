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

    /**
     * @var массив сделанных ходов вида $field[$x][$y] = $player;
     */
    private $field = array();

    /**
     * @var $winnerCells аналогичен $field, но хранит только клетки, которые
     * надо выделить при отображении победившей комбинации.
     */
    private $winnerCells = array();

    private $currentPlayer = 1; // 1 или 2, а после окончания игры - null.
    private $winner = null; // после окончания игры будет содержать 1 или 2.

    function __construct($playerField, $fieldWidth, $fieldHeight) {
        $this->playerField = $playerField;
        $this->fieldWidth = $fieldWidth;
        $this->fieldHeight = $fieldHeight;
        $this->bot = new Bot("Easy", $this->fieldWidth, $this->fieldHeight);
    }

    public function makeShot($y, $x)
    {
        // Учитываем ход, если выполняются все условия:
        // 1) игра ещё идет,
        // 2) клетка находится в пределах игрового поля.
        // 3) в поле на указанном месте ещё пусто,
        if (
            $this->currentPlayer
            &&
            $x >= 0 && $x < $this->fieldWidth
            &&
            $y >= 0 && $y < $this->fieldHeight
            &&
            empty($this->shotField[$y][$x])
        ) {
            $current = $this->currentPlayer;
            if ($this->enemyField[$y][$x] == 0) {
                $this->shotField[$y][$x] = 2;
            } else {
                $this->shotField[$y][$x] = 3;
            }
            if(!$this->checkWinner()){
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
        } else if($countEnemy == $this->SHIPS_CELLS_TO_WIN){
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
    // public function getWinnerCells()
    // {
    //     return $this->winnerCells;
    // }
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
}

class Bot
{
    private $difficulty;
    private $fieldWidth;
    private $fieldHeight;
    function __construct($diff, $fieldWidth, $fieldHeight) {
        switch($diff){
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
    public function EasyShot(array $playerField){
        $field = $playerField;
        $array = array();
        for ($y = 0; $y < $this->fieldHeight; $y++){
            for ($x = 0; $x < $this->fieldWidth; $x++){
                if ($field[$y][$x] != 2 && $field[$y][$x] != 3){
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
        } else
        {
            $field[$fieldY][$fieldX] = 3;
        }
        return $field;
    }
}
