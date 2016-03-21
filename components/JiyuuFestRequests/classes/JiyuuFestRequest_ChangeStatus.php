<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JiyuuFestRequest_ChangeStatus
 *
 * @author Maxim Zaytsev
 * @copyright © 2010-2016, CompuProjec
 * @created 30.11.2015 11:13:40
 */
class JiyuuFestRequest_ChangeStatus {

    // данные
    private $requestID;
    private $status;
    private $oldStatus;
    private $fest;
    private $errorBuffer = array();
    private $justReady;
    // пользователи
    private $yourUser;
    private $yourUserData;
    // вывод
    private $HTML;

    public function __construct($requestID, $status) {
        $this->requestID = $requestID;
        $this->status = $status;
        $this->getUserData();
        $this->localization = new Localization("JiyuuFests");
        if ($this->yourUser->checkAuthorization()) {
            global $_SQL_HELPER;
            $this->SQL_HELPER = $_SQL_HELPER;
            if ($this->getRequestStatus()) {
                $this->execution();
            } else {
                $this->errorBuffer[] = $this->localization->getText("ErrorPermissionDenied");
            }
        } else {
            $this->errorBuffer[] = $this->localization->getText("ErrorUnauthorized");
        }
    }
    private function isCreatedFor() {
        $query = "SELECT count(`request`) as amount FROM `JiyuuFestRequest` WHERE `createdFor`='" . $this->yourUserData['login'] . "' AND `request`='" . $this->requestID . "';";
        $rez = $this->SQL_HELPER->select($query, 1);
        return $rez['amount'] > 0;
    }

    private function getRequestStatus() {
        if($this->yourUser->isAdmin()) {
            $query = "SELECT `status`,`fest` FROM `JiyuuFestRequest` WHERE `request`='" . $this->requestID . "';";
        } else {
            $query = "SELECT `status`,`fest` FROM `JiyuuFestRequest` WHERE `createdFor`='" . $this->yourUserData['login'] . "' AND `request`='" . $this->requestID . "';";
        }
        $rez = $this->SQL_HELPER->select($query, 1);
        if (!empty($rez) && $rez !== null) {
            $this->fest = $rez['fest'];
            $this->oldStatus = $rez['status'];
            return true;
        }
        return false;
    }

    private function execution() {
        if ($this->checkTransition()) {
            $progressBar = new JiyuuFestRequest_ProgressBar($this->requestID);
            if (!$this->justReady || $progressBar->isReady()) {
                $this->transitionStatus();
                $urlHelper = new UrlHelper();
                $this->HTML = '<script language="JavaScript">';
                $this->HTML .= 'window.location.href = "'.$urlHelper->chengeParams(array($this->fest,'showRequest',$this->requestID)).'"';
                $this->HTML .= '</script>';
            } else {
                $this->errorBuffer[] = $this->localization->getText("RequestNotReadyForStatusTransition");
            }
        } else {
            $this->errorBuffer[] = $this->localization->getText("ErrorPermissionDenied");
        }
    }

    private function transitionStatus() {
        $query = "UPDATE `JiyuuFestRequest` SET `status`='" . $this->status . "' WHERE `request`='" . $this->requestID . "';";
        $this->SQL_HELPER->insert($query);
    }

    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }

    private function checkTransition() {
        $query = "SELECT count(`startStatus`) as amount, `justReady` FROM `JiyuuFestRequestStatusTransition` WHERE `startStatus`='" . $this->oldStatus . "' AND `endStatus`='" . $this->status . "' AND ";
        if ($this->yourUser->isAdmin()) {
            if($this->isCreatedFor()) {
                $query .= "(`forUser`='1' OR`forAdmin`='1')";
            } else {
                $query .= "`forAdmin`='1'";
            }
        } else {
            $query .= "`forUser`='1'";
        }
        $rez = $this->SQL_HELPER->select($query, 1);
        if(isset($rez['justReady'])) {
            $this->justReady = $rez['justReady'] > 0;
        }
        return $rez['amount'] > 0;
    }

    public function getHtml() {
        if (count($this->errorBuffer) == 0) {
            return $this->HTML;
        } else {
            $out = "<div class='JRequestError'>";
            foreach ($this->errorBuffer as $error) {
                $out .= "<div>" . $error . "</div>";
            }
            $out .= "</div>";
            return $out;
        }
    }

}
