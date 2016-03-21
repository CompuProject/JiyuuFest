<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JiyuuFestRequest_ProgressBar
 *
 * @author Maxim Zaytsev
 * @copyright © 2010-2016, CompuProjec
 * @created 29.11.2015 13:04:55
 */
class JiyuuFestRequest_ProgressBar {
    // помошники
    private $SQL_HELPER;
    private $localization;
    
    private $requestID;
    private $requestData;
    private $requestUsersData;
    private $requestTypeData;
    private $requestExpansionData;
    private $requestDefileTypeData;
    private $recommendations = array();
    private $errors = array();
    private $progressBarHtml = '';
    private $ready = 0;

    public function __construct($requestID) {
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
        $this->localization = new Localization("JiyuuFests");
        $this->requestID = $requestID;
        $this->getDefileTypeData();
        $this->getRequestData();
        $this->getTypeData();
        $this->getExpansionData();
        $this->getUsersData();
        $this->checkRequestProgress();
        $this->generateProgressBar();
        
//        echo "<pre>";
//        echo "<h2>requestData</h2>";
//        var_dump($this->requestData);
//        echo "<h2>requestUsersData</h2>";
//        var_dump($this->requestUsersData);
//        echo "<h2>requestTypeData</h2>";
//        var_dump($this->requestTypeData);
//        echo "<h2>requestExpansionData</h2>";
//        var_dump($this->requestExpansionData);
//        echo "<h2>recommendations</h2>";
//        var_dump($this->recommendations);
//        echo "<h2>requestError</h2>";
//        var_dump($this->errors);
//        echo "</pre>";
    }
    
    private function checkRequestProgress() {
        $this->checkRequestProgress_Errors();
        $checkUsers = $this->checkRequestProgress_Users();
        $checkFields = $this->checkRequestProgress_Fields();
        $this->ready = round((($checkUsers + $checkFields) / 2), 2);
//        echo "checkUsers: ".$checkUsers." | checkFields: ".$checkFields." | rez: ".$this->ready;
    }
    private function checkRequestProgress_Errors() {
        if($this->requestData['numberOfParticipants'] < $this->requestTypeData['minNumberOfParticipants']) {
            $this->errors[] = "Количество участников меньше разрешенного количества.";
        }
        if($this->requestData['numberOfParticipants'] > $this->requestTypeData['maxNumberOfParticipants']) {
            $this->errors[] = "Количество участников больше разрешенного количества.";
        }
        if(($this->requestData['durationMin']*60)+$this->requestData['durationSec'] < ($this->requestTypeData['minDurationMinutes']*60)+$this->requestTypeData['minDurationSeconds']) {
            $this->errors[] = "Время выступления меньше положенного.";
        }
        if(($this->requestData['durationMin']*60)+$this->requestData['durationSec'] > ($this->requestTypeData['maxDurationMinutes']*60)+$this->requestTypeData['maxDurationSeconds']) {
            $this->errors[] = "Время выступления больше положенного.";
        }
        if($this->requestData['contest'] > $this->requestTypeData['mayBeContest']) {
            $this->errors[] = "Не может быть подана на конкурсной основе.";
        }
    }
    private function checkRequestProgress_Users() {
        $allPoint = $this->requestData['numberOfParticipants'];
        $checkedUpPoints = 0;
        foreach ($this->requestUsersData as $user) {
            $checkedUpPoints += $this->checkRequestProgress_User($user);
        }
        $rezult = $checkedUpPoints/$allPoint;
        if(count($this->requestUsersData) < $allPoint) {
            $this->recommendations[] = "Не все участники были добавлены.";
        }
        if(count($this->requestUsersData) > $allPoint) {
            $this->errors[] = "Было добавлено больше участников чем требуется.";
            $rezult = 1;
        }
        return $rezult;
    }
    
    private function checkRequestProgress_User($user) {
        $allPoint = 1;
        $checkedUpPoints = 0;
        if($user['confirmed'] > 0) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Участник ".$user['nickname']." до сих пор не подтвердил свое участие.";
        }
        if($this->requestTypeData['characterName'] > 0) {
            $allPoint++;
            if($user['characterName'] !== '' && $user['characterName'] !== null) {
                $checkedUpPoints++;
            } else {
                $this->recommendations[] = "Участник ".$user['nickname']." до сих пор не указал своего персонажа.";
            }
        }
        if($this->requestTypeData['photo'] > 0) {
            $allPoint++;
            if($user['photo'] !== '' && $user['photo'] !== null) {
                $checkedUpPoints++;
            } else {
                $this->recommendations[] = "Участник ".$user['nickname']." до сих пор не предоставил фото костюма.";
            }
        }
        if($this->requestTypeData['original'] > 0) {
            $allPoint++;
            if($user['original'] !== '' && $user['original'] !== null) {
                $checkedUpPoints++;
            } else {
                $this->recommendations[] = "Участник ".$user['nickname']." до сих пор не предоставил изображение своего персонажа.";
            }
        }
        return $checkedUpPoints/$allPoint;
    }
    
    private function checkRequestProgress_Fields() {
        switch ($this->requestData['type']) {
            case 'action_defile':
                return $this->checkRequestProgress_ActionDefile();
            case 'amv':
                return $this->checkRequestProgress_AMV();
            case 'dance':
                return $this->checkRequestProgress_Dance();
            case 'defile':
                return $this->checkRequestProgress_Defile();
            case 'image':
                return $this->checkRequestProgress_Image();
            case 'karaoke':
                return $this->checkRequestProgress_Karaoke();
            case 'photo':
                return $this->checkRequestProgress_Photo();
            case 'scene':
                return $this->checkRequestProgress_Scene();
            case 'video_cosplay':
                return $this->checkRequestProgress_VideoCosplay();
            default:
                return 0;
        }
    }
    private function checkRequestProgress_ActionDefile() {
        $allPoint = 7;
        $checkedUpPoints = 0;
        $noFendom = $noCollage = false;
        if($this->checkExpansionDataValue('defileType') && isset($this->requestDefileTypeData[$this->requestExpansionData['defileType']])) {
            $noFendom = $this->requestDefileTypeData[$this->requestExpansionData['defileType']]['fendom'] < 1;
            $noCollage = $this->requestDefileTypeData[$this->requestExpansionData['defileType']]['collage'] < 1;
        }
        // actionDefileTitle
        if($this->checkExpansionDataValue('actionDefileTitle')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Нет названия.";
        }
        // defileType
        if($this->checkExpansionDataValue('defileType')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан тип.";
        }
        // fendom
        if($this->checkExpansionDataValue('fendom') || $noFendom) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан Фендом.";
        }
        // demo audition
        if($this->checkExpansionDataValue('demo') || 
                ($this->checkExpansionDataValue('audition') && $this->requestExpansionData['audition'] > 0)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует демо запись и не назначено прослушивание.";
        }
        // audio instrumental audioInVideo noVideo
        if($this->checkExpansionDataValue('audio') || 
                ($this->checkExpansionDataValue('instrumental') && $this->requestExpansionData['instrumental'] > 0) || 
                ($this->checkExpansionDataValue('audioInVideo') && $this->requestExpansionData['audioInVideo'] > 0 && 
                $this->checkExpansionDataValue('noVideo') && $this->requestExpansionData['noVideo'] < 1)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует музыкальное сопровождение.";
        }
        // video noVideo
        if($this->checkExpansionDataValue('video') || 
                ($this->checkExpansionDataValue('noVideo') && $this->requestExpansionData['noVideo'] > 0)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Заявленное видео не было добавлено.";
        }
        // collage
        if($this->checkExpansionDataValue('collage') || $noCollage) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует коллаж.";
        }
        return $checkedUpPoints/$allPoint;
    }
    private function checkRequestProgress_AMV() {
        $allPoint = 5;
        $checkedUpPoints = 0;
        // amvTitle
        if($this->checkExpansionDataValue('amvTitle')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует название.";
        }
        // fendom
        if($this->checkExpansionDataValue('fendom')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан фендом.";
        }
        // musicTracks
        if($this->checkExpansionDataValue('musicTracks')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указаны музыкальыне треки.";
        }
        // programs
        if($this->checkExpansionDataValue('programs')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан список программ.";
        }
        // amv
        if($this->checkExpansionDataValue('amv')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует ролик.";
        }
        return $checkedUpPoints/$allPoint;
    }
    private function checkRequestProgress_Dance() {
        $allPoint = 4;
        $checkedUpPoints = 0;
        // danceTitle
        if($this->checkExpansionDataValue('danceTitle')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует название.";
        }
        // demo audition
        if($this->checkExpansionDataValue('demo') || 
                ($this->checkExpansionDataValue('audition') && $this->requestExpansionData['audition'] > 0)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует демо запись и не назначено прослушивание.";
        }
        // audio instrumental audioInVideo noVideo
        if($this->checkExpansionDataValue('audio') || 
                ($this->checkExpansionDataValue('instrumental') && $this->requestExpansionData['instrumental'] > 0) || 
                ($this->checkExpansionDataValue('audioInVideo') && $this->requestExpansionData['audioInVideo'] > 0 && 
                $this->checkExpansionDataValue('noVideo') && $this->requestExpansionData['noVideo'] < 1)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует музыкальное сопровождение.";
        }
        // video noVideo
        if($this->checkExpansionDataValue('video') || 
                ($this->checkExpansionDataValue('noVideo') && $this->requestExpansionData['noVideo'] > 0)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Заявленное видео не было добавлено.";
        }
        return $checkedUpPoints/$allPoint;
    }
    private function checkRequestProgress_Defile() {
        $allPoint = 4;
        $checkedUpPoints = 0;
        $noFendom = false;
        if($this->checkExpansionDataValue('defileType') && isset($this->requestDefileTypeData[$this->requestExpansionData['defileType']])) {
            $noFendom = $this->requestDefileTypeData[$this->requestExpansionData['defileType']]['fendom'] < 1;
        }
        // defileTitle
        if($this->checkExpansionDataValue('defileTitle')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует название.";
        }
        // fendom
        if($this->checkExpansionDataValue('fendom') || $noFendom) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан Фендом.";
        }
        // audio
        if($this->checkExpansionDataValue('audio')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует музыкальная дорожка.";
        }
        // defileType
        if($this->checkExpansionDataValue('defileType')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан тип.";
        }
        return $checkedUpPoints/$allPoint;
    }
    private function checkRequestProgress_Image() {
        $allPoint = 2;
        $checkedUpPoints = 0;
        // imageTitle
        if($this->checkExpansionDataValue('imageTitle')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует название.";
        }
        // image
        if($this->checkExpansionDataValue('image')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует изображение.";
        }
        return $checkedUpPoints/$allPoint;
    }
    private function checkRequestProgress_Karaoke() {
        $allPoint = 5;
        $checkedUpPoints = 0;
        // songTitle
        if($this->checkExpansionDataValue('songTitle')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует название песни.";
        }
        // artistSongs
        if($this->checkExpansionDataValue('artistSongs')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан исполнитель.";
        }
        // demo audition
        if($this->checkExpansionDataValue('demo') || 
                ($this->checkExpansionDataValue('audition') && $this->requestExpansionData['audition'] > 0)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует демо запись и не назначено прослушивание.";
        }
        // audio instrumental audioInVideo noVideo
        if($this->checkExpansionDataValue('audio') || 
                ($this->checkExpansionDataValue('instrumental') && $this->requestExpansionData['instrumental'] > 0) || 
                ($this->checkExpansionDataValue('audioInVideo') && $this->requestExpansionData['audioInVideo'] > 0 && 
                $this->checkExpansionDataValue('noVideo') && $this->requestExpansionData['noVideo'] < 1)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует музыкальное сопровождение.";
        }
        // video noVideo
        if($this->checkExpansionDataValue('video') || 
                ($this->checkExpansionDataValue('noVideo') && $this->requestExpansionData['noVideo'] > 0)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Заявленное видео не было добавлено.";
        }
        return $checkedUpPoints/$allPoint;
    }
    private function checkRequestProgress_Photo() {
        $allPoint = 4;
        $checkedUpPoints = 0;
        // fendom
        if($this->checkExpansionDataValue('fendom')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан Фендом.";
        }
        // characters
        if($this->checkExpansionDataValue('characters')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан список персонажей.";
        }
        // photographer
        if($this->checkExpansionDataValue('photographer')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан фотограф.";
        }
        // photo1 photo2 photo3 photo4
        if($this->checkExpansionDataValue('photo1') || 
                $this->checkExpansionDataValue('photo2') || 
                $this->checkExpansionDataValue('photo3') || 
                $this->checkExpansionDataValue('photo4')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "не добавлено ни одной фотографии.";
        }
        return $checkedUpPoints/$allPoint;
    }
    private function checkRequestProgress_Scene() {
        $allPoint = 5;
        $checkedUpPoints = 0;
        // sceneTitle
        if($this->checkExpansionDataValue('sceneTitle')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует название.";
        }
        // demo audition
        if($this->checkExpansionDataValue('demo') || 
                ($this->checkExpansionDataValue('audition') && $this->requestExpansionData['audition'] > 0)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует демо запись и не назначено прослушивание.";
        }
        // audio instrumental audioInVideo noVideo
        if($this->checkExpansionDataValue('audio') || 
                ($this->checkExpansionDataValue('instrumental') && $this->requestExpansionData['instrumental'] > 0) || 
                ($this->checkExpansionDataValue('audioInVideo') && $this->requestExpansionData['audioInVideo'] > 0 && 
                $this->checkExpansionDataValue('noVideo') && $this->requestExpansionData['noVideo'] < 1)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует музыкальное сопровождение.";
        }
        // video noVideo
        if($this->checkExpansionDataValue('video') || 
                ($this->checkExpansionDataValue('noVideo') && $this->requestExpansionData['noVideo'] > 0)) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Заявленное видео не было добавлено.";
        }
        // scenario
        if($this->checkExpansionDataValue('scenario')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует сценарий.";
        }
        return $checkedUpPoints/$allPoint;
    }
    private function checkRequestProgress_VideoCosplay() {
        $allPoint = 7;
        $checkedUpPoints = 0;
        // videoCosplayTitle
        if($this->checkExpansionDataValue('videoCosplayTitle')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует название.";
        }
        // fendom
        if($this->checkExpansionDataValue('fendom')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан Фендом.";
        }
        // characters
        if($this->checkExpansionDataValue('characters')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан список персонажей.";
        }
        // musicTracks
        if($this->checkExpansionDataValue('musicTracks')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указаны музыкальыне треки.";
        }
        // programs
        if($this->checkExpansionDataValue('programs')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан список программ.";
        }
        // videographer
        if($this->checkExpansionDataValue('videographer')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Не указан видеооператор.";
        }
        // video
        if($this->checkExpansionDataValue('video')) {
            $checkedUpPoints++;
        } else {
            $this->recommendations[] = "Отсутствует ролик.";
        }
        return $checkedUpPoints/$allPoint;
    }
    
    private function checkExpansionDataValue($key) {
        return isset($this->requestExpansionData[$key]) && 
                $this->requestExpansionData[$key] !== '' && 
                $this->requestExpansionData[$key] !== null;
    }
    
    private function getRequestData() {
        $query = "SELECT "
                . "`contest`, "
                . "`created`, "
                . "`changed`, "
                . "`type`, "
                . "`fest`, "
                . "`status`, "
                . "`numberOfParticipants`, "
                . "`durationMin`, "
                . "`durationSec` "
                . "FROM `JiyuuFestRequest` "
                . "WHERE `request`='".$this->requestID."';";
        $this->requestData = $this->SQL_HELPER->select($query,1);
        
    }
    
    private function getTypeData() {
        $query = "SELECT "
                . "`type`, "
                . "`tableName`, "
                . "`minNumberOfParticipants`, "
                . "`maxNumberOfParticipants`, "
                . "`minDurationMinutes`, "
                . "`minDurationSeconds`, "
                . "`maxDurationMinutes`, "
                . "`maxDurationSeconds`, "
                . "`mayBeContest`, "
                . "`characterName`, "
                . "`photo`, "
                . "`original`, "
                . "`intramural`, "
                . "`subtype` "
                . "FROM `JiyuuFestRequestType` "
                . "WHERE `type`='".$this->requestData['type']."';";
        $this->requestTypeData = $this->SQL_HELPER->select($query,1);
    }
    
    private function getExpansionData() {
        $query = "SELECT * FROM `".$this->requestTypeData['tableName']."` WHERE `request`='".$this->requestID."';";
        $this->requestExpansionData = $this->SQL_HELPER->select($query,1);
        if($this->checkExpansionDataValue('defileType') && isset($this->requestDefileTypeData[$this->requestExpansionData['defileType']])) {
            $this->requestTypeData['photo'] = $this->requestDefileTypeData[$this->requestExpansionData['defileType']]['photo'];
            $this->requestTypeData['original'] = $this->requestDefileTypeData[$this->requestExpansionData['defileType']]['original'];
            $this->requestTypeData['characterName'] = $this->requestDefileTypeData[$this->requestExpansionData['defileType']]['characterName'];
        }
    }
    
    private function getUsersData() {
        $query = "SELECT 
            JFRU.`request`, 
            JFRU.`user`, 
            Us.`nickname`, 
            JFRU.`confirmed`, 
            JFRU.`characterName`, 
            JFRU.`photo`, 
            JFRU.`original` 
            FROM 
            `JiyuuFestRequestUsers` as JFRU left join `Users` as Us
            on JFRU.`user` = Us.`login`
            where `request`='".$this->requestID."';";
        $this->requestUsersData = $this->SQL_HELPER->select($query);
    }
    
    private function getDefileTypeData() {
        $query = "SELECT "
                . "`type`, "
                . "`fendom`, "
                . "`characterName`, "
                . "`photo`, "
                . "`original`, "
                . "`collage` "
                . "FROM `JiyuuFestRequest_DefileType`;";
        $rezult = $this->SQL_HELPER->select($query);
        foreach ($rezult as $value) {
            $this->requestDefileTypeData[$value['type']] = $value;
        }
    }
    
    private function generateProgressBar() {
        $errorsAmount = count($this->errors);
        $recommendationsAmount = count($this->recommendations);
        $errorId = 'RequestErrors_'.$this->requestID;
        $recommendationId = 'RequestRecommendations_'.$this->requestID;
        $this->progressBarHtml = '';
        $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBarWrapper">';
        $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBar">';
        $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBarRuler">';
        $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBarRulerMark" style="width: '.$this->getReadyPercentage().'%;"></div>';
        $this->progressBarHtml .= '</div>';
        $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBarValue">'.$this->getReadyPercentage().'%</div>';
        $this->progressBarHtml .= '</div>';
        $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBarStatistics">';
        if($errorsAmount > 0) {
            $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBarErrors">';
            $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBarErrorsButton">';
            $this->progressBarHtml .= '<a class="fancybox-doc" href="#'.$errorId.'" title="Ошибки">Ошибки ('.$errorsAmount.')</a>';
            $this->progressBarHtml .= '</div>';
            $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBarErrorsList" id="'.$errorId.'">';
            $this->progressBarHtml .= '<ul class="JiyuuFestRequestProgressBarErrorsListElements">';
            foreach ($this->errors as $error) {
                $this->progressBarHtml .= '<li class="JiyuuFestRequestProgressBarErrorsListElement">'.$error.'</li>';
            } 
            $this->progressBarHtml .= '</ul>';
            $this->progressBarHtml .= '</div>';
            $this->progressBarHtml .= '</div>';
        }
        if($recommendationsAmount > 0) {
            $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBarRecommendations">';
            $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBarRecommendationsButton">';
            $this->progressBarHtml .= '<a class="fancybox-doc" href="#'.$recommendationId.'" title="Рекомендации">Рекомендации ('.$recommendationsAmount.')</a>';
            $this->progressBarHtml .= '</div>';
            $this->progressBarHtml .= '<div class="JiyuuFestRequestProgressBarRecommendationsList" id="'.$recommendationId.'">';
            $this->progressBarHtml .= '<ul class="JiyuuFestRequestProgressBarRecommendationsListElements">';
            foreach ($this->recommendations as $recommendation) {
                $this->progressBarHtml .= '<li class="JiyuuFestRequestProgressBarRecommendationsListElement">'.$recommendation.'</li>';
            } 
            $this->progressBarHtml .= '</ul>';
            $this->progressBarHtml .= '</div>';
            $this->progressBarHtml .= '</div>';
        }
        $this->progressBarHtml .= '</div>';
        $this->progressBarHtml .= '</div>';
    }
    
    public function getProgressBarHtml() {
        return $this->progressBarHtml;
    }
    
    public function getReady() {
        return $this->ready;
    }
    
    public function getReadyPercentage() {
        return $this->ready*100;
    }
    
    public function isReady() {
        return $this->ready >= 1;
    }
}
