<?php
require_once("checkAuthorize.php");
require_once("classes.php");
session_start();
// Получаем из сессии текущую игру.
// Если игры еще нет, создаём новую.
$game = isset($_SESSION['game']) ? $_SESSION['game'] : null;
if (!$game || !is_object($game)) {
    $height = count($_SESSION['PlayerField']);
    $width = count($_SESSION['PlayerField'][0]);
    $game = new SeaBattle($_SESSION['PlayerField'], $width, $height);
}

// Добавляем вновь созданную игру в сессию.
$_SESSION['game'] = $game;

// Обрабатываем запрос пользователя
$params = $_GET + $_POST;
if (isset($params['action'])) {
    $action = $params['action'];

    if ($action == 'move') {
        // Обрабатываем ход пользователя.
        $game->makeShot((int)$params['y'], (int)$params['x']);
    }
}


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
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="window">
    <?php if ($winner) {
        $disabled = " disabled";
        if ($winner == 1) { ?>
            <!-- Отображаем сообщение о победителе -->
            <h1 style="color: green;">Победил Игрок!</h1>
        <?php } else { ?>
            <h1 style="color: red;">Победил Компьютер!</h1>
        <?php } ?>
    <?php } else { ?>
        <h1>Атакуйте!</h1>
        <?php } ?>
        <!-- Player Field -->
        <div class="PlayField">
            <div class="playerField">
                <?php for ($y = 0; $y < $height; $y++) { ?>
                    <div class="row">
                        <?php for ($x = 0; $x < $width; $x++) {
                            $playerCell = $playerField[$y][$x];
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
                                <div><?php //echo $playerCell 
                                        ?></div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <!-- Enemy Field -->
            <div class="enemyField">
                <?php for ($y = 0; $y < $height; $y++) { ?>
                    <div class="row">
                        <?php for ($x = 0; $x < $width; $x++) {

                            $shottedField = isset($field[$y][$x]) ? $field[$y][$x] : null;
                            if ($shottedField === null)
                                $class = ' emptyCell';
                            else if ($shottedField == 2) {
                                $class = ' missCell';
                            } else
                                $class = ' hitCell';
                        ?>
                            <div class="enemyFieldCell<?php echo $class ?>">
                                <?php if ($shottedField === null) { ?>
                                    <a class="<?php echo $disabled ?>" href="?action=move&amp;x=<?php echo $x ?>&amp;y=<?php echo $y ?>"></a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div>
            <br /><a href="./restart.php">Начать новую игру</a>
        </div>
    </div>
</body>

</html>