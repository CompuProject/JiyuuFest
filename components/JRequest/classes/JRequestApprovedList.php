<?php
class JRequestApprovedList {
    private $SQL_HELPER;
    private $URL_PARAMS;
    private $urlHelper;
    private $localization;
    
    private $yourUser;
    private $yourUserData;
    
    private $fest = 'jiyuu2014';
    
    public function __construct() {
        global $_SQL_HELPER;
        global $_URL_PARAMS;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->URL_PARAMS = $_URL_PARAMS['params'];
        $this->urlHelper = new UrlHelper();
        $this->localization = new Localization("JRequest");
        $this->getUserData();
    }
    
    private function getUserData() {
        $this->yourUser = new UserData();
        $this->yourUser->checkAuthorization();
        $this->yourUserData = $this->yourUser->getUserData();
    }
    
    private function getTitle($title) {
        return '<div class="JFestsListHeder"><div class="JRequestTypeTitle">'.$title.'</div></div>';
    }
    
    private function getVideoCosplay() {
        $out = '';
        $out .= $this->getTitle('Видео косплей');
        $query = "
        SELECT
        JR.`id`, 
        JR.`contest`, 
        JR.`applyingFor`,
        JR.`duration`, 
        JR.`kosbendTitle`,
        JR2.`title`, 
        JR2.`fendomTitle`, 
        JR2.`musicTracks`
        FROM  `JRequest` as JR 
        left join `JRequestVideoCosplay` as JR2
        on JR.`id` = JR2.`id`
        where JR.`type`='VideoCosplay' 
        AND JR.`fest`='".$this->fest."'
        AND JR.`approved`>0
        ORDER BY JR.`applyingFor` DESC;";
        $result = $this->SQL_HELPER->select($query);
        if($result!=null && count($result)>0) {
            $out .= '<table class="JRequestUsersListTable approvedList"><tbody>';
            $out .= '<tr>';
            $out .= '<td>id</td>';
            $out .= '<td>Конкурсная</td>';
            $out .= '<td>Автор заявки</td>';
            $out .= '<td>Продолжительность</td>';
            $out .= '<td>Косбенд</td>';
            $out .= '<td>Название</td>';
            $out .= '<td>Фендом</td>';
            $out .= '<td>Музыкальные треки</td>';
            $out .= '</tr>';
            foreach ($result as $request) {
                $out .= '<tr>';
                $out .= '<td>'.$request['id'].'</td>';
                if($request['contest']>0) {
                    $out .= '<td class="tableCellConfirmed">✔</td>';
                } else {
                    $out .= '<td class="tableCellNoConfirmed">✗</td>';
                }
                $params[0] = $request['applyingFor'];
                $url = $this->urlHelper->pageUrl('accounts', $params);
                $out .= '<td><a href="'.$url.'">'.$request['applyingFor'].'</a></td>';
                
                $out .= '<td>'.$request['duration'].'c.</td>';
                $out .= '<td>'.$request['kosbendTitle'].'</td>';
                $out .= '<td>'.$request['title'].'</td>';
                $out .= '<td>'.$request['fendomTitle'].'</td>';
                $out .= '<td>'.$request['musicTracks'].'</td>';
                $out .= '</tr>';
            }
            $out .= '</tbody></table>';
        }
        return $out;
    }
    
    private function getAMV() {
        $out = '';
        $out .= $this->getTitle('AMV');
        $query = "
        SELECT
        JR.`id`, 
        JR.`contest`, 
        JR.`applyingFor`, 
        JR.`duration`, 
        JR2.`title`, 
        JR2.`fendomTitle`, 
        JR2.`musicTracks`
        FROM  `JRequest` as JR 
        left join `JRequestAMV` as JR2
        on JR.`id` = JR2.`id`
        where JR.`type`='AMV' 
        AND JR.`fest`='".$this->fest."'
        AND JR.`approved`>0
        ORDER BY JR.`applyingFor` DESC;";
        $result = $this->SQL_HELPER->select($query);
        if($result!=null && count($result)>0) {
            $out .= '<table class="JRequestUsersListTable approvedList"><tbody>';
            $out .= '<tr>';
            $out .= '<td>id</td>';
            $out .= '<td>Конкурсная</td>';
            $out .= '<td>Автор заявки</td>';
            $out .= '<td>Продолжительность</td>';
            $out .= '<td>Название</td>';
            $out .= '<td>Фендом</td>';
            $out .= '<td>Музыкальные треки</td>';
            $out .= '</tr>';
            foreach ($result as $request) {
                $out .= '<tr>';
                $out .= '<td>'.$request['id'].'</td>';
                if($request['contest']>0) {
                    $out .= '<td class="tableCellConfirmed">✔</td>';
                } else {
                    $out .= '<td class="tableCellNoConfirmed">✗</td>';
                }
                $params[0] = $request['applyingFor'];
                $url = $this->urlHelper->pageUrl('accounts', $params);
                $out .= '<td><a href="'.$url.'">'.$request['applyingFor'].'</a></td>';
                $out .= '<td>'.$request['duration'].'c.</td>';
                $out .= '<td>'.$request['title'].'</td>';
                $out .= '<td>'.$request['fendomTitle'].'</td>';
                $out .= '<td>'.$request['musicTracks'].'</td>';
                $out .= '</tr>';
            }
            $out .= '</tbody></table>';
        }
        return $out;
    }
    
    private function getPhoto() {
        $out = '';
        $out .= $this->getTitle('Фото косплей');
        $query = "
        SELECT
        JR.`id`, 
        JR.`contest`, 
        JR.`applyingFor`, 
        JR2.`title`, 
        JR2.`photographer`
        FROM  `JRequest` as JR 
        left join `JRequestPhoto` as JR2
        on JR.`id` = JR2.`id`
        where JR.`type`='Photo' 
        AND JR.`fest`='".$this->fest."'
        AND JR.`approved`>0
        ORDER BY JR.`applyingFor` DESC;";
        $result = $this->SQL_HELPER->select($query);
        if($result!=null && count($result)>0) {
            $out .= '<table class="JRequestUsersListTable approvedList"><tbody>';
            $out .= '<tr>';
            $out .= '<td>id</td>';
            $out .= '<td>Конкурсная</td>';
            $out .= '<td>Автор заявки</td>';
            $out .= '<td>Название</td>';
            $out .= '<td>Фотограф</td>';
            $out .= '</tr>';
            foreach ($result as $request) {
                $out .= '<tr>';
                $out .= '<td>'.$request['id'].'</td>';
                if($request['contest']>0) {
                    $out .= '<td class="tableCellConfirmed">✔</td>';
                } else {
                    $out .= '<td class="tableCellNoConfirmed">✗</td>';
                }
                $params[0] = $request['applyingFor'];
                $url = $this->urlHelper->pageUrl('accounts', $params);
                $out .= '<td><a href="'.$url.'">'.$request['applyingFor'].'</a></td>';
                $out .= '<td>'.$request['title'].'</td>';
                $out .= '<td>'.$request['photographer'].'</td>';
                $out .= '</tr>';
            }
            $out .= '</tbody></table>';
        }
        return $out;
    }
    
    private function getImage() {
        $out = '';
        $out .= $this->getTitle('Конкурс рисунков');
        $query = "
        SELECT
        JR.`id`, 
        JR.`contest`, 
        JR.`applyingFor`, 
        JR2.`title`
        FROM  `JRequest` as JR 
        left join `JRequestImage` as JR2
        on JR.`id` = JR2.`id`
        where JR.`type`='Image' 
        AND JR.`fest`='".$this->fest."'
        AND JR.`approved`>0
        ORDER BY JR.`applyingFor` DESC;";
        $result = $this->SQL_HELPER->select($query);
        if($result!=null && count($result)>0) {
            $out .= '<table class="JRequestUsersListTable approvedList"><tbody>';
            $out .= '<tr>';
            $out .= '<td>id</td>';
            $out .= '<td>Конкурсная</td>';
            $out .= '<td>Автор заявки</td>';
            $out .= '<td>Название</td>';
            $out .= '</tr>';
            foreach ($result as $request) {
                $out .= '<tr>';
                $out .= '<td>'.$request['id'].'</td>';
                if($request['contest']>0) {
                    $out .= '<td class="tableCellConfirmed">✔</td>';
                } else {
                    $out .= '<td class="tableCellNoConfirmed">✗</td>';
                }
                $params[0] = $request['applyingFor'];
                $url = $this->urlHelper->pageUrl('accounts', $params);
                $out .= '<td><a href="'.$url.'">'.$request['applyingFor'].'</a></td>';
                $out .= '<td>'.$request['title'].'</td>';
                $out .= '</tr>';
            }
            $out .= '</tbody></table>';
        }
        return $out;
    }
    
    private function getDance() {
        $out = '';
        $out .= $this->getTitle('Танцы');
        $query = "
        SELECT
        JR.`id`,
        JR.`contest`,
        JR.`applyingFor`,
        JR.`duration`,
        JR.`kosbendTitle`,
        JR.`numberOfParticipants`,
        JR2.`songTitle`, 
        JR2.`artistSongs`
        FROM  `JRequest` as JR 
        left join `JRequestDance` as JR2
        on JR.`id` = JR2.`id`
        where JR.`type`='Dance' 
        AND JR.`fest`='".$this->fest."'
        AND JR.`approved`>0
        ORDER BY JR.`applyingFor` DESC;";
        $result = $this->SQL_HELPER->select($query);
        if($result!=null && count($result)>0) {
            $out .= '<table class="JRequestUsersListTable approvedList"><tbody>';
            $out .= '<tr>';
            $out .= '<td>id</td>';
            $out .= '<td>Конкурсная</td>';
            $out .= '<td>Автор заявки</td>';
            $out .= '<td>Продолжительность</td>';
            $out .= '<td>Название косбенда</td>';
            $out .= '<td>Участники</td>';
            $out .= '<td>Музыка</td>';
            $out .= '<td>Исполнитель</td>';
            $out .= '</tr>';
            foreach ($result as $request) {
                $query="SELECT `user` FROM `JRequestUsers` WHERE `request`='".$request['id']."';";
                $users = $this->SQL_HELPER->select($query);
                
                $out .= '<tr>';
                $out .= '<td>'.$request['id'].'</td>';
                if($request['contest']>0) {
                    $out .= '<td class="tableCellConfirmed">✔</td>';
                } else {
                    $out .= '<td class="tableCellNoConfirmed">✗</td>';
                }
                $params[0] = $request['applyingFor'];
                $url = $this->urlHelper->pageUrl('accounts', $params);
                $out .= '<td><a href="'.$url.'">'.$request['applyingFor'].'</a></td>';
                
                $out .= '<td>'.$request['duration'].'</td>';
                $out .= '<td>'.$request['kosbendTitle'].'</td>';
                if($users!=null) {
                    $out .= '<td>';
                    foreach ($users as $user) {
                        $url = $this->urlHelper->pageUrl('accounts', $params);
                        $out .= '<a href="'.$url.'">'.$user['user'].'</a><br />';
                    }
                    $out .= '</td>';
                } else {
                    $out .= '<td>'.$request['numberOfParticipants'].'</td>';
                }
                $out .= '<td>'.$request['songTitle'].'</td>';
                $out .= '<td>'.$request['artistSongs'].'</td>';
                $out .= '</tr>';
            }
            $out .= '</tbody></table>';
        }
        return $out;
    }
    
    private function getKaraoke() {
        $out = '';
        $out .= $this->getTitle('Караоке');
        $query = "
        SELECT
        JR.`id`,
        JR.`contest`,
        JR.`applyingFor`,
        JR.`duration`,
        JR.`kosbendTitle`,
        JR.`numberOfParticipants`,
        JR2.`songTitle`, 
        JR2.`artistSongs`
        FROM  `JRequest` as JR 
        left join `JRequestKaraoke` as JR2
        on JR.`id` = JR2.`id`
        where JR.`type`='Karaoke' 
        AND JR.`fest`='".$this->fest."'
        AND JR.`approved`>0
        ORDER BY JR.`applyingFor` DESC;";
        $result = $this->SQL_HELPER->select($query);
        if($result!=null && count($result)>0) {
            $out .= '<table class="JRequestUsersListTable approvedList"><tbody>';
            $out .= '<tr>';
            $out .= '<td>id</td>';
            $out .= '<td>Конкурсная</td>';
            $out .= '<td>Автор заявки</td>';
            $out .= '<td>Продолжительность</td>';
            $out .= '<td>Название косбенда</td>';
            $out .= '<td>Участники</td>';
            $out .= '<td>Музыка</td>';
            $out .= '<td>Исполнитель</td>';
            $out .= '</tr>';
            foreach ($result as $request) {
                $query="SELECT `user` FROM `JRequestUsers` WHERE `request`='".$request['id']."';";
                $users = $this->SQL_HELPER->select($query);
                
                $out .= '<tr>';
                $out .= '<td>'.$request['id'].'</td>';
                if($request['contest']>0) {
                    $out .= '<td class="tableCellConfirmed">✔</td>';
                } else {
                    $out .= '<td class="tableCellNoConfirmed">✗</td>';
                }
                $params[0] = $request['applyingFor'];
                $url = $this->urlHelper->pageUrl('accounts', $params);
                $out .= '<td><a href="'.$url.'">'.$request['applyingFor'].'</a></td>';
                
                $out .= '<td>'.$request['duration'].'</td>';
                $out .= '<td>'.$request['kosbendTitle'].'</td>';
                if($users!=null) {
                    $out .= '<td>';
                    foreach ($users as $user) {
                        $url = $this->urlHelper->pageUrl('accounts', $params);
                        $out .= '<a href="'.$url.'">'.$user['user'].'</a><br />';
                    }
                    $out .= '</td>';
                } else {
                    $out .= '<td>'.$request['numberOfParticipants'].'</td>';
                }
                $out .= '<td>'.$request['songTitle'].'</td>';
                $out .= '<td>'.$request['artistSongs'].'</td>';
                $out .= '</tr>';
            }
            $out .= '</tbody></table>';
        }
        return $out;
    }
    
    private function getScene() {
        $out = '';
        $out .= $this->getTitle('Сценический косплей');
        $query = "
        SELECT
        JR.`id`,
        JR.`contest`,
        JR.`applyingFor`,
        JR.`duration`,
        JR.`kosbendTitle`,
        JR.`numberOfParticipants`,
        JR2.`title`, 
        JR2.`fendomTitle`
        FROM  `JRequest` as JR 
        left join `JRequestScene` as JR2
        on JR.`id` = JR2.`id`
        where JR.`type`='Scene' 
        AND JR.`fest`='".$this->fest."'
        AND JR.`approved`>0
        ORDER BY JR.`applyingFor` DESC;";
        $result = $this->SQL_HELPER->select($query);
        if($result!=null && count($result)>0) {
            $out .= '<table class="JRequestUsersListTable approvedList"><tbody>';
            $out .= '<tr>';
            $out .= '<td>id</td>';
            $out .= '<td>Конкурсная</td>';
            $out .= '<td>Автор заявки</td>';
            $out .= '<td>Продолжительность</td>';
            $out .= '<td>Название косбенда</td>';
            $out .= '<td>Участники</td>';
            $out .= '<td>Название</td>';
            $out .= '<td>Фендом</td>';
            $out .= '</tr>';
            foreach ($result as $request) {
                $query="SELECT `user`,`characterName` FROM `JRequestUsers` WHERE `request`='".$request['id']."';";
                $users = $this->SQL_HELPER->select($query);
                
                $out .= '<tr>';
                $out .= '<td>'.$request['id'].'</td>';
                if($request['contest']>0) {
                    $out .= '<td class="tableCellConfirmed">✔</td>';
                } else {
                    $out .= '<td class="tableCellNoConfirmed">✗</td>';
                }
                $params[0] = $request['applyingFor'];
                $url = $this->urlHelper->pageUrl('accounts', $params);
                $out .= '<td><a href="'.$url.'">'.$request['applyingFor'].'</a></td>';
                
                $out .= '<td>'.$request['duration'].'</td>';
                $out .= '<td>'.$request['kosbendTitle'].'</td>';
                if($users!=null) {
                    $out .= '<td>';
                    foreach ($users as $user) {
                        $url = $this->urlHelper->pageUrl('accounts', $params);
                        $out .= '<a href="'.$url.'">'.$user['user'].'</a> ';
                        $out .= '('.$user['characterName'].')<br />';
                    }
                    $out .= '</td>';
                } else {
                    $out .= '<td>'.$request['numberOfParticipants'].'</td>';
                }
                $out .= '<td>'.$request['title'].'</td>';
                $out .= '<td>'.$request['fendomTitle'].'</td>';
                $out .= '</tr>';
            }
            $out .= '</tbody></table>';
        }
        return $out;
    }
    
    private function getDefile() {
        $out = '';
        $out .= $this->getTitle('Дефиле');
        $query = "
        SELECT
        JR.`id`,
        JR.`contest`,
        JR.`applyingFor`,
        JR.`duration`,
        JR.`kosbendTitle`,
        JR.`numberOfParticipants`,
        JR2.`title`, 
        JR2.`fendomTitle`
        FROM  `JRequest` as JR 
        left join `JRequestDefile` as JR2
        on JR.`id` = JR2.`id`
        where JR.`type`='Defile' 
        AND JR.`fest`='".$this->fest."'
        AND JR.`approved`>0
        ORDER BY JR.`applyingFor` DESC;";
        $result = $this->SQL_HELPER->select($query);
        if($result!=null && count($result)>0) {
            $out .= '<table class="JRequestUsersListTable approvedList"><tbody>';
            $out .= '<tr>';
            $out .= '<td>id</td>';
            $out .= '<td>Конкурсная</td>';
            $out .= '<td>Автор заявки</td>';
            $out .= '<td>Продолжительность</td>';
            $out .= '<td>Название косбенда</td>';
            $out .= '<td>Участники</td>';
            $out .= '<td>Название</td>';
            $out .= '<td>Фендом</td>';
            $out .= '</tr>';
            foreach ($result as $request) {
                $query="SELECT `user`,`characterName` FROM `JRequestUsers` WHERE `request`='".$request['id']."';";
                $users = $this->SQL_HELPER->select($query);
                
                $out .= '<tr>';
                $out .= '<td>'.$request['id'].'</td>';
                if($request['contest']>0) {
                    $out .= '<td class="tableCellConfirmed">✔</td>';
                } else {
                    $out .= '<td class="tableCellNoConfirmed">✗</td>';
                }
                $params[0] = $request['applyingFor'];
                $url = $this->urlHelper->pageUrl('accounts', $params);
                $out .= '<td><a href="'.$url.'">'.$request['applyingFor'].'</a></td>';
                
                $out .= '<td>'.$request['duration'].'</td>';
                $out .= '<td>'.$request['kosbendTitle'].'</td>';
                if($users!=null) {
                    $out .= '<td>';
                    foreach ($users as $user) {
                        $url = $this->urlHelper->pageUrl('accounts', $params);
                        $out .= '<a href="'.$url.'">'.$user['user'].'</a> ';
                        $out .= '('.$user['characterName'].')<br />';
                    }
                    $out .= '</td>';
                } else {
                    $out .= '<td>'.$request['numberOfParticipants'].'</td>';
                }
                $out .= '<td>'.$request['title'].'</td>';
                $out .= '<td>'.$request['fendomTitle'].'</td>';
                $out .= '</tr>';
            }
            $out .= '</tbody></table>';
        }
        return $out;
    }
    
    private function getActionDefile() {
        $out = '';
        $out .= $this->getTitle('Экшен-дефиле');
        $query = "
        SELECT
        JR.`id`,
        JR.`contest`,
        JR.`applyingFor`,
        JR.`duration`,
        JR.`kosbendTitle`,
        JR.`numberOfParticipants`,
        JR2.`title`, 
        JR2.`fendomTitle`
        FROM  `JRequest` as JR 
        left join `JRequestActionDefile` as JR2
        on JR.`id` = JR2.`id`
        where JR.`type`='ActionDefile' 
        AND JR.`fest`='".$this->fest."'
        AND JR.`approved`>0
        ORDER BY JR.`applyingFor` DESC;";
        $result = $this->SQL_HELPER->select($query);
        if($result!=null && count($result)>0) {
            $out .= '<table class="JRequestUsersListTable approvedList"><tbody>';
            $out .= '<tr>';
            $out .= '<td>id</td>';
            $out .= '<td>Конкурсная</td>';
            $out .= '<td>Автор заявки</td>';
            $out .= '<td>Продолжительность</td>';
            $out .= '<td>Название косбенда</td>';
            $out .= '<td>Участники</td>';
            $out .= '<td>Название</td>';
            $out .= '<td>Фендом</td>';
            $out .= '</tr>';
            foreach ($result as $request) {
                $query="SELECT `user`,`characterName` FROM `JRequestUsers` WHERE `request`='".$request['id']."';";
                $users = $this->SQL_HELPER->select($query);
                
                $out .= '<tr>';
                $out .= '<td>'.$request['id'].'</td>';
                if($request['contest']>0) {
                    $out .= '<td class="tableCellConfirmed">✔</td>';
                } else {
                    $out .= '<td class="tableCellNoConfirmed">✗</td>';
                }
                $params[0] = $request['applyingFor'];
                $url = $this->urlHelper->pageUrl('accounts', $params);
                $out .= '<td><a href="'.$url.'">'.$request['applyingFor'].'</a></td>';
                
                $out .= '<td>'.$request['duration'].'</td>';
                $out .= '<td>'.$request['kosbendTitle'].'</td>';
                if($users!=null) {
                    $out .= '<td>';
                    foreach ($users as $user) {
                        $url = $this->urlHelper->pageUrl('accounts', $params);
                        $out .= '<a href="'.$url.'">'.$user['user'].'</a> ';
                        $out .= '('.$user['characterName'].')<br />';
                    }
                    $out .= '</td>';
                } else {
                    $out .= '<td>'.$request['numberOfParticipants'].'</td>';
                }
                $out .= '<td>'.$request['title'].'</td>';
                $out .= '<td>'.$request['fendomTitle'].'</td>';
                $out .= '</tr>';
            }
            $out .= '</tbody></table>';
        }
        return $out;
    }
    
    public function get() {
        $out = '';
        $out .= 'Внимание, данный список обнавляется автоматически по мере '
                . 'одобрения ваших заявок организаторами.<BR>Если вы не нашли себя '
                . 'в списке, не стоит паниковать, возможно вашу заявку '
                . 'еще не успели рассмотреть.<BR>Организаторы фестиваля оставляют '
                . 'за собой право в любой момент вносить изменения в списки.<BR>'
                . 'Готовые списки размещают в группе в контакте.<BR><BR>';
        $out .= $this->getKaraoke();
        $out .= $this->getDance();
        $out .= $this->getScene();
        $out .= $this->getDefile();
        $out .= $this->getActionDefile();
        $out .= $this->getAMV();
        $out .= $this->getVideoCosplay();
        $out .= $this->getImage();
        $out .= $this->getPhoto();
//        $out .= 'Все списки смотрите в группе в контакте<BR>';
        return $out;
    }
}
?>