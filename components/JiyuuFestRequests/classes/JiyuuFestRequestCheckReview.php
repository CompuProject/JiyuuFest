<?php
/**
 * Description of JiyuuFestRequestCheckReview
 *
 * @author maxim
 */
class JiyuuFestRequestCheckReview {
    // помошники
    private $SQL_HELPER;
    private $urlHelper;
    // данные
    private $requestID;
    private $requestInfo;
    private $requestData;
    private $requestUsers;
    private $requestCheck_K=0;
    private $requestCheck_P=0;
    
    
    public function __construct($requestID) {
        $this->requestID = $requestID;
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->getRequestInfo();
        $this->getRequestData();
        $this->getUsersData();
        $this->calculateK();
    }
    
    private function getRequestInfo() {
        $query = "SELECT 
            JFR.`status`,
            JFRT.`type`, 
            JFRT.`code`, 
            JFRT.`name`, 
            JFRT.`regulations`, 
            JFRT.`tableName`, 
            JFRT.`sequence`, 
            JFRT.`minNumberOfParticipants`, 
            JFRT.`maxNumberOfParticipants`, 
            JFRT.`minDurationMinutes`, 
            JFRT.`minDurationSeconds`, 
            JFRT.`maxDurationMinutes`, 
            JFRT.`maxDurationSeconds`, 
            JFRT.`mayBeContest`, 
            JFRT.`characterName`, 
            JFRT.`photo`, 
            JFRT.`original`, 
            JFRT.`intramural`, 
            JFRT.`subtype`
            FROM `JiyuuFestRequest` as JFR 
            LEFT JOIN `JiyuuFestRequestType` as JFRT 
            on JFR.`type` = JFRT.`type`
            where JFR.`request`='".$this->requestID."';";
        $this->requestInfo = $this->SQL_HELPER->select($query,1);
    }
    
    private function getRequestData() {
        $query = "SELECT * 
            FROM `JiyuuFestRequest` as JFR 
            LEFT JOIN `".$this->requestInfo['tableName']."` as JFRT 
            on JFR.`request` = JFRT.`request`
            where JFR.`request`='".$this->requestID."';";
        $this->requestData = $this->SQL_HELPER->select($query,1);
    }
    
    private function getUsersData() {
        $query = "SELECT * FROM `JiyuuFestRequestUsers` 
            where `request`='".$this->requestID."';";
        $this->requestUsers = $this->SQL_HELPER->select($query);
    }
    
    private function checkUsers() {
        $allCount = $this->requestData['numberOfParticipants'];
        $userCount = 0;
        $characterName = $this->requestInfo['characterName'] === '1';
        $photo = $this->requestInfo['photo'] === '1';
        $original = $this->requestInfo['original'] === '1';
        foreach ($this->requestUsers as $user) {
            $userCount += $this->checkUsersInfo($user);
        }
        if($allCount == $userCount) {
            return 1;
        } else {
            return $userCount / $allCount;
        }
    }
    
    private function checkUsersInfo($user) {
        $userCheck = 1;
        if(!isset($user['confirmed']) || $user['confirmed'] === null || $user['confirmed']==='' || $user['confirmed']==='0') {
            $userCheck = 0;
        }
        if($this->requestInfo['characterName'] === '1') {
            if(!isset($user['characterName']) || $user['characterName'] === null || $user['characterName']==='') {
                $userCheck = 0;
            }
        }
        if($this->requestInfo['photo'] === '1') {
            if(!isset($user['photo']) || $user['photo'] === null || $user['photo']==='') {
                $userCheck = 0;
            }
        }
        if($this->requestInfo['original'] === '1') {
            if(!isset($user['original']) || $user['original'] === null || $user['original']==='') {
                $userCheck = 0;
            }
        }
        return $userCheck;
    }
    private function checkForAllData($check) {
        $sum = 0;
        foreach ($check as $el) {
            $sum += $el;
        }
        if(count($check) === $sum) {
            return 1;
        } else {
            return $sum / count($check);
        }
    }
    
    private function checkActionDefile() {
        $check['defileType'] = $this->checkRequestDataElement_RN('defileType');
        $check['actionDefileTitle'] = $this->checkRequestDataElement_RN('actionDefileTitle');
        $check['fendom'] = $this->checkRequestDataElement_RN('fendom');
        //demo audition
        if($this->checkRequestDataElement('demo') || $this->checkRequestDataElement('audition',true)) {
            $check['demo'] = 1;
        } else {
            $check['demo'] = 0;
        }
        //audio instrumental audioInVideo
        if($this->checkRequestDataElement('audio') || $this->checkRequestDataElement('audition',true) || ($this->checkRequestDataElement('audioInVideo',true) && $this->checkRequestDataElement('video'))) {
            $check['audio'] = 1;
        } else {
            $check['audio'] = 0;
        }
        //video noVideo
        if($this->checkRequestDataElement('video') || $this->checkRequestDataElement('noVideo',true)) {
            $check['video'] = 1;
        } else {
            $check['video'] = 0;
        }
        if($this->checkRequestDataElement('collage') || $this->requestData['defileType']==='original') {
            $check['collage'] = 1;
        } else {
            $check['collage'] = 0;
        }
        $check['users'] = $this->checkUsers();
        return $this->checkForAllData($check);
    }
    
    private function checkAMV() {
        
    }
    
    private function checkDance() {
        
    }
    
    private function checkDefile() {
        
    }
    
    private function checkImage() {
        
    }
    
    private function checkKaraoke() {
        
    }
    
    private function checkPhoto() {
        
    }
    
    private function checkScene() {
        
    }
    
    private function checkVideoCosplay() {
        
    }
    
    private function checkRequestDataElement($key, $bool = false) {
        return isset($this->requestData[$key]) && $this->requestData[$key]!==null && $this->requestData[$key]!=='' && (!$bool || $this->requestData[$key]==='1');
    }
    
    private function checkRequestDataElement_RN($key, $bool = false) {
        if($this->checkRequestDataElement($key, $bool)) {
            return 1;
        } else {
            return 0;
        }
    }
    
    private function calculateK() {
        switch ($this->requestInfo['type']) {
            case 'action_defile':
                $this->requestCheck_K = $this->checkActionDefile();
                break;
            case 'amv':
                $this->requestCheck_K = 0.5;
                break;
            case 'dance':
                $this->requestCheck_K = 0.5;
                break;
            case 'defile':
                $this->requestCheck_K = 0.5;
                break;
            case 'image':
                $this->requestCheck_K = 0.5;
                break;
            case 'karaoke':
                $this->requestCheck_K = 0.5;
                break;
            case 'photo':
                $this->requestCheck_K = 0.5;
                break;
            case 'scene':
                $this->requestCheck_K = 0.5;
                break;
            case 'video_cosplay':
                $this->requestCheck_K = 0.5;
                break;
            default:
                $this->requestCheck_K = 0;
                break;
        }
        $this->requestCheck_P = $this->requestCheck_K * 100;
        $this->requestCheck_P .= '%';
    }
    
    public function getK() {
        return $this->requestCheck_K;
    }
    
    public function getP() {
        return $this->requestCheck_P;
    }
}
