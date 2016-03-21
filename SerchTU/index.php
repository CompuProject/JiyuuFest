<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
</head>
<body>
<?php
@session_start();
// Включить отображение ошибок
ini_set("display_errors",1);
error_reporting(E_ALL);
// Подключение библиотек ядра
include_once '../ROOT/functions/includeSistemClasses.php';
includeSistemClasses('../ROOT/');
// Создание помошника базы данных
global $_SQL_HELPER;
$_SQL_HELPER = new MysqliHelper();
include_once './SerchTU.php';
$serchTU = new SerchTU();
$serchTU->getReport();
?>
</body>
</html>
