<?php
class JRequestAddUserFile {
    private $SQL_HELPER;
    private $urlHelper;
    private $localization;
    
    private $requestID;
    private $user;
    private $file;
    private $acceptFileTypes;
    
    private $fest;
    private $type;
    private $applyingFor;
    
    private $yourUser;
    private $yourUserData;


    public function __construct($requestID,$user,$file) {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->urlHelper = new UrlHelper();
        $this->localization = new Localization("JRequest/AddFiles");
        $this->requestID=$requestID;
        $this->user=$user;
        $this->file=$file;
        $this->getUserData();
        $this->getRequestDate();
        $this->setAcceptFileTypes();
    }
    
    private function setAcceptFileTypes() {
        $this->acceptFileTypes = array();
        $this->acceptFileTypes['photo'] = "image/jpeg,image/png,image/gif,image/bmp,image/x-windows-bmp";
        $this->acceptFileTypes['original'] = "image/jpeg,image/png,image/gif,image/bmp,image/x-windows-bmp";
    }
    
    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function getRequestDate() {
        $query = "SELECT `fest`,`type`,`applyingFor` FROM `JRequest` WHERE `id`='".$this->requestID."';";
        $data = $this->SQL_HELPER->select($query,1);
        $this->fest=$data['fest'];
        $this->type=$data['type'];
        $this->applyingFor = $data['applyingFor'];
    }
    
    private function isYouCreator() {
        return $this->yourUserData['login'] == $this->applyingFor;
    }
    
    private function isYouThisUser() {
        return $this->yourUserData['login'] == $this->user;
    }
    
    public function get() {
        // Генерируем ссылку для возврата назад
        $params[0] = $this->fest;
        $backURL = $this->urlHelper->chengeParams($params);
        $out = "";
        $out .= '<div class="JFestsListHeder">';
        $out .= '<div class="JRequestAddIcon back"><a href="'.$backURL.'" title="К списку заявок"> </a></div>';
        $out .= '<div class="JRequestTypeTitle">Прикрепление файла для пользователя '.$this->user.' в заявку '.$this->requestID.'</div>';
        $out .= '</div>';
        if(!$this->isYouCreator() && !$this->isYouThisUser() && !$this->yourUser->isAdmin()) {
            $out .= "<br><br>У вас нет прав на эту операцию.<br>Файлы пользователя может прикреплять только сам пользователь или тот, кто создавал заявку.<br>";
            return $out;
        }
        // Выбиарем файл который будет обрабатывать форму
        $actionFile = $this->urlHelper->getThisPage();
        // Получаем типы файла для загрузки
        $accept = $this->acceptFileTypes[$this->file];
        // гененриурем директорию для сохранения файлов
        $uploaddir = "./resources/JRequest/".$this->fest."/";
        $this->createDir($uploaddir);
        $uploaddir .= $this->type."/";
        $this->createDir($uploaddir);
        $uploaddir .= $this->requestID."/";
        $this->createDir($uploaddir);
        $uploaddir .= $this->user."/";
        $this->createDir($uploaddir);
        // Создаем обработчик
        $safeLoadingFiles = new SafeLoadingFiles($uploaddir, $actionFile, $accept, false, $this->localization->getText($this->file));
        $safeLoadingFiles->setRedirectUrl($backURL);
        $safeLoadingFiles->uploadFile($this->file,true,false);
        $error = null;
        if($safeLoadingFiles->isUpload()) {
            $files = $safeLoadingFiles->getFiles();
            if($this->addFileInToTheTable($uploaddir,$files[0]['file'])) {
                $safeLoadingFiles->redirect();
            } else {
                $error = "Попробуйте еще раз.";
            }
        }
        $out .= $safeLoadingFiles->getMessage()."<br>";
        if($error!=null) {
            $out .= $error."<br>";
        }
        $out .= $safeLoadingFiles->getForm();
        
        return $out;
    }
    
    private function addFileInToTheTable($uploaddir,$fileName) {
        $query = "SELECT `".$this->file."` FROM `JRequestUsers` WHERE `request`='".$this->requestID."' AND `user`='".$this->user."';";
        $date = $this->SQL_HELPER->select($query,1);
        if($date[$this->file]!=$fileName && $date[$this->file]!=null && $date[$this->file]!="") {
            $file = $uploaddir.$date[$this->file];
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $query = "UPDATE `JRequestUsers` SET `".$this->file."`='".$fileName."' WHERE `request`='".$this->requestID."' AND `user`='".$this->user."';;";
        return $this->SQL_HELPER->insert($query);
    }
    
    private function createDir($uploaddir) {
        if(!file_exists ($uploaddir)) {
            mkdir($uploaddir,0777);
        }
    }
}

class JRequestDeleteUserFile {
    private $SQL_HELPER;
    private $urlHelper;
    private $localization;
    
    private $fest;
    private $type;
    private $requestID;
    private $user;
    private $file;
    private $filePath;
    
    private $yourUser;
    private $yourUserData;
    private $applyingFor;
    
    public function __construct($requestID,$user,$file) {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->urlHelper = new UrlHelper();
        $this->localization = new Localization("JRequest/AddFiles");
        $this->requestID=$requestID;
        $this->user=$user;
        $this->file=$file;
        $this->getUserData();
        $this->getRequestDate();
    }
    
    private function getRequestDate() {
        $query = "SELECT `fest`,`type`,`applyingFor` FROM `JRequest` WHERE `id`='".$this->requestID."';";
        $data = $this->SQL_HELPER->select($query,1);
        $this->fest=$data['fest'];
        $this->type=$data['type'];
        $this->applyingFor = $data['applyingFor'];
    }
    
    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function isYouCreator() {
        return $this->yourUserData['login'] == $this->applyingFor;
    }
    
    private function isYouThisUser() {
        return $this->yourUserData['login'] == $this->user;
    }
    
    public function get() {
        $fname = $this->localization->getText($this->file);
        // Генерируем ссылку для возврата назад
        $params[0] = $this->fest;
        $backURL = $this->urlHelper->chengeParams($params);
        $out = "";
        $out .= '<div class="JFestsListHeder">';
        $out .= '<div class="JRequestAddIcon back"><a href="'.$backURL.'" title="К списку заявок"> </a></div>';
        $out .= '<div class="JRequestTypeTitle">Удаление файла "'.$fname.'" пользователя '.$this->user.' из заявки '.$this->requestID.'</div>';
        $out .= '</div>';
        if($this->isYouCreator() || $this->yourUser->isAdmin()) {
            $del[0]=$this->fest;
            $del[1]=$this->requestID;
            $del[2]='deleteUserFile';
            $del[3]=$this->user;
            $del[4]=$this->file;
            $del[5]='confirm';
            $out .= 'Вы уверены, что хотите безвозвратно удалить из заявки <b>'.$this->requestID.'</b> файл: <b>'.$fname.'</b> пользователя <b>'.$this->user.'</b><br>';
            $out .= '<a href="'.$this->urlHelper->chengeParams($del).'">';
            $out .= '<input class="JRequestFormButton small" type="button" value="Да, удалите файл: '.$fname.'">';
            $out .= '</a>';
            $out .= '<a href="'.$backURL.'">';
            $out .= '<input class="JRequestFormButton small" type="button" value="Нет, я передумал">';
            $out .= '</a>';
        } else {
            $out .= '<br><br>У вас нет прав на выполнение данной операции';
        }
        return $out;
    }
    
    private function getFilePath($file) {
        $this->filePath = "./resources/JRequest/".$this->fest."/".$this->type."/".$this->requestID."/".$file;
    }

    public function  del() {
        $fname = $this->localization->getText($this->file);
        // Генерируем ссылку для возврата назад
        $params[0] = $this->fest;
        $backURL = $this->urlHelper->chengeParams($params);
        $out = "";
        $out .= '<div class="JFestsListHeder">';
        $out .= '<div class="JRequestAddIcon back"><a href="'.$backURL.'" title="К списку заявок"> </a></div>';
        $out .= '<div class="JRequestTypeTitle">Удаление файла "'.$fname.'" из заявки '.$this->requestID.'</div>';
        $out .= '</div>';
        if($this->isYouCreator() || $this->isYouThisUser() || $this->yourUser->isAdmin()) {
            $query = "SELECT `".$this->file."` FROM `JRequestUsers` WHERE `request`='".$this->requestID."' AND `user`='".$this->user."';";
            $date = $this->SQL_HELPER->select($query,1);
            $this->getFilePath($date[$this->file]);
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
            $query = "UPDATE `JRequestUsers` SET `".$this->file."`=NULL WHERE `request`='".$this->requestID."' AND `user`='".$this->user."';";
            $this->SQL_HELPER->insert($query);
            $out .= '<br><br>Файл был успешно удален';
        } else {
            $out .= '<br><br>У вас нет прав на выполнение данной операции';
        }
        return $out;
    }
}