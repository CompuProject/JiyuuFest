<?php
/* 
 * 1) Добавлены все пользователи
 * 2) Все пользователи подтвердили участие
 * 3) все файлы залиты на сервер
 */

class JRequestCheck {
    private $requestID;
    private $fest;
    private $type;
    private $applyingFor;
    private $maxUsers;
    private $allUsers;
    private $confirmedUsers;
    private $requestFilesSettings;
    private $checkFiles;
    
    private $SQL_HELPER;
    private $urlHelper;
    private $localization;
    
    private $yourUser;
    private $yourUserData;


    public function __construct($requestID) {
        $this->requestID=$requestID;
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->urlHelper = new UrlHelper();
        $this->localization = new Localization("JRequest/requestCheck");
        $this->getUserData();
        $this->getRequestDate();
        
    }
    
    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function isYouCreator() {
        return $this->yourUserData['login'] == $this->applyingFor;
    }
    
    private function getRequestDate() {
        $query = "SELECT `fest`,`type`,`numberOfParticipants`,`applyingFor` FROM `JRequest` WHERE `id`='".$this->requestID."';";
        $data = $this->SQL_HELPER->select($query,1);
        $this->fest = $data['fest'];
        $this->type = $data['type'];
        $this->maxUsers = $data['numberOfParticipants'];
        $this->applyingFor = $data['applyingFor'];
        $this->countAllUsers();
        $this->countConfirmedUsers();
        $this->getFilesSettings();
        $this->getCheckFiles();
    }
    
    private function countAllUsers() {
        $query = "SELECT count(`user`) as count FROM `JRequestUsers` WHERE `request` = '".$this->requestID."';";
        $data = $this->SQL_HELPER->select($query,1);
        $this->allUsers = $data['count'];
    }
    private function countConfirmedUsers() {
        $query = "SELECT count(`user`) as count FROM `JRequestUsers` WHERE `request` = '".$this->requestID."' AND  `confirmed` =  '1';";
        $data = $this->SQL_HELPER->select($query,1);
        $this->confirmedUsers = $data['count'];
    }


    private function getFilesSettings() {
        /*
         * dependentColumn
         * key1 - Файл
         * value1 - Зависимости "или"
         * key2 - цифровой
         * value1 - Зависимости "и"
         * key3 - файл от которого зависит
         * value3 - текст зависимости
         */
        $defultUserFiles  = array(
            "photo",
            "original"
        );
        switch ($this->type) {
// ActionDefile *************************** ActionDefile ***********************
            case 'ActionDefile':
                $this->requestFilesSettings['table'] = "JRequestActionDefile";
                $this->requestFilesSettings['requestFile']['mandatory'] = array(
                    "audio",
                    "collage"
                );
                $this->requestFilesSettings['requestFile']['dependentColumn'] = null;
                $this->requestFilesSettings['requestFile']['dependentAction'] = null;
                $this->requestFilesSettings['userFile']['mandatory'] = $defultUserFiles;
                $this->requestFilesSettings['userFile']['dependentColumn'] = null;
                $this->requestFilesSettings['userFile']['dependentAction'] = null;
                break;
// AMV          *************************** AMV ********************************
            case 'AMV':
                $this->requestFilesSettings['table'] = "JRequestAMV";
                $this->requestFilesSettings['requestFile']['mandatory'] = array(
                    "amv"
                );
                $this->requestFilesSettings['requestFile']['dependentColumn'] = null;
                $this->requestFilesSettings['requestFile']['dependentAction'] = null;
                $this->requestFilesSettings['userFile']['mandatory'] = null;
                $this->requestFilesSettings['userFile']['dependentColumn'] = null;
                $this->requestFilesSettings['userFile']['dependentAction'] = null;
                break;
// Dance        *************************** Dance ******************************
            case 'Dance':
                $this->requestFilesSettings['table'] = "JRequestDance";
                $this->requestFilesSettings['requestFile']['mandatory'] = null;
                $this->requestFilesSettings['requestFile']['dependentColumn'] = array(
                    "audio" => array(
                        array("video" => "nomandatoryifinvidio")
                    )
                );
                $this->requestFilesSettings['requestFile']['dependentAction'] = array(
                    "demoVideo" => "nomandatoryforlistening"
                );
                $this->requestFilesSettings['userFile']['mandatory'] = null;
                $this->requestFilesSettings['userFile']['dependentColumn'] = null;
                $this->requestFilesSettings['userFile']['dependentAction'] = null;
                break;
// Defile       *************************** Defile *****************************
            case 'Defile':
                $this->requestFilesSettings['table'] = "JRequestDefile";
                $this->requestFilesSettings['requestFile']['mandatory'] = array(
                    "audio",
                    "collage"
                );
                $this->requestFilesSettings['requestFile']['dependentColumn'] = null;
                $this->requestFilesSettings['requestFile']['dependentAction'] = null;
                $this->requestFilesSettings['userFile']['mandatory'] = $defultUserFiles;
                $this->requestFilesSettings['userFile']['dependentColumn'] = null;
                $this->requestFilesSettings['userFile']['dependentAction'] = null;
                break;
// Image        *************************** Image ******************************
            case 'Image':
                $this->requestFilesSettings['table'] = "JRequestImage";
                $this->requestFilesSettings['requestFile']['mandatory'] = array(
                    "image"
                );
                $this->requestFilesSettings['requestFile']['dependentColumn'] = null;
                $this->requestFilesSettings['requestFile']['dependentAction'] = null;
                $this->requestFilesSettings['userFile']['mandatory'] = null;
                $this->requestFilesSettings['userFile']['dependentColumn'] = null;
                $this->requestFilesSettings['userFile']['dependentAction'] = null;
                break;
// Karaoke      *************************** Karaoke ****************************
            case 'Karaoke':
                $this->requestFilesSettings['table'] = "JRequestKaraoke";
                $this->requestFilesSettings['requestFile']['mandatory'] = null;
                $this->requestFilesSettings['requestFile']['dependentColumn'] = array(
                    "minus" => array(
                        array("video" => "nomandatoryifinvidio")
                    )
                );
                $this->requestFilesSettings['requestFile']['dependentAction'] = array(
                    "demo" => "nomandatoryforlistening"
                );
                $this->requestFilesSettings['userFile']['mandatory'] = null;
                $this->requestFilesSettings['userFile']['dependentColumn'] = null;
                $this->requestFilesSettings['userFile']['dependentAction'] = null;
                break;
// Photo        *************************** Photo ******************************
            case 'Photo':
                $this->requestFilesSettings['table'] = "JRequestPhoto";
                $this->requestFilesSettings['requestFile']['mandatory'] = array(
                    "photo1"
                );
                $this->requestFilesSettings['requestFile']['dependentColumn'] = null;
                $this->requestFilesSettings['requestFile']['dependentAction'] = null;
                $this->requestFilesSettings['userFile']['mandatory'] = null;
                $this->requestFilesSettings['userFile']['dependentColumn'] = null;
                $this->requestFilesSettings['userFile']['dependentAction'] = null;
                break;
// Scene        *************************** Scene ******************************
            case 'Scene':
                $this->requestFilesSettings['table'] = "JRequestScene";
                $this->requestFilesSettings['requestFile']['mandatory'] = array(
                    "scenario",
                    "collage"
                );
                $this->requestFilesSettings['requestFile']['dependentColumn'] = array(
                    "audio" => array(
                        array("video" => "nomandatoryifinvidio")
                    )
                );
                $this->requestFilesSettings['requestFile']['dependentAction'] = null;
                $this->requestFilesSettings['userFile']['mandatory'] = $defultUserFiles;
                $this->requestFilesSettings['userFile']['dependentColumn'] = null;
                $this->requestFilesSettings['userFile']['dependentAction'] = null;
                break;
// VideoCosplay *************************** VideoCosplay ***********************
            case 'VideoCosplay':
                $this->requestFilesSettings['table'] = "JRequestVideoCosplay";
                $this->requestFilesSettings['requestFile']['mandatory'] = array(
                    "video"
                );
                $this->requestFilesSettings['requestFile']['dependentColumn'] = null;
                $this->requestFilesSettings['requestFile']['dependentAction'] = null;
                $this->requestFilesSettings['userFile']['mandatory'] = null;
                $this->requestFilesSettings['userFile']['dependentColumn'] = null;
                $this->requestFilesSettings['userFile']['dependentAction'] = null;
                break;
        }
    }
    
    private function checkCountUsers() {
        return $this->maxUsers == $this->allUsers;
    }
    
    private function checkConfirmedUsers() {
        return $this->allUsers == $this->confirmedUsers;
    }
    
    private function getCheckFiles() {
        $this->getCheckRequestFiles();
        $this->getCheckUsersFiles();
    }
    
    private function getCheckRequestFiles() {
        $query = "SELECT * FROM `".$this->requestFilesSettings['table']."` WHERE `id`='".$this->requestID."';";
        $data = $this->SQL_HELPER->select($query,1);
        if($data != null) {
            $this->setCheckFiles($data,'requestFile');
        }
    }
    private function getCheckUsersFiles() {
        $query = "SELECT * FROM `JRequestUsers` WHERE `request`='".$this->requestID."';";
        $usersData = $this->SQL_HELPER->select($query);
        if($usersData != null) {
            foreach ($usersData as $data) {
                $this->setCheckFiles($data,'userFile',$data['user']);
            }
        }
    }


    private function setCheckFiles($data,$filesType,$key=null) {
        $mandatory = $this->requestFilesSettings[$filesType]['mandatory'];
        $dependentColumn = $this->requestFilesSettings[$filesType]['dependentColumn'];
        $dependentAction = $this->requestFilesSettings[$filesType]['dependentAction'];
        
        $checkMandatory = null;
        if($mandatory != null) {
            foreach ($mandatory as $file) {
                $checkMandatory[$file] = (isset($data[$file]) && $data[$file]!=null && $data[$file]!='') ;
            }
        }

        $checkDependentColumn = null;
        if($dependentColumn != null) {
            foreach ($dependentColumn as $file => $dependent) {
                $or_id=0;
                foreach ($dependent as $or_dependent) {
                    $and_id = 0;
                    foreach ($or_dependent as $and_dependent => $text) {
                        $checkDependentColumn[$file][$or_id][$and_id]['dependent'] = 
                                (
                                    (
                                        isset($data[$and_dependent]) && 
                                        $data[$and_dependent]!=null && 
                                        $data[$and_dependent]!='')||
                                    (
                                        isset($data[$file]) && 
                                        $data[$file]!=null && 
                                        $data[$file]!=''
                                    )
                                );
                        $checkDependentColumn[$file][$or_id][$and_id]['text'] = $text;
                        $and_id++;
                    }
                    $or_id++;
                }
            }
        }

        $checkDependentAction = null;
        if($dependentAction != null) {
            foreach ($dependentAction as $file => $text) {
                $checkDependentAction[$file]['dependent'] = (isset($data[$file]) && $data[$file]!=null && $data[$file]!='') ;
                $checkDependentAction[$file]['text'] = $text;
            }
        }
        if($key==null) {
            $this->checkFiles[$filesType]['mandatory'] = $checkMandatory;
            $this->checkFiles[$filesType]['dependentColumn'] = $checkDependentColumn;
            $this->checkFiles[$filesType]['dependentAction'] = $checkDependentAction;
        } else {
            $this->checkFiles[$filesType][$key]['mandatory'] = $checkMandatory;
            $this->checkFiles[$filesType][$key]['dependentColumn'] = $checkDependentColumn;
            $this->checkFiles[$filesType][$key]['dependentAction'] = $checkDependentAction;
        }
    }
    
    private function checkRequestFiles() {
        $filesType = 'requestFile';
        $checkMandatory = true;
        if($this->checkFiles[$filesType]['mandatory'] != null) {
            foreach ($this->checkFiles[$filesType]['mandatory'] as $value) {
                if(!$value) {
                    $checkMandatory = false;
                }
            }
        }
        
        $checkDependentColumn = true;
        if($this->checkFiles[$filesType]['dependentColumn'] != null) {
            foreach ($this->checkFiles[$filesType]['dependentColumn'] as $files) {
                $cOR = false;
                foreach ($files as $or) {
                    $cAND = true;
                    foreach ($or as $and) {
                        if(!$and['dependent']) {
                            $cAND = false;
                        }
                    }
                    if($cAND) {
                        $cOR = true;
                    }
                }
                if(!$cOR) {
                    $checkDependentColumn = false;
                }
            }
        }
        return $checkMandatory && $checkDependentColumn;
    }
    
    private function checkAllUserFiles() {
        $query = "SELECT `user` FROM `JRequestUsers` WHERE `request`='".$this->requestID."';";
        $users = $this->SQL_HELPER->select($query);
        $check = true;
        foreach ($users as $user) {
            if(!$this->checkUserFiles($user['user'])) {
                $check = false;
            }
        }
        return $check;
    }
    
    private function checkUserFiles($user) {
        $filesType = 'userFile';
        $checkMandatory = true;
        if($this->checkFiles[$filesType][$user]['mandatory'] != null) {
            foreach ($this->checkFiles[$filesType][$user]['mandatory'] as $value) {
                if(!$value) {
                    $checkMandatory = false;
                }
            }
        }
        
        $checkDependentColumn = true;
        if($this->checkFiles[$filesType][$user]['dependentColumn'] != null) {
            foreach ($this->checkFiles[$filesType][$user]['dependentColumn'] as $files) {
                $cOR = false;
                foreach ($files as $or) {
                    $cAND = true;
                    foreach ($or as $and) {
                        if(!$and['dependent']) {
                            $cAND = false;
                        }
                    }
                    if($cAND) {
                        $cOR = true;
                    }
                }
                if(!$cOR) {
                    $checkDependentColumn = false;
                }
            }
        }
        return $checkMandatory && $checkDependentColumn;
    }
    
    public function getCheckApprovedInfoHTML() {
        // Генерируем ссылку для возврата назад
        $params[0] = $this->fest;
        $backURL = $this->urlHelper->chengeParams($params);
        $out = "";
        $out .= '<div class="JFestsListHeder">';
        $out .= '<div class="JRequestAddIcon back"><a href="'.$backURL.'" title="К списку заявок"> </a></div>';
        $out .= '<div class="JRequestTypeTitle">Отправка заявки '.$this->requestID.' на одобрение</div>';
        $out .= '</div>';
//        $out .= $this->getRequestMandatoryFileTable();
        $checkCountUsers = $this->checkCountUsers();
        $checkConfirmedUsers = $this->checkConfirmedUsers();
        $checkRFiles = $this->checkRequestFiles();
        $checkUFiles = $this->checkAllUserFiles();
        if($checkCountUsers) {
            $out .= "Все пользователи были добавлены к заявке<br>"; 
        } else {
            $out .= "Не все пользователи были добавлены к заявке<br>"; 
        }
        if($checkConfirmedUsers) {
            $out .= "Все пользователи подтвердили свое участие<br>"; 
        } else {
            $out .= "Не все пользователи подтвердили свое участие<br>"; 
        }
        if($checkRFiles) {
            $out .= "Все общие файлы прикреплены к завяке<br>"; 
        } else {
            $out .= "Не все общие файлы прикреплены к завяке<br>"; 
        }
        if($checkUFiles) {
            $out .= "Все пользователи прикрепили файлы<br>"; 
        } else {
            $out .= "Не все пользователи прикрепили файлы<br>"; 
        }
        if($this->yourUser->isAdmin()) {
            if($this->getCheckStatus()) {
                $preapprovedParam[0]=$this->fest;
                $preapprovedParam[1]=$this->requestID;
                $preapprovedParam[2]='sendApproved';
                $preapprovedParam[3]='approved';
                $out .= '<br><br>Вы уверены, что хотите одобрить завку <b>'.$this->requestID.'</b>?<br>';
                $out .= '<a href="'.$this->urlHelper->chengeParams($preapprovedParam).'">';
                $out .= '<input class="JRequestFormButton small" type="button" value="Да, хочу">';
                $out .= '</a>';
                $out .= '<a href="'.$backURL.'">';
                $out .= '<input class="JRequestFormButton small" type="button" value="Нет, я передумал">';
                $out .= '</a>';
            } else {
                $out .= '<br>Не все условия были выполнены.<br>Заявка не может быть одобрена.<br>Она была снята с рассмотрения<br>';
                $this->unsetApproved();
                $this->unsetPreapproved();
            }
        } else {
            $out .= "<br><br>У вас нет прав на эту операцию.<br>Вы должны быть администратором.<br>";
        }
        return $out;
    }
    
    public function getCheckInfoHTML() {
        // Генерируем ссылку для возврата назад
        $params[0] = $this->fest;
        $backURL = $this->urlHelper->chengeParams($params);
        $out = "";
        $out .= '<div class="JFestsListHeder">';
        $out .= '<div class="JRequestAddIcon back"><a href="'.$backURL.'" title="К списку заявок"> </a></div>';
        $out .= '<div class="JRequestTypeTitle">Отправка заявки '.$this->requestID.' на рассмотрение</div>';
        $out .= '</div>';
//        $out .= $this->getRequestMandatoryFileTable();
        $checkCountUsers = $this->checkCountUsers();
        $checkConfirmedUsers = $this->checkConfirmedUsers();
        $checkRFiles = $this->checkRequestFiles();
        $checkUFiles = $this->checkAllUserFiles();
        if($checkCountUsers) {
            $out .= "Все пользователи были добавлены к заявке<br>"; 
        } else {
            $out .= "Не все пользователи были добавлены к заявке<br>"; 
        }
        if($checkConfirmedUsers) {
            $out .= "Все пользователи подтвердили свое участие<br>"; 
        } else {
            $out .= "Не все пользователи подтвердили свое участие<br>"; 
        }
        if($checkRFiles) {
            $out .= "Все общие файлы прикреплены к завяке<br>"; 
        } else {
            $out .= "Не все общие файлы прикреплены к завяке<br>"; 
        }
        if($checkUFiles) {
            $out .= "Все пользователи прикрепили файлы<br>"; 
        } else {
            $out .= "Не все пользователи прикрепили файлы<br>"; 
        }
        if($this->isYouCreator() || $this->yourUser->isAdmin()) {
            if($this->getCheckStatus()) {
                $preapprovedParam[0]=$this->fest;
                $preapprovedParam[1]=$this->requestID;
                $preapprovedParam[2]='sendCheckRequest';
                $preapprovedParam[3]='preapproved';
                $out .= '<br><br>Вы уверены, что хотите отправить завку <b>'.$this->requestID.'</b> на рассмотрение?<br>';
                $out .= '<a href="'.$this->urlHelper->chengeParams($preapprovedParam).'">';
                $out .= '<input class="JRequestFormButton small" type="button" value="Да, хочу">';
                $out .= '</a>';
                $out .= '<a href="'.$backURL.'">';
                $out .= '<input class="JRequestFormButton small" type="button" value="Нет, я передумал">';
                $out .= '</a>';
            }
        } else {
            $out .= "<br><br>У вас нет прав на эту операцию.<br>Отправить заявку на проверку может только ее создатель.<br>";
        }
        return $out;
    }
    
    public function getCheckStatus() {
        $checkCountUsers = $this->checkCountUsers();
        $checkConfirmedUsers = $this->checkConfirmedUsers();
        $checkRFiles = $this->checkRequestFiles();
        $checkUFiles = $this->checkAllUserFiles();
        return $checkCountUsers && $checkConfirmedUsers && $checkRFiles && $checkUFiles;
    }


    private function getRequestMandatoryFileTable() {
        $out = "";
        if($this->checkFiles['requestFile']['mandatory']!=null) {
            $out .= "<table class='JRequestFilesTable'>";
            $out .= "<tr>";
            $out .= "<th>Обязательные файлы</th>";
            $out .= "<th></th>";
            $out .= "</tr>";
            foreach ($this->checkFiles['requestFile']['mandatory'] as $file => $mandatory) {
                $out .= "<tr>";
                $out .= "<td>".$this->localization->getText($file)."</td>";
                if($mandatory) {
                    $out .= '<td class="tableCellConfirmed">&#10004;</th>';
                } else {
                    $out .= '<td class="tableCellNoConfirmed">&#10007;</th>';
                }
                $out .= "</tr>";
            }
            $out .= "</table>";
        } else {
            $out .= "Обязательных файлов нет";
        }
        return $out;
    }
    
    public function preapproved() {
        // Генерируем ссылку для возврата назад
        $params[0] = $this->fest;
        $backURL = $this->urlHelper->chengeParams($params);
        $out = "";
        $out .= '<div class="JFestsListHeder">';
        $out .= '<div class="JRequestAddIcon back"><a href="'.$backURL.'" title="К списку заявок"> </a></div>';
        $out .= '<div class="JRequestTypeTitle">Отправка заявки '.$this->requestID.' на рассмотрение</div>';
        $out .= '</div>';
        if($this->isYouCreator() || $this->yourUser->isAdmin()) {
            if($this->getCheckStatus()) {
                $out .= $this->setPreapproved();
            } else {
                $out .= $this->unsetPreapproved();
            }
        } else {
            $out .= "<br><br>У вас нет прав на эту операцию.<br>Отправить заявку на проверку может только ее создатель.<br>";
        }
        return $out;
    }
    
    public function delPreapproved() {
        // Генерируем ссылку для возврата назад
        $params[0] = $this->fest;
        $backURL = $this->urlHelper->chengeParams($params);
        $out = "";
        $out .= '<div class="JFestsListHeder">';
        $out .= '<div class="JRequestAddIcon back"><a href="'.$backURL.'" title="К списку заявок"> </a></div>';
        $out .= '<div class="JRequestTypeTitle">Снятие заявки '.$this->requestID.' с рассмотрения</div>';
        $out .= '</div>';
        if($this->yourUser->isAdmin()) {
            $out .= $this->unsetPreapproved();
        } else {
            $out .= "<br><br>У вас нет прав на эту операцию.<br>Отправить заявку на проверку может только ее создатель.<br>";
        }
        return $out;
    }
    
    private function setPreapproved() {
        $query = "UPDATE `JRequest` SET `preapproved`='1' WHERE `id`='".$this->requestID."';";
        $this->SQL_HELPER->insert($query);
        return '<br><br>Заявка отправлена на рассмотрение';
    }
    
    private function unsetPreapproved() {
        $query = "UPDATE `JRequest` SET `preapproved`='0' WHERE `id`='".$this->requestID."';";
        $this->SQL_HELPER->insert($query);
        return '<br><br>Заявка снята с рассмотрения';
    }
    
    /********************/
    
    public function approved() {
        // Генерируем ссылку для возврата назад
        $params[0] = $this->fest;
        $backURL = $this->urlHelper->chengeParams($params);
        $out = "";
        $out .= '<div class="JFestsListHeder">';
        $out .= '<div class="JRequestAddIcon back"><a href="'.$backURL.'" title="К списку заявок"> </a></div>';
        $out .= '<div class="JRequestTypeTitle">Одобрение заявки '.$this->requestID.'</div>';
        $out .= '</div>';
        if($this->yourUser->isAdmin()) {
            if($this->getCheckStatus()) {
                $out .= $this->setApproved();
            } else {
                $out .= $this->unsetApproved();
            }
        } else {
            $out .= "<br><br>У вас нет прав на эту операцию.<br>Вы должны быть администратором.<br>";
        }
        return $out;
    }
    
    public function delApproved() {
        // Генерируем ссылку для возврата назад
        $params[0] = $this->fest;
        $backURL = $this->urlHelper->chengeParams($params);
        $out = "";
        $out .= '<div class="JFestsListHeder">';
        $out .= '<div class="JRequestAddIcon back"><a href="'.$backURL.'" title="К списку заявок"> </a></div>';
        $out .= '<div class="JRequestTypeTitle">Снятие заявки '.$this->requestID.' с одобрения</div>';
        $out .= '</div>';
        if($this->yourUser->isAdmin()) {
            $out .= $this->unsetApproved();
        } else {
            $out .= "<br><br>У вас нет прав на эту операцию.<br>Вы должны быть администратором.<br>";
        }
        return $out;
    }
    
    private function setApproved() {
        $query = "UPDATE `JRequest` SET `approved`='1' WHERE `id`='".$this->requestID."';";
        $this->SQL_HELPER->insert($query);
        return '<br><br>Заявка одобрена';
    }
    
    private function unsetApproved() {
        $query = "UPDATE `JRequest` SET `approved`='0' WHERE `id`='".$this->requestID."';";
        $this->SQL_HELPER->insert($query);
        return '<br><br>Заявка снята с одобрения';
    }
}