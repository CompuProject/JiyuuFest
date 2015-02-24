<?php

class JiyuuFestsList {
    private $SQL_HELPER;
    private $localization;
    protected $urlHelper;
    
    private $festsData;
    private $HTML = '';
    
    protected $bannerDir = "./resources/Components/JiyuuFestRequest/JiyuuFest/banners/";
    
    public function __construct() {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->localization = new Localization("JiyuuFests");
        $this->urlHelper = new UrlHelper();
        $this->getFestsData();
        $this->generateFestsList();
    }
    
    public function getHtml() {
        return $this->HTML;
    }
    
    public function get() {
        echo $this->HTML;
    }


    private function getFestsData() {
        $query = "SELECT * FROM `JiyuuFest` order by `sequence` desc;";
        $this->festsData = $this->SQL_HELPER->select($query);
    }
    
    private function generateFestsList() {
        $this->HTML = '';
        $this->HTML .= '<div class="FestsList">';
        foreach ($this->festsData as $festData) {
            $this->HTML .= $this->generateFestElement($festData);
        }
        $this->HTML .= '</div>';
    }
    
    private function checkBeforeStartDate($thisDate,$festData) {
        $date['IntStart'] = new DateTime($festData['filingRequest_Intramural_Start']);
        $date['ExtStart'] = new DateTime($festData['filingRequest_Extramural_Start']);
        return $thisDate < $date['IntStart'] && $thisDate < $date['ExtStart'];
    }
    
    private function checkStartStopDate($thisDate,$festData) {
        $date['IntStart'] = new DateTime($festData['filingRequest_Intramural_Start']);
        $date['IntStop'] = new DateTime($festData['filingRequest_Intramural_Stop']);
        $date['ExtStart'] = new DateTime($festData['filingRequest_Extramural_Start']);
        $date['ExtStop'] = new DateTime($festData['filingRequest_Extramural_Stop']);
        return ($thisDate >= $date['IntStart'] || $thisDate >= $date['ExtStart']) && ($thisDate <=$date['IntStop'] || $thisDate <= $date['ExtStop']);
    }
    
    private function checkStopEndDate($thisDate,$festData) {
        $date['IntStop'] = new DateTime($festData['filingRequest_Intramural_Stop']);
        $date['IntEnd'] = new DateTime($festData['filingRequest_Intramural_End']);
        $date['ExtStop'] = new DateTime($festData['filingRequest_Extramural_Stop']);
        $date['ExtEnd'] = new DateTime($festData['filingRequest_Extramural_End']);
        return $thisDate >= $date['IntStop'] && $thisDate >= $date['ExtStop'] && $thisDate <= $date['IntEnd'] && $thisDate <= $date['ExtEnd'];
    }
    
    private function checkEndDate($thisDate,$festData) {
        $date['IntEnd'] = new DateTime($festData['filingRequest_Intramural_End']);
        $date['ExtEnd'] = new DateTime($festData['filingRequest_Extramural_End']);
        $festivalDay = new DateTime($festData['festivalDay']);
        return $thisDate > $date['IntEnd'] && $thisDate > $date['ExtEnd'] && $thisDate <= $festivalDay;
    }
    
    private function checkEndFestDate($thisDate,$festData) {
        $festivalDay = new DateTime($festData['festivalDay']);
        return $thisDate > $festivalDay;
    }
    
    private function  generateFestElementHeadButtonBlock($thisDate,$festData) {
        $out = '';
        $out .= '<div class="FestElementHederFestDate">';
        if($this->checkBeforeStartDate($thisDate,$festData)) {
            $out .= $this->localization->getText('RequestNoStart');
        } 
        else if($this->checkStartStopDate($thisDate,$festData)) {
            $out .= $this->localization->getText('RequestStart');
        } 
        else if($this->checkStopEndDate($thisDate,$festData)) {
            $out .= $this->localization->getText('RequestStop');
        } 
        else if($this->checkEndDate($thisDate,$festData)) {
            $out .= $this->localization->getText('RequestEnd');
        } 
        else if($this->checkEndFestDate($thisDate,$festData)) {
            $out .= $this->localization->getText('FestEnd');
        }
        $out .= '</div>';
        return $out;
    }

    private function generateFestElement($festData) {
        $thisDate = new DateTime();
        if(isset($festData['festivalStart'])) {
            $festivalDay = new DateTime($festData['festivalStart']);
            $festivalDayText = $festivalDay->format('d M Y H:i');
        } else if(isset($festData['festivalDay'])) {
            $festivalDay = new DateTime($festData['festivalDay']);
            $festivalDayText = $festivalDay->format('d M Y');
        } else {
            $festivalDayText = $this->localization->getText("noFestDate");
        }
        $out = '';
        $out .= '<div class="FestElement '.$festData['fest'].'">';
            $out .= '<div class="FestElementHeder">';
                $out .= '<div class="FestElementHederTitle">';
                $out .= $festData['name'];
                $out .= '</div>';
//                if(!$this->checkEndFestDate($thisDate,$festData)) {
                    $out .= '<div class="FestElementHederSendRequestButton">';
                    $out .= '<a href="'.$this->urlHelper->chengeParams(array($festData['fest'])).'">';
                    $out .= $this->localization->getText("ShowFest");
                    $out .= '</a>';
                    $out .= '</div>';
//                }
                $out .= $this->generateFestElementHeadButtonBlock($thisDate,$festData);
                
                if(isset($festivalDayText)) {
                    $out .= '<div class="FestElementHederFestDate">';
                    $out .= $festData['venue'].": ".$festivalDayText;
                    $out .= '</div>';
                }
            $out .= '</div>';
            
            $IMG_URL = $this->bannerDir.$festData['fest'].".png";
            if(!file_exists($IMG_URL)) {
                $IMG_URL = $this->bannerDir.$festData['fest'].".jpg";
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
                        $out .= $this->generateFestElementInfoData($this->localization->getText("filingRequest_Intramural_Start"),$festData['filingRequest_Intramural_Start']);
                        $out .= $this->generateFestElementInfoData($this->localization->getText("filingRequest_Intramural_Stop"),$festData['filingRequest_Intramural_Stop']);
                        $out .= $this->generateFestElementInfoData($this->localization->getText("filingRequest_Intramural_End"),$festData['filingRequest_Intramural_End']);
                    $out .= '</div>';
                    $out .= '<div class="FestElementInfoDataBlock">';
                        $out .= '<div class="FestElementInfoDataHeder">'.$this->localization->getText("Extramural").'</div>';
                        $out .= $this->generateFestElementInfoData($this->localization->getText("filingRequest_Extramural_Start"),$festData['filingRequest_Extramural_Start']);
                        $out .= $this->generateFestElementInfoData($this->localization->getText("filingRequest_Extramural_Stop"),$festData['filingRequest_Extramural_Stop']);
                        $out .= $this->generateFestElementInfoData($this->localization->getText("filingRequest_Extramural_End"),$festData['filingRequest_Extramural_End']);
                    $out .= '</div>';
                $out .= '</div>';
                $out .= '<div class="FestElementDescription">';
                    $out .= '<div class="FestElementDescriptionHeder">'.$this->localization->getText("FestElementDescriptionHeder").'</div>';
                    $out .= '<div class="FestElementDescriptionText">'.$festData['description'].'</div>';
                $out .= '</div>';
                $out .= '<div class="clear"></div>';
            $out .= '</div>';
        $out .= '</div>';
        return $out;
    }
    
    private function generateFestElementInfoData($text,$date,$fullDate = true) {
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
}
