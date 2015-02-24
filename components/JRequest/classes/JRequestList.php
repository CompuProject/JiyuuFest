<?php
class JRequestList {
    private $SQL_HELPER;
    private $URL_PARAMS;
    private $urlHelper;
    private $types;
    private $fest;
    
    private $requesStop = false;
    private $yourUser;
    private $yourUserData;
    private $all;
    
    public function __construct($all=false) {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->urlHelper = new UrlHelper();
        $this->all=$all;
        $this->getUserData();
        $this->getAllTypes();
        $this->getFestData();
    }
    
    private function getAllTypes() {
        $query = "Select * from `JRequestType` ORDER BY `sequence` ASC;";
        $this->types = $this->SQL_HELPER->select($query);
    }
    
    private function getFestData() {
        global $_URL_PARAMS;
        $this->URL_PARAMS = $_URL_PARAMS['params'];
        if(isset($this->URL_PARAMS[0])) {
            $thisdate = date("Y-m-d h:i:s");
            if($this->yourUser->isAdmin()) {
                $query="SELECT * FROM `JRequestFest` where `id`='".$this->URL_PARAMS[0]."';";
            } else {
                $query="SELECT * FROM `JRequestFest` where `start`<='".$thisdate."' AND `stop`>='".$thisdate."' AND `id`='".$this->URL_PARAMS[0]."';";
            }
            $this->fest = $this->SQL_HELPER->select($query,1);
            if($this->fest==null) {
                $this->requesStop = true;
            }
        } else {
            $this->requesStop = true;
        }
    }
    
    protected function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function getFest() {
        $start = new DateTime($this->fest['start']);
        $stop = new DateTime($this->fest['stop']);
        $date = new DateTime($this->fest['date']);
        $out = "";
        $out .= '<div class="JFestsListElement">';
            $out .= '<div class="JFestsListHeder">';
                $out .= '<div class="JRequestAddIcon back">';
                    $url = $this->urlHelper->getThisParentPage();
                    $out .= '<a href="'.$url.'" title="К списку фестивалей">';
                    $out .= ' ';
                    $out .= '</a>';
                $out .= '</div>';
                $out .= '<div class="JRequestTypeTitle">';
                    $out .= $this->fest['name'];
                $out .= '</div>';
            $out .= '</div>';
            $out .= '<div class="JFestsListInfo '.$this->fest['id'].'">';
                $out .= '<div class="JFestsListInfoDate '.$this->fest['id'].'">';
                    $out .= '<span class="infoType">Начало приема заявок: </span>';
                    $out .= '<span class="infoData">'.$start->format('d M Y h:i').'</span>';
                    $out .= '<br />';
                    $out .= '<span class="infoType">Окончание приема заявок: </span>';
                    $out .= '<span class="infoData">'.$stop->format('d M Y h:i').'</span>';
                    $out .= '<br />';
                    $out .= '<span class="infoType">Дата проведения фестиваля: </span>';
                    $out .= '<span class="infoData">'.$date->format('d M Y').'</span>';
                    $out .= '<br />';
                $out .= '</div>';
                $out .= '<div class="JFestsListInfoTitle">';
                $out .= 'Информация о фестивале';
                $out .= '</div>';
                $out .= '<div class="JFestsListInfoDescription">';
                    $out .= $this->fest['description'];
                $out .= '</div>';
                //$out .= '<div class="JFestsListInfoTitle">';
                //$out .= 'Правила проведения фестиваля';
                //$out .= '</div>';
                //$out .= '<div class="JFestsListInfoRegulations">';
                //$out .= $this->fest['regulations'];
                //$out .= '</div>';
                $out .= '<div class="JFestsListInfoTitle">';
                $out .= 'Ваши заявки';
                $out .= '</div><br />';
//                $out .= '<div class="JFestsListMainInfo">';
//                $out .= 'ВНИМАНИЕ! Сайт находится в стадии разработки, поэтому '
//                        . 'некоторые функции недоступны.<br><br>На данный момент не работают:<br>'
//                        . '1) Прикрепление материалов (файлов) к заявкам<br>'
//                        . 'В ближайшее время эти функции будут вам доступны.<br>'
//                        . 'Администрация сайта приносит свои извинения за доставленные неудобства.';
//                $out .= '</div><br />';
                $yourUser = new UserData();
                if($yourUser->checkAuthorization()) {
                    $out .= $this->getTypesList();
                } else {
                    $out .= "<div class='JRequestError'>Только зарегистрированный пользователь может просматривать и подавать заявки.</div>";
                }
            $out .= '</div>';
        $out .= '</div>';
        return $out;
    }
    
    private function getTypesList() {
        $jRequestData = new JRequestData($this->fest['id'],$this->all);
        $out = "";
        if($this->yourUser->isAdmin()) {
            $out .= '<center>';
            $allRequest[0]=$this->fest['id'];
            $allRequest[1]='AllRequest';
            $JRequestAllURL = $this->urlHelper->chengeParams($allRequest);
            $out .= '<a href="'.$JRequestAllURL.'">';
            $out .= '<input class="JRequestFormButton small" type="button" value="Показать все заявки">';
            $out .= '</a>';
            $notAllRequest[0]=$this->fest['id'];
            $JRequestNotAllURL = $this->urlHelper->chengeParams($notAllRequest);
            $out .= '<a href="'.$JRequestNotAllURL.'">';
            $out .= '<input class="JRequestFormButton small" type="button" value="Показать свои заявки">';
            $out .= '</a>';
            $out .= '</center><br>';
        }
        foreach ($this->types as $type) {
            $params[0] = $this->fest['id'];
            $params[1] = $type['type'];
            $params[2] = 'add';
            $url = $this->urlHelper->chengeParams($params);
            $out .= '<div class="JRequestListElement">';
                $out .= '<div class="JRequestListHeder">';
                    $out .= '<div class="JRequestAddIcon">';
                        $out .= '<a href="'.$url.'">&#9997;</a>';
                    $out .= '</div>';
                    $out .= '<div class="JRequestTypeTitle">';
                        $out .= $type['name'];
                    $out .= '</div>';
                $out .= '</div>';
                $requestListHTML = $jRequestData->get($type['type']);
                if($requestListHTML!=null && $requestListHTML!="") {
                    $out .= '<div class="JRequestList">';
                    $out .= $requestListHTML;
                    $out .= '</div>';
                }
            $out .= '</div>';
        }
        return $out;
    }

    public function get() {
        $out = "";
        if($this->requesStop) {
            $out .= "Извините, вы не можете подать и просмотреть заявки.<br>";
            $out .= "Вы видите эту страницу потом что:<br>";
            $out .= "Выбранный вами фестиваль не существует<br>";
            $out .= "Прием заявок на этот фестиваль еще не начался<br>";
            $out .= "Прием заявок на этот фестиваль уже закончился<br>";
        } else {
            $params = array();
            $out .=  $this->getFest();
        }
        return $out;
    }
    
    public function getTypes() {
        return $this->types;
    }
}

?>