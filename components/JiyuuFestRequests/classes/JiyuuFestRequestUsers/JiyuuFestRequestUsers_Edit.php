<?php
/**
 * Description of JiyuuFestRequestUsers_Edit
 *
 * @author maxim
 */
class JiyuuFestRequestUsers_Edit {
    // помошники
    private $SQL_HELPER;
    private $_SITECONFIG;
    private $urlHelper;
    private $inputHelper;
    private $localization;
    private $downloadFileHelper;
    private $downloadImageHelper;
    private $mimeType = array();
    private $numberOfParticipants = 1;
    // данные
    private $requestID;
    private $requestUser;
    private $festsData = array();
    private $requestTypeData = array();
    private $errorBuffer = array();
    private $requestUsersData = array();
    private $insertData = array();
    // пользователи
    private $yourUser;
    private $yourUserData;
    private $authorization;
    // параметры
    private $fest = null;
    private $requestType = null;
    // настрйоки
    private $fileDir = "./resources/Components/JiyuuFestRequest/Requests/";
    private $bannerDir = "./resources/Components/JiyuuFestRequest/JiyuuFest/banners/";
    // вывод
    private $HTML;
    private $executionOut = '';
    private $insertDataErrors = array();
    private $executeSuccess = false;
    
    public function __construct($requestID, $user) {
        global $_SITECONFIG;
        $this->_SITECONFIG = $_SITECONFIG;
        $this->errorBuffer = array();
        $this->localization = new Localization("JiyuuFests");
        $this->urlHelper = new UrlHelper();
        $this->requestID = $requestID;
        $this->requestUser = mb_strtolower($user, $this->_SITECONFIG->getCharset());
        $this->requestUserData = array();
        $this->getUserData();
        if($this->authorization) {
            global $_SQL_HELPER;
            $this->SQL_HELPER = $_SQL_HELPER;
            $this->getFest();
            $this->getFestsData();
            $this->getRequestTypeDate();
            $this->getUsersData();
            $this->getRequestUserData();
            $this->urlHelper = new UrlHelper();
            $this->inputHelper = new InputHelper();
            $this->fileDir = $this->fileDir.$this->fest."/".$this->requestID."/".mb_strtolower($user, $this->_SITECONFIG->getCharset())."/";
            if($this->checkUser()) {
                $this->apdateInsertData();
                $this->execute();
                $this->generateHtml();
            } else {
                $this->errorBuffer[] = $this->localization->getText("ErrorPermissionDenied");
            }
        } else {
            $this->errorBuffer[] = $this->localization->getText("ErrorUnauthorized");
        }
    }
    private function getRequestUserData() {
        $query = "SELECT * FROM `JiyuuFestRequestUsers` WHERE `request`='".$this->requestID."' && `user`='".$this->requestUser."'";
        $this->requestUserData = $this->SQL_HELPER->select($query,1);
    }

    private function getUserData() {
        $this->yourUser = new UserData();
        $this->authorization = $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function getFest() {
        $query = "SELECT `fest`,`type`,`numberOfParticipants` FROM `JiyuuFestRequest` WHERE `request`='".$this->requestID."';";
        $rezult = $this->SQL_HELPER->select($query,1);
        $this->fest = $rezult['fest'];
        $this->requestType = $rezult['type'];
        $this->numberOfParticipants = $rezult['numberOfParticipants'];
    }
    
    private function getFestsData() {
        $this->festsData = array();
        $query = "SELECT * FROM `JiyuuFest` WHERE `fest`='".$this->fest."';";
        $this->festsData = $this->SQL_HELPER->select($query,1);
    }
    
    private function getRequestTypeDate() {
        $this->requestTypeData = array();
        $query = "SELECT * FROM `JiyuuFestRequestType` WHERE `type`='".$this->requestType."';";
        $this->requestTypeData = $this->SQL_HELPER->select($query,1);
    }
    
    private function getUsersData() {
        $query = "SELECT 
            JFRU.`request`, 
            JFRU.`user`, 
            Us.`nickname`, 
            JFRU.`confirmed`, 
            JFRU.`characterName`, 
            JFRU.`photo`, 
            JFRU.`original` 
            FROM 
            `JiyuuFestRequestUsers` as JFRU left join `Users` as Us
            on JFRU.`user` = Us.`login`
            where `request`='".$this->requestID."';";
        $this->requestUsersData = $this->SQL_HELPER->select($query);
    }
    
    private function checkUser() {
        $query = "SELECT `request` FROM `JiyuuFestRequest` WHERE `request`='".$this->requestID."' AND `createdFor`='".$this->yourUserData['login']."';";
        $rezult = $this->SQL_HELPER->select($query);
        return count($rezult) > 0 || $this->yourUser->isAdmin() || $this->yourUserData['login'] === $this->requestUser;
    }
    
    private function generateHtml() {
        $this->HTML = '';
        if($this->executeSuccess) {
            echo '<script language="JavaScript">';
            echo 'window.location.href = "'.$this->urlHelper->chengeParams(array($this->fest,'showRequest',$this->requestID)).'"';
            echo '</script>';
        } else {
            $this->HTML .= $this->generateFestsHtml();
            $this->HTML .= $this->generateForm();
        }
    }

    private function generateFestsHtml() {
        if(isset($this->festsData['festivalStart'])) {
            $festivalDay = new DateTime($this->festsData['festivalStart']);
            $festivalDayText = $festivalDay->format('d M Y H:i');
        } else if(isset($this->festsData['festivalDay'])) {
            $festivalDay = new DateTime($this->festsData['festivalDay']);
            $festivalDayText = $festivalDay->format('d M Y');
        } else {
            $festivalDayText = $this->localization->getText("noFestDate");
        }
        $out = '';
        $out .= '<div class="FestElement '.$this->festsData['fest'].'">';
            $out .= '<div class="FestElementHeder">';
                $out .= '<div class="FestElementHederTitle">';
                $out .= $this->festsData['name'];
                $out .= '</div>';
                $out .= '<div class="FestElementHederSendRequestButton">';
                $out .= '<a href="'.$this->urlHelper->chengeParams(array($this->festsData['fest'])).'">';
                $out .= $this->localization->getText("BackToFest");
                $out .= '</a>';
                $out .= '</div>';
                $out .= '<div class="FestElementHederRightInforBlock">';
                $out .= $this->requestTypeData['name'];
                $out .= '</div>';
                if(isset($festivalDayText)) {
                    $out .= '<div class="FestElementHederFestDate">';
                    $out .= $this->festsData['venue'].": ".$festivalDayText;
                    $out .= '</div>';
                }
            $out .= '</div>';
            
            $IMG_URL = $this->bannerDir.$this->festsData['fest'].".png";
            if(!file_exists($IMG_URL)) {
                $IMG_URL = $this->bannerDir.$this->festsData['fest'].".jpg";
                if(!file_exists($IMG_URL)) {
                    $IMG_URL = null;
                }
            }
            if($IMG_URL!=null) {
                $out .= '<div class="FestElementBanner" style="background: url('.$IMG_URL.') no-repeat;"></div>';
            }
            $out .= '<div class="FestElementInfo">';
                $out .= '<div class="FestElementInfoData">';
                    $out .= '<div class="FestElementInfoDataBlock">';
                        $out .= '<div class="FestElementInfoDataHeder">'.$this->localization->getText("Intramural").'</div>';
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Intramural_Start"),$this->festsData['filingRequest_Intramural_Start']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Intramural_Stop"),$this->festsData['filingRequest_Intramural_Stop']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Intramural_End"),$this->festsData['filingRequest_Intramural_End']);
                    $out .= '</div>';
                    $out .= '<div class="FestElementInfoDataBlock">';
                        $out .= '<div class="FestElementInfoDataHeder">'.$this->localization->getText("Extramural").'</div>';
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Extramural_Start"),$this->festsData['filingRequest_Extramural_Start']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Extramural_Stop"),$this->festsData['filingRequest_Extramural_Stop']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Extramural_End"),$this->festsData['filingRequest_Extramural_End']);
                    $out .= '</div>';
                $out .= '</div>';
                $out .= '<div class="FestElementDescription">';
                    $out .= '<div class="FestElementDescriptionHeder">'.$this->localization->getText("FestElementDescriptionHeder").'</div>';
                    $out .= '<div class="FestElementDescriptionText">'.$this->festsData['description'].'</div>';
                $out .= '</div>';
                $out .= '<div class="clear"></div>';
                $out .= '<div class="FestElementRegulations">';
                    $out .= '<div class="FestElementRegulationsAgree">';
                    $out .= $this->localization->getText("AllFestivalRules");
                    $out .= '</div>';
                    $out .= '<div id="FestElementRegulationsHide" class="FestElementRegulations FestElementRegulationsHide" style="display: none;">';
                        $out .= '<div class="FestElementRegulationsHeder">'.$this->localization->getText("FestElementRegulationsHeder").'</div>';
                        $out .= '<div class="FestElementRegulationsText">'.$this->festsData['regulations'].'</div>';
                        $out .= '<div class="FestElementRegulationsHeder">'.$this->localization->getText("RequestRegulationsHeder").'</div>';
                        $out .= '<div class="FestElementRegulationsText">'.$this->requestTypeData['regulations'].'</div>';
                    $out .= '</div>';
                $out .= '</div>';
            $out .= '</div>';
        $out .= '</div>';
        return $out;
    }

    private function generateFestInfoData($text,$date,$fullDate = true) {
        $dateText = new DateTime($date);
        $out = '<div class="FestElementInfoDataArea">';
        $out .= '<div class="infoType">'.$text.'</div>';
        if($fullDate) {
            $out .= '<div class="infoData">'.$dateText->format('d M Y H:i').'</div>';
        } else {
            $out .= '<div class="infoData">'.$dateText->format('d M Y').'</div>';
        }
        $out .= '</div>';
        return $out;
    }
    
    private function generateForm() {
        $this->mimeType = 'image/jpeg,image/pjpeg,image/png';
        $out = '';
        $out .= $this->generateFormInformationHtml();
        $out .= '<form class="JFRequestForm" name="JFRequestForm" action="'.$this->urlHelper->getThisPage().'" enctype="multipart/form-data" method="post" accept-charset="UTF-8" autocomplete="on">';
        $out .= '<center>';
        $out .= '<table class="JFRequestFormTable" >';
        $out .= '<tr><td colspan="2" class="JFRequestFormTableRequestID">'.$this->festsData['name'].' | '.$this->requestTypeData['name'].' | '.$this->requestID.' | '.$this->requestUser.'</td></tr>';
        if($this->requestTypeData['characterName']>0) {
            $characterName = $this->inputHelper->textBox('characterName', 'characterName', 'characterName', 100, true, $this->getInsertData('characterName'));
            $out .= $this->inputHelper->createFormRow($characterName, true, 'Персонаж;');
        }
        if($this->requestTypeData['photo']>0) {
            $photo = $this->getFileUrl('photo')."<div>".$this->inputHelper->loadFiles('photo', 'photo', 'photo', false, false, $this->mimeType)."</div>";
            $photo_info = $this->localization->getText("loadFileNowOrLater")."<br><br>".$this->localization->getText("loadFile15MB");
            $out .= $this->inputHelper->createFormRow($photo, true, 'Фотограяия костюма', $photo_info);
        }
        if($this->requestTypeData['original']>0) {
            $original = $this->getFileUrl('original')."<div>".$this->inputHelper->loadFiles('original', 'original', 'original', false, false, $this->mimeType)."</div>";
            $original_info = $this->localization->getText("loadFileNowOrLater")."<br><br>".$this->localization->getText("loadFile15MB");
            $out .= $this->inputHelper->createFormRow($original, true, 'Изображение персонажа', $original_info);
        }
        $out .= '</table>';
        $out .= '<center>';
        $out .= '<input class="JFRequestFormButton" type="submit" name="JFRequestFormSubmit" value="Применить изменения">';
        $out .= '</form>';
        return $out;
    }

    protected function generateFormInformationHtml() {
        $out = "<div class='JRequestError'>";
        foreach ($this->insertDataErrors as $error) {
            $out .= "<div>".$error."</div>";
        }
        $out .= "</div>";
        return $out;
    }
    
    private function execute() {
        if(isset($_POST['JFRequestFormSubmit']) && $_POST['JFRequestFormSubmit']!==null && $_POST['JFRequestFormSubmit']!=='') {
            if($this->checkData()) {
                $this->createdDir();
                $this->insertData();
                $this->executeSuccess = true;
            }
        }
    }
    private function apdateInsertData() {
        $this->insertData['request'] = $this->requestID;
        $this->insertData['user'] = $this->requestUser;
        $this->insertData['characterName'] = $this->getPostValue('characterName');
        $this->insertData['deletFile'] = $this->getPostValue('deletFile');
        if($this->insertData['characterName'] === null) {
            $this->insertData['characterName'] = $this->requestUserData['characterName'];
        }
        $this->insertData['photo'] = $this->requestUserData['photo'];
        $this->insertData['original'] = $this->requestUserData['original'];
    }
    
    private function insertData() {
        if(isset($_POST['characterName']) && $_POST['characterName']!==null && $_POST['characterName']!=='') {
            $query = "UPDATE `JiyuuFestRequestUsers` SET `characterName`='".
                    $this->getInsertData('characterName')."' WHERE `request` = '".
                    $this->requestID."' AND `user` = '".$this->requestUser."'";
            $this->SQL_HELPER->insert($query);
        }
        $this->deletFiles();
        
        $this->downloadFileHelper = new DownloadFile($this->fileDir);
        $this->downloadImageHelper = new DownloadImage($this->fileDir);
        
        $this->downloadImageHelper->uploadFile('photo', 'photo', null, null, '5MB');
        $this->downloadImageHelper->makeMiniature('photo_s', 200, 200, 'default');
        $photoFileName = $this->downloadImageHelper->getFileName();
        
        $this->downloadImageHelper->uploadFile('original', 'original', null, null, '5MB');
        $this->downloadImageHelper->makeMiniature('original_s', 200, 200, 'default');
        $originalFileName = $this->downloadImageHelper->getFileName();
        
        $query = "UPDATE `JiyuuFestRequestUsers` SET ";
        $update = '';
        if(file_exists($this->fileDir.$photoFileName)) {
            $update .= "`photo`='".$photoFileName."', ";
        }
        if(file_exists($this->fileDir.$originalFileName)) {
            $update .= "`original`='".$originalFileName."', ";
        }
        if($update!='') {
            $query .= $update;
            $query = substr($query, 0, strlen($query)-2);
            $query .= " where `request`='".$this->requestID."' AND `user`='".mb_strtolower($this->getInsertData('user'), $this->_SITECONFIG->getCharset())."';";
            $this->SQL_HELPER->insert($query);
        }
    }
    
    protected function deletFiles() {
        if(isset($this->insertData['deletFile'])) {
            foreach ($this->insertData['deletFile'] as $deletFile) {
                $query = "SELECT `".$deletFile."` FROM `JiyuuFestRequestUsers` WHERE `request`='".$this->requestID."' AND  `user`='".$this->requestUser."';";
                $rezult = $this->SQL_HELPER->select($query,1);
                $file = $rezult[$deletFile];
                $file_s = $file;
                $file_s = str_replace(".jpg", '_s.jpg', $file_s);
                $file_s = str_replace(".JPG", '_s.JPG', $file_s);
                $file_s = str_replace(".png", '_s.png', $file_s);
                $file_s = str_replace(".PNG", '_s.PNG', $file_s);
                if(file_exists($this->fileDir.$file)) {
                    unlink($this->fileDir.$file);
                }
                if(file_exists($this->fileDir.$file_s)) {
                    unlink($this->fileDir.$file_s);
                }
                $query = "UPDATE `JiyuuFestRequestUsers` SET `".$deletFile."`=NULL WHERE `request`='".$this->requestID."' AND  `user`='".$this->requestUser."';";
                $this->SQL_HELPER->insert($query);
            }
        }
    }
    
    private function checkData() {
        $query = "SELECT count(`request`) as userAmount FROM `JiyuuFestRequestUsers` WHERE `request`='".
                $this->requestID."' AND `user`='".mb_strtolower($this->requestUser, $this->_SITECONFIG->getCharset())."'";
        $rezult = $this->SQL_HELPER->select($query,1);
        $issetUser = $rezult['userAmount'] > '0';
        return $issetUser;
    }


    
    private function createdDir() {
        if (!file_exists($this->fileDir)) { 
            mkdir($this->fileDir, 0777, true);
            chmod($this->fileDir, 0777);
        }
    }

    private function getInsertData($key) {
        if(isset($this->insertData[$key])) {
            return $this->insertData[$key];
        } else {
            return null;
        }
    }
    
    protected function getPostValue($key) {        
        return (
                isset($_POST[$key]) && 
                $_POST[$key]!==null && 
                $_POST[$key]!=""
        ) ? $_POST[$key] : null;
    }
    
    protected function getFileUrl($key) {
        $out = '';        
        $query = "SELECT `".$key."` FROM `JiyuuFestRequestUsers` WHERE `request`='".$this->requestID."' AND  `user`='".$this->requestUser."';";
        $rezult = $this->SQL_HELPER->select($query,1);
        $file = $rezult[$key];
        
        if($file !== null && $file != '') {
            $file_s = $file;
            $file_s = str_replace(".jpg", '_s.jpg', $file_s);
            $file_s = str_replace(".JPG", '_s.JPG', $file_s);
            $file_s = str_replace(".png", '_s.png', $file_s);
            $file_s = str_replace(".PNG", '_s.PNG', $file_s);
            $out .= '<div>';
            $out .= '<span class="fileExists"><a href="'.$this->fileDir.$file.'" target="_blank"><img src="'.$this->fileDir.$file_s.'?q='.rand (100,999).'"></a></span>';
            $out .= ' | Удалить этот файл ';
            $out .= $this->inputHelper->checkbox("deletFile[]", 'deletFile', 'deletFile', false, $key);
            $out .= '</div>';
        } else {
            $out .= '<div>';
            $out .= '<span class="fileNoExists">Файл отсутствует</span>';
            $out .= '</div>';
        }
        return $out;
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
