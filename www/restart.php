<?php
require_once("checkAuthorize.php");
session_start();
session_destroy();
header("Location: prepareToBattle.php");
?>