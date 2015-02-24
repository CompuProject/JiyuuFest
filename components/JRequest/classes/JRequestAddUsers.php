<?php
/**
 * Класс формы регистрации пользователей
 */
class JRequestAddUsers {
    private $inputHelper;
    private $localization;
    private $form;
    private $insertValue;
    private $message;
    private $SQL_HELPER;
    private $params;
    private $agreementsData;
    
    private $fest;
    private $requestID;
    
    /**
     * Конструктор
     * @global type $_SQL_HELPER
     * @global type $_URL_PARAMS
     */
    public function __construct($fest,$requestID) {
        $this->fest = $fest;
        $this->requestID = $requestID;
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        global $_URL_PARAMS;
        $this->params = $_URL_PARAMS['params'];
        $this->inputHelper = new InputHelper();
        $this->localization = new Localization("Users/RegistrationForm");
        $this->message = "";
        $this->clearInsertValueArray();
        $this->getUsersAgreementsData();
    }
    
    
    /**
     * Добавить строку к форме
     * @param type $input - input элемент формы
     * @param type $mandatory - обязателен к заполнению
     * @param type $text - текст
     * @param type $info - дополнительная информация
     * @return string - вернет код строки для таблицы формы
     */
    private function createFormRow($input,$mandatory,$text,$info=null) {
        $mandatoryText = "";
        if($mandatory) {
            $mandatoryText = '* ';
        }
        $out =  '<tr>';
        $out .=  '<td class="RegistrationFormTable_Text">';
        $out .=  '<div class="text">'.$mandatoryText.$text.'</div>';
        if($info != null && $info != "") {
            $out .=  '<div class="info">'.$info.'</div>';
        }
        $out .=  '</td>';
        $out .=  '<td class="RegistrationFormTable_Input">'.$input.'</td>';
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
    private function createLocalizationFormRow($input,$mandatory,$text,$info=null) {
        $text = $this->localization->getText($text);
        if($info != null && $info != "") {
            $info = $this->localization->getText($info);
        }
        return $this->createFormRow($input,$mandatory,$text,$info);
    }

    /**
     * Создание формы
     */
    private function createForm() {
        $urlHelper = new UrlHelper();
        $params[0] = $this->fest;
        $params[1] = $this->requestID;
        $params[2] = 'createAndAddUser';
        $url = $urlHelper->chengeParams($params);
        $this->form = '';
        $this->form .= '<form class="RegistrationForm" name="RegistrationForm" action="'.$urlHelper->getThisPage().'" 
            method="post" accept-charset="UTF-8" autocomplete="on">';
        $this->form .= '<center>';
        $this->form .= "<div class='message'>$this->message</div>";
        $this->form .= '<table class="RegistrationFormTable" >';
        // ferstName
        $namePatern = $this->localization->getText("namePatern");
        $ferstName = $this->inputHelper->paternTextBox("ferstName", "ferstName", "ferstName", 20, true, $namePatern, "[А-Яа-яЁёЙйЦцA-Za-z]{3,20}", $this->insertValue['ferstName']);
        $this->form .= $this->createLocalizationFormRow($ferstName, true, 'ferstName');
        // lastName
        $lastName = $this->inputHelper->paternTextBox("lastName", "lastName", "lastName", 20, true, $namePatern, "[А-Яа-яЁёЙйЦцA-Za-z]{3,20}", $this->insertValue['lastName']);
        $this->form .= $this->createLocalizationFormRow($lastName, true, 'lastName');
        // birthday
        $birthday = $this->inputHelper->paternTextBox("birthday", "birthday", "birthday", 20, true, "ГГГГ-ММ-ДД", "[0-9]{4}-[0-9]{2}-[0-9]{2}", $this->insertValue['birthday']);
        $this->form .= $this->createLocalizationFormRow($birthday, true, 'birthday','birthdayInfo');
        // sex
        $sexArray = array();
        $sexArray[0]['value'] = 1;
        $sexArray[0]['text'] = $this->localization->getText("male");
        $sexArray[1]['value'] = 0;
        $sexArray[1]['text'] = $this->localization->getText("female");
        $sex = $this->inputHelper->select('sex', 'sex', $sexArray, true, $this->insertValue['sex']==null?1:$this->insertValue['sex']);
        $this->form .= $this->createLocalizationFormRow($sex, true, 'sex');
        // city
        $cityPatern = $this->localization->getText("cityPatern");
        $city = $this->inputHelper->paternTextBox("city", "city", "city", 200, true, $cityPatern, "[А-ЯЁЦЙA-Z]{1}[а-яёцйa-z]{2,199}", $this->insertValue['city']);
        $this->form .= $this->createLocalizationFormRow($city, true, 'city');
        // login
        $loginAndPasswordPatern = "loginAndPasswordPatern";
        $login = $this->inputHelper->paternTextBox("login", "login", "login", 20, true, $this->localization->getText($loginAndPasswordPatern), "[A-Za-z0-9]{6,20}", $this->insertValue['login']);
        $this->form .= $this->createLocalizationFormRow($login, true, 'login',$loginAndPasswordPatern);
        // email
        $email = $this->inputHelper->paternTextBox("email", "email", "email", 200, true, "user@domen.zone", "^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$", $this->insertValue['email']);
        $this->form .= $this->createLocalizationFormRow($email, true, 'email');
        // phone
        $phone = $this->inputHelper->paternTextBox("phone", "phone", "phone", 20, true, "+7(XXX)XXX-XX-XX", "^((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?$", $this->insertValue['phone']);
        $this->form .= $this->createLocalizationFormRow($phone, true, 'phone');
        // captcha
        $captcha = $this->inputHelper->textBox("captcha", "captcha", "captcha", 20, true, null);
        $this->form .= $this->createFormRow($captcha, true, getCaptcha(120, 25));
        $this->form .= '</table>';
        $this->form .= $this->getUsersAgreements();
        $this->form .= '<div class="mandatoryText">'.$this->localization->getText('mandatoryText').'</div>';
        $this->form .= '<center>';
        $this->form .= '<input class="RegistrationFormButton" type="submit" name="RegistrationFormSubmit" value="'.$this->localization->getText("registrationFormButtonText").'">';
        $this->form .= '</form>';
    }
    
    /**
     * Проверка каптчи
     * @return type - вернет true если проверка удачная
     */
    private function checkCaptcha() {
        @session_start();
        return (
                isset($_SESSION['captcha']) && 
                strtoupper($_SESSION['captcha']) == strtoupper($_POST['captcha'])
        );
    }
    
    /**
     * Проверка значений
     * @param type $key - ключ для $_POST массива
     * @param type $preg - регулярное выражение
     * @return type
     */
    private function checkValue($key,$preg=null) {
        return (
                isset($_POST[$key]) && 
                $_POST[$key]!=null && 
                $_POST[$key]!="" &&
                ($preg==null || preg_match($preg, $_POST[$key]))
        );
    }
    
    /**
     * Проверка на то, были ли приняты все условия договора
     * @return boolean
     */
    private function checkAgreementsValue() {
        foreach ($this->agreementsData as $agreement) {
            if(!$this->checkValue('agreements_'.$agreement['id'])){
                return false;
            }
        }
        return true;
    }


    /**
     * Проверка павторного введения пароля
     * @return type
     */
    private function checkRepeatPassword() {
        return ($_POST['password'] == $_POST['repeatPassword']);
    }
    
    /**
     * Проверка всех значений
     * @return type
     */
    private function checkAllValue() {
//        echo "<br>";
//        echo $this->checkValue('ferstName',"/[^А-Я]{1}[а-я]{2,29}+$/u") ? "ferstName-YES":"ferstName-NO";
//        echo "<br>";
//        echo $this->checkValue('lastName',"/[^А-Я]{1}[а-я]{2,29}+$/u") ? "lastName-YES":"lastName-NO";
//        echo "<br>"; 
//        echo $this->checkValue('city',"/[^А-Я]{1}[а-я]{1,199}+$/u") ? "city-YES":"city-NO";
//        echo "<br>";
//        echo $this->checkValue('birthday',"/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/") ? "birthday-YES":"birthday-NO";
//        echo "<br>";
//        echo $this->checkValue('sex',"/^[0-1]$/") ? "sex-YES":"sex-NO";
//        echo "<br>";
//        echo $this->checkValue('login',"/^[A-Za-z0-9]{6,20}$/") ? "login-YES":"login-NO"; 
//        echo "<br>";
//        echo $this->checkValue('password',"/^[A-Za-z0-9]{6,20}$/") ? "password-YES":"password-NO";
//        echo "<br>";
//        echo $this->checkValue('repeatPassword',"/^[A-Za-z0-9]{6,20}$/") ? "repeatPassword-YES":"repeatPassword-NO";
//        echo "<br>";
//        echo $this->checkValue('email',"/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/") ? "email-YES":"email-NO";
//        echo "<br>";
//        echo $this->checkValue('phone',"/^((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?$/") ? "phone-YES":"phone-NO";
//        echo "<br>";
//        echo $this->checkValue('captcha',"/^[A-Za-z0-9]{1,20}$/") ? "captcha-YES":"captcha-NO";
//        echo "<br>";
//        echo $this->checkAgreementsValue() ? "Agreements-YES":"Agreements-NO";
//        echo "<br>";
        return (
                $this->checkValue('ferstName',"/[^А-Я]{1}[а-я]{2,29}+$/u") && 
                $this->checkValue('lastName',"/[^А-Я]{1}[а-я]{2,29}+$/u") && 
                $this->checkValue('city',"/[^А-Я]{1}[а-я]{1,199}+$/u") && 
                $this->checkValue('birthday',"/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/") &&
                $this->checkValue('sex',"/^[0-1]$/") && 
                $this->checkValue('login',"/^[A-Za-z0-9]{6,20}$/") && 
                $this->checkValue('email',"/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/") &&
                $this->checkValue('phone',"/^((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?$/") &&
                $this->checkValue('captcha',"/^[A-Za-z0-9]{1,20}$/") &&
                $this->checkAgreementsValue()
        );
    }
    
    /**
     * Првоерка $_POST значений
     * @param type $key
     * @return type
     */
    private function getPostValue($key,$mysqlRealEscape=true,$br=false) {        
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
    private function getMysqlText($text,$mysqlRealEscape=true,$br=false) {
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
     * Создание неша для активации
     * @param type $var1
     * @param type $var2
     * @param type $var3
     * @return type
     */
    private function getActivatedHash($var1,$var2,$var3) {
        $step1 = md5($var1.$var2);
        $step2 = md5($var2.$var3);
        $step3 = md5($var3.$var1);
        return md5($step1.$step2.$step3);
    }

    /**
     * Очистка значений формы
     */
    private function clearInsertValueArray() {
        $this->insertValue = array();
        $this->insertValue['ferstName'] = null;
        $this->insertValue['lastName'] = null;
        $this->insertValue['birthday'] = null;
        $this->insertValue['sex'] = null;
        $this->insertValue['city'] = null;
        $this->insertValue['login'] = null;
        $this->insertValue['password'] = null;
        $this->insertValue['email'] = null;
        $this->insertValue['phone'] = null;
        $this->insertValue['registered'] = null;
        $this->insertValue['lastVisit'] = null;
        $this->insertValue['group'] = null;
        $this->insertValue['activatedHash'] = null;
    }
    
    /**
     * Инициализация значений формы
     */
    private function getInsertValueArray() {
        $this->insertValue = array();
        $this->insertValue['ferstName'] = $this->getPostValue('ferstName');
        $this->insertValue['lastName'] = $this->getPostValue('lastName');
        $this->insertValue['birthday'] = $this->getPostValue('birthday');
        $this->insertValue['sex'] = $this->getPostValue('sex');
        $this->insertValue['city'] = $this->getPostValue('city');
        $this->insertValue['login'] = $this->getPostValue('login');
        $this->insertValue['password'] = $this->getPasswordCod();
        $this->insertValue['email'] = $this->getPostValue('email');
        $this->insertValue['phone'] = $this->getPostValue('phone');
        $datetime = date("Y-m-d h:i:s");
        $this->insertValue['registered'] = $datetime;
        $this->insertValue['lastVisit'] = $datetime;
        $this->insertValue['group'] = $this->getGroup();
        $this->insertValue['activatedHash'] = $this->getActivatedHash($this->getPostValue('email'), $this->getPostValue('login'), $datetime);
    }
    
    private function getPasswordCod() {
        $fcletters = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTYVWXYZ123456789'; 
        $fclen = 10;
        $password = '';
        for ($i = 0; $i < $fclen; $i++){
            $password .= $fcletters[rand(0, strlen($fcletters)-1) ];
        }
        return $password;
    }

    /**
     * Формирование Query запроса на вставку
     * @return string
     */
    private function getQuery() {
        $query = "INSERT INTO `Users` SET ";
        foreach ($this->insertValue as $key => $value ) {
            $query .= "`$key`='".$value."',";
        }
        $query = substr($query, 0, strlen($query)-1);
        $query .= ";";
        return $query;
    }
    
    /**
     * Получение дефолтной группы
     * @return type
     */
    private function getGroup() {
        $query = "Select `group` from `UsersGroups` where `siteDefault`='1' limit 0,1;";
        $result = $this->SQL_HELPER->select($query,1);
        return $result['group'];
    }
    
    /**
     * вставка 
     */
    private function insert() {
        if($this->checkCaptcha()) {
            if($this->checkLogin()) {
                if($this->checkAllValue()) {
                    if($this->SQL_HELPER->insert($this->getQuery())) {
                        if($this->sendUserActivateMail()) {
                            $this->clearInsertValueArray();
                            $this->message = $this->localization->getText("insertOK");
                        } else {
                            $query = "DELETE FROM `Users` WHERE `login` = '".$this->insertValue['login']."';";
                            $this->agreementsData = $this->SQL_HELPER->select($query);
                            $this->message = $this->localization->getText("mailError");
                        }
                    } else {
                        $this->message = $this->localization->getText("dbError");
                    }
                } else {
                    $this->message = $this->localization->getText("checkAllValueFalse");
                }
            } else {
                $this->message = $this->localization->getText("loginAlreadyUse");
            }
        } else {
            $this->message = $this->localization->getText("checkCaptchaFalse");
        }
    }
    
    /**
     * Активация пользователя
     */
    private function activateUser() {
        if($this->params!=null && count($this->params)==2) {
            $query = "SELECT `login` from `Users` WHERE `login`='".$this->params[0]."' AND `activatedHash`='".$this->params[1]."';";
            if($this->SQL_HELPER->select($query)!=null) {
                $query = "UPDATE `Users` SET `activated`='1' WHERE `login`='".$this->params[0]."' AND `activatedHash`='".$this->params[1]."';";
                if($this->SQL_HELPER->insert($query)) {
                    $this->message = $this->localization->getText("activateUserOK");
                } else {
                    $this->message = $this->localization->getText("activateUserDBError");
                }
            } else {
                $this->message = $this->localization->getText("activatedHashError");
            }
        }
    }
    
    /**
     * отправка сообщения для активации
     */
    private function sendUserActivateMail2() {
        $urlHelper = new UrlHelper();
        $headers = "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "From: Служба поддержки\r\n";
        $url = $urlHelper->getThisPage()."/".$this->insertValue['login']."/".$this->insertValue['activatedHash'];
        $message = $this->localization->getText("activateMailText")."\r\n";
        $message .= "<a href='$url'>".$this->localization->getText("activate")."</a>\r\n";
        $message .= "Ваш логин: ".$this->insertValue['login']."\r\n";
        $message .= "Дата регистрации: ".date("d.m.Y - h:i:s")."\r\n";
        # Отправляем
        return mail($this->insertValue['email'], 'Отзыв', $message, $headers );
    }
    
    private function sendUserActivateMail() {
        global $_SITECONFIG;
        $siteConf = $_SITECONFIG;
        $urlHelper = new UrlHelper();
        $url = $urlHelper->createThisPageWithHTTP()."/".$this->insertValue['login']."/".$this->insertValue['activatedHash'];
        
        $to  = $this->insertValue['email']; 
        $subject = "Подтверждение вашего аккаунта на сайте Yiuu"; 

        $message = '<html><head>';
        $message .= '<title>Подтверждение вашего аккаунта на сайте '.$siteConf->getHostName().'</title>';
        $message .= '</head><body>';                
        $message .= $this->localization->getText("activateMailText")."<br>";
        $message .= "<a href='$url'>".$this->localization->getText("activate")."</a><br>";
        $message .= "Ваш логин: ".$this->insertValue['login']."<br>";
        $message .= "Дата регистрации: ".date("d.m.Y - h:i:s")."<br>";
        $message .= '</body></html>'; 
        
        $headers = "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "From: Служба поддержки\r\n";
        return mail($to, $subject, $message, $headers); 
    }

    /**
     * Отобразить форму
     */
    public function show() {
        if($this->params!=null && count($this->params)==2) {
            $this->activateUser();
            echo "<center><div class='message'>$this->message</div></center>";
        } else {
            if(isset($_POST['RegistrationFormSubmit'])) {
                $this->getInsertValueArray();
                $this->insert();
            }
            $this->createForm();
            echo $this->form;
        }
        
        
    }
    
    private function checkLogin() {
        $query = "Select `login` from `Users` where `login`='".$this->insertValue['login']."';";
        $login = $this->SQL_HELPER->select($query);
        return count($login)==0;
    }

    /**
     * Поулчение информации по условиям
     */
    public function getUsersAgreementsData() {
        $query = "Select `id`,`name`,`text`,`dateOfChange` from `UsersAgreements` where `site`='1' order by `sequence` asc;";
        $this->agreementsData = $this->SQL_HELPER->select($query);
    }

    /**
     * Генерировать поля с условиями договора
     * @return string
     */
    public function getUsersAgreements() {
        $out = "";
        if(count($this->agreementsData)>0) {
            foreach ($this->agreementsData as $value) {
                $out .= "<div class='agreements'>";
                    $out .= "<div class='agreementsTitle'>";
                        $out .= $value['name'];
                    $out .= "</div>";
                    $out .= "<div class='agreementsText'>";
                        $out .= $value['text'];
                        $out .= "<div class='agreementsCheckBox'>";
                            $id = 'agreements_'.$value['id'];
                            $out .= $this->localization->getText('agreements')." ".
                                    $this->inputHelper->checkbox($id, $id, $id, true, $id);
                        $out .= "</div>";
                    $out .= "</div>";
                    $out .= "<div class='agreementsDateOfChange'>";
                        $date = new DateTime($value['dateOfChange']);
                        $out .= $date->format('d.m.Y');
                    $out .= "</div>";
                $out .= "</div>";
            }
        }
        return $out;
    }
}
?>