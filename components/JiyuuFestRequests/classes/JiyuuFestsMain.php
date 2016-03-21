<?php

class JiyuuFestsMain {
    
    private $URL_PARAMS;
    private $SQL_HELPER;
    private $HTML;
    private $localization;
    private $errorBuffer = array();
    // пользователи
    protected $yourUser;
    protected $yourUserData;
    protected $festData;
    protected $thisDate;
    
    public function __construct() {
        $this->thisDate = new DateTime();
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->getUserData();
        $this->localization = new Localization("JiyuuFests");
        if($this->yourUser->checkAuthorization()) {
            global $_URL_PARAMS;
            $this->URL_PARAMS = $_URL_PARAMS['params'];
            $this->HTML = '';
            if(!isset($this->URL_PARAMS[0])) {
                $jiyuuFestsList = new JiyuuFestsList();
                $this->HTML = $jiyuuFestsList->getHtml();
            } else {
                if(isset($this->URL_PARAMS[0]) && $this->checkFestId($this->URL_PARAMS[0])) {
                    if(!isset($this->URL_PARAMS[1])){
                        $jiyuuFests = new JiyuuFests($this->URL_PARAMS[0]);
                        $this->HTML = $jiyuuFests->getHtml();
                    } else {
                        switch ($this->URL_PARAMS[1]) {
                            case 'createRequest':
                                if($this->yourUser->isAdmin() || $this->checkStartStopDate()) {
                                    if(!isset($this->URL_PARAMS[2])) {
                                        $jiyuuFestsRequestType = new JiyuuFestRequestType($this->URL_PARAMS[0]);
                                        $this->HTML = $jiyuuFestsRequestType->getHtml();
                                    } else {
                                        $this->HTML = $this->jiyuuFest_CreateRequest($this->URL_PARAMS[0],$this->URL_PARAMS[2]);
                                    }
                                } else {
                                    $this->errorBuffer[] = $this->localization->getText("ErrorCreateRequestStopOrNotStart");
                                }
                                break;
                            case 'editRequest':
                                if ($this->yourUser->isAdmin() || $this->checkStartStopDate() || $this->checkStopEndDate()) {
                                    if(isset($this->URL_PARAMS[2])) {
                                        $editeRequest = new JiyuuFestRequest_EditeRequestMain($this->URL_PARAMS[2]);
                                        $this->HTML = $editeRequest->getHtml(); 
                                    } else {
                                        $this->errorBuffer[] = $this->localization->getText("ErrorNoRequestID");
                                    }
                                }
                                break;
                            case 'deleteRequest':
                                if($this->yourUser->isAdmin() || isset($this->URL_PARAMS[2])) {
                                    $deleteRequest = new JiyuuFestRequest_DeleteRequest($this->URL_PARAMS[2], (isset($this->URL_PARAMS[3]) && $this->URL_PARAMS[3] == 'success'));
                                    $this->HTML = $deleteRequest->getHtml();
                                } else {
                                    $this->errorBuffer[] = $this->localization->getText("ErrorNoRequestID");
                                }
                                break;
                            case 'addRequestUser':
                                if ($this->yourUser->isAdmin() || $this->checkStartStopDate() || $this->checkStopEndDate()) {
                                    if(isset($this->URL_PARAMS[2])) {
                                        $user = new JiyuuFestRequestUsers_Add($this->URL_PARAMS[2]);
                                        $this->HTML = $user->getHtml();
                                    } else {
                                        $this->errorBuffer[] = $this->localization->getText("ErrorNoRequestID");
                                    }
                                }
                                break;
                            case 'deleteRequestUser':
                                if ($this->yourUser->isAdmin() || $this->checkStartStopDate() || $this->checkStopEndDate()) {
                                    if(isset($this->URL_PARAMS[2]) && isset($this->URL_PARAMS[3])) {
                                        $user = new JiyuuFestRequestUsers_Delete($this->URL_PARAMS[2],$this->URL_PARAMS[3],(isset($this->URL_PARAMS[4]) && $this->URL_PARAMS[4] == 'success'));
                                        $this->HTML = $user->getHtml();
                                    } else {
                                        $this->errorBuffer[] = $this->localization->getText("ErrorNoRequestID");
                                    }
                                }
                                break;
                            case 'editRequestUsers':
                                if ($this->yourUser->isAdmin() || $this->checkStartStopDate() || $this->checkStopEndDate()) {
                                    if(isset($this->URL_PARAMS[2]) && isset($this->URL_PARAMS[3])) {
                                        $user = new JiyuuFestRequestUsers_Edit($this->URL_PARAMS[2],$this->URL_PARAMS[3]);
                                        $this->HTML = $user->getHtml();
                                    } else {
                                        $this->errorBuffer[] = $this->localization->getText("ErrorNoRequestID");
                                    }
                                }
                                break;
                            case 'adminpage':
                                if ($this->yourUser->isAdmin()) {
                                    $requestsListFromFilter = new JiyuuFestRequestFilter($this->URL_PARAMS[0]);
                                    $this->HTML = $requestsListFromFilter->getHtml();
                                } else {
                                    $this->errorBuffer[] = $this->localization->getText("ErrorPermissionDenied");
                                }
                                break;
                            case 'changeStatus':
                                if ($this->yourUser->isAdmin() || $this->checkStartStopDate() || $this->checkStopEndDate()) {
                                    if(isset($this->URL_PARAMS[2]) && isset($this->URL_PARAMS[3])) {
                                        $user = new JiyuuFestRequest_ChangeStatus($this->URL_PARAMS[2],$this->URL_PARAMS[3]);
                                        $this->HTML = $user->getHtml();
                                    } else {
                                        $this->errorBuffer[] = $this->localization->getText("ErrorNoRequestID");
                                    }
                                } else {
                                    $this->errorBuffer[] = $this->localization->getText("ErrorChangeStatusForOldRequest");
                                }
                                break;
                            case 'showApprovedRequest':
                                $this->HTML ='показать одобренные заявки';
                                break;
                            case 'showRequest':
                                if(isset($this->URL_PARAMS[2])) {
                                    if(isset($this->URL_PARAMS[3]) && $this->URL_PARAMS[3] === 'true') {
                                        $administratorAccess = true;
                                    } else {
                                        $administratorAccess = false;
                                    }
                                    $showRequest = new JiyuuFestRequest_ShowRequest($this->URL_PARAMS[2],$administratorAccess);
                                    $this->HTML = $showRequest->getHtml();
                                } else {
                                    $this->errorBuffer[] = $this->localization->getText("ErrorNoRequestID");
                                }
                                break;
                            default:
                                $this->errorBuffer[] = $this->localization->getText("ErrorUnknownRequestAction");
                                break;
                        }
                    }
                } else if(isset($this->URL_PARAMS[0]) && $this->URL_PARAMS[0]=='adminpage') {
                    if ($this->yourUser->isAdmin()) {
                        $requestsListFromFilter = new JiyuuFestRequestFilter();
                        $this->HTML = $requestsListFromFilter->getHtml();
                    } else {
                        $this->errorBuffer[] = $this->localization->getText("ErrorPermissionDenied");
                    }
                }else {
                    $this->errorBuffer[] = $this->localization->getText("ErrorUnknownFest");
                }
            }
        } else {
            $this->errorBuffer[] = $this->localization->getText("ErrorUnauthorized");
        }
    }
    
    private function getFestData($fest) {
        $query = "SELECT * FROM `JiyuuFest` WHERE `fest`='".$fest."';";
        $this->festData = $this->SQL_HELPER->select($query,1);
    }


    private function checkFestId($fest) {
        $this->festData = array();
        $this->getFestData($fest);
        return count($this->festData) > 0;
    }


    private function jiyuuFest_CreateRequest($fest,$type) {
        $jiyuuFestRequest = null;
        switch ($type) {
            case 'karaoke':
                $jiyuuFestRequest = new JiyuuFestRequest_CreateRequest_Karaoke($fest,$type);
                break;
            case 'dance':
                $jiyuuFestRequest = new JiyuuFestRequest_CreateRequest_Dance($fest,$type);
                break;
            case 'scene':
                $jiyuuFestRequest = new JiyuuFestRequest_CreateRequest_Scene($fest,$type);
                break;
            case 'defile':
                $jiyuuFestRequest = new JiyuuFestRequest_CreateRequest_Defile($fest,$type);
                break;
            case 'action_defile':
                $jiyuuFestRequest = new JiyuuFestRequest_CreateRequest_ActionDefile($fest,$type);
                break;
            case 'amv':
                $jiyuuFestRequest = new JiyuuFestRequest_CreateRequest_AMV($fest,$type);
                break;
            case 'video_cosplay':
                $jiyuuFestRequest = new JiyuuFestRequest_CreateRequest_VideoCosplay($fest,$type);
                break;
            case 'image':
                $jiyuuFestRequest = new JiyuuFestRequest_CreateRequest_Image($fest,$type);
                break;
            case 'photo':
                $jiyuuFestRequest = new JiyuuFestRequest_CreateRequest_Photo($fest,$type);
                break;
            default:
                $this->errorBuffer[] = $this->localization->getText("ErrorUnknownRequestType");
                break;
        }
        $jiyuuFestRequest != null ? $out = $jiyuuFestRequest->getHtml() : $out = '';
        return $out;
    }
    
    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function checkBeforeStartDate() {
        $date['IntStart'] = new DateTime($this->festData['filingRequest_Intramural_Start']);
        $date['ExtStart'] = new DateTime($this->festData['filingRequest_Extramural_Start']);
        return $this->thisDate < $date['IntStart'] && $this->thisDate < $date['ExtStart'];
    }
    
    private function checkStartStopDate() {
        $date['IntStart'] = new DateTime($this->festData['filingRequest_Intramural_Start']);
        $date['IntStop'] = new DateTime($this->festData['filingRequest_Intramural_Stop']);
        $date['ExtStart'] = new DateTime($this->festData['filingRequest_Extramural_Start']);
        $date['ExtStop'] = new DateTime($this->festData['filingRequest_Extramural_Stop']);
        return ($this->thisDate >= $date['IntStart'] || $this->thisDate >= $date['ExtStart']) && 
        ($this->thisDate <=$date['IntStop'] || $this->thisDate <= $date['ExtStop']);
    }
    
    private function checkStopEndDate() {
        $date['IntStop'] = new DateTime($this->festData['filingRequest_Intramural_Stop']);
        $date['IntEnd'] = new DateTime($this->festData['filingRequest_Intramural_End']);
        $date['ExtStop'] = new DateTime($this->festData['filingRequest_Extramural_Stop']);
        $date['ExtEnd'] = new DateTime($this->festData['filingRequest_Extramural_End']);
        return $this->thisDate >= $date['IntStop'] && $this->thisDate >= $date['ExtStop'] && 
                $this->thisDate <= $date['IntEnd'] && $this->thisDate <= $date['ExtEnd'];
    }
    
    private function checkEndDate() {
        $date['IntEnd'] = new DateTime($this->festData['filingRequest_Intramural_End']);
        $date['ExtEnd'] = new DateTime($this->festData['filingRequest_Extramural_End']);
        $festivalDay = new DateTime($this->festData['festivalDay']);
        return $this->thisDate > $date['IntEnd'] && $this->thisDate > $date['ExtEnd'] && $this->thisDate <= $festivalDay;
    }
    
    private function checkEndFestDate() {
        $festivalDay = new DateTime($this->festData['festivalDay']);
        return $this->thisDate > $festivalDay;
    }
    
    public function getHtml() {
        if(count($this->errorBuffer) == 0) {
            return $this->HTML;
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
        echo $this->getHtml();
    }
}
