<?php
class JiyuuFests {
    private $SQL_HELPER;
    private $types = array();
    private $typesDate = array();
    private $fest;
    private $festData = array();
    private $urlHelper;
    private $localization;
    private $requestsListData;
    private $HTML;
    private $bannerDir = "./resources/Components/JiyuuFestRequest/JiyuuFest/banners/";
    
    private $yourUser;
    private $yourUserData;


    public function __construct($fest) {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->fest = $fest;
        $this->urlHelper = new UrlHelper();
        $this->localization = new Localization("JiyuuFests");
        $this->getUserData();
        $this->getFestsData();
        $this->getRequestTypeDate();
        $this->getRequestsListData();
        $this->generateHtml();
    }
    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function getRequestTypeOnFest() {
        $this->types = array();
        $query = "SELECT `type` FROM `JiyuuFestRequestTypeOnFest` WHERE `fest`='".$this->fest."' order by `sequence` asc;";
        $rezult = $this->SQL_HELPER->select($query);
        foreach ($rezult as $type) {
            $this->types[] = $type['type'];
        }
    }
    
    private function getTypeQueryWhereString() {
        $out = '';
        if(count($this->types) > 0) {
            $out .= 'WHERE ';
            foreach ($this->types as $type) {
                $out .= "`type`='".$type."' OR ";
            }
            $out = substr($out, 0, strlen($out)-4);
        }
        return $out;
    }
    
    private function getRequestTypeDate() {
        $this->getRequestTypeOnFest();
        $this->typesDate = array();
        $query = 'SELECT * FROM `JiyuuFestRequestType` '.$this->getTypeQueryWhereString().';';
        $this->typesDate = $this->SQL_HELPER->select($query);
    }
    
    private function getFestsData() {
        $this->festData = array();
        $query = "SELECT * FROM `JiyuuFest` WHERE `fest`='".$this->fest."';";
        $this->festData = $this->SQL_HELPER->select($query,1);
    }

    private function generateHtml() {
        $this->HTML = $this->generateFestsHtml();
    }
    
    private function generateFestsHtml() {
        if(isset($this->festData['festivalStart'])) {
            $festivalDay = new DateTime($this->festData['festivalStart']);
            $festivalDayText = $festivalDay->format('d M Y H:i');
        } else if(isset($this->festData['festivalDay'])) {
            $festivalDay = new DateTime($this->festData['festivalDay']);
            $festivalDayText = $festivalDay->format('d M Y');
        } else {
            $festivalDayText = $this->localization->getText("noFestDate");
        }
        $out = '';
        $out .= '<div class="FestElement '.$this->festData['fest'].'">';
            $out .= '<div class="FestElementHeder">';
                $out .= '<div class="FestElementHederTitle">';
                $out .= $this->festData['name'];
                $out .= '</div>';
                $out .= '<div class="FestElementHederSendRequestButton">';
                $out .= '<a href="'.$this->urlHelper->getThisParentPage().'">';
                $out .= $this->localization->getText("BackToFestList");
                $out .= '</a>';
                $out .= '</div>';
                if(isset($festivalDayText)) {
                    $out .= '<div class="FestElementHederFestDate">';
                    $out .= $this->festData['venue'].": ".$festivalDayText;
                    $out .= '</div>';
                }
            $out .= '</div>';
            
            $IMG_URL = $this->bannerDir.$this->festData['fest'].".png";
            if(!file_exists($IMG_URL)) {
                $IMG_URL = $this->bannerDir.$this->festData['fest'].".jpg";
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
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Intramural_Start"),$this->festData['filingRequest_Intramural_Start']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Intramural_Stop"),$this->festData['filingRequest_Intramural_Stop']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Intramural_End"),$this->festData['filingRequest_Intramural_End']);
                    $out .= '</div>';
                    $out .= '<div class="FestElementInfoDataBlock">';
                        $out .= '<div class="FestElementInfoDataHeder">'.$this->localization->getText("Extramural").'</div>';
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Extramural_Start"),$this->festData['filingRequest_Extramural_Start']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Extramural_Stop"),$this->festData['filingRequest_Extramural_Stop']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Extramural_End"),$this->festData['filingRequest_Extramural_End']);
                    $out .= '</div>';
                $out .= '</div>';
                $out .= '<div class="FestElementDescription">';
                    $out .= '<div class="FestElementDescriptionHeder">'.$this->localization->getText("FestElementDescriptionHeder").'</div>';
                    $out .= '<div class="FestElementDescriptionText">'.$this->festData['description'].'</div>';
                $out .= '</div>';
                $out .= '<div class="clear"></div>';
                $out .= $this->getCreateRequestButton();
                $out .= '<div class="clear"></div>';
                if(count($this->requestsListData)>0) {
                    $out .= "<div class='JRequestError'>";
                    $out .= '<div>На данный момент временно не доступно часть операций с заявками:<br>'
                            . '- удаление заявок;<br>'
                            . '- редактирование заявок;<br>'
                            . '- добавление участников;<br>'
                            . '- подробный просмотр заявок.<br><br>'
                            . 'Данный функционал будет добавлен с ближайшим обновлением сайта в конце недели.'
                            . '</div>';
                    $out .= '</div>';
                    foreach ($this->requestsListData as $request) {
                        $requestUI = new JiyuuFestRequest_ShowRequest($request['request']);
                        $out .= $requestUI->getHtml();
                    }
                    $out .= '<div class="clear"></div>';
                    $out .= $this->getCreateRequestButton();
                    $out .= '<div class="clear"></div>';
                }
            $out .= '</div>';
        $out .= '</div>';
        return $out;
    }
    
    private function getCreateRequestButton() {
        if($this->checkStartStopDate()) {
            return '<a class="FestElementSendRequestButton" href="'.
                    $this->urlHelper->chengeParams(array($this->festData['fest'],'createRequest')).
                    '" title="Принять правила">'.$this->localization->getText("SendRequest").'</a>';
        } else {
            return '';
        }
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
    
    private function checkStartStopDate() {
        $thisDate = new DateTime();
        $date['IntStart'] = new DateTime($this->festData['filingRequest_Intramural_Start']);
        $date['IntStop'] = new DateTime($this->festData['filingRequest_Intramural_Stop']);
        $date['ExtStart'] = new DateTime($this->festData['filingRequest_Extramural_Start']);
        $date['ExtStop'] = new DateTime($this->festData['filingRequest_Extramural_Stop']);
        return ($thisDate >= $date['IntStart'] || $thisDate >= $date['ExtStart']) && 
        ($thisDate <=$date['IntStop'] || $thisDate <= $date['ExtStop']);
    }
    
    private function getRequestsListData() {
        $query = "SELECT `request` FROM `JiyuuFestRequestUsers` WHERE `user`='".$this->yourUserData['login']."';";
        $query = "SELECT 
            JFR.`request`
             FROM 
            `JiyuuFestRequestUsers` as JFRU left Join 
            `JiyuuFestRequest` as JFR on
            JFRU.`request` = JFR.`request`
            where JFRU.`user` = '".$this->yourUserData['login']."' 
            AND JFR.`fest`='".$this->fest."'
            ORDER BY JFR.`changed` DESC;";
        $this->requestsListData = $this->SQL_HELPER->select($query);
    }


    public function getHtml() {
        return $this->HTML;
    }
    
    public function get() {
        echo $this->HTML;
    }
}
