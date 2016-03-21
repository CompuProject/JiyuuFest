<?php
header("Content-type: text/html; charset=UTF-8");

if(isset($_POST['requestID']) && $_POST['requestID']!=='' && $_POST['requestID']!==null) {
//    @session_start();
//    // Включить отображение ошибок
//    ini_set("display_errors",1);
//    error_reporting(E_ALL);
//    // Подключение библиотек ядра
//    include_once '../../../ROOT/functions/includeSistemClasses.php';
//    includeSistemClasses('../../../ROOT/');
//    // Инициализация параметров
//    global $_PARAM;
//    $_PARAM = null;
//    // Инициализация конфигурации
//    global $_SITECONFIG;
//    $_SITECONFIG = new SiteConfig();
//    // Создание помошника базы данных
//    global $_SQL_HELPER;
//    $_SQL_HELPER = new MysqliHelper();
//    // Поулчение параметров ссылки
//    $urlParams = new UrlParams();
//    global $_URL_PARAMS;
//    $_URL_PARAMS = $urlParams->getUrlParam();
//    $urlHelper = new UrlHelper();
//    include_once '../classes/JiyuuFestRequestUsers/JiyuuFestRequestUsers_Add.php';
//    $addUser = new JiyuuFestRequestUsers_Add($_POST['requestID']);
    echo 'LOL';
} else {
    echo 'Нет данных';
}