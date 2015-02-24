<?php
class JRequestMain {
    private $SQL_HELPER;
    private $URL_PARAMS;
    private $yourUser;
    private $yourUserData;
    private $urlHelper;
    
    
    public function __construct() {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        global $_URL_PARAMS;
        $this->URL_PARAMS = $_URL_PARAMS['params'];
        $this->urlHelper = new UrlHelper();
        $this->getUserData();
    }
    

    protected function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    /**
     * $this->URL_PARAMS[0] - Фестиваль
     * $this->URL_PARAMS[1] - Тип заявки | Номер заявки для редактирования
     * $this->URL_PARAMS[2] - Тип действия [add|edit]
     * $this->URL_PARAMS[3] - параметры
     */
    public function get() {
        if(isset($this->URL_PARAMS[0])) {
            $festListUrlParams[0]=$this->URL_PARAMS[0];
            $festListUrl = '<a href="'.$this->urlHelper->chengeParams($festListUrlParams).'">';
            $festListUrl .= '<input class="JRequestFormButton small" type="button" value="К списку заявок">';
            $festListUrl .= '</a>';
        }
        
        if(!isset($this->URL_PARAMS[0])) {
            return $this->getFestsList();
        } else if(isset($this->URL_PARAMS[0]) && (!isset($this->URL_PARAMS[1]) || ($this->yourUser->isAdmin() && $this->URL_PARAMS[1]=='AllRequest'))){
            if(isset($this->URL_PARAMS[1]) && $this->URL_PARAMS[1]=='AllRequest') {
                return $this->getRequestList(true);
            } else {
                return $this->getRequestList();
            }
        } else if(isset($this->URL_PARAMS[0]) && isset($this->URL_PARAMS[1]) && isset($this->URL_PARAMS[2])) {
            if(isset($this->URL_PARAMS[1]) && isset($this->URL_PARAMS[2]) && $this->URL_PARAMS[2]=='sendCheckRequest') {
                // Првоеряем заявку
                // URL_PARAMS[0] - ID Фестиваля
                // URL_PARAMS[1] - ID Заявки
                // URL_PARAMS[2] - sendCheckRequest
                // URL_PARAMS[3] - confirm
                $sendCheckRequest = new JRequestCheck($this->URL_PARAMS[1]);
                if(isset($this->URL_PARAMS[3]) && $this->URL_PARAMS[3]=="preapproved") {
                    return $sendCheckRequest->preapproved();
                } else if(isset($this->URL_PARAMS[3]) && $this->URL_PARAMS[3]=="unset"){
                    return $sendCheckRequest->delPreapproved();
                } else {
                    return $sendCheckRequest->getCheckInfoHTML();
                }
            } if(isset($this->URL_PARAMS[1]) && isset($this->URL_PARAMS[2]) && $this->URL_PARAMS[2]=='sendApproved') {
                $sendApproved = new JRequestCheck($this->URL_PARAMS[1]);
                if(isset($this->URL_PARAMS[3]) && $this->URL_PARAMS[3]=="approved") {
                    return $sendApproved->approved();
                } else if(isset($this->URL_PARAMS[3]) && $this->URL_PARAMS[3]=="unset"){
                    return $sendApproved->delApproved();
                } else {
                    return $sendApproved->getCheckApprovedInfoHTML();
                }
            
            } else if($this->URL_PARAMS[2]=='add') {
                return $this->getJRequest($this->URL_PARAMS[1]);
            } else if ($this->URL_PARAMS[2]=='confirmed') {
                if($this->ConfirmedUser($this->URL_PARAMS[1])) {
                    return "Ваше участие в заявке <b>".$this->URL_PARAMS[1]."</b> подтверждено.<br>$festListUrl";
                }
            } else if ($this->URL_PARAMS[2]=='delconfirmed') {
                if(isset($this->URL_PARAMS[3]) && $this->URL_PARAMS[3]='confirm') {
                    if($this->DelConfirmedUserConfirm($this->URL_PARAMS[1])) {
                        return "Вы отказались от участия в заявке <b>".$this->URL_PARAMS[1]."</b> и были удалены из нее.<br>$festListUrl";
                    }
                } else {
                    return $this->DelConfirmedUser($this->URL_PARAMS[0],$this->URL_PARAMS[1]);
                }
            } else if ($this->URL_PARAMS[2]=='delUser' && isset ($this->URL_PARAMS[3])) {
                if(isset($this->URL_PARAMS[4]) && $this->URL_PARAMS[4]='confirm') {
                    if($this->DelJRequestUserConfirm($this->URL_PARAMS[1],$this->URL_PARAMS[3])) {
                        return "Пользователь <b>".$this->URL_PARAMS[3]."</b> был удален из заявки <b>".$this->URL_PARAMS[1]."</b>.<br>$festListUrl";
                    }
                } else {
                    return $this->DelJRequestUser($this->URL_PARAMS[0],$this->URL_PARAMS[1],$this->URL_PARAMS[3]);
                }
            } else if ($this->URL_PARAMS[2]=='requestDelete') {
                if(isset($this->URL_PARAMS[3]) && $this->URL_PARAMS[3]='confirm') {
                    if($this->getJRequestDeleteConfirm($this->URL_PARAMS[1])) {
                        return "Заявка <b>".$this->URL_PARAMS[1]."</b> была удален.<br>$festListUrl";
                    }
                } else {
                    return $this->getJRequestDelete($this->URL_PARAMS[0],$this->URL_PARAMS[1]);
                }
            } else if ($this->URL_PARAMS[2]=='addUser') {
                // Добавялем пользователя
                $requestAddUsers = new JRequestAddUsers2($this->URL_PARAMS[0],$this->URL_PARAMS[1]);
                return $requestAddUsers->addUser();
            } else if (isset ($this->URL_PARAMS[1]) && isset ($this->URL_PARAMS[2]) && $this->URL_PARAMS[2]=='addFile' && isset ($this->URL_PARAMS[3])) {
                // добавляем файлы в заявку
                $requestAddUsers = new JRequestUploadFile($this->URL_PARAMS[1], $this->URL_PARAMS[3]);
                return $requestAddUsers->get();
            } else if (isset ($this->URL_PARAMS[1]) && isset ($this->URL_PARAMS[2]) && $this->URL_PARAMS[2]=='deleteFile' && isset ($this->URL_PARAMS[3])) {
                // Удяляем файлы из заявки
                $requestDeleteFile = new JRequestDeleteFile($this->URL_PARAMS[1], $this->URL_PARAMS[3]);
                if(isset($this->URL_PARAMS[4]) && $this->URL_PARAMS[4]="confirm") {
                    return $requestDeleteFile->del();
                } else {
                    return $requestDeleteFile->get();
                }
            } else if (isset ($this->URL_PARAMS[1]) && isset ($this->URL_PARAMS[2]) && $this->URL_PARAMS[2]=='addUserFile' && isset ($this->URL_PARAMS[3]) && isset ($this->URL_PARAMS[4])) {
                // Доабвляем файлы пользователю
                // URL_PARAMS[0] - ID Фестиваля
                // URL_PARAMS[1] - ID Заявки
                // URL_PARAMS[2] - addUserFile
                // URL_PARAMS[3] - Пользователь
                // URL_PARAMS[4] - Тип файла
                $requestAddUserFile = new JRequestAddUserFile($this->URL_PARAMS[1], $this->URL_PARAMS[3], $this->URL_PARAMS[4]);
                return $requestAddUserFile->get();
            } else if (isset ($this->URL_PARAMS[1]) && isset ($this->URL_PARAMS[2]) && $this->URL_PARAMS[2]=='deleteUserFile' && isset ($this->URL_PARAMS[3]) && isset ($this->URL_PARAMS[4])) {
                // Удаляем файлы пользователя
                // URL_PARAMS[0] - ID Фестиваля
                // URL_PARAMS[1] - ID Заявки
                // URL_PARAMS[2] - deleteUserFile
                // URL_PARAMS[3] - Пользователь
                // URL_PARAMS[4] - Тип файла
                $requestDeleteFile = new JRequestDeleteUserFile($this->URL_PARAMS[1], $this->URL_PARAMS[3], $this->URL_PARAMS[4]);
                if(isset($this->URL_PARAMS[5]) && $this->URL_PARAMS[5]="confirm") {
                    return $requestDeleteFile->del();
                } else {
                    return $requestDeleteFile->get();
                }
            }
        }
        return "Некорректная строка адреса.";
    }
    
    private function getFestsList() {
        $jRequestFestsList = new JRequestFestsList();
        return $jRequestFestsList->get();
    }
    
    private function getRequestList($all=false) {
        $jRequestList = new JRequestList($all);
        return $jRequestList->get();
    }
    
    private function getJRequest($id) {
        switch ($id) {
            case "Karaoke":
                return $this->getJRequestKaraoke();
                break;
            case "Dance":
                return $this->getJRequestDance();
                break;
            case "Scene":
                return $this->getJRequestScene();
                break;
            case "Defile":
                return $this->getJRequestDefile();
                break;
            case "ActionDefile":
                return $this->getJRequestActionDefile();
                break;
            case "AMV":
                return $this->getJRequestAMV();
                break;
            case "VideoCosplay":
                return $this->getJRequestVideoCosplay();
                break;
            case "Image":
                return $this->getJRequestImage();
                break;
            case "Photo":
                return $this->getJRequestPhoto();
                break;
        }
    }


    
    private function getJRequestAMV() {
        $jRequest = new JRequestAMV();
        return $jRequest->getForm();
    }
    
    private function getJRequestActionDefile() {
        $jRequest = new JRequestActionDefile();
        return $jRequest->getForm();
    }
    
    private function getJRequestDance() {
        $jRequest = new JRequestDance();
        return $jRequest->getForm();
    }
    
    private function getJRequestDefile() {
        $jRequest = new JRequestDefile();
        return $jRequest->getForm();
    }
    
    private function getJRequestImage() {
        $jRequest = new JRequestImage();
        return $jRequest->getForm();
    }
    
    private function getJRequestKaraoke() {
        $jRequest = new JRequestKaraoke();
        return $jRequest->getForm();
    }
    
    private function getJRequestPhoto() {
        $jRequest = new JRequestPhoto();
        return $jRequest->getForm();
    }
    
    private function getJRequestScene() {
        $jRequest = new JRequestScene();
        return $jRequest->getForm();
    }
    
    private function getJRequestVideoCosplay() {
        $jRequest = new JRequestVideoCosplay();
        return $jRequest->getForm();
    }
    
    private function ConfirmedUser($requestId) {
        $query="UPDATE `JRequestUsers` SET `confirmed`='1' WHERE `request`='".$requestId."' and`user`='".$this->yourUserData['login']."';";
        return $this->SQL_HELPER->insert($query);
    }
    
    private function DelConfirmedUser($fest,$id) {
        $out = "";
        $requestDate = $this->getJRequestDate($id);
        if($requestDate!=null && $requestDate['applyingFor'] != $this->yourUserData['login']) {
            $del[0]=$params[0]=$fest;
            $del[1]=$id;
            $del[2]='delconfirmed';
            $del[3]='confirm';
            $out .= 'Вы уверены в том что хотите отказаться от участия в заявке <b>'.$id.'</b><br>';
            $out .= '<a href="'.$this->urlHelper->chengeParams($del).'">';
            $out .= '<input class="JRequestFormButton small" type="button" value="Да, удалите меня из заявки">';
            $out .= '</a>';
            $out .= '<a href="'.$this->urlHelper->chengeParams($params).'">';
            $out .= '<input class="JRequestFormButton small" type="button" value="Нет, я передумал">';
            $out .= '</a>';
        } else {
            $out .= 'Вы не можете отказаться от заявки которую сами создали. Вы можете только удалить эту заявку.';
        }
        return $out;
    }
    
    private function DelConfirmedUserConfirm($id) {
        $requestDate = $this->getJRequestDate($id);
        if($requestDate!=null && $requestDate['applyingFor'] != $this->yourUserData['login']) {
            $query="DELETE FROM `JRequestUsers` WHERE `request`='".$id."' and`user`='".$this->yourUserData['login']."';";
            return $this->SQL_HELPER->insert($query);
        } else {
            return false;
        }
    }
    
    private function DelJRequestUser($fest,$id,$user) {
        $out = "";
        $requestDate = $this->getJRequestDate($id);
        if($requestDate!=null && $requestDate['applyingFor'] == $this->yourUserData['login'] && $requestDate['applyingFor']!=$user) {
            $del[0]=$params[0]=$fest;
            $del[1]=$id;
            $del[2]='delUser';
            $del[3]=$user;
            $del[4]='confirm';
            $out .= 'Вы уверены в том что хотите удалить пользователя <b>'.$user.'</b> из заявки <b>'.$id.'</b><br>';
            $out .= '<a href="'.$this->urlHelper->chengeParams($del).'">';
            $out .= '<input class="JRequestFormButton small" type="button" value="Да, удалите пользователя">';
            $out .= '</a>';
            $out .= '<a href="'.$this->urlHelper->chengeParams($params).'">';
            $out .= '<input class="JRequestFormButton small" type="button" value="Нет, я передумал, пусть остается">';
            $out .= '</a>';
        } else {
            $out .= 'У вас нет прав на выполнение данной операции';
        }
        return $out;
    }
    private function DelJRequestUserConfirm($requestId,$user) {
        $requestDate = $this->getJRequestDate($requestId);
        if($requestDate!=null && $requestDate['applyingFor'] == $this->yourUserData['login'] && $requestDate['applyingFor']!=$user) {
            $query="DELETE FROM `JRequestUsers` WHERE `request`='".$requestId."' and`user`='".$user."';";
            return $this->SQL_HELPER->insert($query);
        } else {
            return false;
        }
    }
    
    private function getJRequestDate($id) {
        $query="SELECT * FROM `JRequest` where `id`='".$id."';";
        return $this->SQL_HELPER->select($query,1);
    }
    
    private function getJRequestDelete($fest,$id) {
        $out = "";
        $requestDate = $this->getJRequestDate($id);
        if($requestDate!=null && $requestDate['applyingFor'] == $this->yourUserData['login']) {
            $del[0]=$params[0]=$fest;
            $del[1]=$id;
            $del[2]='requestDelete';
            $del[3]='confirm';
            $out .= 'Вы уверены в том что хотите безвозвратно удалить заявку <b>'.$id.'</b><br>';
            $out .= '<a href="'.$this->urlHelper->chengeParams($del).'">';
            $out .= '<input class="JRequestFormButton small" type="button" value="Да, удалите заявку">';
            $out .= '</a>';
            $out .= '<a href="'.$this->urlHelper->chengeParams($params).'">';
            $out .= '<input class="JRequestFormButton small" type="button" value="Нет, я передумал">';
            $out .= '</a>';
        } else {
            $out .= 'У вас нет прав на выполнение данной операции';
        }
        return $out;
    }
    private function getJRequestDeleteConfirm($id) {
        $requestDate = $this->getJRequestDate($id);
        if($requestDate!=null && $requestDate['applyingFor'] == $this->yourUserData['login']) {
            $query = "SELECT `fest`,`type` FROM `JRequest` WHERE `id`='".$id."';";
            $data = $this->SQL_HELPER->select($query,1);
            $dir = "./resources/JRequest/".$data['fest']."/".$data['type']."/".$id."/";
            $this->removeDirectory($dir);
            $query="DELETE FROM `JRequest` where `id`='".$id."';";
            return $this->SQL_HELPER->insert($query);
        } else {
            return false;
        }
    }
    
    function removeDirectory($dir) {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }
    
    
}
?>