<?php
class JRequestAddUsers2 {
    private $form;
    private $insertValue;
    private $SQL_HELPER;
    
    private $fest;
    private $requestID;

    public function __construct($fest,$requestID) {
        $this->fest = $fest;
        $this->requestID = $requestID;
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->getInsertValueArray();
    }
    
    public function addUser() {
        if($this->checkValue()) {
            $query = "INSERT INTO `JRequestUsers` SET ";
            foreach ($this->insertValue as $key => $value ) {
                $query .= "`$key`='".$value."',";
            }
            $query = substr($query, 0, strlen($query)-1);
            $query .= ";";
            $this->SQL_HELPER->insert($query);
            echo "Пользователь ".$this->insertValue['user']." добавлен в заявку.<br>Ему необходимо подтвердить свое участие в заявке.<br>";
        } else {
            echo "Пользователь с таким ником не зарегистрирован в системе или уже добавлен к заявке.<br>"
            . "Только зарегистрированные на сайте пользователи могут принять участие в выступлении на фестивале.<br>";
        }
        
    }

    private function checkValue() {
        return ($this->getUsers() && $this->getJRequestUsers());
    }
    
    private function getUsers() {
        $query = "Select `login` from `Users` where `login`='".$this->insertValue['user']."';";
        $result = $this->SQL_HELPER->select($query,1);
        return $result!=null;
    }
    
    private function getJRequestUsers() {
        $query = "Select `user` from `JRequestUsers` where `user`='".$this->insertValue['user']."' AND `request`='".$this->requestID."';";
        $result = $this->SQL_HELPER->select($query,1);
        return $result==null;
    }

    private function getInsertValueArray() {
        $this->insertValue = array();
        $this->insertValue['request'] = $this->requestID;
        $this->insertValue['user'] = $this->getPostValue('login');
        $this->insertValue['characterName'] = $this->getPostValue('characterName');
        $this->insertValue['confirmed'] = '0';
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
}
