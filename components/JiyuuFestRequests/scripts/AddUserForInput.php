<?php

header("Content-type: text/html; charset=UTF-8");
if(isset($_POST['SearchUser'])) {
    @session_start();
    // Включить отображение ошибок
    ini_set("display_errors",1);
    error_reporting(E_ALL);
    // Подключение библиотек ядра
    include_once "../../../ROOT/functions/includeSistemClasses.php";
    includeSistemClasses('../../../ROOT/');
    // Инициализация параметров
    global $_PARAM;
    $_PARAM = null;
    // Инициализация конфигурации
    global $_SITECONFIG;
    $_SITECONFIG = new SiteConfig();
    // Создание помошника базы данных
    global $_SQL_HELPER;
    $_SQL_HELPER = new MysqliHelper();
    // Поулчение параметров ссылки
    $urlParams = new UrlParams();
    global $_URL_PARAMS;
    $_URL_PARAMS = $urlParams->getUrlParam();
    $urlHelper = new UrlHelper();
    
    include_once "../classes/JiyuuFestRequestUsers/JiyuuFestRequestUsers_SerchForInput.php";
    
    $serchForInput = new JiyuuFestRequestUsers_SerchForInput();
    $serchForInput->get();
//    
//    
//    
//
//    
//
//    if($search == ''){
//            exit("");
//    }
//    $sqlHelper = new MysqliHelper();
//    $query = "SELECT * FROM `Users` WHERE "
//            . "`login` LIKE '%".$search."%' OR "
//            . "`nickname` LIKE '%".$search."%' OR "
//            . "`ferstName` LIKE '%".$search."%' OR "
//            . "`lastName` LIKE '%".$search."%' OR "
//            . "`email` LIKE '".$search."'  OR "
//            . "`phone` LIKE '".getPhone($search)."';";
//    $result = $sqlHelper->select($query);
//
//
//
//    $page = 'accounts';
//    if($result!=null) {
//        $counter = 0;
//        foreach ($result as $user) {
//            if($counter++ < 10) {
//                $params[0]= $user['login'];
//                $url = $urlHelper->pageUrl($page,$params);
//                echo "<div class='searchResultElement'><a href='$url'>".$user['nickname']."</a></div>";
//            } else {
//                break;
//            }
//        }
//    } else {
//        echo "Нет Результатов";
//    }
}