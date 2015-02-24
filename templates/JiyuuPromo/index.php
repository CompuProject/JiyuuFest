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
    <link rel="shortcut icon" href="<?php $ROOT->templatePath();?>favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?php $ROOT->templatePath();?>css/main.css" type="text/css" />
    <link rel="stylesheet" href="<?php $ROOT->templatePath();?>css/content.css" type="text/css" />
    <link rel="stylesheet" href="<?php $ROOT->templatePath();?>css/materials.css" type="text/css" />
    <link rel="stylesheet" href="<?php $ROOT->templatePath();?>css/account.css" type="text/css" />
    <link rel="stylesheet" href="<?php $ROOT->templatePath();?>css/JiyuuFestRequests.css" type="text/css" />
    <link rel="stylesheet" href="<?php $ROOT->templatePath();?>css/fests.css" type="text/css" />
    <link rel="stylesheet" href="<?php $ROOT->templatePath();?>css/modules.css" type="text/css" />
    <link rel="stylesheet" href="<?php $ROOT->templatePath();?>css/AdminPanel.css" type="text/css" />
</head>
<body class='PromoTemplate'>
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
            <div class="HeadBlock">
                <div class="Hand"><div class="HandImg"></div></div>
                <div class="HeadBlockContent">
                    <?php $ROOT->block('HeadBlock');?>
                </div>
            </div>
            <div class="TitleBlock">
                <?php $ROOT->title();?>
            </div>
            <div class="ContentBlock">
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