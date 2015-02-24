<?php
class JRequestAMV extends JRequest{
    
    public function __construct() {
        $this->jRequestType = "AMV";
        parent::__construct();
    }
    
//    protected function generateForm() {
//        parent::generateForm();
//        $this->form .= '<form class="JRequestForm" name="JRequestForm" action="'.$this->urlHelper->getThisPage().'" 
//            method="post" accept-charset="UTF-8" autocomplete="on">';
//        $this->form .= '<center>';
//        $this->form .= '<table class="JRequestFormTable" >';
//        
//        // contest
//        $contestArray = array();
//        $contestArray[0]['value'] = 1;
//        $contestArray[0]['text'] = $this->localization->getText("contest");
//        $contestArray[1]['value'] = 0;
//        $contestArray[1]['text'] = $this->localization->getText("noContest");
//        $contest = $this->inputHelper->select('contest', 'contest', $contestArray, true, $this->insertValue['contest']==null?1:$this->insertValue['contest']);
//        $this->form .= $this->createLocalizationFormRow($contest, true, 'contestLable');
//        
//        // participant
//        $participantsArray = array();
//        for($i=1; $i <= $this->maxNumberOfParticipants; $i++) {
//            $participantsArray[$i]['value'] = $i;
//            $participantsArray[$i]['text'] = $i;
//        }
//        $participant = $this->inputHelper->select('participant', 'participant', $contestArray, true, $this->insertValue['participant']);
//        $this->form .= $this->createLocalizationFormRow($participant, true, 'participant');
//        
//        // duration
//        $durationPatern = $this->localization->getText("durationPatern");
//        $duration = $this->inputHelper->paternTextBox("duration", "duration", "duration", 11, true, $durationPatern, "[0-9]{1,11}", $this->insertValue['duration']);
//        $this->form .= $this->createLocalizationFormRow($duration, true, 'duration','durationInfo');
//        
//        // kosbendTitle
//        $kosbendTitle = $this->inputHelper->textBox('kosbendTitle', 'kosbendTitle', 'kosbendTitle', 150, false, $this->insertValue['kosbendTitle']);
//        $this->form .= $this->createLocalizationFormRow($kosbendTitle, false, 'kosbendTitle','kosbendTitleInfo');
//        
//        // title        
//        $title = $this->inputHelper->textBox('title', 'title', 'title', 200, true, $this->insertValue['title']);
//        $this->form .= $this->createLocalizationFormRow($title, true, 'titleAMV');
//        
//        // fendomTitle        
//        $fendomTitle = $this->inputHelper->textarea("fendomTitle", "fendomTitle", "fendomTitle", 600, true, $this->insertValue['fendomTitle']);
//        $this->form .= $this->createLocalizationFormRow($fendomTitle, true, 'fendomTitles', 'fendomTitlesInfo');
//        
//        // musicTracks
//        $musicTracks = $this->inputHelper->textarea("musicTracks", "musicTracks", "musicTracks", 600, true, $this->insertValue['musicTracks']);
//        $this->form .= $this->createLocalizationFormRow($musicTracks, true, 'musicTracksAMV', 'musicTracksInfoAMV');
//        
//        // programs
//        $programs = $this->inputHelper->textarea("programs", "programs", "programs", 600, true, $this->insertValue['programs']);
//        $this->form .= $this->createLocalizationFormRow($programs, true, 'programsAMV', 'programsInfoAMV');
//        
//        $this->form .= '</table>';
//        $this->generateRules();
//        $this->form .= '<center>';
//        $this->form .= '<input class="JRequestFormButton" type="submit" name="JRequestFormSubmit" value="'.$this->localization->getText("JRequestFormButtonText").'">';
//        $this->form .= '</form>';
//    }
    
    protected function generateOthernessForm() {
        // title        
        $title = $this->inputHelper->textBox('title', 'title', 'title', 200, true, $this->insertValue['title']);
        $this->form .= $this->createLocalizationFormRow($title, true, 'titleAMV');
        
        // fendomTitle        
        $fendomTitle = $this->inputHelper->textarea("fendomTitle", "fendomTitle", "fendomTitle", 600, true, $this->insertValue['fendomTitle']);
        $this->form .= $this->createLocalizationFormRow($fendomTitle, true, 'fendomTitles', 'fendomTitlesInfo');
        
        // musicTracks
        $musicTracks = $this->inputHelper->textarea("musicTracks", "musicTracks", "musicTracks", 600, true, $this->insertValue['musicTracks']);
        $this->form .= $this->createLocalizationFormRow($musicTracks, true, 'musicTracksAMV', 'musicTracksInfoAMV');
        
        // programs
        $programs = $this->inputHelper->textarea("programs", "programs", "programs", 600, true, $this->insertValue['programs']);
        $this->form .= $this->createLocalizationFormRow($programs, true, 'programsAMV', 'programsInfoAMV');
    }

    /**
     * Очистка значений формы
     */
    protected function clearInsertValueArray() {
        parent::clearInsertValueArray();
        $this->insertValue['fendomTitle'] = null;
        $this->insertValue['title'] = null;
        $this->insertValue['musicTracks'] = null;
        $this->insertValue['programs'] = null;
    }
    
    /**
     * Инициализация значений формы
     */
    protected function getInsertValueArray() {
        parent::getInsertValueArray();
        $this->insertValue['fendomTitle'] = $this->getPostValue('fendomTitle');
        $this->insertValue['title'] = $this->getPostValue('title');
        $this->insertValue['musicTracks'] = $this->getPostValue('musicTracks');
        $this->insertValue['programs'] = $this->getPostValue('programs');
    }
    
    protected function checkAllValue() {
        return (
                $this->checkValue('fendomTitle') && 
                $this->checkValue('title') &&
                $this->checkValue('musicTracks') && 
                $this->checkValue('programs') && 
                $this->checkMainValue()
        );
    }
    
    protected function generateJRequestQueryArray() {
        parent::generateJRequestQueryArray();
        $query = "INSERT INTO `JRequestAMV` SET ";
        $query .= "`id`='".$this->requestID."', ";
        $query .= "`title`='".$this->insertValue['title']."', ";
        $query .= "`fendomTitle`='".$this->insertValue['fendomTitle']."', ";
        $query .= "`musicTracks`='".$this->insertValue['musicTracks']."', ";
        $query .= "`programs`='".$this->insertValue['programs']."';";
        $this->addInQueryArray($query);
    }
}
?>