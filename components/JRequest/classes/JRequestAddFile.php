<?php
class JRequestAddFile {
    private $SQL_HELPER;
    private $urlHelper;
    private $inputHelper;
    private $localization;
    private $typeTable;
    
    private $fest;
    private $type;
    private $requestID;
    
    private $yourUser;
    private $yourUserData;
    private $applyingFor;
    
    public function __construct($fest,$type,$requestID) {
        $this->fest=$fest;
        $this->type=$type;
        $this->requestID=$requestID;
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->urlHelper = new UrlHelper();
        $this->inputHelper = new InputHelper();
        $this->localization = new Localization("JRequest/AddFiles");
        $this->getUserData();
        $this->getRequestDate();
        $this->getTypeTable();
    }
    
    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function isYouCreator() {
        return $this->yourUserData['login'] == $this->applyingFor;
    }
    
    private function getRequestDate() {
        $query = "SELECT `fest`,`type`,`applyingFor` FROM `JRequest` WHERE `id`='".$this->requestID."';";
        $data = $this->SQL_HELPER->select($query,1);
        $this->fest=$data['fest'];
        $this->type=$data['type'];
        $this->applyingFor = $data['applyingFor'];
    }
    
    private function getTypeTable() {
        $this->typeTable['ActionDefile']['table'] ="JRequestActionDefile";
        $this->typeTable['ActionDefile']['viewer'] = array(
            "audio" => "audio",
            "explication" => "doc",
            "collage" => "image"); 
        $this->typeTable['ActionDefile']['columns'] = array(
            "audio" => "mandatory",
            "explication" => "nomandatory",
            "collage" => "mandatory");
        
        $this->typeTable['AMV']['table']="JRequestAMV";
        $this->typeTable['AMV']['viewer'] = array("amv" => "video");
        $this->typeTable['AMV']['columns'] = array("amv" => "mandatory");
        
        $this->typeTable['Dance']['table']="JRequestDance";
        $this->typeTable['Dance']['viewer'] = array(
            "audio" => "audio",
            "video" => "video",
            "demoVideo" => "video",
            "explication" => "doc");
        $this->typeTable['Dance']['columns'] = array(
            "audio" => "nomandatoryifinvidio",
            "video" => "nomandatory",
            "demoVideo" => "nomandatoryforlistening",
            "explication" => "nomandatory");
        
        $this->typeTable['Defile']['table']="JRequestDefile";
        $this->typeTable['Defile']['viewer'] = array(
            "audio" => "audio",
            "explication" => "doc",
            "collage" => "image");
        $this->typeTable['Defile']['columns'] = array(
            "audio" => "mandatory",
            "explication" => "nomandatory",
            "collage" => "mandatory");
        
        $this->typeTable['Image']['table']="JRequestImage";
        $this->typeTable['Image']['viewer'] = array("image" => "image");
        $this->typeTable['Image']['columns'] = array("image" => "mandatory");
        
        $this->typeTable['Karaoke']['table']="JRequestKaraoke";
        $this->typeTable['Karaoke']['viewer'] = array(
            "demo" => "audio",
            "minus" => "audio",
            "video" => "video",
            "explication" => "doc");
        $this->typeTable['Karaoke']['columns'] = array(
            "demo" => "nomandatoryforlistening",
            "minus" => "nomandatoryifinvidio",
            "video" => "nomandatory",
            "explication" => "nomandatory");
        
        $this->typeTable['Photo']['table']="JRequestPhoto";
        $this->typeTable['Photo']['viewer'] = array(
            "photo1" => "image",
            "photo2" => "image",
            "photo3" => "image");
        $this->typeTable['Photo']['columns'] = array(
            "photo1" => "mandatory",
            "photo2" => "nomandatory",
            "photo3" => "nomandatory");
        
        $this->typeTable['Scene']['table']="JRequestScene";
        $this->typeTable['Scene']['viewer'] = array(
            "audio" => "audio",
            "video" => "video",
            "scenario" => "doc",
            "explication" => "doc",
            "collage" => "image");
        $this->typeTable['Scene']['columns'] = array(
            "audio" => "nomandatoryifinvidio",
            "video" => "nomandatory",
            "scenario" => "mandatory",
            "explication" => "nomandatory",
            "collage" => "mandatory");
        
        $this->typeTable['VideoCosplay']['table']="JRequestVideoCosplay";
        $this->typeTable['VideoCosplay']['viewer'] = array("video" => "video");
        $this->typeTable['VideoCosplay']['columns'] = array("video" => "mandatory");
    }


    public function get() {
        $data = $this->getFilesDate();
        $out = "";
        $out .= "<table class='JRequestFilesTable'>";
        foreach ($this->typeTable[$this->type]['columns'] as $column => $mandatory) {
            $out .= "<tr>";
            $out .= "<td>";
            $out .= $this->localization->getText($column);
            $out .= "</td>";
            $addFileParams[0]=$delFileParams[0]=$this->fest;
            $addFileParams[1]=$delFileParams[1]=$this->requestID;
            $addFileParams[2]="addFile";
            $delFileParams[2]="deleteFile";
            $addFileParams[3]=$delFileParams[3]=$column;
            $addFileURL = $this->urlHelper->chengeParams($addFileParams);
            $delFileURL = $this->urlHelper->chengeParams($delFileParams);
            if(isset($data[$column]) && $data[$column]!=null && $data[$column]!="") {
                $out .= "<td>";
                $out .= $this->getViewer($this->typeTable[$this->type]['viewer'][$column],$data[$column]);
                $out .= "</td>";
                if($this->isYouCreator() || $this->yourUser->isAdmin()) {
                    $out .= "<td>";
                    $out .= '<a href="'.$addFileURL.'">';
                    $out .= '<div class="JRequestFileIcon JRequestFileAdd" title="Заменить файл"></div>';
                    $out .= '</a>';
                    $out .= "</td>";
                    $out .= "<td>";
                    $out .= '<a href="'.$delFileURL.'">';
                    $out .= '<div class="JRequestFileIcon JRequestFileDelete" title="удалить файл"></div>';
                    $out .= '</a>';
                    $out .= "</td>";
                }
            } else {
                $out .= "<td>";
                $out .= '<span class="tableCellNoConfirmed">отсутствует</span>';
                $out .= "</td>";
                if($this->isYouCreator() || $this->yourUser->isAdmin()) {
                    $out .= "<td>";
                    $out .= '<a href="'.$addFileURL.'">';
                    $out .= '<div class="JRequestFileIcon JRequestFileAdd" title="добавить файл"></div>';
                    $out .= '</a>';
                    $out .= "</td>";
                    $out .= "<td></td>";
                }
            }
            $out .= "<td>";
//            if($mandatory) {
//                $out .= '<span class="icon_star" title="Обязательный"></span>';
//            }
            $out .= $this->localization->getText($mandatory);
            $out .= "</td>";
            $out .= "</tr>";
        }
        $out .= "</table>";
        return $out;
    }
    
    private function getFilesDate() {
        $query = "SELECT ";
        foreach ($this->typeTable[$this->type]['columns'] as $key => $value) {
            $query .= "`".$key."`,";
        }
        $query = substr($query, 0, strlen($query)-1);
        $query .= " FROM ";
        $query .= "`".$this->typeTable[$this->type]['table']."`";
        $query .= " WHERE `id`='".$this->requestID."';";
        return $this->SQL_HELPER->select($query,1);
    }
    
    private function getFilePath($file) {
        return "./resources/JRequest/".$this->fest."/".$this->type."/".$this->requestID."/".$file;
    }
    
    private function getViewer($viewer,$file){
        $filePath = $this->getFilePath($file);
        switch ($viewer) {
            case "audio":
                return $this->getAudioViewer($filePath);
                break;
            case "video":
                return $this->getVideoViewer($filePath);
                break;
            case "image":
                return $this->getImageViewer($filePath);
                break;
            case "doc":
                return $this->getDocViewer($filePath);
                break;
        }
    }


    private function getAudioViewer($file) {
        return '<a href="'.$file.'" target="_blank">Скачать трек</a>';
//        $out = "";
//        $out .= '<div class="JRequestAudioPlayer">';
//        $out .= AudioPlayer::getPalaer($file);
//        $out .= '</div>';
//        return $out;
    }
    
    private function getImageViewer($file) {
//        return '<a class="fancybox" href="'.$file.'" title="Нажми для просмотра"><img src="'.$file.'" height="50px"></a>';
        return '<a class="fancybox" href="'.$file.'" title="Нажми для просмотра">Просмотреть фото</a>';
    }
    
    private function getDocViewer($file) {
        return '<a href="'.$file.'" target="_blank">Скачать документ</a>';
    }
    
    private function getVideoViewer($file) {
        return '<a href="'.$file.'" target="_blank">Скачать видео</a>';
    }
}

class JRequestUploadFile {
    private $SQL_HELPER;
    private $urlHelper;
    private $localization;
    
    private $fest;
    private $type;
    private $requestID;
    private $file;
    private $acceptFileTypes;
    
    private $yourUser;
    private $yourUserData;
    private $applyingFor;
    
    public function __construct($requestID,$file) {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->urlHelper = new UrlHelper();
        $this->localization = new Localization("JRequest/AddFiles");
        $this->requestID=$requestID;
        $this->file=$file;
        $this->getUserData();
        $this->getRequestDate();
        $this->setAcceptFileTypes();
    }
    
    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function isYouCreator() {
        return $this->yourUserData['login'] == $this->applyingFor;
    }

    private function setAcceptFileTypes() {
        $this->acceptFileTypes = array();
        $this->acceptFileTypes['ActionDefile']['table'] ="JRequestActionDefile";
        $this->acceptFileTypes['ActionDefile']['type'] = array(
            "audio" => "audio/mp3,audio/mpeg",
            "explication" => "application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.oasis.opendocument.text,application/octet-stream",
            "collage" => "image/jpeg,image/png,image/gif,image/bmp,image/x-windows-bmp");
        
        $this->acceptFileTypes['AMV']['table']="JRequestAMV";
        $this->acceptFileTypes['AMV']['type'] = array("amv" => "video/mpeg,video/mp4,video/x-ms-wmv,video/webm,video/quicktime,application/x-troff-msvideo,video/avi,video/msvideo,video/x-msvideo");
        
        $this->acceptFileTypes['Dance']['table']="JRequestDance";
        $this->acceptFileTypes['Dance']['type'] = array(
            "audio" => "audio/mp3,audio/mpeg",
            "video" => "video/mpeg,video/mp4,video/x-ms-wmv,video/webm,video/quicktime,application/x-troff-msvideo,video/avi,video/msvideo,video/x-msvideo",
            "demoVideo" => "video/mpeg,video/mp4,video/x-ms-wmv,video/webm,video/quicktime,application/x-troff-msvideo,video/avi,video/msvideo,video/x-msvideo",
            "explication" => "application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.oasis.opendocument.text,application/octet-stream");
        
        $this->acceptFileTypes['Defile']['table']="JRequestDefile";
        $this->acceptFileTypes['Defile']['type'] = array(
            "audio" => "audio/mp3,audio/mpeg",
            "explication" => "application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.oasis.opendocument.text,application/octet-stream",
            "collage" => "image/jpeg,image/png,image/gif,image/bmp,image/x-windows-bmp");
        
        $this->acceptFileTypes['Image']['table']="JRequestImage";
        $this->acceptFileTypes['Image']['type'] = array("image" => "image/jpeg,image/png,image/gif,image/bmp,image/x-windows-bmp");
        
        $this->acceptFileTypes['Karaoke']['table']="JRequestKaraoke";
        $this->acceptFileTypes['Karaoke']['type'] = array(
            "demo" => "audio/mp3,audio/mpeg",
            "minus" => "audio/mp3,audio/mpeg",
            "video" => "video/mpeg,video/mp4,video/x-ms-wmv,video/webm,video/quicktime,application/x-troff-msvideo,video/avi,video/msvideo,video/x-msvideo",
            "explication" => "application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.oasis.opendocument.text,application/octet-stream");
        
        $this->acceptFileTypes['Photo']['table']="JRequestPhoto";
        $this->acceptFileTypes['Photo']['type'] = array(
            "photo1" => "image/jpeg,image/png,image/gif,image/bmp,image/x-windows-bmp",
            "photo2" => "image/jpeg,image/png,image/gif,image/bmp,image/x-windows-bmp",
            "photo3" => "image/jpeg,image/png,image/gif,image/bmp,image/x-windows-bmp");
        
        $this->acceptFileTypes['Scene']['table']="JRequestScene";
        $this->acceptFileTypes['Scene']['type'] = array(
            "audio" => "audio/mp3,audio/mpeg",
            "video" => "video/mpeg,video/mp4,video/x-ms-wmv,video/webm,video/quicktime,application/x-troff-msvideo,video/avi,video/msvideo,video/x-msvideo",
            "scenario" => "application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.oasis.opendocument.text,application/octet-stream",
            "explication" => "application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.oasis.opendocument.text,application/octet-stream",
            "collage" => "image/jpeg,image/png,image/gif,image/bmp,image/x-windows-bmp");
        
        $this->acceptFileTypes['VideoCosplay']['table']="JRequestVideoCosplay";
        $this->acceptFileTypes['VideoCosplay']['type'] = array("video" => "video/mpeg,video/mp4,video/x-ms-wmv,video/webm,video/quicktime,application/x-troff-msvideo,video/avi,video/msvideo,video/x-msvideo");
    }


    private function getRequestDate() {
        $query = "SELECT `fest`,`type`,`applyingFor` FROM `JRequest` WHERE `id`='".$this->requestID."';";
        $data = $this->SQL_HELPER->select($query,1);
        $this->fest=$data['fest'];
        $this->type=$data['type'];
        $this->applyingFor = $data['applyingFor'];
    }

    public function get() {
        // Генерируем ссылку для возврата назад
        $params[0] = $this->fest;
        $backURL = $this->urlHelper->chengeParams($params);
        $out = "";
        $out .= '<div class="JFestsListHeder">';
        $out .= '<div class="JRequestAddIcon back"><a href="'.$backURL.'" title="К списку заявок"> </a></div>';
        $out .= '<div class="JRequestTypeTitle">Добавление файла в заявку '.$this->requestID.'</div>';
        $out .= '</div>';
        if(!$this->isYouCreator() && !$this->yourUser->isAdmin()) {
            $out .= "<br><br>У вас нет прав на эту операцию.<br>Файлы может добавляеть только тот, кто создавал заявку.<br>";
            return $out;
        }
        
        // Выбиарем файл который будет обрабатывать форму
        $actionFile = $this->urlHelper->getThisPage();
        // Получаем типы файла для загрузки
        $accept = $this->acceptFileTypes[$this->type]['type'][$this->file];
        // гененриурем директорию для сохранения файлов
        $uploaddir = "./resources/JRequest/".$this->fest."/";
        $this->createDir($uploaddir);
        $uploaddir .= $this->type."/";
        $this->createDir($uploaddir);
        $uploaddir .= $this->requestID."/";
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
        $table = $this->acceptFileTypes[$this->type]['table'];
        $query = "SELECT `".$this->file."` FROM `".$table."` WHERE `id`='".$this->requestID."';";
        $date = $this->SQL_HELPER->select($query,1);
        if($date[$this->file]!=$fileName && $date[$this->file]!=null && $date[$this->file]!="") {
            $file = $uploaddir.$date[$this->file];
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $query = "UPDATE `".$table."` SET `".$this->file."`='".$fileName."' WHERE `id`='".$this->requestID."';";
        return $this->SQL_HELPER->insert($query);
    }


    private function createDir($uploaddir) {
        if(!file_exists ($uploaddir)) {
            mkdir($uploaddir,0777);
        }
    }
}

class JRequestDeleteFile {
    private $SQL_HELPER;
    private $urlHelper;
    private $localization;
    
    private $fest;
    private $type;
    private $requestID;
    private $file;
    private $filePath;
    
    private $yourUser;
    private $yourUserData;
    private $applyingFor;
    
    public function __construct($requestID,$file) {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->urlHelper = new UrlHelper();
        $this->localization = new Localization("JRequest/AddFiles");
        $this->requestID=$requestID;
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
    
    public function get() {
        $fname = $this->localization->getText($this->file);
        // Генерируем ссылку для возврата назад
        $params[0] = $this->fest;
        $backURL = $this->urlHelper->chengeParams($params);
        $out = "";
        $out .= '<div class="JFestsListHeder">';
        $out .= '<div class="JRequestAddIcon back"><a href="'.$backURL.'" title="К списку заявок"> </a></div>';
        $out .= '<div class="JRequestTypeTitle">Удаление файла "'.$fname.'" из заявки '.$this->requestID.'</div>';
        $out .= '</div>';
        if($this->isYouCreator() || $this->yourUser->isAdmin()) {
            $del[0]=$this->fest;
            $del[1]=$this->requestID;
            $del[2]='deleteFile';
            $del[3]=$this->file;
            $del[4]='confirm';
            $out .= 'Вы уверены, что хотите безвозвратно удалить из заявки <b>'.$this->requestID.'</b> файл: <b>'.$fname.'</b><br>';
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
    
    private function getTable() {
        $query = "SELECT `tableName` FROM `JRequestType` WHERE `type`='".$this->type."';";
        $date = $this->SQL_HELPER->select($query,1);
        return $date['tableName'];
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
        if($this->isYouCreator() || $this->yourUser->isAdmin()) {
            $table = $this->getTable();
            $query = "SELECT `".$this->file."` FROM `".$table."` WHERE `id`='".$this->requestID."';";
            $date = $this->SQL_HELPER->select($query,1);
            $this->getFilePath($date[$this->file]);
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
            $query = "UPDATE `".$table."` SET `".$this->file."`=NULL WHERE `id`='".$this->requestID."';";
            $this->SQL_HELPER->insert($query);
            $out .= '<br><br>Файл был успешно удален';
        } else {
            $out .= '<br><br>У вас нет прав на выполнение данной операции';
        }
        return $out;
    }
}
