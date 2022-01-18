<?php
require_once("checkAuthorize.php");
require_once("classes.php");
require_once("db_classes.php");
session_start();
$playerFieldPlayeble = false;
for ($y = 0; $y < $_SESSION['FieldHeight']; $y++) {
    for ($x = 0; $x < $_SESSION['FieldWidth']; $x++) {
        if($_SESSION['PlayerField'][$y][$x] == true)
            $playerFieldPlayeble = true;
    }
}
if(!$playerFieldPlayeble || $_SESSION['PlayerField'] == null){
    header("Location: prepareToBattle.php");
}
// Получаем из сессии текущую игру.
// Если игры еще нет, создаём новую.
$game = isset($_SESSION['game']) ? $_SESSION['game'] : null;
if (!$game || !is_object($game)) {
    $height = count($_SESSION['PlayerField']);
    $width = count($_SESSION['PlayerField'][0]);
    $difficult = $_SESSION['Difficult'];
    $game = new SeaBattle($_SESSION['PlayerField'], $width, $height, $difficult);
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
if($winner){
    $_SESSION['game'] = null;
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
</head>

<body>
    <?php
    include("header.php");
    ?>
    <div class="container">
        <div class="row">
            <div class="col-6 text-end my-auto">
                <div class="action-info">
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
                </div>
            </div>
            <div class="col-6 text-start my-auto">
                <div>
                    <?php if($winner) {?>
                    <a class="start-button" href="./restart.php">Начать новую игру</a>
                    <?php } else { ?>
                    <span class="button-disabled">Начать новую игру</span>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class=" row">
            <div class="col-6 text-center my-auto">
                <div class="field">
                    <?php for ($y = 0; $y < $height; $y++) { ?>
                    <div class="simple-row">
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
                        <div class="playerFieldCell<?php echo $class ?>" style="<?php $size = $_SESSION['FieldHeight'] > $_SESSION['FieldWidth']? $_SESSION['FieldHeight'] : $_SESSION['FieldWidth'];
                            echo("height: ".(500/(int)$size)."px;width: ".(500/(int)$size)."px;")?>">
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="col-6 text-center my-auto">
                <div class="field">
                    <?php for ($y = 0; $y < $height; $y++) { ?>
                    <div class="simple-row">
                        <?php for ($x = 0; $x < $width; $x++) {

                            $shottedField = isset($field[$y][$x]) ? $field[$y][$x] : null;
                            if ($shottedField === null)
                                $class = ' emptyCell';
                            else if ($shottedField == 2) {
                                $class = ' missCell';
                            } else
                                $class = ' hitCell';
                        ?>
                        <div class="enemyFieldCell<?php echo $class." ".$disabled ?>" style="<?php $size = $_SESSION['FieldHeight'] > $_SESSION['FieldWidth']? $_SESSION['FieldHeight'] : $_SESSION['FieldWidth'];
                            echo("height: ".(500/(int)$size)."px;width: ".(500/(int)$size)."px;")?>">
                            <?php if ($shottedField === null) { ?>
                            <a href="?action=move&amp;x=<?php echo $x ?>&amp;y=<?php echo $y ?>"></a>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php 
if($winner){
    $querry = new Querry();
    $querry->InsertGame($winner);
}
?>