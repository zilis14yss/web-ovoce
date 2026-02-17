<?php
session_start();
unset($_SESSION['kosik']);
header("Location: kosik.php");
exit();