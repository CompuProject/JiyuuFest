<?php
/** 
* @version      1.0 
* @package      
* @copyright    Copyright (C) 2012 - 2014 Compu Project - All Rights Reserved. 
* @license      GNU General Public License version 3 or later;
*/
global $ROOT;
?>
<!DOCTYPE HTML>
<html>
<head>
    <?php $ROOT->head();?>
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic&subset=latin,cyrillic-ext,latin-ext,cyrillic' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="./templates/JiyuuPromo/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./templates/JiyuuPromo/css/main.css" type="text/css" />
    <link rel="stylesheet" href="<?php $ROOT->templatePath();?>css/main.css" type="text/css" />
    <link rel="stylesheet" href="./templates/JiyuuPromo/css/content.css" type="text/css" />
    <link rel="stylesheet" href="./templates/JiyuuPromo/css/materials.css" type="text/css" />
    <link rel="stylesheet" href="./templates/JiyuuPromo/css/account.css" type="text/css" />
    <!--<link rel="stylesheet" href="./templates/JiyuuPromo/css/JRequest.css" type="text/css" />-->
    <link rel="stylesheet" href="./templates/JiyuuPromo/css/JiyuuFestRequests.css" type="text/css" />
    <link rel="stylesheet" href="./templates/JiyuuPromo/css/fests.css" type="text/css" />
    <link rel="stylesheet" href="/templates/JiyuuPromo/css/modules.css" type="text/css" />
    <link rel="stylesheet" href="/templates/JiyuuPromo/css/AdminPanel.css" type="text/css" />
</head>
<body>
    <?php $ROOT->bodyStart();?>
    <div class="TopPanel">
        <div class="TopPanelBlock">
            <div class="HomeIcon"><a href="./"></a></div>
            <?php $ROOT->block('TopPanel');?>
        </div>
    </div>
    <div class="LeftPanel">
        <?php $ROOT->block('LeftPanel');?>
    </div>
    <div class="Wraper">
        <div class="MainWraper">
            <div class="LogoBlock"></div>
            <div class="Hand"><div class="HandImg"></div></div>
            <div class="ContentBlock">
                <div class="TitleBlock">
                    <?php $ROOT->title();?>
                </div>
                <?php $ROOT->content();?>
            </div>
        </div>
        <div class="FutterBlock">
            <?php $ROOT->block('FutterBlock');?>
        </div>
    </div>
    <?php $ROOT->bodyEnd();?>
</body>
</html>