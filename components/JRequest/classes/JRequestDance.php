<?php
class JRequestDance extends JRequest{
    
    public function __construct() {
        $this->jRequestType = "Dance";
        $this->maxNumberOfParticipants = 15;
        parent::__construct();
    }
    
    protected function generateOthernessForm() {
        // songTitle
        $songTitle = $this->inputHelper->textBox('songTitle', 'songTitle', 'songTitle', 200, true, $this->insertValue['songTitle']);
        $this->form .= $this->createLocalizationFormRow($songTitle, true, 'songTitleDance','songTitleInfoDance');
        // artistSongs
        $artistSongs = $this->inputHelper->textBox('artistSongs', 'artistSongs', 'artistSongs', 200, true, $this->insertValue['artistSongs']);
        $this->form .= $this->createLocalizationFormRow($artistSongs, true, 'artistSongsDance','artistSongsInfoDance');
        // sceneryAndProps
        $sceneryAndProps = $this->inputHelper->textarea("sceneryAndProps", "sceneryAndProps", "sceneryAndProps", 600, true, $this->insertValue['sceneryAndProps']);
        $this->form .= $this->createLocalizationFormRow($sceneryAndProps, true, 'sceneryAndProps', 'sceneryAndPropsInfo');
    }

    /**
     * Очистка значений формы
     */
    protected function clearInsertValueArray() {
        parent::clearInsertValueArray();
        $this->insertValue['songTitle'] = null;
        $this->insertValue['artistSongs'] = null;
        $this->insertValue['sceneryAndProps'] = null;
    }
    
    /**
     * Инициализация значений формы
     */
    protected function getInsertValueArray() {
        parent::getInsertValueArray();
        $this->insertValue['songTitle'] = $this->getPostValue('songTitle');
        $this->insertValue['artistSongs'] = $this->getPostValue('artistSongs');
        $this->insertValue['sceneryAndProps'] = $this->getPostValue('sceneryAndProps');
    }
    
    protected function checkAllValue() {
        return (
                $this->checkValue('songTitle') && 
                $this->checkValue('artistSongs') &&
                $this->checkValue('sceneryAndProps') && 
                $this->checkMainValue()
        );
    }
    
    protected function generateJRequestQueryArray() {
        parent::generateJRequestQueryArray();
        $query = "INSERT INTO `JRequestDance` SET ";
        $query .= "`id`='".$this->requestID."', ";
        if(isset($this->insertValue['sceneryAndProps']) && 
                $this->insertValue['sceneryAndProps']!=null && 
                $this->insertValue['sceneryAndProps']!="") {
            $query .= "`sceneryAndProps`='".$this->insertValue['sceneryAndProps']."', ";
        }
        $query .= "`songTitle`='".$this->insertValue['songTitle']."', ";
        $query .= "`artistSongs`='".$this->insertValue['artistSongs']."';";
        $this->addInQueryArray($query);
    }
    
}
?>