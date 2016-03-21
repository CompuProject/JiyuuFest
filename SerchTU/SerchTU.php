<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SerchTU
 *
 * @author Maxim Zaitsev
 * @copyright © 2010-2016, CompuProjec
 * @created 07.02.2016 13:43:31
 */
class SerchTU {
    private $SQL_HELPER;
    private $allUsers = array();
    private $disableOrDeleteUsers = array();
    private $USER_FOR_BAN = array();
    private $USER_FOR_BAN_COUNTER = array();
    
    private $yourUser;
    private $checkAuthorization;
    private $yourUserData;
    private $isAdmin;
    
    function __construct() {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->getUserData();
        if($this->isAdmin) {
            $this->getAllUsers();
            $this->getDisableOrDeleteUsers();
            $this->incorrectBirthday();
            $this->equalBirthday();
            $this->equalLastName();
            $this->coincidenceInEmail();
            $this->coincidenceInVk();
            $this->coincidenceLoginInLogin();
            $this->coincidenceLoginInEmail();
            $this->incorrectSex();
        }
    }
    
    private function getUserData() {
        $this->yourUser = new UserData();
        $this->checkAuthorization = $this->yourUser->checkAuthorization();
        $this->isAdmin = $this->yourUser->isAdmin();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function getAlertStatus($errorKey) {
        switch ($errorKey) {
            case 'Недостоверный день рождения':
                return 1;
            case 'Совпадают дни рождения':
                return 0.4;
            case 'Совпадают фамилии':
                return 0.2;
            case 'Для одного и того же имени были указаны разные половые принадлежности':
                return 0.4;
            case 'Частичное совпадение email адреса':
                return 0.3;
            case 'Частичное совпадение vk адреса':
                return 0.6;
            case 'Частичное совпадение Логина с чужим Логином':
                return 0.3;
            case 'Частичное совпадение Логина с чужим Email адресом':
                return 0.3;
            default:
                return 0.1;
        }
        
    }
    
    private function getAllUsers() {
        $query = "SELECT * FROM  `Users`";
        $rezult = $this->SQL_HELPER->select($query);
        foreach ($rezult as $user) {
            $this->allUsers[$user['login']] = $user;
        }
    }
    
    private function getDisableOrDeleteUsers() {
        $query = "SELECT * FROM  `Users` WHERE `disable` = '1' OR `delete` = '1'";
        $rezult = $this->SQL_HELPER->select($query);
        foreach ($rezult as $user) {
            $this->disableOrDeleteUsers[$user['login']] = $user;
        }
    }
    
    private function setCounter($users, $errorKey, $description = "") {
        foreach ($users as $user) {
            if(!isset($this->USER_FOR_BAN_COUNTER[$user['login']])) {
                $this->USER_FOR_BAN_COUNTER[$user['login']] = array();
            }
            $counter = count($this->USER_FOR_BAN_COUNTER[$user['login']]);
            $this->USER_FOR_BAN_COUNTER[$user['login']][$counter]['foul'] = $errorKey;
            $this->USER_FOR_BAN_COUNTER[$user['login']][$counter]['description'] = $description;
        }
    }
    
    private function getEmailPart($email) {
        $str = strpos($email, "@"); 
        $emailCut = substr($email, 0, $str);
        $parts = array();
        $rowArray = explode(".", $emailCut);
        foreach ($rowArray as $r) {
            foreach (explode("_", $r) as $p) {
                foreach (explode("-", $p) as $e) {
                    $parts[] = $e;
                }
            }
        }
        return $parts;
    }
    
    private function getVkId($vk) {
        $str = strpos($vk, "vk.com/");
        if($str > 0) {
            $vk = substr($vk, $str + 7);
        }
        return str_replace('/', "", $vk);
    }
    
    private function getArrayToString($array) {
        if(is_array($array)) {
            $str = "";
            foreach ($array as $element) {
                $str .= "<div class='ArrayToStringElement'>".$element."</div>";
            }
            if(mb_strlen($str) > 0) {
                $str = mb_strcut($str, 0, mb_strlen($str) - 2);
            }
            return $str;
        } else {
            return $array;
        }
    }
    
    private function getUserArrayToStringArray($userArray,$columns = array('ferstName','lastName')) {
        $array = array();
        foreach ($userArray as $user) {
            $str = "";
            foreach ($columns as $value) {
                if(isset($user[$value])) {
                    $str .= $user[$value]." ";
                }
                
            }
            $array[] = "<span class='login'>".$user['login']."</span> <span class='fio'>".$str."</span>";
        }
        return $array;
    }
    
    private function getUserArrayToString($userArray,$columns = array('ferstName','lastName')) {
        return $this->getArrayToString($this->getUserArrayToStringArray($userArray,$columns));
    }
    
    private function incorrectBirthday() {
        $errorKey = 'Недостоверный день рождения';
        $this->USER_FOR_BAN[$errorKey]['val'] = array();
        $this->USER_FOR_BAN[$errorKey]['valtype'] = 'user';
        $query = "SELECT * 
            FROM  `Users` 
            WHERE  `birthday` >=  '2004-01-01 00:00:00'";
        $this->USER_FOR_BAN[$errorKey]['val'] = $this->SQL_HELPER->select($query);
        $this->setCounter($this->USER_FOR_BAN[$errorKey]['val'], $errorKey);
    }
    
    private function equalBirthday() {
        $errorKey = 'Совпадают дни рождения';
        $this->USER_FOR_BAN[$errorKey]['val'] = array();
        $this->USER_FOR_BAN[$errorKey]['valtype'] = 'users_array';
        $userForBirthday = array();
        foreach ($this->allUsers as $user) {
            $userForBirthday[$user['birthday']][] = $user;
        }
        foreach ($userForBirthday as $birthday) {
            if(count($birthday) > 1) {
                $this->USER_FOR_BAN[$errorKey]['val'][] = $birthday;
                $this->USER_FOR_BAN[$errorKey]['description'][] = "Пользователи с датой рождения <span class='main'>".$birthday[0]['birthday']."</span>.";
                $this->setCounter($birthday, $errorKey, $this->getUserArrayToString($birthday));
            }
        }
    }
    
    private function equalLastName() {
        $errorKey = 'Совпадают фамилии';
        $this->USER_FOR_BAN[$errorKey]['val'] = array();
        $this->USER_FOR_BAN[$errorKey]['valtype'] = 'users_array';
        $userForBirthday = array();
        foreach ($this->allUsers as $user) {
            $userForBirthday[$user['lastName']][] = $user;
        }
        foreach ($userForBirthday as $birthday) {
            if(count($birthday) > 1) {
                $this->USER_FOR_BAN[$errorKey]['val'][] = $birthday;
                $this->USER_FOR_BAN[$errorKey]['description'][] = "Пользователи с фамилией <span class='main'>".$birthday[0]['lastName']."</span>.";
                $this->setCounter($birthday, $errorKey, $this->getUserArrayToString($birthday));
            }
        }
    }
    
    private function incorrectSex() {
        $errorKey = 'Для одного и того же имени были указаны разные половые принадлежности';
        $this->USER_FOR_BAN[$errorKey]['val'] = array();
        $this->USER_FOR_BAN[$errorKey]['valtype'] = 'multy_sex_name';
        $sexForName = array();
        foreach ($this->allUsers as $user) {
            $sexForName[$user['ferstName']][$user['sex']] = $user['sex'];
        }
        foreach ($sexForName as $name => $sex) {
            if(count($sex) > 1) {
                $this->USER_FOR_BAN[$errorKey]['val'][] = $name;
                $multySexUsers = array();
                foreach ($this->allUsers as $user) {
                    if($user['ferstName'] == $name) {
                        $multySexUsers[] = $user;
                    }
                }
                $this->setCounter($multySexUsers, $errorKey, $this->getUserArrayToString($multySexUsers,array('ferstName','lastName','sex')));
            }
        }
    }
    
    private function coincidenceInEmail() {
        $errorKey = 'Частичное совпадение email адреса';
        $this->USER_FOR_BAN[$errorKey]['val'] = array();
        $this->USER_FOR_BAN[$errorKey]['valtype'] = 'users_array';
        foreach ($this->allUsers as $user) {
            $query = "SELECT `login` FROM `Users` WHERE ";
            $emailParts = $this->getEmailPart($user['email']);
            if(count($emailParts) > 0) {
                $where = "";
                foreach ($emailParts as $part) {
                    if(mb_strlen($part) > 4) {
                        $where .= "`email` LIKE '%".$part."%' OR ";
                    }
                }
                if(mb_strlen($where) > 4) {
                    $where = substr($where, 0, mb_strlen($where) - 4);
                    $query .= "(".$where.") AND `email` != '".$user['email']."';";
                    $rezult = $this->SQL_HELPER->select($query);
                    if(count($rezult) > 0) {
                        $userArr = array();
                        $userArr[] = $user;
                        foreach ($rezult as $userData) {
                            $userArr[] = $this->allUsers[$userData['login']];
                        }
                        $this->setCounter($userArr, $errorKey, $this->getUserArrayToString($userArr,array('ferstName','lastName','email')));
                        $this->USER_FOR_BAN[$errorKey]['val'][] = $userArr;
                        $this->USER_FOR_BAN[$errorKey]['description'][] = "Были найдены следующие email которые частично совпадают с частями email <span class='main'>".$user['email']."</span>.";
                    }
                }
            }
        }
    }
    
    private function coincidenceInVk() {
        $errorKey = 'Частичное совпадение vk адреса';
        $this->USER_FOR_BAN[$errorKey]['val'] = array();
        $this->USER_FOR_BAN[$errorKey]['valtype'] = 'users_array';
        $vkArray = array();
        foreach ($this->allUsers as $user) {
            $vk = $this->getVkId($user['vk']);
            if(!in_array($vk, $vkArray) && $vk != "") {
                $vkArray[] = $vk;
                $query = "SELECT `login` FROM `Users` WHERE `vk` LIKE '%".$vk."%' AND `vk` != '".$user['vk']."';";
                $rezult = $this->SQL_HELPER->select($query);
                if(count($rezult) > 0) {
                    $userArr = array();
                    $userArr[] = $user;
                    foreach ($rezult as $userData) {
                        $userArr[] = $this->allUsers[$userData['login']];
                    }
                    $this->setCounter($userArr, $errorKey, $this->getUserArrayToString($userArr,array('ferstName','lastName','vk')));
                    $this->USER_FOR_BAN[$errorKey]['val'][] = $userArr;
                    $this->USER_FOR_BAN[$errorKey]['description'][] = "Были найдены следующие VKid которые частично совпадают с <span class='main'>".$vk."</span>.";
                }
            }
        }
    }
    
    private function coincidenceLoginInLogin() {
        $errorKey = 'Частичное совпадение Логина с чужим Логином';
        $this->USER_FOR_BAN[$errorKey]['val'] = array();
        $this->USER_FOR_BAN[$errorKey]['valtype'] = 'users_array';
        foreach ($this->allUsers as $user) {
            $query = "SELECT `login` FROM `Users` WHERE `login` LIKE '%".$user['login']."%' AND `login` != '".$user['login']."';";
            $rezult = $this->SQL_HELPER->select($query);
            if(count($rezult) > 0) {
                $userArr = array();
                $userArr[] = $user;
                foreach ($rezult as $userData) {
                    $userArr[] = $this->allUsers[$userData['login']];
                }
                $this->setCounter($userArr, $errorKey, $this->getUserArrayToString($userArr));
                $this->USER_FOR_BAN[$errorKey]['val'][] = $userArr;
                $this->USER_FOR_BAN[$errorKey]['description'][] = "Логин <span class='main'>".$user['login']."</span> был найден в следующих Логинах.";
            }
        }
    }
    
    private function coincidenceLoginInEmail() {
        $errorKey = 'Частичное совпадение Логина с чужим Email адресом';
        $this->USER_FOR_BAN[$errorKey]['val'] = array();
        $this->USER_FOR_BAN[$errorKey]['valtype'] = 'users_array';
        foreach ($this->allUsers as $user) {
            $query = "SELECT `login` FROM `Users` WHERE `email` LIKE '%".$user['login']."%' AND `login` != '".$user['login']."';";
            $rezult = $this->SQL_HELPER->select($query);
            if(count($rezult) > 0) {
                $userArr = array();
//                $userArr[] = $user;
                foreach ($rezult as $userData) {
                    $userArr[] = $this->allUsers[$userData['login']];
                }
                $this->setCounter($userArr, $errorKey, $this->getUserArrayToString($userArr,array('ferstName','lastName','email')));
                $this->USER_FOR_BAN[$errorKey]['val'][] = $userArr;
                $this->USER_FOR_BAN[$errorKey]['description'][] = "Логин <span class='main'>".$user['login']."</span> был найден в следующих Email адресах.";
            }
        }
    }
    
    private function generateUsersList($users, $description = null) {
        $out = "";
        $out .= "<table cellspacing='0' class='simple-little-table'>";
        if($description != null) {
            $out .= "<caption>".$description."</caption>";
        }
        $out .= "<tr>";
        $out .= "<th class='login'>логин</th>";
        $out .= "<th class='nickname'>ник</th>";
        $out .= "<th class='email'>email</th>";
        $out .= "<th class='phone'>телефон</th>";
        $out .= "<th class='ferstName'>имя</th>";
        $out .= "<th class='lastName'>фамилия</th>";
        $out .= "<th class='birthday'>д.р.</th>";
        $out .= "<th class='sex'>пол</th>";
        $out .= "<th class='city'>город</th>";
        $out .= "<th class='registered'>зарегистрирован</th>";
        $out .= "<th class='vk'>vk</th>";
        $out .= "</tr>";
        foreach ($users as $user) {
            $classes = "";
            if($user['disable'] > 0) {
                $classes = "disableUser ";
            }
            if($user['delete'] > 0) {
                $classes = "deleteUser ";
            }
            if($classes != "") {
                $classes = "class='".$classes."' ";
            }
            $disableOrDeleteComments = $user['disableOrDeleteComments'];
            if($disableOrDeleteComments != null && $disableOrDeleteComments != "") {
                $title = "title='".$disableOrDeleteComments."' ";
            } else {
                $title = "";
            }
            
            $out .= "<tr ".$classes.$title.">";
            $out .= "<td>".$user['login']."</td>";
            $out .= "<td>".$user['nickname']."</td>";
            $out .= "<td>".$user['email']."</td>";
            $out .= "<td>".$user['phone']."</td>";
            $out .= "<td>".$user['ferstName']."</td>";
            $out .= "<td>".$user['lastName']."</td>";
            $out .= "<td>".$user['birthday']."</td>";
            if($user['sex']>0) {
                $out .= "<td>муж</td>";
            } else {
                $out .= "<td>жен</td>";
            }
            
            $out .= "<td>".$user['city']."</td>";
            $out .= "<td>".$user['registered']."</td>";
            $out .= "<td>".$user['vk']."</td>";
            $out .= "</tr>";
        }
        $out .= "</table>";
        return $out;
    }
    
    private function generateArrayUsersList($usersArray, $description) {
        $out = "";
        foreach ($usersArray as $key => $users) {
            if($description != null && isset($description[$key])) {
                $out .= $this->generateUsersList($users, $description[$key]);
            } else {
                $out .= $this->generateUsersList($users);
            }
        }
        return $out;
    }
    
    private function generateMultySexNameList($names) {
        $out = "";
        $out .= "<table cellspacing='0' class='simple-little-table'>";
        foreach ($names as $name) {
            $out .= "<tr><td>".$name."</td></tr>";
        }
        $out .= "</table>";
        return $out;
    }
    
    private function generateReport() {
        $out = "";
        foreach ($this->USER_FOR_BAN as $key => $value) {
            $id = ID_GENERATOR::generateID();
            $out .= "<div class='Block'>";
            $out .= "<div class='BlockName' onclick=\"showHideBlock('".$id."');\">".$key."</div>";
            $out .= "<div id='".$id."' class='BlockData'>";
            if(isset($this->USER_FOR_BAN[$key]['description'])) {
                $description = $this->USER_FOR_BAN[$key]['description'];
            } else {
                $description = NULL;
            }
            switch ($value['valtype']) {
                case 'user':
                    $out .= $this->generateUsersList($value['val'], $description);
                    break;
                case 'users_array':
                    $out .= $this->generateArrayUsersList($value['val'], $description);
                    break;
                case 'multy_sex_name':
                    $out .= $this->generateMultySexNameList($value['val']);
                    break;
            }
            $out .= "</div>";
            $out .= "</div>";
        }
        return $out;
    }
    
    private function generateUserFoulReport() {
        $out = "";
        foreach ($this->USER_FOR_BAN_COUNTER as $key => $fouls) {
            $id = ID_GENERATOR::generateID();
            $user = $this->allUsers[$key];
            $alertStatus = 0;
            $usedFoul = array();
            foreach ($fouls as $foul) {
                if(!in_array($foul['foul'], $usedFoul)) {
                    $usedFoul[] = $foul['foul'];
                    $alertStatus += $this->getAlertStatus($foul['foul']);
                }
            }
            $out .= "<div class='Block'>";
            $out .= "<div class='BlockName' onclick=\"showHideBlock('".$id."');\">Пользователь ".$key." (".$user['ferstName']." ".$user['lastName'].") | Оснований: ".count($fouls)." (".$alertStatus.")</div>";
            $out .= "<div id='".$id."' class='BlockData'>";
            $out .= "<table cellspacing='0' class='simple-little-table foul-table'>";
            $out .= "<tr>";
            $out .= "<th class='foul'>Основание</th>";
            $out .= "<th class='fouldescription'>Дополнительно</th>";
            $out .= "</tr>";
            foreach ($fouls as $foul) {
                $out .= "<tr>";
                $out .= "<td class='foul'>";
                $out .= $foul['foul']." (".$this->getAlertStatus($foul['foul']).")";
                $out .= "</td>";
                $out .= "<td class='fouldescription'>";
                $out .= $foul['description'];
                $out .= "</td>";
                $out .= "</tr>";
            }
            $out .= "</table>";
            $out .= "</div>";
            $out .= "</div>";
        }
        return $out;
        
    }
    
    private function generateDisableOrDeleteUsers() {
        $out = "";
        $out .= "<table cellspacing='0' class='simple-little-table'>";
        $out .= "<tr>";
        $out .= "<th class='number'>№</th>";
        $out .= "<th class='login'>логин</th>";
        $out .= "<th class='nickname'>ник</th>";
        $out .= "<th class='email'>email</th>";
        $out .= "<th class='phone'>телефон</th>";
        $out .= "<th class='ferstName'>имя</th>";
        $out .= "<th class='lastName'>фамилия</th>";
        $out .= "<th class='birthday'>д.р.</th>";
        $out .= "<th class='sex'>пол</th>";
        $out .= "<th class='city'>город</th>";
        $out .= "<th class='registered'>зарегистрирвоан</th>";
        $out .= "<th class='vk'>vk</th>";
        $out .= "<th class='disable'>состояние</th>";
//        $out .= "<th class='delete'>delete</th>";
        $out .= "<th class='disableOrDeleteComments'>комментарий</th>";
        $out .= "</tr>";
        $i = 1;
        foreach ($this->disableOrDeleteUsers as $user) {       
            $out .= "<tr>";
            $out .= "<td>".$i++."</td>";
            $out .= "<td>".$user['login']."</td>";
            $out .= "<td>".$user['nickname']."</td>";
            $out .= "<td>".$user['email']."</td>";
            $out .= "<td>".$user['phone']."</td>";
            $out .= "<td>".$user['ferstName']."</td>";
            $out .= "<td>".$user['lastName']."</td>";
            $out .= "<td>".$user['birthday']."</td>";
            if($user['sex']>0) {
                $out .= "<td>муж</td>";
            } else {
                $out .= "<td>жен</td>";
            }
            $out .= "<td>".$user['city']."</td>";
            $out .= "<td>".$user['registered']."</td>";
            $out .= "<td>".$user['vk']."</td>";

            if($user['delete']>0) {
                $out .= "<td>Удален</td>";
            } else {
                if($user['disable']>0) {
                    $out .= "<td>Бан</td>";
                } else {
                    $out .= "<td>Активен</td>";
                }
            }
            $out .= "<td>".$user['disableOrDeleteComments']."</td>";
            $out .= "</tr>";
        }
        $out .= "</table>";
        return $out;
        
    }
    
    public function getAllReport() {
        $out = "";
        
        if($this->isAdmin) {
            $id1 = ID_GENERATOR::generateID();
            $id2 = ID_GENERATOR::generateID();
            $id3 = ID_GENERATOR::generateID();

            $out .= "<div class='Block MainBlock'>";
            $out .= "<div class='BlockName' onclick=\"showHideBlock('".$id1."');\">Удаленные и заблокированные пользователи</div>";
            $out .= "<div id='".$id1."' class='BlockData'>";
            $out .= $this->generateDisableOrDeleteUsers();
            $out .= "</div>";
            $out .= "</div>";

            $out .= "<div class='Block MainBlock'>";
            $out .= "<div class='BlockName' onclick=\"showHideBlock('".$id2."');\">Фильтр по оснвоаниям</div>";
            $out .= "<div id='".$id2."' class='BlockData'>";
            $out .= $this->generateReport();
            $out .= "</div>";
            $out .= "</div>";

            $out .= "<div class='Block MainBlock'>";
            $out .= "<div class='BlockName' onclick=\"showHideBlock('".$id3."');\">Фильтр по пользователям</div>";
            $out .= "<div id='".$id3."' class='BlockData'>";
            $out .= $this->generateUserFoulReport();
            $out .= "</div>";
            $out .= "</div>";
        } else {
            $out = "Вы не были авторизованы или не являетесь администратором.";
        }
        echo $out;
    }
}
