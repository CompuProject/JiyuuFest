<?php
class JiyuuFestRequest_CreateRequest {
    // помошники
    protected $SQL_HELPER;
    protected $urlHelper;
    protected $inputHelper;
    protected $localization;
    protected $downloadFileHelper;
    protected $downloadImageHelper;
    protected $mimeType = array();
    // данные
    protected $requestID;
    protected $festsData = array();
    protected $requestTypeData = array();
    protected $errorBuffer = array();
    protected $insertData = array();
    // пользователи
    protected $yourUser;
    protected $yourUserData;
    // параметры
    protected $fest = null;
    protected $requestType = null;
    // настрйоки
    protected $fileDir = "./resources/Components/JiyuuFestRequest/Requests/";
    protected $bannerDir = "./resources/Components/JiyuuFestRequest/JiyuuFest/banners/";
    // вывод
    protected $HTML;
    protected $executionOut = '';
    protected $insertDataErrors = array();
    protected $executeSuccess = false;
    
    public function __construct($fest,$requestType) {
        $this->errorBuffer = array();
        $this->localization = new Localization("JiyuuFests");
        $this->getUserData();
        if($this->yourUser->checkAuthorization()) {
            global $_SQL_HELPER;
            $this->SQL_HELPER = $_SQL_HELPER;
            // поулчение информации
            $this->fest = $fest;
            $this->requestType = $requestType;
            $this->getFestsData();
            $this->getRequestTypeDate();
            // Вспомогательное
            $this->setMimeType();
            $this->generateRequestID();
            $this->fileDir = $this->fileDir.$this->festsData['fest']."/".$this->requestID."/";
            $this->urlHelper = new UrlHelper();
            $this->inputHelper = new InputHelper();
            $this->downloadFileHelper = new DownloadFile($this->fileDir);
            $this->downloadImageHelper = new DownloadImage($this->fileDir);
            // Генерация HTML
            $this->apdateInsertData();
            $this->execute();
            $this->generateHtml();
        } else {
            $this->errorBuffer[] = $this->localization->getText("ErrorUnauthorized");
        }
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
    
    public function get() {
        echo $this->HTML;
    }
    
    protected function createdDir() {
        if (!file_exists($this->fileDir)) { 
            mkdir($this->fileDir, 0777, true);
            chmod($this->fileDir, 0777);
        }
    }
    
    private function setMimeType() {
        $this->mimeType['video'] = 'application/x-troff-msvideo,video/avi,video/msvideo,video/x-msvideo,video/mp4,video/mpeg,video/x-mpeg';
        $this->mimeType['audio'] = 'audio/mpeg3,audio/x-mpeg-3,audio/mpeg';
        $this->mimeType['doc'] = 'application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        $this->mimeType['img'] = 'image/jpeg,image/pjpeg,image/png';
    }
    
    protected function getPostValue($key) {        
        return (
                isset($_POST[$key]) && 
                $_POST[$key]!==null && 
                $_POST[$key]!=""
        ) ? $_POST[$key] : null;
    }
    protected function checkValue($key,$preg=null) {
        return (
                isset($this->insertData[$key]) && 
                $this->insertData[$key]!==null && 
                $this->insertData[$key]!="" &&
                ($preg==null || preg_match($preg, $this->insertData[$key]))
        );
    }
    
    protected function generateRequestID() {
        $codeletters = 'ABCDEFGKIJKLMNOPQRSTUVWXYZ123456789';
        $date = date("ymd");
        $codelen = 6;
        $this->requestID = $this->festsData['code'].$this->requestTypeData['code']."_";
        for ($i = 0; $i < strlen($date); $i++){ 
            $this->requestID .= $codeletters[$date[$i]]; 
            $this->requestID .= $codeletters[rand(0, strlen($codeletters)-1) ]; 
        }
    }

    protected function getInsertData($key) {
        if(isset($this->insertData[$key])) {
            return $this->insertData[$key];
        } else {
            return null;
        }
    }


    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
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
    
    private function generateHtml() {
        $this->HTML = "";
        $this->HTML .= $this->generateFestsHtml();
        if($this->executeSuccess) {
            echo '<script language="JavaScript">';
            echo 'window.location.href = "'.$this->urlHelper->chengeParams(array($this->fest,'showRequest',$this->requestID)).'"';
            echo '</script>';
            $showRequest = new JiyuuFestRequest_ShowRequest($this->requestID);
            $this->HTML .= $showRequest->getHtml();
        } else {
            $this->HTML .= $this->generateFormHtml();
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
                $out .= '<a href="'.$this->urlHelper->chengeParams(array($this->festsData['fest'],'createRequest')).'">';
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
    
    protected function checkMayBeContest() {
        return $this->requestTypeData['mayBeContest'] > 0;
    }

    protected function checkNecessaryNumberOfParticipants() {
        return $this->requestTypeData['minNumberOfParticipants'] != $this->requestTypeData['maxNumberOfParticipants'];
    }
    
    protected function checkNecessaryDuration() {
        return isset($this->requestTypeData['minDurationMinutes']) && 
                $this->requestTypeData['minDurationMinutes']!='' && 
                $this->requestTypeData['minDurationMinutes']!=null && 
                isset($this->requestTypeData['maxDurationMinutes']) && 
                $this->requestTypeData['maxDurationMinutes']!='' && 
                $this->requestTypeData['maxDurationMinutes']!=null;
    }

    
    private function generateFormHtml() {
        $out = '';
        $out .= $this->generateFormInformationHtml();
        $out .= '<form class="JFRequestForm" name="JFRequestForm" action="'.$this->urlHelper->getThisPage().'" enctype="multipart/form-data" method="post" accept-charset="UTF-8" autocomplete="on">';
        $out .= '<center>';
        $out .= '<table class="JFRequestFormTable" >';
        if($this->checkMayBeContest()) {
            // contest
            $contestValueArray = array();
            $contestValueArray[0]['value']='1';
            $contestValueArray[0]['text']=$this->localization->getText("ContestVal");
            $contestValueArray[1]['value']='0';
            $contestValueArray[1]['text']=$this->localization->getText("NoContestVal");
            $contest = $this->inputHelper->select('contest', 'contest', $contestValueArray, true, $this->getInsertData('contest'));
            $out .= $this->inputHelper->createFormRow($contest, true, $this->localization->getText("contest"));
        }
        if($this->checkNecessaryNumberOfParticipants()) {
            // numberOfParticipants
            $numberOfParticipants = $this->inputHelper->textBox('numberOfParticipants', 'numberOfParticipants', 'numberOfParticipants minTextBox', 2, true, $this->getInsertData('numberOfParticipants'));
            $numberOfParticipantsInfo = $this->localization->getText("numberOfParticipants")." ".
                    $this->localization->getText("from")." ".$this->requestTypeData['minNumberOfParticipants']." ".
                    $this->localization->getText("to")." ".$this->requestTypeData['maxNumberOfParticipants'];
            $out .= $this->inputHelper->createFormRow($numberOfParticipants, true, $this->localization->getText("numberOfParticipants"),$numberOfParticipantsInfo);
        }
        if($this->checkNecessaryDuration()) {
            // duration
            $durationMin = $this->inputHelper->textBox('durationMin', 'durationMin', 'durationMin minTextBox', 2, true, $this->getInsertData('durationMin'));
            $durationSec = $this->inputHelper->textBox('durationSec', 'durationSec', 'durationSec minTextBox', 2, true, $this->getInsertData('durationSec'));
            $durationMin_durationSec = $durationMin." ".$this->localization->getText("min")." ".$durationSec." ".$this->localization->getText("sec");
            $durationInfo = $this->localization->getText("duration")." ".
                    $this->localization->getText("from")." ".$this->requestTypeData['minDurationMinutes']." : ".$this->requestTypeData['minDurationSeconds']." ".
                    $this->localization->getText("to")." ".$this->requestTypeData['maxDurationMinutes']." : ".$this->requestTypeData['maxDurationSeconds'];
            $out .= $this->inputHelper->createFormRow($durationMin_durationSec, true, $this->localization->getText("duration"),$durationInfo);
        }
        $out .= $this->generateFormInputElementsHtml();
        // wish
        $wish = $this->inputHelper->textarea('wish', 'wish', 'wish', 5000, false, $this->getInsertData('wish'));
        $out .= $this->inputHelper->createFormRow($wish, false, $this->localization->getText("wish"));
        $out .= '</table>';
        $out .= '<center>';
        $out .= '<input class="JFRequestFormButton" type="submit" name="JFRequestFormSubmit" value="'.$this->localization->getText("createButton").'">';
        $out .= '</form>';
        return $out;
    }
    
    private function apdateInsertData() {
        $this->insertData['request'] = $this->requestID;
        if($this->checkMayBeContest()) {
            $this->insertData['contest'] = $this->getPostValue('contest');
        } else {
            $this->insertData['contest'] = '0';
        }
        $this->insertData['createdFor'] = $this->yourUserData['login'];
        $thisdate = date("Y-m-d H:i:s");
        $this->insertData['created'] = $thisdate;
        $this->insertData['changed'] = $thisdate;
        $this->insertData['type'] = $this->requestType;
        $this->insertData['fest'] = $this->fest;
        $this->insertData['status'] = 'issued';
        if($this->checkNecessaryNumberOfParticipants()) {
            $this->insertData['numberOfParticipants'] = $this->getPostValue('numberOfParticipants');
            if($this->insertData['numberOfParticipants']===null || $this->insertData['numberOfParticipants']==='') {
                $this->insertData['numberOfParticipants'] = $this->requestTypeData['minNumberOfParticipants'];
            }
        } else {
            $this->insertData['numberOfParticipants'] = $this->requestTypeData['minNumberOfParticipants'];
        }
        if($this->checkNecessaryDuration()) {
            $this->insertData['durationMin'] = $this->getPostValue('durationMin');
            $this->insertData['durationSec'] = $this->getPostValue('durationSec');
            if($this->insertData['durationMin']===null || $this->insertData['durationMin']==='') {
                $this->insertData['durationMin'] = "0";
            }
            if($this->insertData['durationSec']===null || $this->insertData['durationSec']==='') {
                $this->insertData['durationSec'] = "0";
            }
            $minTime = $this->requestTypeData['minDurationMinutes']*60+$this->requestTypeData['minDurationSeconds'];
            $maxTime = $this->requestTypeData['maxDurationMinutes']*60+$this->requestTypeData['maxDurationMinutes'];
            $time = $this->insertData['durationMin']*60+$this->insertData['durationSec'];
            if($time < $minTime) {
                $this->insertData['durationMin'] = $this->requestTypeData['minDurationMinutes'];
                $this->insertData['durationSec'] = $this->requestTypeData['minDurationSeconds'];
            } else if($time > $maxTime) {
                $this->insertData['durationMin'] = $this->requestTypeData['maxDurationMinutes'];
                $this->insertData['durationSec'] = $this->requestTypeData['maxDurationSeconds'];
            }
        }
        $this->apdateOthersInsertData();
        $this->insertData['wish'] = $this->getPostValue('wish');
    }
    
    private function checkMainInsertValue() {
        $contest = $this->insertData['contest'] === '0' || $this->insertData['contest'] === '1';
        $numberOfParticipants = $this->checkValue('numberOfParticipants','/^[0-9]{1,2}$/') && $this->insertData['numberOfParticipants'] >= $this->requestTypeData['minNumberOfParticipants'] && $this->insertData['numberOfParticipants'] <= $this->requestTypeData['maxNumberOfParticipants'];
        if($this->checkNecessaryDuration()) {
            $minTime = $this->requestTypeData['minDurationMinutes']*60+$this->requestTypeData['minDurationSeconds'];
            $maxTime = $this->requestTypeData['maxDurationMinutes']*60+$this->requestTypeData['maxDurationSeconds'];
            $time = $this->insertData['durationMin']*60+$this->insertData['durationSec'];
            $duration = $this->checkValue('durationMin','/^[0-9]{1,2}$/') && $this->checkValue('durationSec','/^[0-9]{1,2}$/') && $this->insertData['durationSec']<60 && $time >= $minTime && $time <= $maxTime;
        } else {
            $duration = true;
        }
        return $contest && $numberOfParticipants && $duration;
    }
    
    private function mysqlInsertMainData() {
        $query = "INSERT INTO `JiyuuFestRequest` SET ";
        $query .= "`request`='".$this->requestID."', ";
        $query .= "`contest`='".$this->insertData['contest']."', ";
        $query .= "`createdFor`='".$this->insertData['createdFor']."', ";
        $query .= "`created`='".$this->insertData['created']."', ";
        $query .= "`changed`='".$this->insertData['changed']."', ";
        $query .= "`type`='".$this->insertData['type']."', ";
        $query .= "`fest`='".$this->insertData['fest']."', ";
        $query .= "`status`='".$this->insertData['status']."', ";
        $query .= "`numberOfParticipants`='".$this->insertData['numberOfParticipants']."', ";
        if($this->checkNecessaryDuration()) {
            $query .= "`durationMin`='".$this->insertData['durationMin']."', ";
            $query .= "`durationSec`='".$this->insertData['durationSec']."', ";
        }
        $query .= "`wish`='".$this->insertData['wish']."';";
        $this->SQL_HELPER->insert($query);
        
        $query = "INSERT INTO `JiyuuFestRequestUsers` SET ";
        $query .= "`request`='".$this->requestID."', ";
        $query .= "`user`='".$this->insertData['createdFor']."', ";
        $query .= "`confirmed`='1';";
        $this->SQL_HELPER->insert($query);
    }
    
    private function execute() {
        if(isset($_POST['JFRequestFormSubmit']) && $_POST['JFRequestFormSubmit']!==null && $_POST['JFRequestFormSubmit']!=='') {
            if($this->checkMainInsertValue() && $this->checkOthersInsertValue()) {
                $this->createdDir();
                $this->mysqlInsertMainData();
                $this->mysqlInsertOthersData();
                $this->generateExecutionOut();
                $this->executeSuccess = true;
            } else {
                $this->insertDataErrors[] = $this->localization->getText("ErrorsInsertDataCheckMainInsert");
            }
        }
    }
    
    private function generateExecutionOut() {
        $this->executionOut;
    }

/*~~~~~~~~~~* Переопределяемые функции *~~~~~~~~~~*/

    protected function generateFormInformationHtml() {
        $out = "<div class='JRequestError'>";
        foreach ($this->insertDataErrors as $error) {
            $out .= "<div>".$error."</div>";
        }
        $out .= "</div>";
        return $out;
    }
    
    protected function generateFormInputElementsHtml() {
        return '';
    }

    protected function apdateOthersInsertData() {
        return;
    }
    
    protected function checkOthersInsertValue() {
        return true;
    }
    
    protected function mysqlInsertOthersData() {
        return;
    }
}
