<?php
/**
 * Description of JiyuuFestRequest_DeleteRequest
 *
 * @author Максим
 */
class JiyuuFestRequest_DeleteRequest {
    private $SQL_HELPER;
    private $requestID;
    private $fest;
    private $HTML;
    private $yourUser;
    private $yourUserData;
    private $errorBuffer = array();
    private $fileDir = "./resources/Components/JiyuuFestRequest/Requests/";


    public function __construct($requestID, $success = false) {
        $this->urlHelper = new UrlHelper();
        $this->requestID = $requestID;
        $this->getUserData();
        if($this->yourUser->checkAuthorization()) {
            global $_SQL_HELPER;
            $this->SQL_HELPER = $_SQL_HELPER;
            $this->getFest();
            if($this->checkUser()) {
                if($success) {
                    $this->getSuccess();
                } else {
                    $this->getNoSuccess();
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

    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function getNoSuccess () {
        $this->HTML = '';
        $yes = $this->urlHelper->chengeParams(array($this->fest,'deleteRequest',$this->requestID,'success'));
        $no = $this->urlHelper->chengeParams(array($this->fest));
        $showRequest = new JiyuuFestRequest_ShowRequest($this->requestID);
        $this->HTML .= '<div class="DeleteRequestMessage">Вы точно хотите удалить эту заявку? <a href="'.$no.'" class="delNo">НЕТ</a><a href="'.$yes.'" class="delYes">ДА</a></div>';
        $this->HTML .= $showRequest->getHtml();
    }
    
    public function getSuccess () {
        $this->fileDir = $this->fileDir.$this->fest."/".$this->requestID."/";
        $query = "DELETE FROM `JiyuuFestRequest` WHERE `request`='".$this->requestID."';";
        $this->SQL_HELPER->insert($query);
        if(file_exists($this->fileDir)) {
            $this->removeDirectory($this->fileDir);
        }
        $this->HTML = 'Заявка успешно удалена. <a href="'.$this->urlHelper->chengeParams(array($this->fest)).'">Назад</a>.';
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
