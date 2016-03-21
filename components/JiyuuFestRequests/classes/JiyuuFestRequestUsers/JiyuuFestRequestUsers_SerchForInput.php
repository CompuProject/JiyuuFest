<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JiyuuFestRequestUsers_SerchForInput
 *
 * @author Maxim Zaytsev
 * @copyright © 2010-2016, CompuProjec
 * @created 15.12.2015 9:27:44
 */
class JiyuuFestRequestUsers_SerchForInput {
    private $SQL_HELPER;
    private $userData;
    private $userSerch;
    private $html;


    public function __construct() {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->getUserSerch();
        if($this->userSerch != '') {
            $this->getUserSerchData();
            $this->generationHtml();
        }
    }

    private function getUserSerch() {
        $this->userSerch = stripslashes(htmlspecialchars(addslashes($_POST['SearchUser'])));
    }
    
    private function getUserSerchData() {
        $query = "SELECT * FROM `Users` WHERE "
            . "`login` LIKE '%".$this->userSerch."%' OR "
            . "`nickname` LIKE '%".$this->userSerch."%' OR "
            . "`ferstName` LIKE '%".$this->userSerch."%' OR "
            . "`lastName` LIKE '%".$this->userSerch."%' OR "
            . "`email` LIKE '".$this->userSerch."'  OR "
            . "`phone` LIKE '".$this->getPhone($this->userSerch)."';";
        $this->userData = $this->SQL_HELPER->select($query);
    }

    private function getPhone($phone) {
        $s = array("(",")","-","+"," ");
        $phone = str_replace("+7", "8", $phone);
        $phone = str_replace($s, "", $phone);
        return $phone;
    }
    
    private function generationHtml() {
        $this->html = "";
        if(isset($this->userData) && $this->userData != null) {
            $counter = 0;
            foreach ($this->userData as $user) {
                if($counter++ < 10) {
                    $this->html .= "<div class='searchUserResultElement' userId='".$user['login']."'>".$user['nickname']."</div>";
                } else {
                    break;
                }
            }
            $this->html .= "<div class='clear' />";
            $this->html .= $this->getJS();
        } else {
            $this->html .= "Нет Результатов";
        }
    }
    
    private function getJS() {
        $out = '<script type="text/javascript">';
        $out .= '$(function() {';
        $out .= '        $(".searchUserResultElement").click(function() {';
        $out .= '                var userId = $(this).attr("userId");';
        $out .= '                $("#AddUserInput").val(userId);';
        $out .= '                $("#AddUserRezult").html("");';
        $out .= '        });';
        $out .= '});';
        $out .= '</script>';
        return $out;
    }
    
    public function getHtml() {
        return $this->html;
    }
    
    public function get() {
        echo $this->getHtml();
    }
}
