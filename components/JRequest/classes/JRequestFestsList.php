<?php

class JRequestFestsList {
    private $fests;
    private $urlHelper;
    private $yourUser;
    private $yourUserData;
    
    public function __construct() {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->urlHelper = new UrlHelper();
        $this->getUserData();
        $this->getFestsData();
    }
    
    protected function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function getFestsData() {
        $thisdate = date("Y-m-d h:i:s");
        //$query="SELECT * FROM `JRequestFest` where `start`<='".$thisdate."' AND `stop`>='".$thisdate."';";
        $query="SELECT * FROM `JRequestFest` ORDER BY `stop` DESC;";
        $this->fests = $this->SQL_HELPER->select($query);
    }
    
    public function get() {
        $thisdate = date("Y-m-d h:i:s");
        $out = "";
        foreach ($this->fests as $fest) {
            $start = new DateTime($fest['start']);
            $stop = new DateTime($fest['stop']);
            $date = new DateTime($fest['date']);
            
            $JRequestAddStatus=false;
            if($thisdate >= $fest['start']) {
                if($thisdate <= $fest['stop']) {
                    $JRequestText = "Подача заявок открыта";
                    $JRequestAddStatus=true;
                } else {
                    $JRequestText = "Подача заявок окончена";
                }
            } else {
                $JRequestText = "Подача заявок еще не началась";
            }
            if($this->yourUser->isAdmin()) {
                $JRequestAddStatus=true;
            }
            
            $out .= '<div class="JFestsListElement">';
                $out .= '<div class="JFestsListHeder">';
                    if($JRequestAddStatus) {
                        $out .= '<div class="JRequestAddIcon">';
                            $params = null;
                            $params[0] = $fest['id'];
                            $url = $this->urlHelper->chengeParams($params);
                            $out .= '<a href="'.$url.'" title="просмотреть заявки/подать заявку">';
                            $out .= '<div id="eye" class="gap"><span class="inner"></span></div>';
                            $out .= '</a>';
                        $out .= '</div>';
                    }
                    $out .= '<div class="JRequestAddStatus">';
                        $out .= $JRequestText;
                    $out .= '</div>';
                    $out .= '<div class="JRequestTypeTitle">';
                        $out .= $fest['name'];
                    $out .= '</div>';
                $out .= '</div>';
                $out .= '<div class="JFestsListInfo '.$fest['id'].'">';
                    $out .= '<div class="JFestsListInfoDate '.$fest['id'].'">';
                        $out .= '<span class="infoType">Начало приема заявок: </span>';
                        $out .= '<span class="infoData">'.$start->format('d M Y h:m').'</span>';
                        $out .= '<br />';
                        $out .= '<span class="infoType">Окончание приема заявок: </span>';
                        $out .= '<span class="infoData">'.$stop->format('d M Y h:m').'</span>';
                        $out .= '<br />';
                        $out .= '<span class="infoType">Дата проведения фестиваля: </span>';
                        $out .= '<span class="infoData">'.$date->format('d M Y').'</span>';
                        $out .= '<br />';
                    $out .= '</div>';
                    $out .= '<div class="JFestsListInfoTitle">';
                    $out .= 'Информация о фестивале';
                    $out .= '</div>';
                    $out .= '<div class="JFestsListInfoDescription">';
                        $out .= $fest['description'];
                    $out .= '</div>';
                    /*
                    $out .= '<div class="JFestsListInfoTitle">';
                    $out .= 'Правила проведения фестиваля';
                    $out .= '</div>';
                    $out .= '<div class="JFestsListInfoRegulations">';
                        $out .= $fest['regulations'];
                    $out .= '</div>';
                     */
                $out .= '</div>';
            $out .= '</div>';
        }
        return $out;
    }
}
