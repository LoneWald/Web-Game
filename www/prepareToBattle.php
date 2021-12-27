<?php
require_once("checkAuthorize.php");
require_once("classes.php");
session_start();
$_SESSION['FieldWidth'] = isset($_SESSION['FieldWidth']) ? $_SESSION['FieldWidth'] : 10;
$_SESSION['FieldHeight'] = isset($_SESSION['FieldHeight']) ? $_SESSION['FieldHeight'] : 10;
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
}
// print_r($_SESSION['PlayerField']);
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="window">
        <?php
        if ($checker->CheckShipsArrangement($_SESSION['PlayerField'])) { ?>
            <h1>Расставьте корабли</h1>
        <?php } else { ?>
            <h1 style="color: red;">Корабли слишком близко</h1>
        <?php }
        ?>
        <div>
            <a href="?action=setSize&amp;width=8&amp;height=8">8x8</a>
            <a href="?action=setSize&amp;width=10&amp;height=10">10x10</a>
            <a href="?action=setSize&amp;width=15&amp;height=15">15x15</a>
            <a href="?action=setSize&amp;width=20&amp;height=20">20x20</a>
        </div>
        <div class="field">
            <?php for ($y = 0; $y < count($_SESSION['PlayerField']); $y++) { ?>
                <div class="row">
                    <?php for ($x = 0; $x < count($_SESSION['PlayerField'][0]); $x++) {
                        if ($_SESSION['PlayerField'][$y][$x] == 1)
                            $selected = true;
                        else
                            $selected = false;
                        $class = ($selected ? ' selected' : ' unSelected');
                    ?>
                        <div class="cell<?php echo $class ?>">
                            <a href="?action=change&amp;x=<?php echo $x ?>&amp;y=<?php echo $y ?>"></a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <div>
            <?php
            if ($checker->GetReady()) { ?>
                <a href="./game.php">Начать</a>
            <?php } else { ?>
                <span>Начать</span>
            <?php } ?>
        </div>
    </div>
</body>

</html>