<?php
/**
 * Description of JiyuuFestRequestUsers_Add
 *
 * @author maxim
 */
class JiyuuFestRequestUsers_Add {
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
    private $festsData = array();
    private $requestTypeData = array();
    private $errorBuffer = array();
    private $requestUsersData = array();
    private $insertData = array();
    // пользователи
    private $yourUser;
    private $yourUserData;
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
    
    public function __construct($requestID) {
        global $_SITECONFIG;
        $this->_SITECONFIG = $_SITECONFIG;
        $this->errorBuffer = array();
        $this->localization = new Localization("JiyuuFests");
        $this->urlHelper = new UrlHelper();
        $this->requestID = $requestID;
        $this->getUserData();
        if($this->authorization) {
            global $_SQL_HELPER;
            $this->SQL_HELPER = $_SQL_HELPER;
            $this->getFest();
            $this->getFestsData();
            $this->getRequestTypeDate();
            $this->getUsersData();
            $this->urlHelper = new UrlHelper();
            $this->inputHelper = new InputHelper();
            if($this->checkUser()) {
                $this->execute();
                $this->generateHtml();
            } else {
                $this->errorBuffer[] = $this->localization->getText("ErrorPermissionDenied");
            }
        } else {
            $this->errorBuffer[] = $this->localization->getText("ErrorUnauthorized");
        }
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
        return count($rezult) > 0 || $this->yourUser->isAdmin();
    }
    
    private function generateHtml() {
        $this->HTML = '';
        $this->HTML .= $this->generateFestsHtml();
        $this->HTML .= $this->generateForm();
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
        $user = $this->inputHelper->textBox('user', 'user', 'user', 25, true, $this->getInsertData('user'));
        $out .= $this->inputHelper->createFormRow($user, true, 'Никнейм пользователя');
        if($this->requestTypeData['characterName']>0) {
            $characterName = $this->inputHelper->textBox('characterName', 'characterName', 'characterName', 100, true, $this->getInsertData('characterName'));
            $out .= $this->inputHelper->createFormRow($characterName, true, 'Персонаж;');
        }
        if($this->requestTypeData['photo']>0) {
            $photo = $this->inputHelper->loadFiles('photo', 'photo', 'photo', false, false, $this->mimeType);
            $photo_info = $this->localization->getText("loadFileNowOrLater")."<br><br>".$this->localization->getText("loadFile15MB");
            $out .= $this->inputHelper->createFormRow($photo, true, 'Фотограяия костюма', $photo_info);
        }
        if($this->requestTypeData['original']>0) {
            $original = $this->inputHelper->loadFiles('original', 'original', 'original', false, false, $this->mimeType);
            $original_info = $this->localization->getText("loadFileNowOrLater")."<br><br>".$this->localization->getText("loadFile15MB");
            $out .= $this->inputHelper->createFormRow($original, true, 'Изображение персонажа', $original_info);
        }
        $out .= '</table>';
        $out .= '<center>';
        $out .= '<input class="JFRequestFormButton" type="submit" name="JFRequestFormSubmit" value="'.$this->localization->getText("createButton").'">';
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
                $this->apdateInsertData();
                $this->insertData();
            }
        }
    }
    private function apdateInsertData() {
        $this->insertData['request'] = $this->requestID;
        $this->insertData['user'] = $this->getPostValue('user');
        $this->insertData['characterName'] = $this->getPostValue('characterName');
    }
    
    private function insertData() {
        $query = "INSERT INTO `JiyuuFestRequestUsers` SET ";
        $query .= "`request`='".$this->requestID."', ";
        $query .= "`user`='".mb_strtolower($this->getInsertData('user'), $this->_SITECONFIG->getCharset())."', ";
        if(isset($_POST['characterName']) && $_POST['characterName']!==null && $_POST['characterName']!=='') {
            $query .= "`characterName`='".$this->getInsertData('characterName')."', ";
        }
        $query .= "`confirmed`='0';";
        $this->SQL_HELPER->insert($query);
        
        $this->fileDir = $this->fileDir.$this->fest."/".$this->requestID."/".mb_strtolower($this->getInsertData('user'), $this->_SITECONFIG->getCharset())."/";
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
    
    private function checkData() {
        // пришло значение
        $isset_user = isset($_POST['user']) && $_POST['user']!==null && $_POST['user']!=='';
        // првоеряем есть ли место
        $query = "SELECT count(`request`) as userAmount FROM `JiyuuFestRequest` WHERE `request`='".$this->requestID."'";
        $rezult = $this->SQL_HELPER->select($query,1);
        $number = $rezult['userAmount'] < $this->numberOfParticipants;
        // првоеряем есть ли пользователь
        $query = "select `login` from `Users`
            where 
            `login`='".mb_strtolower($_POST['user'], $this->_SITECONFIG->getCharset())."';";
        $isUser = count($this->SQL_HELPER->select($query,1));
        // првоеряем не был ли он уже добавлен
        $query = "SELECT count(`request`) as userAmount FROM `JiyuuFestRequestUsers` WHERE `request`='".$this->requestID."' AND `user`='".mb_strtolower($_POST['user'], $this->_SITECONFIG->getCharset())."'";
        $rezult = $this->SQL_HELPER->select($query,1);
        $noDublicate = $rezult['userAmount'] < '1';
        
        if(!$isset_user) {
            $this->insertDataErrors[] = 'Неуказан логин';
        }
        if(!$number) {
            $this->insertDataErrors[] = 'Все места заняты';
        }
        if(!$isUser) {
            $this->insertDataErrors[] = 'Такого пользователя нет';
        }
        if(!$noDublicate) {
            $this->insertDataErrors[] = 'Такой пользователь уже учавствует';
        }
        
        return $isset_user && $number && $isUser && $noDublicate;
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
