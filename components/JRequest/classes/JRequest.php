<?php
class JRequest {
    protected $SQL_HELPER;
    protected $URL_PARAMS;
    protected $urlHelper;
    protected $inputHelper;
    protected $localization;
    
    protected $festInfo = null;
    protected $jRequestType = null;
    protected $typeInfo = null;
    protected $yourUser;
    protected $yourUserData;
    
    protected $form;
    protected $insertValue;
    protected $usersList;
    
    protected $requestID;
    
    
    private $queryArray = array();


    protected $fileDir="./resources/JRequest/";
    
    protected $maxNumberOfParticipants = 1;
    protected $durationYes = true;
    protected $kosbendTitleYes = true;
    protected $characterNameYes = false;


    /**
     * Конструктор класса
     * @global type $_SQL_HELPER
     * @global type $_URL_PARAMS
     */
    public function __construct() {
        global $_SQL_HELPER;
        global $_URL_PARAMS;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->URL_PARAMS = $_URL_PARAMS['params'];
        // поулчение информации о фествиале
        $this->getFestData();
        $this->fetTypeDate();
        // Вспомогательное
        $this->fileDir = $this->fileDir.$this->festInfo['id']."/";
        $this->urlHelper = new UrlHelper();
        $this->inputHelper = new InputHelper();
        $this->localization = new Localization("JRequest");
        // получение данных о пользователе
        $this->getUserData();
        // генерация формы
        $this->generateForm();
    }
    
    protected function getFestData() {
        if(isset($this->URL_PARAMS[0]) && $this->URL_PARAMS[0]!=null && $this->URL_PARAMS[0]!="") {
            $query = "SELECT * FROM `JRequestFest` where `id`='".$this->URL_PARAMS[0]."';";
            $this->festInfo = $this->SQL_HELPER->select($query,1);
            if($this->festInfo==null) {
                exit();
            }
        }
    }
    
    protected function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    public function getForm() {
        if($this->yourUser->checkAuthorization()) {
            return $this->form;
        } else {
            return "<div class='JRequestError'>Только зарегистрированный пользователь может просматривать и подавать заявки.</div>";
        }
    }
    
    protected function fetTypeDate() {
        if($this->jRequestType!=null) {
            $query = "SELECT * FROM `JRequestType` where `type`='".$this->jRequestType."';";
            $this->typeInfo = $this->SQL_HELPER->select($query,1);
        }
    }

    protected function generateForm() {
        $message = "";
        if(isset($_POST['JRequestFormSubmit'])) {
            $this->getInsertValueArray();
            $message = $this->insert()."<br>";
        }
        //$this->fetTypeDate();
        $params = null;
        $params[0] = $this->festInfo['id'];
        $url = $this->urlHelper->chengeParams($params);
        $this->form = "";
        $this->form .= '<div class="JFestsListHeder">';
            $this->form .= '<div class="JRequestAddIcon back">';
                $this->form .= '<a href="'.$url.'" title="К списку заявок">';
                $this->form .= ' ';
                $this->form .= '</a>';
            $this->form .= '</div>';
            $this->form .= '<div class="JRequestTypeTitle">';
                $this->form .= $this->festInfo['name'];
                $this->form .= " - ";
                $this->form .= $this->typeInfo['name'];
            $this->form .= '</div>';
        $this->form .= '</div>';
        $this->form .= $message;
        
        $this->form .= '<form class="JRequestForm" name="JRequestForm" action="'.$this->urlHelper->getThisPage().'" 
            method="post" accept-charset="UTF-8" autocomplete="on">';
        $this->form .= '<center>';
        $this->form .= '<table class="JRequestFormTable" >';
        // contest
        $contestArray = array();
        $contestArray[0]['value'] = 1;
        $contestArray[0]['text'] = $this->localization->getText("contest");
        $contestArray[1]['value'] = 0;
        $contestArray[1]['text'] = $this->localization->getText("noContest");
        $contest = $this->inputHelper->select('contest', 'contest', $contestArray, true, $this->insertValue['contest']==null?1:$this->insertValue['contest']);
        $this->form .= $this->createLocalizationFormRow($contest, true, 'contestLable');
        // participant
        if($this->maxNumberOfParticipants > 1) {
            $participantsArray = array();
            for($i=1; $i <= $this->maxNumberOfParticipants; $i++) {
                $participantsArray[$i]['value'] = $i;
                $participantsArray[$i]['text'] = $i;
            }
            $numberOfParticipants = $this->inputHelper->select('numberOfParticipants', 'numberOfParticipants', $participantsArray, true, $this->insertValue['numberOfParticipants']);
            $this->form .= $this->createLocalizationFormRow($numberOfParticipants, true, 'numberOfParticipants');
        }
        // duration
        if($this->durationYes) {
            $durationPatern = $this->localization->getText("durationPatern");
            $duration = $this->inputHelper->paternTextBox("duration", "duration", "duration", 11, true, $durationPatern, "[0-9]{1,11}", $this->insertValue['duration']);
            $this->form .= $this->createLocalizationFormRow($duration, true, 'duration','durationInfo');
        }
        // kosbendTitle
        if($this->kosbendTitleYes) {
            $kosbendTitle = $this->inputHelper->textBox('kosbendTitle', 'kosbendTitle', 'kosbendTitle', 150, false, $this->insertValue['kosbendTitle']);
            $this->form .= $this->createLocalizationFormRow($kosbendTitle, false, 'kosbendTitle','kosbendTitleInfo');
        }
        // OthernessForm
        $this->generateOthernessForm();
        // characterName     
        if($this->characterNameYes) {   
            $characterName = $this->inputHelper->textBox('characterName', 'characterName', 'characterName', 200, true, $this->insertValue['characterName']);
            $this->form .= $this->createLocalizationFormRow($characterName, true, 'characterName','characterNameInfo');
        }
        // wish
        $wish = $this->inputHelper->textarea("wish", "wish", "wish", 600, false, $this->insertValue['wish']);
        $this->form .= $this->createLocalizationFormRow($wish, false, 'wish');
        
        $this->form .= '</table>';
        $this->generateRules();
        $this->form .= '<center>';
        $this->form .= '<input class="JRequestFormButton" type="submit" name="JRequestFormSubmit" value="'.$this->localization->getText("JRequestFormButtonText").'">';
        $this->form .= '</form>';
    }
    
    protected function generateOthernessForm() {
    }

    protected function generateRules() {
        $this->form .= '<div class="JRequestAgreementsTitle">Правила проведения фестиваля (Обязательно к прочтению)</div>';
        $this->form .= '<div class="JRequestAgreementsText">';
            $this->form .= $this->festInfo['regulations'];
            $this->form .= '<br><hr>';
            $this->form .= '<div class="JRequestAgreementsCheckBox">';
                $this->form .= 'Я ознакомился с правилами, понимаю их суть<br>и осознаю, что несу ответственность за не соблюдение этих правил.<br><br>';
                $this->form .= 'Подтвердить <input type="checkbox" class="agreementsCheckbox" name="agreementsFest" value="agreementsFest" id="agreementsFest" required="" autocomplete="off">';
            $this->form .= '</div>';
        $this->form .= '</div>';
        $this->form .= '<div class="JRequestAgreementsTitle">Правила подачи заявки (Обязательно к прочтению)</div>';
        $this->form .= '<div class="JRequestAgreementsText">';
            $this->form .= $this->typeInfo['description'];
            $this->form .= '<br><hr>';
            $this->form .= '<div class="JRequestAgreementsCheckBox">';
                $this->form .= 'Я ознакомился с правилами, понимаю их суть<br>и осознаю, что несу ответственность за не соблюдение этих правил.<br><br>';
                $this->form .= 'Подтвердить <input type="checkbox" class="agreementsCheckbox" name="agreementsRequest" value="agreementsRequest" id="agreementsRequest" required="" autocomplete="off">';
            $this->form .= '</div>';
        $this->form .= '</div>';
        
        // captcha
        $captcha = $this->inputHelper->textBox("captcha", "captcha", "captcha", 20, true, null);
        $this->form .= $this->createFormRow($captcha, true, getCaptcha(120, 25));
    }


    /**
     * Добавить строку к форме
     * @param type $input - input элемент формы
     * @param type $mandatory - обязателен к заполнению
     * @param type $text - текст
     * @param type $info - дополнительная информация
     * @return string - вернет код строки для таблицы формы
     */
    protected function createFormRow($input,$mandatory,$text,$info=null) {
        $mandatoryText = "";
        if($mandatory) {
            $mandatoryText = '* ';
        }
        $out =  '<tr>';
        $out .=  '<td class="JRequestFormTable_Text">';
        $out .=  '<div class="text">'.$mandatoryText.$text.'</div>';
        if($info != null && $info != "") {
            $out .=  '<div class="info">'.$info.'</div>';
        }
        $out .=  '</td>';
        $out .=  '<td class="JRequestFormTable_Input">'.$input.'</td>';
        $out .=  '</tr>';
        return $out;
    }
    
    /**
     * Аналогичен createFormRow() за исключением того, что данная функция 
     *      принмиает на вход не текст а переменные для локализации
     * @param type $input
     * @param type $mandatory
     * @param type $text
     * @param type $info
     * @return type
     */
    protected function createLocalizationFormRow($input,$mandatory,$text,$info=null) {
        $text = $this->localization->getText($text);
        if($info != null && $info != "") {
            $info = $this->localization->getText($info);
        }
        return $this->createFormRow($input,$mandatory,$text,$info);
    }
    
    /**
     * Проверка каптчи
     * @return type - вернет true если проверка удачная
     */
    protected function checkCaptcha() {
        @session_start();
        return (
                isset($_SESSION['captcha']) && 
                strtoupper($_SESSION['captcha']) == strtoupper($_POST['captcha'])
        );
    }
    
    protected function checkJRequestID() {
        $query = "Select `id` from `JRequest` where `id`='".$this->requestID."';";
        return $this->SQL_HELPER->select($query)==null;
    }
    
    protected function checkAllValue() {
        return false;
    }
    
    protected function checkMainValue() {
        return (
            $this->checkValue('contest') && 
            ($this->checkValue('duration') || !$this->durationYes)&&
            $this->checkValue('agreementsFest') &&
            $this->checkValue('agreementsRequest') && 
            $this->checkValue('captcha',"/^[A-Za-z0-9]{1,20}$/")
        );
    }

    /**
     * Проверка значений
     * @param type $key - ключ для $_POST массива
     * @param type $preg - регулярное выражение
     * @return type
     */
    protected function checkValue($key,$preg=null) {
        return (
                isset($_POST[$key]) && 
                $_POST[$key]!=null && 
                $_POST[$key]!="" &&
                ($preg==null || preg_match($preg, $_POST[$key]))
        );
    }
    
    /**
     * Првоерка $_POST значений
     * @param type $key
     * @return type
     */
    protected function getPostValue($key,$mysqlRealEscape=true,$br=false) {        
        return (
                isset($_POST[$key]) && 
                $_POST[$key]!=null && 
                $_POST[$key]!=""
        ) ? $this->getMysqlText($_POST[$key]) : null;
    }

    /**
     * Преобразование текста для Mysql
     * @global type $_DBSETTINGS
     * @param type $text
     * @param type $mysqlRealEscape
     * @param type $br
     * @return null
     */
    protected function getMysqlText($text,$mysqlRealEscape=true,$br=false) {
        global $_DBSETTINGS;
        $link = mysql_connect($_DBSETTINGS['host'], $_DBSETTINGS['user'], $_DBSETTINGS['password']) OR die(mysql_error());
        $text = nl2br($text);
        if($br) {
            $text = strip_tags($text, '<br>');
        } else {
            $text = strip_tags($text);
        }
        $text = htmlspecialchars_decode($text);
        if($mysqlRealEscape) {
            $text = mysql_real_escape_string($text);
        }
        if($text=="") {
            $text = null;
        }
        return $text;
    }

    /**
     * Очистка значений формы
     */
    protected function clearInsertValueArray() {
        $this->requestID = null;
        $this->insertValue = array();
        $this->insertValue['fest'] = null;
        $this->insertValue['type'] = null;
        $this->insertValue['applyingFor'] = null;
        $this->insertValue['created'] = null;
        $this->insertValue['changed'] = null;
        $this->insertValue['approved'] = null;
        $this->insertValue['preapproved'] = null;
        
        $this->insertValue['contest'] = null;
        $this->insertValue['numberOfParticipants'] = null;
        $this->insertValue['duration'] = null;
        $this->insertValue['kosbendTitle'] = null;
        $this->insertValue['characterName'] = null;
        $this->insertValue['wish'] = null;
    }
    
    protected function generateRequestID() {
        //$this->requestID = md5(md5($this->festInfo['id']).md5($this->jRequestType).md5($this->yourUserData['login']).md5(date("Y-m-d h:i:s")));
        //$this->requestID = $this->festInfo['id']."_".$this->jRequestType."_".md5(md5($this->yourUserData['login']).md5(date("Y-m-d h:i:s")));
        //$this->requestID = md5(md5($this->festInfo['id']).md5($this->jRequestType))."_".md5(md5($this->yourUserData['login']).md5(date("Y-m-d h:i:s")));
        //$this->requestID = md5(md5($this->yourUserData['login']).md5(date("Y-m-d h:i:s")));
        
        $codeletters = 'ABCDEFGKIJKLMNOPQRSTUVWXYZabcdefghijlkmnopqrstuvwxyz123456789'; 
        $codelen = 6;
        $this->requestID=$this->festInfo['code'].$this->typeInfo['code']."_";
        for ($i = 0; $i < $codelen; $i++){ 
            $this->requestID .= $codeletters[rand(0, strlen($codeletters)-1) ]; 
        }

    }


    /**
     * Инициализация значений формы
     */
    protected function getInsertValueArray() {
        $this->generateRequestID();
        $this->insertValue = array();
        $this->insertValue['fest'] = $this->festInfo['id'];
        $this->insertValue['type'] = $this->jRequestType;
        $this->insertValue['applyingFor'] = $this->yourUserData['login'];
        $thisdate = date("Y-m-d H:i:s");
        $this->insertValue['created'] = $thisdate;
        $this->insertValue['changed'] = $thisdate;
        $this->insertValue['approved'] = "0";
        $this->insertValue['preapproved'] = "0";
        
        $this->insertValue['contest'] = $this->getPostValue('contest');
        $this->insertValue['numberOfParticipants'] = $this->getPostValue('numberOfParticipants');
        $this->insertValue['duration'] = $this->getPostValue('duration');
        $this->insertValue['kosbendTitle'] = $this->getPostValue('kosbendTitle');
        $this->insertValue['characterName'] = $this->getPostValue('characterName');
        $this->insertValue['wish'] = $this->getPostValue('wish');
    }
    
    /**
     * Формирование Query запроса на вставку
     * @param type $db - база
     * @param type $array - набор данных
     * @return string
     */
    protected function getQuery($db,$array) {
        $query = "INSERT INTO `".$db."` SET ";
        foreach ($array as $key => $value ) {
            $query .= "`$key`='".$value."',";
        }
        $query = substr($query, 0, strlen($query)-1);
        $query .= ";";
        return $query;
    }
    
    
    protected function getUserList() {
        $query = "Select * from `Users`;";
        $this->usersList = $this->SQL_HELPER->select($query);
    }
    
    protected function insert() {
        if($this->checkCaptcha()) {
            if($this->checkJRequestID()) {
                if($this->checkAllValue()) {
                    $this->generateJRequestQueryArray();
                    foreach ($this->getQueryArray() as $query) {
                        $this->SQL_HELPER->insert($query);
                        //echo $query;
                        //echo '<br><br><br>';
                    }
                    $this->clearInsertValueArray();
                    return "<div class='JRequestInserOk'>Заявка успешно создана<br>"
                            . "Теперь вам необходимо прикрепить к заявке все необходимые файлы и<br>"
                            . "указать всех участников данной заявки.<br>"
                            . "Вы можете просматривать статус и редактировать заявки через список ваших заявок<br></div>";
                }
            }
        }
        return "<div class='JRequestInserError'>Заявка не была отправлена.<br>"
                . "Возможно вы не заполнели все обязательные поля или заполнели их неверно.<br>"
                . "Проверьте введенные данные и попробуйте снова.</div>";
    }
    
    protected function generateJRequestQueryArray() {
        $this->clearQueryArray();
    }
    
    protected function getJRequestFirstQuery() {
        $this->queryArray[0] = "INSERT INTO `JRequest` SET ";
        $this->queryArray[0] .= "`id`='".$this->requestID."', ";
        $this->queryArray[0] .= "`contest`='".$this->insertValue['contest']."', ";
        $this->queryArray[0] .= "`applyingFor`='".$this->insertValue['applyingFor']."', ";
        $this->queryArray[0] .= "`created`='".$this->insertValue['created']."', ";
        $this->queryArray[0] .= "`changed`='".$this->insertValue['changed']."', ";
        $this->queryArray[0] .= "`type`='".$this->insertValue['type']."', ";
        $this->queryArray[0] .= "`fest`='".$this->insertValue['fest']."', ";
        if(isset($this->insertValue['duration']) && 
                $this->insertValue['duration']!=null && 
                $this->insertValue['duration']!="") {
            $this->queryArray[0] .= "`duration`='".$this->insertValue['duration']."', ";
        }
        if(isset($this->insertValue['kosbendTitle']) && 
                $this->insertValue['kosbendTitle']!=null && 
                $this->insertValue['kosbendTitle']!="") {
            $this->queryArray[0] .= "`kosbendTitle`='".$this->insertValue['kosbendTitle']."', ";
        }
        if(isset($this->insertValue['wish']) && 
                $this->insertValue['wish']!=null && 
                $this->insertValue['wish']!="") {
            $this->queryArray[0] .= "`wish`='".$this->insertValue['wish']."', ";
        }
        if(isset($this->insertValue['numberOfParticipants']) && 
                $this->insertValue['numberOfParticipants']!=null && 
                $this->insertValue['numberOfParticipants']!="") {
            $this->queryArray[0] .= "`numberOfParticipants`='".$this->insertValue['numberOfParticipants']."', ";
        } else {
            $this->queryArray[0] .= "`numberOfParticipants`='1', ";
        }
        $this->queryArray[0] .= "`approved`='".$this->insertValue['approved']."', ";
        $this->queryArray[0] .= "`preapproved`='".$this->insertValue['preapproved']."';";
    }
    
    protected function getJRequestFirstUserQuery() {
        $this->queryArray[1] = "INSERT INTO `JRequestUsers` SET ";
        $this->queryArray[1] .= "`request`='".$this->requestID."', ";
        $this->queryArray[1] .= "`user`='".$this->insertValue['applyingFor']."', ";
        if(isset($this->insertValue['characterName']) && 
                $this->insertValue['characterName']!=null && 
                $this->insertValue['characterName']!="") {
            $this->queryArray[1] .= "`characterName`='".$this->insertValue['characterName']."', ";
        }
        $this->queryArray[1] .= "`confirmed`='1';";
    }
    
    protected function addInQueryArray($query) {
        $this->queryArray[count($this->queryArray)] = $query;
    }
    
    protected function clearQueryArray() {
        $this->queryArray = array();
        $this->getJRequestFirstQuery();
        $this->getJRequestFirstUserQuery();
    }
    
    protected function getQueryArray() {
        return $this->queryArray;
    }
}

?>