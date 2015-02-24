<?php

class JiyuuFestRequestType {
    private $SQL_HELPER;
    private $typesDate = array();
    private $fest;
    private $festsData = array();
    private $urlHelper;
    private $localization;
    private $HTML;
    private $bannerDir = "./resources/Components/JiyuuFestRequest/JiyuuFest/banners/";


    public function __construct($fest) {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->fest = $fest;
        $this->urlHelper = new UrlHelper();
        $this->localization = new Localization("JiyuuFests");
        $this->getFestsData();
        $this->getRequestTypeDate();
        $this->generateHtml();
    }
    
    private function getRequestTypeDate() {
        $this->typesDate = array();
        $query = "SELECT 
            RT.`type`, 
            RT.`code`, 
            RT.`name`, 
            RT.`regulations`, 
            RT.`tableName`, 
            RT.`sequence`, 
            RT.`minNumberOfParticipants`, 
            RT.`maxNumberOfParticipants`, 
            RT.`minDurationMinutes`, 
            RT.`minDurationSeconds`, 
            RT.`maxDurationMinutes`, 
            RT.`maxDurationSeconds`, 
            RT.`mayBeContest`, 
            RT.`characterName`, 
            RT.`photo`, 
            RT.`original`, 
            RT.`intramural`
            FROM `JiyuuFestRequestType` as RT left join `JiyuuFestRequestTypeOnFest` as RTF on
            RT.`type` = RTF.`type`
            where RTF.`fest` = '".$this->fest."'
            order by RTF.`sequence` asc";
        $this->typesDate = $this->SQL_HELPER->select($query);
    }
    
    private function getFestsData() {
        $this->festsData = array();
        $query = "SELECT * FROM `JiyuuFest` WHERE `fest`='".$this->fest."';";
        $this->festsData = $this->SQL_HELPER->select($query,1);
    }

    private function generateHtml() {
        $this->HTML = $this->generateFestsHtml();
        $this->HTML .= $this->generateRequestTypeDateHtml();
    }
    
    private function generateRequestTypeDateHtml() {
        $out = '';
        $out .= '<div class="FestElementRequestTypeDate">';
            foreach ($this->typesDate as $type) {
                $out .= $this->generateRequestTypeHtml($type);
            }
            $out .= '<div class="clear"></div>';
        $out .= '</div>';
        return $out;
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
    
    private function generateRequestTypeHtml($type) {
        $out = '<div class="FestElementRequestTypeDateElement">';
            $out .= '<div class="FestElementRequestTypeDateElementName">';
            $out .= '<a class="fancybox-doc" href="#FestElementRegulationsHide_'.$type['type'].'" title="Прпринять правила">';
            $out .= $type['name'];
            $out .= '</a>';
            $out .= '</div>';
        $out .= '</div>';
        $out .= '<div class="FestElementRegulations">';
            $out .= '<div id="FestElementRegulationsHide_'.$type['type'].'" class="FestElementRegulations FestElementRegulationsHide" style="display: none;">';
                $out .= '<div class="FestElementRegulationsHeder">'.$this->localization->getText("FestElementRegulationsHeder").'</div>';
                $out .= '<div class="FestElementRegulationsText">'.$this->festsData['regulations'].'</div>';
                $out .= '<div class="FestElementRegulationsHeder">'.$this->localization->getText("RequestRegulationsHeder").' ('.$type['name'].')</div>';
                $out .= '<div class="FestElementRegulationsText">'.$type['regulations'].'</div>';
                $out .= '<a class="FestElementSendRequestButton" href="'.$this->urlHelper->chengeParams(array($this->festsData['fest'],'createRequest',$type['type'])).'">'.$this->localization->getText("AgreeAndSendRequest").'</a>';
            $out .= '</div>';
        $out .= '</div>';
        return $out;
    }

    public function getHtml() {
        return $this->HTML;
    }
    
    public function get() {
        echo $this->HTML;
    }
}
