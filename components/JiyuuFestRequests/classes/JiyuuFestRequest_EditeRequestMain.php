<?php
class JiyuuFestRequest_EditeRequestMain {
    private $HTML;
    private $SQL_HELPER;
    private $requestID;
    private $requestType;
    
    public function __construct($requestID) {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->requestID = $requestID;
        $this->getRequestData();
        $this->generateUi();
//        $this->HTML = $requestID;
    }
    
    private function generateUi() {
        switch ($this->requestType) {
            case 'karaoke':
                $this->HTML = 'karaoke';
                break;
            case 'dance':
                $this->HTML = 'dance';
                break;
            case 'scene':
                $this->HTML = 'scene';
                break;
            case 'defile':
                $this->HTML = 'defile';
                break;
            case 'action_defile':
                $editeRequest = new JiyuuFestRequest_EditeRequest_ActionDefile($this->requestID);
                $this->HTML = $editeRequest->getHtml();
                break;
            case 'amv':
                $this->HTML = 'amv';
                break;
            case 'video_cosplay':
                $this->HTML = 'video_cosplay';
                break;
            case 'image':
                $this->HTML = 'image';
                break;
            case 'photo':
                $this->HTML = 'photo';
                break;
            default:
                $this->HTML = 'ERROR';
                break;
        }
    }
    
    private function getRequestData() {
        $query = "SELECT `type` FROM `JiyuuFestRequest` WHERE `request`='".$this->requestID."';";
        $rezult = $this->SQL_HELPER->select($query,1);
        if(isset($rezult['type'])) {
            $this->requestType = $rezult['type'];
        } else {
            $this->requestType = 'undefinedType';
        }
    }

    public function getHtml() {
        return $this->HTML;
    }
}
