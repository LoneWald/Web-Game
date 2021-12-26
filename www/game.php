<?php
require_once("checkAuthorize.php");
require_once("classes.php");
session_start();
// Получаем из сессии текущую игру.
// Если игры еще нет, создаём новую.
$game = isset($_SESSION['game']) ? $_SESSION['game'] : null;
if (!$game || !is_object($game)) {
    $game = new SeaBattle($_SESSION['PlayerField']);
}

// Обрабатываем запрос пользователя, выполняя нужное действие.
$params = $_GET + $_POST;
if (isset($params['action'])) {
    $action = $params['action'];

    if ($action == 'move') {
        // Обрабатываем ход пользователя.
        $game->makeShot((int)$params['x'], (int)$params['y']);
    } else if ($action == 'newGame') {
        // Пользователь решил начать новую игру.
        $game = new SeaBattle($_SESSION['PlayerField']);
    }
}
// Добавляем вновь созданную игру в сессию.
$_SESSION['game'] = $game;


// Отображаем текущее состояние игры в виде HTML страницы.
$width = $game->getFieldWidth();
$height = $game->getFieldHeight();
$field = $game->getField();
$playerField = $game->getPlayerField();
$winner = $game->getWinner();
?>
<!DOCTYPE html>
<html>

<head>
</head>

<body>

    <!-- Отображаем состояние игры и игровое поле. -->

    <!-- CSS-стили, задающие внешний вид элементов HTML. -->
    <style type="text/css">
        .PlayField {}

        .playerField,
        .enemyField {
            overflow: hidden;
            display: inline-block;
            margin: 10px 30px 10px 30px;
        }

        .row {
            clear: both;
        }

        .playerFieldCell,
        .enemyFieldCell {
            float: left;
            border: 1px solid #ccc;
            width: 20px;
            height: 20px;
            position: relative;
            text-align: center;
        }

        .playerFieldCell p {
            position: relative;
            text-align: center;
        }

        .enemyFieldCell a {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0
        }

        .enemyFieldCell a:hover {
            background: #ccc;
        }

        .enemyFieldCell.winner {
            background: #f00;
        }

        .icon {
            display: inline-block;
        }

        .player1:after {
            content: 'X';
        }

        .player2:after {
            content: 'O';
        }

        .emptyCell {
            background-color: #55f;
        }

        .missCell {
            background-color: #aaa;
        }

        .undetectedCell{
            background-color: #8c4;
        }

        .hitCell {
            background-color: #f00;
        }

        .playerCell {}
    </style>




    <?php if ($winner) {
        if ($winner == 1) { ?>
            <!-- Отображаем сообщение о победителе -->
            Победил Игрок!
        <?php } else { ?>
            Победил Компьютер!
        <?php } ?>
    <?php } ?>
    <!-- Player Field -->
    <div class="PlayField">
        <div class="playerField">
            <?php for ($y = 0; $y < $height; $y++) { ?>
                <div class="row">
                    <?php for ($x = 0; $x < $width; $x++) {
                        $playerCell = $playerField[$x][$y];
                        switch ($playerCell) {
                            case 3:
                                $class = " hitCell";
                                break;
                            case 2:
                                $class = " missCell";
                                break;
                            case 1:
                                $class = " undetectedCell";
                                break;
                                case 0:
                                    $class = " emptyCell";
                                    break;
                        }
                    ?>
                        <div class="playerFieldCell<?php echo $class ?>">
                            <div><?php //echo $playerCell ?></div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <!-- Enemy Field -->
        <div class="enemyField">
            <?php for ($x = 0; $x < $height; $x++) { ?>
                <div class="row">
                    <?php for ($y = 0; $y < $width; $y++) {

                        $shottedField = isset($field[$x][$y]) ? $field[$x][$y] : null;
                        if ($shottedField === null)
                            $class = ' ';
                        else if ($shottedField == 2) {
                            $class = ' missCell';
                        } else
                            $class = ' hitCell';
                    ?>
                        <div class="enemyFieldCell<?php echo $class ?>">
                            <?php if ($shottedField === null) { ?>
                                <a href="?action=move&amp;x=<?php echo $x ?>&amp;y=<?php echo $y ?>"></a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <br /><a href="./restart.php">Начать новую игру</a>

</body>

</html>