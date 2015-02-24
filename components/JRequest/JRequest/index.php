<?php

include_once './ROOT/classes/SafeLoadingFiles.php';
include_once './components/JRequest/classes/JRequestAddFile.php';
include_once './components/JRequest/classes/JRequestAddUserFile.php';
include_once './components/JRequest/classes/JRequestData.php';
include_once './components/JRequest/classes/JRequestFestsList.php';
include_once './components/JRequest/classes/JRequestList.php';
include_once './components/JRequest/classes/JRequest.php';
include_once './components/JRequest/classes/JRequestAddUsers.php';
include_once './components/JRequest/classes/JRequestAddUsers2.php';
include_once './components/JRequest/classes/JRequestCheck.php';

include_once './components/JRequest/classes/JRequestAMV.php';
include_once './components/JRequest/classes/JRequestActionDefile.php';
include_once './components/JRequest/classes/JRequestDance.php';
include_once './components/JRequest/classes/JRequestDefile.php';
include_once './components/JRequest/classes/JRequestImage.php';
include_once './components/JRequest/classes/JRequestKaraoke.php';
include_once './components/JRequest/classes/JRequestPhoto.php';
include_once './components/JRequest/classes/JRequestScene.php';
include_once './components/JRequest/classes/JRequestVideoCosplay.php';
include_once './components/JRequest/classes/JRequestMain.php';

$jRequestMain = new JRequestMain();
echo $jRequestMain->get();
?>