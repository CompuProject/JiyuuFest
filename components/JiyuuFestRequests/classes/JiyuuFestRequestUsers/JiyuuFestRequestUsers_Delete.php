<?php
/**
 * Description of JiyuuFestRequestUsers_Delete
 *
 * @author maxim
 */
class JiyuuFestRequestUsers_Delete {
    private $SQL_HELPER;
    private $_SITECONFIG;
    private $localization;
    private $urlHelper;
    // данные
    private $requestID;
    private $user;
    private $fest;
    private $errorBuffer = array();
    // пользователи
    private $yourUser;
    private $yourUserData;
    private $authorization;
    private $fileDir = "./resources/Components/JiyuuFestRequest/Requests/";
    
    public function __construct($requestID, $user, $success = false) {
        global $_SITECONFIG;
        $this->_SITECONFIG = $_SITECONFIG;
        $this->requestID = $requestID;
        $this->user = mb_strtolower($user, $this->_SITECONFIG->getCharset());
        $this->localization = new Localization("JiyuuFests");
        $this->urlHelper = new UrlHelper();
        $this->getUserData();
        if($this->yourUser->checkAuthorization()) {
            global $_SQL_HELPER;
            $this->SQL_HELPER = $_SQL_HELPER;
            $this->getFest();
            if($this->checkUser()) {
                if($this->checkDeleteUser()) {
                    if($this->checkCreatedUser()) {
                        if($success) {
                            $this->getSuccess();
                        } else {
                            $this->getNoSuccess();
                        }
                    } else {
                        $this->errorBuffer[] = 'Невозможно удалить создателя заявки';
                    }
                } else {
                    $this->errorBuffer[] = 'Пользователь не является участником заявки';
                }
            } else {
                $this->errorBuffer[] = $this->localization->getText("ErrorPermissionDenied");
            }
        } else {
            $this->errorBuffer[] = $this->localization->getText("ErrorUnauthorized");
        }
    }
    
    private function getFest() {
        $query = "SELECT `fest` FROM `JiyuuFestRequest` WHERE `request`='".$this->requestID."';";
        $rezult = $this->SQL_HELPER->select($query,1);
        $this->fest = $rezult['fest'];
    }
    
    private function checkUser() {
        $query = "SELECT `request` FROM `JiyuuFestRequest` WHERE `request`='".$this->requestID."' AND `createdFor`='".$this->yourUserData['login']."';";
        $rezult = $this->SQL_HELPER->select($query);
        return count($rezult) > 0 || $this->yourUser->isAdmin();
    }
    
    private function checkCreatedUser() {
        $query = "SELECT count(`request`) as num FROM `JiyuuFestRequest` WHERE `request`='".$this->requestID."' AND `createdFor`='".$this->user."';";
        $rezult = $this->SQL_HELPER->select($query,1);
        return $rezult['num']==='0';
    }
    
    private function checkDeleteUser() {
        $query = "SELECT count(`request`) as num FROM `JiyuuFestRequestUsers` WHERE `request`='".$this->requestID."' AND `user`='".$this->user."';";
        $rezult = $this->SQL_HELPER->select($query,1);
        return $rezult['num']>0;
    }

    private function getUserData() {
        $this->yourUser = new UserData();
        $this->authorization = $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
        if($this->authorization) {
            
        }
    }
    
    private function getNoSuccess () {
        $this->HTML = '';
        $yes = $this->urlHelper->chengeParams(array($this->fest,'deleteRequestUser',$this->requestID,$this->user,'success'));
        $no = $this->urlHelper->chengeParams(array($this->fest));
        $showRequest = new JiyuuFestRequest_ShowRequest($this->requestID);
        $this->HTML .= '<div class="DeleteRequestMessage">Вы точно хотите удалить '.$this->user.' из заявки '.$this->requestID.'? <a href="'.$no.'" class="delNo">НЕТ</a><a href="'.$yes.'" class="delYes">ДА</a></div>';
        $this->HTML .= $showRequest->getHtml();
    }
    
    private function getSuccess () {
        $this->fileDir = $this->fileDir.$this->fest."/".$this->requestID."/".$this->user;
        $query = "DELETE FROM `JiyuuFestRequestUsers` WHERE `request`='".$this->requestID."' AND `user`='".$this->user."';";
        $this->SQL_HELPER->insert($query);
        if(file_exists($this->fileDir)) {
            $this->removeDirectory($this->fileDir);
        }
        $this->HTML = 'Участник '.$this->user.' успешно удален из участния в заявке '.$this->requestID.'. <a href="'.$this->urlHelper->chengeParams(array($this->fest)).'">Назад</a>.';
    }
    
    private function removeDirectory($dir) {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
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
