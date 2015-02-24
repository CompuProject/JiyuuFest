<?php
class JRequestVideoCosplay extends JRequest{
    
    public function __construct() {
        $this->jRequestType = "VideoCosplay";
        parent::__construct();
    }
    
    protected function generateOthernessForm() {
        // title        
        $title = $this->inputHelper->textBox('title', 'title', 'title', 200, true, $this->insertValue['title']);
        $this->form .= $this->createLocalizationFormRow($title, true, 'titleVideoCosplay');
        // videographer        
        $videographer= $this->inputHelper->textBox('videographer', 'videographer', 'videographer', 200, true, $this->insertValue['videographer']);
        $this->form .= $this->createLocalizationFormRow($videographer, true, 'videographer','videographerInfo');
        // fendomTitle        
        $fendomTitle = $this->inputHelper->textBox('fendomTitle', 'fendomTitle', 'fendomTitle', 200, true, $this->insertValue['fendomTitle']);
        $this->form .= $this->createLocalizationFormRow($fendomTitle, true, 'fendomTitle');
        // musicTracks
        $musicTracks = $this->inputHelper->textarea("musicTracks", "musicTracks", "musicTracks", 600, true, $this->insertValue['musicTracks']);
        $this->form .= $this->createLocalizationFormRow($musicTracks, true, 'musicTracksVideoCosplay', 'musicTracksInfoVideoCosplay');
        // programs
        $programs = $this->inputHelper->textarea("programs", "programs", "programs", 600, true, $this->insertValue['programs']);
        $this->form .= $this->createLocalizationFormRow($programs, true, 'programsVideoCosplay', 'programsInfoVideoCosplay');
    }

    /**
     * Очистка значений формы
     */
    protected function clearInsertValueArray() {
        parent::clearInsertValueArray();
        $this->insertValue['fendomTitle'] = null;
        $this->insertValue['title'] = null;
        $this->insertValue['videographer'] = null;
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
        $this->insertValue['videographer'] = $this->getPostValue('videographer');
        $this->insertValue['musicTracks'] = $this->getPostValue('musicTracks');
        $this->insertValue['programs'] = $this->getPostValue('programs');
    }
    
    protected function checkAllValue() {
        return (
                $this->checkValue('fendomTitle') && 
                $this->checkValue('title') &&
                $this->checkValue('videographer') && 
                $this->checkValue('musicTracks') && 
                $this->checkValue('programs') && 
                $this->checkMainValue()
        );
    }
    
    protected function generateJRequestQueryArray() {
        parent::generateJRequestQueryArray();
        $query = "INSERT INTO `JRequestVideoCosplay` SET ";
        $query .= "`id`='".$this->requestID."', ";
        $query .= "`title`='".$this->insertValue['title']."', ";
        $query .= "`videographer`='".$this->insertValue['videographer']."', ";
        $query .= "`fendomTitle`='".$this->insertValue['fendomTitle']."', ";
        $query .= "`musicTracks`='".$this->insertValue['musicTracks']."', ";
        $query .= "`programs`='".$this->insertValue['programs']."';";
        $this->addInQueryArray($query);
    }
}
?>