<?php
/**
 * Description of JiyuuFestRequest_ShowRequest
 *
 * @author maxim
 */
class JiyuuFestRequest_ShowRequest {
    // помошники
    private $SQL_HELPER;
    private $requestsPermissions;
    private $localization;
    // данныеданные
    private $requestMainData;
    private $requestFestData;
    private $requestInformationData;
    private $requestUsersData;
    private $requestTypeData;
    private $requestExpansionData;
    // параметры
    private $requestID;
    private $administratorAccess = false;
    // пользователи
    private $yourUser;
    private $yourUserData;
    // вывод
    private $errorBuffer = array();
    private $UI;
    
    public function __construct($requestID, $administratorAccess = false) {
        $this->getUserData();
        if($this->yourUser->checkAuthorization()) {
            global $_SQL_HELPER;
            $this->SQL_HELPER = $_SQL_HELPER;
            $this->localization = new Localization("JiyuuFests");
            $this->requestID = $requestID;
            $this->thisDate = new DateTime();
            $this->requestsPermissions = new RequestsPermissions();
            if($administratorAccess && $this->yourUser->isAdmin()) {
                $this->administratorAccess = true;
            }
            $this->getData();
//            echo $requestID;
//            echo '<pre>';
//            var_dump($this->requestMainData);
//            echo '<br><br>';
//            var_dump($this->requestFestData);
//            echo '<br><br>';
//            var_dump($this->requestInformationData);
//            echo '<br><br>';
//            var_dump($this->requestTypeData);
//            echo '<br><br>';
//            var_dump($this->requestExpansionData);
//            echo '</pre>';
        }
    }

    public function getHtml() {
        if(count($this->errorBuffer) == 0) {
            return $this->UI->getHtml();
        } else {
            $out = "<div class='JRequestError'>";
            foreach ($this->errorBuffer as $error) {
                $out .= "<div>".$error."</div>";
            }
            $out .= "</div>";
            return $out;
        }
    }
    
    public function get() {
        if(count($this->errorBuffer) == 0) {
            echo $this->UI->getHtml();
        } else {
            $out = "<div class='JRequestError'>";
            foreach ($this->errorBuffer as $error) {
                $out .= "<div>".$error."</div>";
            }
            $out .= "</div>";
            echo $out;
        }
    }
    
    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function checkBeforeStartDate() {
        $date['IntStart'] = new DateTime($this->requestFestData['filingRequest_Intramural_Start']);
        $date['ExtStart'] = new DateTime($this->requestFestData['filingRequest_Extramural_Start']);
        return $this->thisDate < $date['IntStart'] && $this->thisDate < $date['ExtStart'];
    }
    
    private function checkStartStopDate() {
        $date['IntStart'] = new DateTime($this->requestFestData['filingRequest_Intramural_Start']);
        $date['IntStop'] = new DateTime($this->requestFestData['filingRequest_Intramural_Stop']);
        $date['ExtStart'] = new DateTime($this->requestFestData['filingRequest_Extramural_Start']);
        $date['ExtStop'] = new DateTime($this->requestFestData['filingRequest_Extramural_Stop']);
        return ($this->thisDate >= $date['IntStart'] || $this->thisDate >= $date['ExtStart']) && 
        ($this->thisDate <=$date['IntStop'] || $this->thisDate <= $date['ExtStop']);
    }
    
    private function checkStopEndDate() {
        $date['IntStop'] = new DateTime($this->requestFestData['filingRequest_Intramural_Stop']);
        $date['IntEnd'] = new DateTime($this->requestFestData['filingRequest_Intramural_End']);
        $date['ExtStop'] = new DateTime($this->requestFestData['filingRequest_Extramural_Stop']);
        $date['ExtEnd'] = new DateTime($this->requestFestData['filingRequest_Extramural_End']);
        return $this->thisDate >= $date['IntStop'] && $this->thisDate >= $date['ExtStop'] && 
                $this->thisDate <= $date['IntEnd'] && $this->thisDate <= $date['ExtEnd'];
    }
    
    private function checkEndDate() {
        $date['IntEnd'] = new DateTime($this->requestFestData['filingRequest_Intramural_End']);
        $date['ExtEnd'] = new DateTime($this->requestFestData['filingRequest_Extramural_End']);
        $festivalDay = new DateTime($this->requestFestData['festivalDay']);
        return $this->thisDate > $date['IntEnd'] && $this->thisDate > $date['ExtEnd'] && $this->thisDate <= $festivalDay;
    }
    
    private function checkEndFestDate() {
        $festivalDay = new DateTime($this->requestFestData['festivalDay']);
        return $this->thisDate > $festivalDay;
    }
    
    private function getData() {
        $this->getMainData();
        $this->getFestData();
        $this->getUsersData();
        $this->getInformationData();
        $this->getTypeData();
        $this->getExpansionData();
        $this->UI = new JiyuuFestRequest_ShowRequestUI(
                $this->requestFestData, 
                $this->requestMainData, 
                $this->requestUsersData,
                $this->requestTypeData, 
                $this->requestExpansionData, 
                $this->requestInformationData);
    }

    private function getMainData() {
        $query = "SELECT * FROM `JiyuuFestRequest` WHERE `request`='".$this->requestID."';";
        $this->requestMainData = $this->SQL_HELPER->select($query,1);
        $this->getMainDataMore();
    }
    private function getUsersData() {
        $query = "SELECT 
            JFRU.`request`, 
            JFRU.`user`, 
            Us.`nickname`, 
            JFRU.`confirmed`, 
            JFRU.`characterName`, 
            JFRU.`photo`, 
            JFRU.`original` 
            FROM 
            `JiyuuFestRequestUsers` as JFRU left join `Users` as Us
            on JFRU.`user` = Us.`login`
            where `request`='".$this->requestID."';";
        $this->requestUsersData = $this->SQL_HELPER->select($query);
    }
    
    private function getMainDataMore() {
        if($this->requestMainData['contest']>0) {
            $this->requestMainData['contestText'] = $this->localization->getText("ContestRequest");
        } else {
            $this->requestMainData['contestText'] = $this->localization->getText("NoContestRequest");
        }
        $query = "SELECT * FROM `JiyuuFestRequestType` WHERE `type`='".$this->requestMainData['type']."';";
        $type = $this->SQL_HELPER->select($query,1);
        $this->requestMainData['typeName'] = $type['name'];
        $this->requestMainData['typeRegulations'] = $type['regulations'];
        
        $query = "SELECT * FROM `JiyuuFestRequestStatus` WHERE `status`='".$this->requestMainData['status']."';";
        $status = $this->SQL_HELPER->select($query,1);
        $this->requestMainData['statusName'] = $status['name'];
        $this->requestMainData['statusDescription'] = $status['description'];
        
        $query = "SELECT `nickname` FROM `Users` WHERE `login`='".$this->requestMainData['createdFor']."';";
        $createdForNickname = $this->SQL_HELPER->select($query,1);
        $this->requestMainData['createdForNickname'] = $createdForNickname['nickname'];
    }
    
    private function getFestData() {
        $query = "SELECT * FROM `JiyuuFest` WHERE `fest`='".$this->requestMainData['fest']."';";
        $this->requestFestData = $this->SQL_HELPER->select($query,1);
    }
    
    private function getInformationData() {
        $this->getInformationData_TimeFrame();
        $this->getInformationData_User();
        $this->getInformationData_Permissions();
    }
    
    private function getTypeData() {
        $query = "SELECT * FROM `JiyuuFestRequestType` WHERE `type`='".$this->requestMainData['type']."';";
        $this->requestTypeData = $this->SQL_HELPER->select($query,1);
    }
    
    private function getExpansionData() {
        $query = "SELECT * FROM `".$this->requestTypeData['tableName']."` WHERE `request`='".$this->requestID."';";
        $this->requestExpansionData = $this->SQL_HELPER->select($query,1);
    }
    
    private function getInformationData_TimeFrame() {
        if($this->checkBeforeStartDate()) {
            $this->requestInformationData['TimeFrame'] = 'RequestNoStart';
        } 
        else if($this->checkStartStopDate()) {
            $this->requestInformationData['TimeFrame'] = 'RequestStart';
        } 
        else if($this->checkStopEndDate()) {
            $this->requestInformationData['TimeFrame'] = 'RequestStop';
        } 
        else if($this->checkEndDate()) {
            $this->requestInformationData['TimeFrame'] = 'RequestEnd';
        } 
        else if($this->checkEndFestDate()) {
            $this->requestInformationData['TimeFrame'] = 'FestEnd';
        }
    }
    
    private function getInformationData_Permissions() {
        $this->requestInformationData['Permissions']['showRequest'] = $this->requestsPermissions->getPermissions($this->requestInformationData['UserType'], $this->requestInformationData['TimeFrame'], $this->requestMainData['status'], 'showRequest');
        $this->requestInformationData['Permissions']['editRequest'] = $this->requestsPermissions->getPermissions($this->requestInformationData['UserType'], $this->requestInformationData['TimeFrame'], $this->requestMainData['status'], 'editRequest');
        $this->requestInformationData['Permissions']['editUsers'] = $this->requestsPermissions->getPermissions($this->requestInformationData['UserType'], $this->requestInformationData['TimeFrame'], $this->requestMainData['status'], 'editUsers');
        $this->requestInformationData['Permissions']['deleteRequest'] = $this->requestsPermissions->getPermissions($this->requestInformationData['UserType'], $this->requestInformationData['TimeFrame'], $this->requestMainData['status'], 'deleteRequest');
        $this->requestInformationData['Permissions']['changeStatus'] = $this->requestsPermissions->getPermissions($this->requestInformationData['UserType'], $this->requestInformationData['TimeFrame'], $this->requestMainData['status'], 'changeStatus');
    }

    private function getInformationData_User() {
        if($this->administratorAccess) {
             $this->requestInformationData['UserType'] = 'Admin';
        } else {
            $query = "SELECT COUNT(`request`) AS count FROM `JiyuuFestRequest` WHERE `request`='".$this->requestID."' AND `createdFor`='".$this->yourUserData['login']."';";
            $createdForData = $this->SQL_HELPER->select($query,1);
            if($createdForData['count'] > 0) {
                 $this->requestInformationData['UserType'] = 'Creator';
            } else {
                $query = "SELECT COUNT(`request`) AS count FROM `JiyuuFestRequestUsers` WHERE `request`='".$this->requestID." AND `user`='".$this->yourUserData['login']."';";
                $requestUsersData = $this->SQL_HELPER->select($query,1);
                if($requestUsersData['count'] > 0) {
                    $this->requestInformationData['UserType'] = 'Member';
                } else {
                    $this->requestInformationData['UserType'] = 'Browsing';
                }
            }
        }
    }
}
