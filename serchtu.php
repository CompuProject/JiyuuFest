<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
<style>
body {font: 14px/15px "PT Sans",helvetica,"segoe UI",arial,sans-serif;}
.simple-little-table {
    font-family:Arial, Helvetica, sans-serif;
    color:#666;
    font-size:12px;
    text-shadow: 1px 1px 0px #fff;
    background:#eaebec;
    margin:20px;
    border:#ccc 1px solid;
    border-collapse:separate;

    -moz-border-radius:3px;
    -webkit-border-radius:3px;
    border-radius:3px;

    -moz-box-shadow: 0 1px 2px #d1d1d1;
    -webkit-box-shadow: 0 1px 2px #d1d1d1;
    box-shadow: 0 1px 2px #d1d1d1;
}
 
.simple-little-table th {
    font-weight:bold;
    /*padding:21px 25px 22px 25px;*/
    padding:11px 15px 12px 15px;
    border-top:1px solid #fafafa;
    border-bottom:1px solid #e0e0e0;

    background: #ededed;
    background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
    background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
}
.simple-little-table th:first-child{
    text-align: left;
    /*padding-left:20px;*/
    padding-left:10px;
}
.simple-little-table tr:first-child th:first-child{
    -moz-border-radius-topleft:3px;
    -webkit-border-top-left-radius:3px;
    border-top-left-radius:3px;
}
.simple-little-table tr:first-child th:last-child{
    -moz-border-radius-topright:3px;
    -webkit-border-top-right-radius:3px;
    border-top-right-radius:3px;
}
.simple-little-table tr{
    text-align: center;
    /*padding-left:20px;*/
    padding-left:10px;
}
.simple-little-table tr td:first-child{
    /*text-align: left;*/
    padding-left:20px;
    border-left: 0;
}
.simple-little-table tr td {
    /*padding:18px;*/
    padding:10px;
    border-top: 1px solid #ffffff;
    border-bottom:1px solid #e0e0e0;
    border-left: 1px solid #e0e0e0;

    background: #fafafa;
    background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
    background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
}
.simple-little-table tr:nth-child(even) td{
    background: #f6f6f6;
    background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
    background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
}
.simple-little-table tr:last-child td{
    border-bottom:0;
}
.simple-little-table tr:last-child td:first-child{
    -moz-border-radius-bottomleft:3px;
    -webkit-border-bottom-left-radius:3px;
    border-bottom-left-radius:3px;
}
.simple-little-table tr:last-child td:last-child{
    -moz-border-radius-bottomright:3px;
    -webkit-border-bottom-right-radius:3px;
    border-bottom-right-radius:3px;
}
.simple-little-table tr:hover td{
    background: #f2f2f2;
    background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
    background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);
}
 
.simple-little-table a:link {
    color: #666;
    font-weight: bold;
    text-decoration:none;
}
.simple-little-table a:visited {
    color: #999999;
    font-weight:bold;
    text-decoration:none;
}
.simple-little-table a:active,
.simple-little-table a:hover {
    color: #bd5a35;
    text-decoration:underline;
}
.simple-little-table td {
    text-align: center;
}
.simple-little-table .login {width: 150px;}
.simple-little-table .nickname {width: 150px;}
.simple-little-table .email {width: 200px;}
.simple-little-table .phone {width: 90px;}
.simple-little-table .ferstName {width: 100px;}
.simple-little-table .lastName {width: 100px;}
.simple-little-table .birthday {width: 120px;}
.simple-little-table .sex {width: 30px;}
.simple-little-table .city {width: 100px;}
.simple-little-table .registered {width: 120px;}
.simple-little-table .vk {width: 200px;}
.simple-little-table .disable {width: 30px;}
.simple-little-table .delete {width: 30px;}
.simple-little-table .disableOrDeleteComments {width: 300px;}

.simple-little-table.foul-table {width: 90%}
.simple-little-table.foul-table .foul{ vertical-align: central;width: 350px;}
.simple-little-table.foul-table .fouldescription{}

.main {
    color: #bd5a35;
    font-weight: bold;
}
.login {
    color: #0000ff;
    font-weight: bold;
}
.fio {
    color: #2FB12F;
}
.ArrayToStringElement {
    float: left;
    margin: 2px 0px 0px 2px;
    padding: 2px;
    border: 1px solid #bd5a35;
}

.Block {
    border: 1px solid #ccc;
    border-bottom: none;
    margin-bottom: 10px;
}
.BlockName {
    font: bold 20px/30px "PT Sans",helvetica,"segoe UI",arial,sans-serif;
    cursor: pointer;
    border-bottom: 1px solid #ccc;
    background: #ededed;
    padding: 0px;
    margin: 0px;
    text-align: center;
}
.BlockData {
    border-bottom: 1px solid #ccc;
    display: none;
    background: #fafafa;
    padding-top: 1px;
    margin: 0px;
    text-align: center;
}
.MainBlock .BlockData {
    padding: 10px;
}
.MainBlock .BlockName {
    color: #e71414;
}

.MainBlock .BlockData .Block .BlockData {
    padding: 0px;
}
.MainBlock .BlockData .Block .BlockName {
    color: #666;
}

.simple-little-table tr.disableUser td { background: #FFF0C6;}
.simple-little-table tr.deleteUser td { background: #FFE2D6;}
.simple-little-table tr.disableUser.deleteUser td {background: #FFD7D7;}

</style>
<script type="text/javascript" src="./plugins/jquery/lib/jquery-2.0.3.min.js"></script>
<script type="text/javascript">
    function showHideBlock(BlockId) {
        $("#" + BlockId).toggle();
    }
</script>
</head>
<body>
<?php
@session_start();
// Включить отображение ошибок
ini_set("display_errors",1);
error_reporting(E_ALL);
// Подключение библиотек ядра
include_once './ROOT/functions/includeSistemClasses.php';
includeSistemClasses();
// Инициализация параметров
global $_PARAM;
$_PARAM = null;
// Инициализация конфигурации
global $_SITECONFIG;
$_SITECONFIG = new SiteConfig();
// Создание помошника базы данных
global $_SQL_HELPER;
// Создание помошника базы данных
global $_SQL_HELPER;
$_SQL_HELPER = new MysqliHelper();
// Поулчение параметров ссылки
$urlParams = new UrlParams();
global $_URL_PARAMS;
$_URL_PARAMS = $urlParams->getUrlParam();
$urlHelper = new UrlHelper();
// Получение карты сайта
global $SITE_MAP;
$SITE_MAP = new SiteMap();
include_once './SerchTU/SerchTU.php';
$serchTU = new SerchTU();
$serchTU->getAllReport();
?>
</body>
</html>
