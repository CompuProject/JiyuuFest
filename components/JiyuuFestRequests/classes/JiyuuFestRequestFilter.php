<?php
/**
 * Description of JiyuuFestRequestFilter
 *
 * @author maxim
 */
class JiyuuFestRequestFilter {
    private $SQL_HELPER;
    private $inputHelper;
    private $localization;
    private $urlHelper;
    
    private $fest;
    private $filterData;
    private $requestType;
    private $requestStatus;
    private $requestIdLists;
    
    private $errorBuffer;
    private $HTML;
    
    public function __construct($fest) {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->fest = $fest;
        $this->errorBuffer = array();
        $this->inputHelper = new InputHelper();
        $this->localization = new Localization("JiyuuFests");
        $this->urlHelper = new UrlHelper();
        $this->getRequestType();
        $this->getRequestStatus();
        $this->getAllFilterData();
        $this->getRequestIdLists();
        $this->generateHTML();
    }
    
    private function getAllFilterData() {
        $this->filterData['contest'] = $this->getFilterData('contest');
        $this->filterData['requestType'] = $this->getFilterData('requestType');
        $this->filterData['requestStatus'] = $this->getFilterData('requestStatus');
        $this->filterData['createdFor'] = $this->getFilterData('createdFor');
        $this->filterData['user'] = $this->getFilterData('user');
    }
    
    private function getRequestIdLists() {
        $query = "SELECT 
            JFR.`request`
            FROM `JiyuuFestRequest` as JFR 
            LEFT JOIN `JiyuuFestRequestUsers` as JFRU 
            on JFR.`request` = JFRU.`request`";
        
        $filter = '';
        if($this->filterData['contest'] !== 'all') {
            $filter .= "JFR.`contest`='".$this->filterData['contest']."' AND ";
        }
        if($this->filterData['requestType'] !== 'all') {
            $filter .= "JFR.`type`='".$this->filterData['requestType']."' AND ";
        }
        if($this->filterData['requestStatus'] !== 'all') {
            $filter .= "JFR.`status`='".$this->filterData['requestStatus']."' AND ";
        }
        if($this->filterData['createdFor'] !== null) {
            $filter .= "JFR.`createdFor`='".$this->filterData['createdFor']."' AND ";
        }
        if($this->filterData['user'] !== null) {
            $filter .= "JFRU.`user`='".$this->filterData['user']."' AND ";
        }
        if($filter !== '') {
            $filter = ' WHERE '.substr($filter, 0, strlen($filter)-5);
        }
        $query .= $filter." GROUP BY JFR.`request`;";
        $this->requestIdLists = $this->SQL_HELPER->select($query);
    }
    
    private function getRequestType() {
        $query = "SELECT `type`,`name` FROM `JiyuuFestRequestType`";
        $this->requestType = $this->SQL_HELPER->select($query);
    }
    
    private function getRequestStatus() {
        $query = "SELECT `status`,`name` FROM `JiyuuFestRequestStatus`";
        $this->requestStatus = $this->SQL_HELPER->select($query);
    }
    
    private function generateHTML() {
        $this->HTML = '';
        $this->HTML .= $this->generateFilter();
        $this->HTML .= $this->generateList();
    }
    private function generateList() {
        $out = '';
        foreach ($this->requestIdLists as $request) {
            $requestUI = new JiyuuFestRequest_ShowRequest($request['request'],true);
            $out .= $requestUI->getHtml();
        }
        return $out;
    }

    private function generateFilter() {
        $out = '';
        $out .= '<form class="JFRequestForm" name="JFRequestForm" action="'.$this->urlHelper->getThisPage().'" enctype="multipart/form-data" method="post" accept-charset="UTF-8" autocomplete="on">';
        $out .= '<center>';
        $out .= '<table class="JFRequestFormTable" >';
        $contestValueArray = array();
        $contestValueArray[0]['value']='all';
        $contestValueArray[0]['text']='Не важно';
        $contestValueArray[1]['value']='1';
        $contestValueArray[1]['text']=$this->localization->getText("ContestVal");
        $contestValueArray[2]['value']='0';
        $contestValueArray[2]['text']=$this->localization->getText("NoContestVal");
        $contest = $this->inputHelper->select('contest', 'contest', $contestValueArray, true, $this->getFilterData('contest'));
        $out .= $this->inputHelper->createFormRow($contest, true, $this->localization->getText("contest"));
        $requestTypeData = array();
        $requestTypeData[0]['value']='all';
        $requestTypeData[0]['text']='Все типы';
        foreach ($this->requestType as $key => $type) {
            $requestTypeData[$key+1]['value']=$type['type'];
            $requestTypeData[$key+1]['text']=$type['name'];
        }
        $requestType = $this->inputHelper->select('requestType', 'requestType', $requestTypeData, true, $this->getFilterData('requestType'));
        $out .= $this->inputHelper->createFormRow($requestType, true, 'Тип');
        
        $requestStatusData = array();
        $requestStatusData[0]['value']='all';
        $requestStatusData[0]['text']='Все статусы';
        foreach ($this->requestStatus as $key => $status) {
            $requestStatusData[$key+1]['value']=$status['status'];
            $requestStatusData[$key+1]['text']=$status['name'];
        }
        $requestStatus = $this->inputHelper->select('requestStatus', 'requestStatus', $requestStatusData, true, $this->getFilterData('requestStatus'));
        $out .= $this->inputHelper->createFormRow($requestStatus, true, 'Статус');
        
        $createdFor = $this->inputHelper->textBox('createdFor', 'createdFor', 'createdFor', 25, false, $this->getFilterData('createdFor'));
        $out .= $this->inputHelper->createFormRow($createdFor, false, 'Создатель заявки');
        
        $user = $this->inputHelper->textBox('user', 'user', 'user', 25, false, $this->getFilterData('user'));
        $out .= $this->inputHelper->createFormRow($user, false, 'Участник заявки');
        
        $out .= '</table>';
        $out .= '<center>';
        $out .= '<input class="JFRequestFormButton" type="submit" name="JFRequestFormSubmit" value="Показать">';
        $out .= '</form>';
        return $out;
    }

    private function getFilterData($key) {
        return (
                isset($_POST[$key]) && 
                $_POST[$key]!==null && 
                $_POST[$key]!=""
        ) ? $_POST[$key] : null;
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
}
