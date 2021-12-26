<?php
require_once("checkAuthorize.php");
session_start();
$playerField = isset($_SESSION['PlayerField']) ? $_SESSION['PlayerField'] : null;
if (!$playerField) {
    $playerField = array();
    for ($y = 0; $y < 5; $y++) {
        $playerField[$y] = array();
        for ($x = 0; $x < 5; $x++) {
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
        if ($_SESSION['PlayerField'][$params['x']][$params['y']] == 1) {
            $_SESSION['PlayerField'][$params['x']][$params['y']] = 0;
        } else
        {
            $_SESSION['PlayerField'][(int)$params['x']][(int)$params['y']] = 1;
        }
        //$playerField[(int)$params['x']][(int)$params['y']] = !$playerField[(int)$params['x']][(int)$params['y']];
    }
}
//print_r($_SESSION['PlayerField']);
$height = 5;
$width = 5;
?>
<!DOCTYPE html>
<html>

<head>
</head>

<body>
    <style>
        .field {
            overflow: hidden;
            display: inline-block;
            margin: 10px 30px 10px 30px;
        }

        .row {
            clear: both;
        }

        .cell {
            float: left;
            border: 1px solid #ccc;
            width: 20px;
            height: 20px;
            position: relative;
            text-align: center;
        }

        .cell p {
            position: relative;
            text-align: center;
        }

        .cell a {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0
        }

        .cell a:hover {
            background: #ccc;
        }

        .selected {
            background-color: #8c4;
        }

        .unSelected {
            background-color: #55f;
        }
    </style>
    <h1>Расставьте корабли</h1>
    <div class="field">
        <?php for ($y = 0; $y < $height; $y++) { ?>
            <div class="row">
                <?php for ($x = 0; $x < $width; $x++) {
                    if ($_SESSION['PlayerField'][$x][$y] == 1)
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
    <a href="./game.php">Начать</a>

</body>

</html>