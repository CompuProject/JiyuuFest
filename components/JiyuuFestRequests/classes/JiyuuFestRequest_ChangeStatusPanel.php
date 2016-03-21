<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JiyuuFestRequest_ChangeStatusPanel
 *
 * @author Maxim Zaytsev
 * @copyright © 2010-2016, CompuProjec
 * @created 30.11.2015 12:24:09
 */
class JiyuuFestRequest_ChangeStatusPanel {
    // помошники
    private $SQL_HELPER;
    private $localization;
    // пользователи
    protected $yourUser;
    protected $yourUserData;
    // данные
    private $requestID;
    private $fest;
    private $festData;
    private $status;
    private $availableTransitions = array();
    private $thisDate;
    // вывод
    private $HTML = "";

    public function __construct($requestID) {
        $this->thisDate = new DateTime();
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->getUserData();
        $this->localization = new Localization("JiyuuFests");
        $this->requestID = $requestID;
        if($this->getRequestData()) {
            $this->getFestData();
            $this->getAvailableTransition();
            if($this->yourUser->isAdmin() || $this->checkStartStopDate() || $this->checkStopEndDate()) {
                $this->generateChangeStatusPanel();
            }
        }
    }

    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function getFestData() {
        $query = "SELECT * FROM `JiyuuFest` WHERE `fest`='".$this->fest."';";
        $this->festData = $this->SQL_HELPER->select($query,1);
    }

    private function getRequestData() {
        if($this->yourUser->isAdmin()) {
            $query = "SELECT `status`,`fest` FROM `JiyuuFestRequest` WHERE `request`='" . $this->requestID . "';";
        } else {
            $query = "SELECT `status`,`fest` FROM `JiyuuFestRequest` WHERE `createdFor`='" . $this->yourUserData['login'] . "' AND `request`='" . $this->requestID . "';";
        }
        $rez = $this->SQL_HELPER->select($query, 1);
        if (!empty($rez) && $rez !== null) {
            $this->fest = $rez['fest'];
            $this->status = $rez['status'];
            return true;
        }
        return false;
    }
    
    private function isCreatedFor() {
        $query = "SELECT count(`request`) as amount FROM `JiyuuFestRequest` WHERE `createdFor`='" . $this->yourUserData['login'] . "' AND `request`='" . $this->requestID . "';";
        $rez = $this->SQL_HELPER->select($query, 1);
        return $rez['amount'] > 0;
    }
    
    private function getAvailableTransition() {
        $query = "SELECT `endStatus`,`bottonText`,`justReady` FROM `JiyuuFestRequestStatusTransition` WHERE `startStatus`='" . $this->status . "' AND ";
        if ($this->yourUser->isAdmin()) {
            if($this->isCreatedFor()) {
                $query .= "(`forUser`='1' OR`forAdmin`='1')";
            } else {
                $query .= "`forAdmin`='1'";
            }
        } else {
            $query .= "`forUser`='1'";
        }
        $this->availableTransitions = $this->SQL_HELPER->select($query);
    }
    
    private function generateChangeStatusPanel() {
        $urlHelper = new UrlHelper();
        $progressBar = new JiyuuFestRequest_ProgressBar($this->requestID);
        $this->HTML = '<div class="RequestChangeStatusPanel">';
        foreach ($this->availableTransitions as $transition) {
            if($transition['justReady'] < 1 || $progressBar->isReady()) {
                $this->HTML .= '<div class="RequestChangeStatusPanelBotton">';
                $this->HTML .= '<a href="'.$urlHelper->chengeParams(array($this->fest,'changeStatus',$this->requestID,$transition['endStatus'])).'">';
                $this->HTML .= $transition['bottonText'];
                $this->HTML .= '</a>';
                $this->HTML .= '</div>';
            }
        }
        $this->HTML .= '<div class="clear"></div>';
        $this->HTML .= '</div>';
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

    public function getChangeStatusPanelHtml() {
        return $this->HTML;
    }
}
