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
                $editeRequest = new JiyuuFestRequest_EditeRequest_Karaoke($this->requestID);
                break;
            case 'dance':
                $editeRequest = new JiyuuFestRequest_EditeRequest_Dance($this->requestID);
                break;
            case 'scene':
                $editeRequest = new JiyuuFestRequest_EditeRequest_Scene($this->requestID);
                break;
            case 'defile':
                $editeRequest = new JiyuuFestRequest_EditeRequest_Defile($this->requestID);
                break;
            case 'action_defile':
                $editeRequest = new JiyuuFestRequest_EditeRequest_ActionDefile($this->requestID);
                break;
            case 'amv':
                $editeRequest = new JiyuuFestRequest_EditeRequest_AMV($this->requestID);
                break;
            case 'video_cosplay':
                $editeRequest = new JiyuuFestRequest_EditeRequest_VideoCosplay($this->requestID);
                break;
            case 'image':
                $editeRequest = new JiyuuFestRequest_EditeRequest_Image($this->requestID);
                break;
            case 'photo':
                $editeRequest = new JiyuuFestRequest_EditeRequest_Photo($this->requestID);
                break;
        }
        if(isset($editeRequest)) {
            $this->HTML = $editeRequest->getHtml();
        } else {
            $this->HTML = 'ERROR';
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
