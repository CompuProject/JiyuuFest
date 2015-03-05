<?php
    include_once './components/JiyuuFestRequests/classes/RequestsPermissions.php';
    
    include_once './components/JiyuuFestRequests/classes/JiyuuFestsMain.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestsList.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFests.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequestType.php';
    
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_CreateRequest.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_CreateRequest/JiyuuFestRequest_CreateRequest_Karaoke.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_CreateRequest/JiyuuFestRequest_CreateRequest_Dance.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_CreateRequest/JiyuuFestRequest_CreateRequest_Scene.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_CreateRequest/JiyuuFestRequest_CreateRequest_Defile.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_CreateRequest/JiyuuFestRequest_CreateRequest_ActionDefile.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_CreateRequest/JiyuuFestRequest_CreateRequest_AMV.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_CreateRequest/JiyuuFestRequest_CreateRequest_VideoCosplay.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_CreateRequest/JiyuuFestRequest_CreateRequest_Image.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_CreateRequest/JiyuuFestRequest_CreateRequest_Photo.php';
    
    
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_EditeRequestMain.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_EditeRequest.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_EditeRequest/JiyuuFestRequest_EditeRequest_Karaoke.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_EditeRequest/JiyuuFestRequest_EditeRequest_Dance.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_EditeRequest/JiyuuFestRequest_EditeRequest_Scene.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_EditeRequest/JiyuuFestRequest_EditeRequest_Defile.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_EditeRequest/JiyuuFestRequest_EditeRequest_ActionDefile.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_EditeRequest/JiyuuFestRequest_EditeRequest_AMV.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_EditeRequest/JiyuuFestRequest_EditeRequest_VideoCosplay.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_EditeRequest/JiyuuFestRequest_EditeRequest_Image.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_EditeRequest/JiyuuFestRequest_EditeRequest_Photo.php';
    
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_DeleteRequest.php';
    
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_ShowRequest.php';
    include_once './components/JiyuuFestRequests/classes/JiyuuFestRequest_ShowRequestUI.php';
    $jiyuuFestsMain = new JiyuuFestsMain();
    $jiyuuFestsMain->get();
?>
