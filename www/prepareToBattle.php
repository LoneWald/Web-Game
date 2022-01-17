<?php
require_once("checkAuthorize.php");
require_once("classes.php");
session_start();
$_SESSION['FieldWidth'] = isset($_SESSION['FieldWidth']) ? $_SESSION['FieldWidth'] : 10;
$_SESSION['FieldHeight'] = isset($_SESSION['FieldHeight']) ? $_SESSION['FieldHeight'] : 10;
$_SESSION['Difficult'] = isset($_SESSION['Difficult']) ? $_SESSION['Difficult'] : "easy";
$_SESSION['Checker'] = isset($_SESSION['Checker']) ? $_SESSION['Checker'] : new Checker();
$checker = $_SESSION['Checker'];
$playerField = isset($_SESSION['PlayerField']) ? $_SESSION['PlayerField'] : null;
if (!$playerField) {
    $playerField = array();
    for ($y = 0; $y < $_SESSION['FieldHeight']; $y++) {
        $playerField[$y] = array();
        for ($x = 0; $x < $_SESSION['FieldWidth']; $x++) {
            array_push($playerField[$y], false);
        }
    }
    $_SESSION['PlayerField'] = $playerField;
}
// print_r($playerField);
// $playerField = null;
// $_SESSION['PlayerField'] = $playerField;
// print("dsmvciedrfnivnerfivjweoviwernvowejhfnwenfviwevnejvw");
$params = $_GET + $_POST;
if (isset($params['action'])) {
    $action = $params['action'];
    if ($action == 'change') {
        if ($_SESSION['PlayerField'][$params['y']][$params['x']] == 1) {
            $_SESSION['PlayerField'][$params['y']][$params['x']] = 0;
        } else {
            $_SESSION['PlayerField'][(int)$params['y']][(int)$params['x']] = 1;
        }
        //$playerField[(int)$params['x']][(int)$params['y']] = !$playerField[(int)$params['x']][(int)$params['y']];
    }
    if ($action == 'setSize') {
        $_SESSION['FieldWidth'] = $params['width'];
        $_SESSION['FieldHeight'] = $params['height'];
        $playerField = array();
        for ($y = 0; $y < $_SESSION['FieldHeight']; $y++) {
            $playerField[$y] = array();
            for ($x = 0; $x < $_SESSION['FieldWidth']; $x++) {
                array_push($playerField[$y], false);
            }
        }
        $_SESSION['PlayerField'] = $playerField;
    }
    if ($action == 'setDifficult') {
        $_SESSION['Difficult'] = $params['difficult'];
    }
}
$shipsArray = $checker->GetShipsArray();
// print_r($_SESSION['PlayerField']);
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
            <div class="col-7 text-center my-auto">
                <div class="field">
                    <?php for ($y = 0; $y < count($_SESSION['PlayerField']); $y++) { ?>
                    <div class="simple-row">
                        <?php for ($x = 0; $x < count($_SESSION['PlayerField'][0]); $x++) {
                        if ($_SESSION['PlayerField'][$y][$x] == 1)
                            $selected = true;
                        else
                            $selected = false;
                        $class = ($selected ? ' selected' : ' unSelected');
                    ?>
                        <div class="cell<?php echo $class ?>" style="<?php $size = $_SESSION['FieldHeight'] > $_SESSION['FieldWidth']? $_SESSION['FieldHeight'] : $_SESSION['FieldWidth'];
                            echo("height: ".(500/(int)$size)."px;width: ".(500/(int)$size)."px;")?>">
                            <a href="?action=change&amp;x=<?php echo $x ?>&amp;y=<?php echo $y ?>"></a>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-5 text-center my-auto">
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="action-info">
                            <?php
                                if ($checker->CheckShipsArrangement($_SESSION['PlayerField'])) { ?>
                            <h1>Расставьте корабли</h1>
                            <?php } else { ?>
                            <h1 style="color: red;">Корабли слишком близко</h1>
                            <?php }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-5 text-center">
                        <div class="select-size">
                            <a class="<?php echo ($_SESSION['FieldHeight'] == 3 ? "active" : "") ?>"
                                href="?action=setSize&amp;width=3&amp;height=3">3x3</a>
                            <a class="<?php echo ($_SESSION['FieldHeight'] == 5 ? "active" : "") ?>"
                                href="?action=setSize&amp;width=5&amp;height=5">5x5</a>
                            <a class="<?php echo ($_SESSION['FieldHeight'] == 10 ? "active" : "") ?>"
                                href="?action=setSize&amp;width=10&amp;height=10">10x10</a>
                            <a class="<?php echo ($_SESSION['FieldHeight'] == 15 ? "active" : "") ?>"
                                href="?action=setSize&amp;width=15&amp;height=15">15x15</a>
                            <a class="<?php echo ($_SESSION['FieldHeight'] ==20 ? "active" : "") ?>"
                                href="?action=setSize&amp;width=20&amp;height=20">20x20</a>
                            <a class="<?php echo ($_SESSION['FieldHeight'] ==11 ? "active" : "") ?>"
                                href="?action=setSize&amp;width=18&amp;height=11">18x11</a>
                        </div>
                    </div>
                    <div class="col-5 text-center my-auto">
                        <div class="select-size">
                            <a class="<?php echo ($_SESSION['Difficult'] == "hard" ? "active" : "") ?>"
                                href="?action=setDifficult&amp;difficult=hard">Hard</a>
                            <a class="<?php echo ($_SESSION['Difficult'] == "easy" ? "active" : "") ?>"
                                href="?action=setDifficult&amp;difficult=easy">Easy</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="window">
            <div>
                <?php
            if ($checker->GetReady()) { ?>
                <a class="start-button" href="./game.php">Начать</a>
                <?php } else { ?>
                <span class="button-disabled">Начать</span>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>