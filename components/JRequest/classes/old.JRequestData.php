<?php

class JRequestData {
    private $SQL_HELPER;
    private $urlHelper;
    private $inputHelper;
    private $localization;
    private $yourUser;
    private $yourUserData;
    private $yourUserConfirmedDate;
    
    private $fest;
    private $data;
    
    private $JRequestDate = null;
    
    private $typeTable = array();
    private $userTableSettings = array();
    
    public function __construct($fest,$all=false) {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->urlHelper = new UrlHelper();
        $this->inputHelper = new InputHelper();
        $this->localization = new Localization("JRequest");
        $this->fest = $fest;
        $this->all = $all;
        
        $this->typeTable['ActionDefile']="JRequestActionDefile";
        $this->typeTable['AMV']="JRequestAMV";
        $this->typeTable['Dance']="JRequestDance";
        $this->typeTable['Defile']="JRequestDefile";
        $this->typeTable['Image']="JRequestImage";
        $this->typeTable['Karaoke']="JRequestKaraoke";
        $this->typeTable['Photo']="JRequestPhoto";
        $this->typeTable['Scene']="JRequestScene";
        $this->typeTable['VideoCosplay']="JRequestVideoCosplay";
        $this->getUserTableSettings();
        $this->getUserData();
        $this->getConfirmedDate();
        $this->getAllTypes();
        
        if($this->yourUser->isAdmin() && $this->all) {
            $this->getJRequestAllIdForList();
        } else {
            $this->getJRequestIdForList();
        }
    }

    protected function getUserTableSettings () {
        
        $this->userTableSettings['ActionDefile']['characterName']=true;
        $this->userTableSettings['ActionDefile']['photo']=true;
        $this->userTableSettings['ActionDefile']['original']=true;
        
        $this->userTableSettings['AMV']['characterName']=false;
        $this->userTableSettings['AMV']['photo']=false;
        $this->userTableSettings['AMV']['original']=false;
        
        $this->userTableSettings['Dance']['characterName']=false;
        $this->userTableSettings['Dance']['photo']=false;
        $this->userTableSettings['Dance']['original']=false;
        
        $this->userTableSettings['Defile']['characterName']=true;
        $this->userTableSettings['Defile']['photo']=true;
        $this->userTableSettings['Defile']['original']=true;
        
        $this->userTableSettings['Image']['characterName']=false;
        $this->userTableSettings['Image']['photo']=false;
        $this->userTableSettings['Image']['original']=false;
        
        $this->userTableSettings['Karaoke']['characterName']=false;
        $this->userTableSettings['Karaoke']['photo']=false;
        $this->userTableSettings['Karaoke']['original']=false;
        
        $this->userTableSettings['Photo']['characterName']=false;
        $this->userTableSettings['Photo']['photo']=false;
        $this->userTableSettings['Photo']['original']=false;
        
        $this->userTableSettings['Scene']['characterName']=true;
        $this->userTableSettings['Scene']['photo']=true;
        $this->userTableSettings['Scene']['original']=true;
        
        $this->userTableSettings['VideoCosplay']['characterName']=false;
        $this->userTableSettings['VideoCosplay']['photo']=false;
        $this->userTableSettings['VideoCosplay']['original']=false;
        
    }

    protected function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function getAllTypes() {
        $query = "Select `type` from `JRequestType` ORDER BY `sequence` ASC;";
        $this->types = $this->SQL_HELPER->select($query);
    }
    
    protected function getJRequestIdForList() {
        $this->data = null;
        foreach ($this->types as $type) {
            $query="SELECT JR.`id`
                FROM `JRequest` as JR left join `JRequestUsers` 
                as JRU on JR.`id`=JRU.`request`
                where 
                JRU.`user`='".$this->yourUserData['login']."' AND
                JR.`fest`='".$this->fest."' AND
                JR.`type`='".$type['type']."';";
            //echo $query;
            $idData = $this->SQL_HELPER->select($query);
            $i = 0;
            if($idData!=null) {
                foreach ($idData as $id) {
                    $this->data[$type['type']][$i++] = $id['id'];
                }
            } else {
                $this->data[$type['type']]=null;
            }
        }
    }
    
    protected function getJRequestAllIdForList() {
        $this->data = null;
        foreach ($this->types as $type) {
            $query="SELECT `id` FROM  `JRequest`
                WHERE `fest`='".$this->fest."' AND
                `type`='".$type['type']."' ORDER BY  `created` DESC;";
            //echo $query;
            $idData = $this->SQL_HELPER->select($query);
            $i = 0;
            if($idData!=null) {
                foreach ($idData as $id) {
                    $this->data[$type['type']][$i++] = $id['id'];
                }
            } else {
                $this->data[$type['type']]=null;
            }
        }
    }

    private function getUI_Request($id) {
        $out = "";
        // duration
        if(isset($this->JRequestDate['Request'][$id]['duration']) && 
                $this->JRequestDate['Request'][$id]['duration']!=null && 
                $this->JRequestDate['Request'][$id]['duration']!="" && 
                $this->JRequestDate['Request'][$id]['duration']>0) {
            $out .= $this->getLable('Время выступления: ',$this->JRequestDate['Request'][$id]['duration']."c.");
        }
        // kosbendTitle
        if(isset($this->JRequestDate['Request'][$id]['kosbendTitle']) && 
                $this->JRequestDate['Request'][$id]['kosbendTitle']!=null && 
                $this->JRequestDate['Request'][$id]['kosbendTitle']!="") {
            $out .= $this->getLable('Название косбенда: ',$this->JRequestDate['Request'][$id]['kosbendTitle']);
        }
        return $out;
    }
    
    private function getUI_Whish($id) {
        $out = "";
        // numberOfParticipants
        if(isset($this->JRequestDate['Request'][$id]['wish']) && 
                $this->JRequestDate['Request'][$id]['wish']!=null && 
                $this->JRequestDate['Request'][$id]['wish']!="") {
            $out .= $this->getLable('Пожелания и комментарии:<br>',$this->JRequestDate['Request'][$id]['wish']);
        }
        return $out;
    }


    private function getUI_BottomInfo($id) {
        $out = "";
        $params[0] = $this->JRequestDate['Request'][$id]['applyingFor'];
        $url = $this->urlHelper->pageUrl('accounts', $params);
        $applyingFor = "";
        $applyingFor .= '<a href="'.$url.'" target="_blanck">';
        $applyingFor .= $this->JRequestDate['Request'][$id]['applyingFor'];
        $applyingFor .= '</a>';
        $created = new DateTime($this->JRequestDate['Request'][$id]['created']);
        $changed = new DateTime($this->JRequestDate['Request'][$id]['changed']);
        if($this->JRequestDate['Request'][$id]['preapproved']>0) {
            if($this->JRequestDate['Request'][$id]['approved']>0) {
                $approved = '<span class="approved">Заявка прошла</span>';
            } else {
                $approved = '<span class="noapproved">В очереди на рассмотрение</span>';
            }
        } else {
            $approved = '<span class="nopreapproved">Оформляется</span>';
        }
        if($this->JRequestDate['Request'][$id]['contest']>0) {
            $contest = '<span class="contest">На конкурс</span>';
        } else {
            $contest = '<span class="nocontest">Вне конкурса</span>';
        }
        
        $out .= '<div class="requestBottomLableBlock">';
            $out .= '<div class="requestBottomLableBlockRight">';
                $out .= "Тип заявки: ";
                $out .= $contest;
                $out .= '<br>';
                $out .= "Статус заявки: ";
                $out .= $approved;
                $out .= '<br>';
                $out .= "Заявку сформировал ";
                $out .= $applyingFor;
            $out .= '</div>';
            $out .= '<div class="requestBottomLableBlockLeft">';
                $out .= "Заявка создана: ";
                $out .= $created->format('d M Y H:i');
                $out .= '<br>';
                $out .= "Заявка отредактирована: ";
                $out .= $changed->format('d M Y H:i');
                $out .= '<br>';
                $out .= $this->getConfirmedButton($id);
                $out .= $this->getPreapprovedButton($id);
            $out .= '</div>';
            $out .= '<div class="requestBottomLableBlockBottom">';
            $out .= '</div>';
        $out .= '</div>';
        return $out;
    }

    private function getLable($text,$value) {
        $out = "";
        $out .= '<div class="requestLableBlock">';
            $out .= '<span class="requestLable">';
                $out .= $text;
            $out .= '</span>';
            $out .= '<span class="requestLableValue">';
                $out .= $value;
            $out .= '</span>';
        $out .= '</div>';
        return $out;
    }

    private function getJRequestDate($id) {
        $query="SELECT * FROM `JRequest` where `id`='".$id."';";
        $this->JRequestDate['Request'][$id] = $this->SQL_HELPER->select($query,1);
    }
    
    private function getConfirmedDate() {
        $query="SELECT `request`,`confirmed` FROM `JRequestUsers` where `user`='".$this->yourUserData['login']."'";
        $result = $this->SQL_HELPER->select($query);
        if($result!=null) {
            foreach ($result as $value) {
                $this->yourUserConfirmedDate[$value['request']] = $value['confirmed'] > 0;
            }
        }
    }
    
    private function getJRequestUsersDate($id) {
        $query="SELECT * FROM `JRequestUsers` where `request`='".$id."'";
        $this->JRequestDate['Users'][$id] = $this->SQL_HELPER->select($query);
    }
    
    private function getJRequestAdditionallyDate($type,$id) {
        $query="SELECT * FROM `".$this->typeTable[$type]."` where `id`='".$id."';";
        $this->JRequestDate[$type][$id] = $this->SQL_HELPER->select($query,1);
    }
    
    public function get($type) {
        $out = "";
        if(isset($this->data[$type]) && $this->data[$type]!=null && count($this->data[$type])>0) {
            foreach ($this->data[$type] as $id) {
                $this->getJRequestDate($id);
                $this->getJRequestUsersDate($id);
                $this->getJRequestAdditionallyDate($type,$id);
            }
            
            foreach ($this->data[$type] as $id) {
                
                if($this->isYouInRequest($id)) {
                    $className = "yourUsers";
                } else {
                    $className = "allUsers";
                }
                
                $out .= '<div class="requestElement '.$className.'">';
                    $out .= '<div class="requestId">';
                        $out .= $id;
                        $out .= '<div class="requestIdButtonPanel">';
                        $out .= $this->getJRequestDeleteButton($id);
                        $out .= $this->getJRequestShowButton($id);
                        $out .= '</div>';
                    $out .= '</div>';
                    $out .= '<div class="requestMainBlock" id="'.$id.'_block" style="display: none;">';
                        $out .= '<div class="requestInfoBlock">';
                            $out .= '<div class="requestInfo">';
                                $out .= '<div class="requestTitle">';
                                    $out .= 'Информация';
                                $out .= '</div>';
                                $out .= $this->getUI_Request($id);
                                $out .= $this->getJRequestAdditionallyUI($type,$id);
                                $out .= $this->getUI_Whish($id);
                            $out .= '</div>';
                        $out .= '</div>';
                        
                        $out .= '<div class="requestUsersListBlock">';
                            $out .= '<div class="requestUsersList">';
                                $out .= '<div class="requestTitle">';
                                    $out .= 'Участники ['.count($this->JRequestDate['Users'][$id]).' из '.$this->JRequestDate['Request'][$id]['numberOfParticipants'].']';
                                $out .= '</div>';
                                $out .= $this->getJRequestUsersList($type,$id);
                            $out .= '</div>';
                        $out .= '</div>';
                        $out .= '<div class="requestFilesBlock">';
                            $out .= '<div class="requestTitle">';
                                $out .= 'Файлы';
                            $out .= '</div>';
                            $out .= $this->getFilesUI($type, $id);
                        $out .= '</div>';
                    $out .= '</div>';
                    $out .= $this->getUI_BottomInfo($id);
                $out .= '</div>';
            }
        }
        return $out;
    }
    
    private function getFilesUI($type, $requestID) {
        $jRequestAddFile = new JRequestAddFile($this->fest, $type, $requestID);
        return $jRequestAddFile->get();
    }


    private function isYouInRequest($id) {
        $usersDate = $this->JRequestDate['Users'][$id];
        $users = array();
        foreach ($this->JRequestDate['Users'][$id] as $usersDate) {
            $users[] = $usersDate['user'];
        }
        //echo $this->yourUserData['login']." ".print_r($users);
        return in_array($this->yourUserData['login'], $users);
    }

    private function getJRequestDeleteButton($id) {
        $out = "";
        if($this->isApplyingForYouUser($id)) {
            $params[0]=$this->JRequestDate['Request'][$id]['fest'];
            $params[1]=$id;
            $params[2]='requestDelete';
            $out .= '<a href="'.$this->urlHelper->chengeParams($params).'">';
            $out .= '<div class="requestIdButtonPanelButton">';
            $out .= '<div class="JRequestDelete" title="Удалить заявку"></div>';
            $out .= '</div>';
            $out .= '</a>';
        }
        return $out;
    }
    private function getJRequestShowButton($id) {
        $out = "";
        $out .= '<div class="requestIdButtonPanelButton" title="Развернуть и свернуть" onClick="show_hide(\''.$id.'_block\');">';
        //$out .= '<div class="JRequestShowButton" title="Развернуть и свернуть" onClick="show_hide(\''.$id.'_block\');"></div>';
        $out .= '<div id="eye" class="gap"><span class="inner"></span></div>';
        $out .= '</div>';
        return $out;
    }
    private function  getJRequestAdditionallyUI($type,$id) {
        switch ($type) {
            case "Karaoke":
                return $this->getJRequestKaraoke($type,$id);
                break;
            case "Dance":
                return $this->getJRequestDance($type,$id);
                break;
            case "Scene":
                return $this->getJRequestScene($type,$id);
                break;
            case "Defile":
                return $this->getJRequestDefile($type,$id);
                break;
            case "ActionDefile":
                return $this->getJRequestActionDefile($type,$id);
                break;
            case "AMV":
                return $this->getJRequestAMV($type,$id);
                break;
            case "VideoCosplay":
                return $this->getJRequestVideoCosplay($type,$id);
                break;
            case "Image":
                return $this->getJRequestImage($type,$id);
                break;
            case "Photo":
                return $this->getJRequestPhoto($type,$id);
                break;
        }
    }
    
    private function getJRequestKaraoke($type,$id) {
        $out = "";
        $out .= $this->getLable('Название композиции: ',$this->JRequestDate[$type][$id]['songTitle']);
        $out .= $this->getLable('Исполнитель: ',$this->JRequestDate[$type][$id]['artistSongs']);
        if($this->JRequestDate[$type][$id]['sceneryAndProps']!=null && $this->JRequestDate[$type][$id]['sceneryAndProps']!="") {
            $out .= $this->getLable('Используемые декорации и реквизит:<br>',$this->JRequestDate[$type][$id]['sceneryAndProps']);
        }
        return $out;
    }
    
    private function getJRequestDance($type,$id) {
        $out = "";
        $out .= $this->getLable('Название композиции: ',$this->JRequestDate[$type][$id]['songTitle']);
        $out .= $this->getLable('Исполнитель: ',$this->JRequestDate[$type][$id]['artistSongs']);
        if($this->JRequestDate[$type][$id]['sceneryAndProps']!=null && $this->JRequestDate[$type][$id]['sceneryAndProps']!="") {
            $out .= $this->getLable('Используемые декорации и реквизит:<br>',$this->JRequestDate[$type][$id]['sceneryAndProps']);
        }
        return $out;
    }
    
    private function getJRequestScene($type,$id) {
        $out = "";
        $out .= $this->getLable('Название Сценки: ',$this->JRequestDate[$type][$id]['title']);
        $out .= $this->getLable('Фендом: ',$this->JRequestDate[$type][$id]['fendomTitle']);
        return $out;
    }
    
    private function getJRequestDefile($type,$id) {
        $out = "";
        $out .= $this->getLable('Название Сценки: ',$this->JRequestDate[$type][$id]['title']);
        $out .= $this->getLable('Фендом: ',$this->JRequestDate[$type][$id]['fendomTitle']);
        return $out;
    }
    
    private function getJRequestActionDefile($type,$id) {
        $out = "";
        $out .= $this->getLable('Название Сценки: ',$this->JRequestDate[$type][$id]['title']);
        $out .= $this->getLable('Фендом: ',$this->JRequestDate[$type][$id]['fendomTitle']);
        return $out;
    }
    
    private function getJRequestAMV($type,$id) {
        $out = "";
        $out .= $this->getLable('Название Сценки: ',$this->JRequestDate[$type][$id]['title']);
        $out .= $this->getLable('Фендом(ы):<br>',$this->JRequestDate[$type][$id]['fendomTitle']);
        $out .= $this->getLable('Трек(и):<br>',$this->JRequestDate[$type][$id]['musicTracks']);
        $out .= $this->getLable('Программы:<br>',$this->JRequestDate[$type][$id]['programs']);
        return $out;
    }
    
    private function getJRequestVideoCosplay($type,$id) {
        $out = "";
        $out .= $this->getLable('Название Сценки: ',$this->JRequestDate[$type][$id]['title']);
        $out .= $this->getLable('Трек(и):<br>',$this->JRequestDate[$type][$id]['musicTracks']);
        $out .= $this->getLable('Программы:<br>',$this->JRequestDate[$type][$id]['programs']);
        $out .= $this->getLable('Видеооператор: ',$this->JRequestDate[$type][$id]['videographer']);
        return $out;
    }
    
    private function getJRequestImage($type,$id) {
        $out = "";
        $out .= $this->getLable('Название: ',$this->JRequestDate[$type][$id]['title']);
        return $out;
    }
    
    private function getJRequestPhoto($type,$id) {
        $out = "";
        $out .= $this->getLable('Название: ',$this->JRequestDate[$type][$id]['title']);
        $out .= $this->getLable('Фотограф: ',$this->JRequestDate[$type][$id]['photographer']);
        return $out;
    }
    
    private function getJRequestUsersList($type,$id) {
        $out = "";
        $out .= '<table class="JRequestUsersListTable">';
        $out .= '<tr>';
        $out .= '<th>Пользователь</th>';
        if($this->userTableSettings[$type]['characterName']) {
            $out .= '<th>Персонажа</th>';
        }
        if($this->userTableSettings[$type]['photo']) {
            $out .= '<th>Фото в костюме</th>';
        }
        if($this->userTableSettings[$type]['original']) {
            $out .= '<th>Фото персонажа</th>';
        }
        $out .= '<th></th>';
        if($this->isApplyingForYouUser($id)) {
            $out .= '<th></th>';
        }
        $out .= '</tr>';
        foreach ($this->JRequestDate['Users'][$id] as $user) {
            $out .= '<tr>';
            
            $params[0] = $user['user'];
            $url = $this->urlHelper->pageUrl('accounts', $params);
            $userURL = "";
            $userURL .= '<a href="'.$url.'" target="_blanck">';
            $userURL .= $user['user'];
            $userURL .= '</a>';
            
            $out .= '<td>'.$userURL.'</td>';
            
            $url_par = array();
            $url_par[0]=$this->fest;
            $url_par[1]=$id;
            $url_par[2]='addUserFile';
            $url_par[3]=$user['user'];
            $url_par[4]='photo';
            if($this->userTableSettings[$type]['characterName']) {
                $out .= '<td>'.$user['characterName'].'</td>';
            }
            if($this->userTableSettings[$type]['photo']) {
                $filePath = $this->getFilePath($type,$id,$user['user'],$user['photo']);
                $url_par[4]='photo';
                $out .= '<td>';
                if($user['photo']!=null && $user['photo']!="" && file_exists($filePath)) {
                    $url_par[2]='deleteUserFile';
                    $out .= '<div class="JRequestFileIcon JRequestFileDelete" title="'.$this->localization->getText("JRequestdelFileText").'">';
                    $out .= '<a href="'.$this->urlHelper->chengeParams($url_par).'"></a>';
                    $out .= '</div>';
//                    $out .= '<span class="tableCellConfirmed">'.$this->localization->getText("downloaded").'</span> ';
                    $out .= $this->getImageViewer($filePath);
                } else {
                    $url_par[2]='addUserFile';
                    $out .= '<div class="JRequestFileIcon JRequestFileAdd" title="'.$this->localization->getText("JRequestAddFileText").'">';
                    $out .= '<a href="'.$this->urlHelper->chengeParams($url_par).'"></a>';
                    $out .= '</div>';
                    $out .= '<span class="tableCellNoConfirmed">'.$this->localization->getText("noDownloaded").'</span> ';
                }
                $out .= '</td>';
            }
            if($this->userTableSettings[$type]['original']) {
                $filePath = $this->getFilePath($type,$id,$user['user'],$user['original']);
                $url_par[4]='original';
                $out .= '<td>';
                if($user['original']!=null && $user['original']!="" && file_exists($filePath)) {
                    $url_par[2]='deleteUserFile';
                    $out .= '<div class="JRequestFileIcon JRequestFileDelete" title="'.$this->localization->getText("JRequestdelFileText").'">';
                    $out .= '<a href="'.$this->urlHelper->chengeParams($url_par).'"></a>';
                    $out .= '</div>';
//                    $out .= '<span class="tableCellConfirmed">'.$this->localization->getText("downloaded").'</span> ';
                    $out .= $this->getImageViewer($filePath);
                } else {
                    $url_par[2]='addUserFile';
                    $out .= '<div class="JRequestFileIcon JRequestFileAdd" title="'.$this->localization->getText("JRequestAddFileText").'">';
                    $out .= '<a href="'.$this->urlHelper->chengeParams($url_par).'"></a>';
                    $out .= '</div>';
                    $out .= '<span class="tableCellNoConfirmed">'.$this->localization->getText("noDownloaded").'</span> ';
                }
                $out .= '</td>';
            }
            // confirmed
            if($user['confirmed']>0) {
                $out .= '<td class="tableCellConfirmed">&#10004;</th>';
            } else {
                $out .= '<td class="tableCellNoConfirmed">&#10007;</th>';
            }
            // del
            if($this->isApplyingForYouUser($id)) {
                if($this->JRequestDate['Request'][$id]['applyingFor']!=$user['user']) {
                    $delUserParams[0]=$confirmedParams[0]=$this->JRequestDate['Request'][$id]['fest'];
                    $delUserParams[1]=$confirmedParams[1]=$id;
                    $delUserParams[2]='delUser';
                    $delUserParams[3] = $user['user'];
                    $out .= '<td>';
                    $out .= '<a href="'.$this->urlHelper->chengeParams($delUserParams).'">';
                    $out .= '<div class="JRequestUserDelete" title="'.$this->localization->getText("JRequestdelUserText").'"></div>';
                    $out .= '</a>';
                    $out .= '</td>';
                } else {
                    $out .= '<td></td>';
                }
            }
            $out .= '</tr>';
        }
        $out .= '</table>';
        if(count($this->JRequestDate['Users'][$id]) < $this->JRequestDate['Request'][$id]['numberOfParticipants']) {
            $out .= $this->JRequestUserAddForm($type,$id);
        } else {
            $out .= '<div class="JFestsAllUsersAdd">Добавлены все участники.</div>';
        }
        return $out;
    }
    
    private function getFilePath($type,$id,$user,$file) {
        return "./resources/JRequest/".$this->fest."/".$type."/".$id."/".$user."/".$file;
    }
    
    private function getImageViewer($file) {
        return '<a class="fancybox" href="'.$file.'" title="Нажми для просмотра"><img src="'.$file.'" height="50px"></a>';
    }
    
    private function JRequestUserAddForm($type,$id) {
        $params[0]=$this->JRequestDate['Request'][$id]['fest'];
        $params[1]=$id;
        $params[2]='addUser';
        $url = $this->urlHelper->chengeParams($params);
        
        $out = "";
        $out .= '<form class="JRequestUserAddForm" name="JRequestUserAddForm" action="'.$url.'" method="post" accept-charset="UTF-8" autocomplete="on">';
        $out .= '<span class="requestLableText">'.$this->localization->getText("login").':</span> ';
        $out .= $this->inputHelper->paternTextBox("login", "login", "login", 25, true, $this->localization->getText("loginAndPasswordPatern"), "[A-Za-z0-9]{3,25}", null);
        if($this->userTableSettings[$type]['characterName']) {
            $out .= '<br><span class="requestLableText">'.$this->localization->getText("characterName").':</span> ';
            $out .= $this->inputHelper->textBox("characterName", "characterName", "characterName", 200, true, null);
        }
        
        $out.= '<br><input class="JRequestFormButton small" type="submit" name="JRequestUserAddSubmit" value="'.$this->localization->getText("JRequestUserAddSubmitText").'">';
        $out .= '</form>';
        return $out;
    }

    private function isApplyingForYouUser($id) {
        return $this->JRequestDate['Request'][$id]['applyingFor'] == $this->yourUserData['login'];
    }

    private function getPreapprovedButton($id) {
        $out = "";
        if(!$this->JRequestDate['Request'][$id]['preapproved']>0) {
            $JRequestCheck = new JRequestCheck($id);
            if($JRequestCheck->getCheckStatus()) {
                if($this->isApplyingForYouUser($id) || $this->yourUser->isAdmin()) {
                    $preapprovedParam[0]=$this->fest;
                    $preapprovedParam[1]=$id;
                    $preapprovedParam[2]='sendCheckRequest';
                    $out .= '<a href="'.$this->urlHelper->chengeParams($preapprovedParam).'">';
                    $out .= '<input class="JRequestFormButton small" type="button" value="отправить на рассмотрение">';
                    $out .= '</a>';
                }
            }
        } else {
            if($this->yourUser->isAdmin() || ($this->JRequestDate['Request'][$id]['preapproved']>0 && $this->isApplyingForYouUser($id))) {
                $preapprovedParam[0]=$this->fest;
                $preapprovedParam[1]=$id;
                $preapprovedParam[2]='sendCheckRequest';
                $preapprovedParam[3]='unset';
                $out .= '<a href="'.$this->urlHelper->chengeParams($preapprovedParam).'">';
                $out .= '<input class="JRequestFormButton small" type="button" value="Снять с рассмотрения">';
                $out .= '</a>';
            }
        }
        return $out;
    }

    private function getConfirmedButton($id) {
        $delconfirmedParams[0]=$confirmedParams[0]=$this->JRequestDate['Request'][$id]['fest'];
        $delconfirmedParams[1]=$confirmedParams[1]=$id;
        $delconfirmedParams[2]='delconfirmed';
        $confirmedParams[2]='confirmed';
        $out = "";
        if(!$this->isApplyingForYouUser($id) && $this->isYouInRequest($id)) {
            if($this->yourUserConfirmedDate[$id]) {
                //$out .= 'Вы подтвердили свое участие<br>[<a href="'.$this->urlHelper->chengeParams($delconfirmedParams).'">Отказаться от участия</a>]';
                $out .= 'Вы подтвердили свое участие<br>';
                $out .= '<a href="'.$this->urlHelper->chengeParams($delconfirmedParams).'">';
                $out .= '<input class="JRequestFormButton small" type="button" value="Отказаться от участия">';
                $out .= '</a>';
            } else {
                //$out .= 'Вы не подтвердили свое участие<br>[<a href="'.$this->urlHelper->chengeParams($confirmedParams).'">Подтвердить свое участие</a>] [<a href="'.$this->urlHelper->chengeParams($delconfirmedParams).'">Отказаться от участия</a>] ';
                $out .= 'Вы не подтвердили свое участие<br>';
                $out .= '<a href="'.$this->urlHelper->chengeParams($confirmedParams).'">';
                $out .= '<input class="JRequestFormButton small" type="button" value="Подтвердить свое участие">';
                $out .= '</a>';
                $out .= '<a href="'.$this->urlHelper->chengeParams($delconfirmedParams).'">';
                $out .= '<input class="JRequestFormButton small" type="button" value="Отказаться от участия">';
                $out .= '</a>';
            }
        }
        return $out;
    }
}

?>