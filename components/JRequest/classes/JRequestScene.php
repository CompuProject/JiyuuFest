<?php
class JRequestScene extends JRequest{
    
    public function __construct() {
        $this->jRequestType = "Scene";
        $this->maxNumberOfParticipants = 20;
        parent::__construct();
    }
    
    protected function generateOthernessForm() {
        // title        
        $title = $this->inputHelper->textBox('title', 'title', 'title', 200, true, $this->insertValue['title']);
        $this->form .= $this->createLocalizationFormRow($title, true, 'titleScene');
        // fendomTitle        
        $fendomTitle = $this->inputHelper->textBox('fendomTitle', 'fendomTitle', 'fendomTitle', 200, false, $this->insertValue['fendomTitle']);
        $this->form .= $this->createLocalizationFormRow($fendomTitle, false, 'fendomTitle');
        // characterName        
        $characterName = $this->inputHelper->textBox('characterName', 'characterName', 'characterName', 200, true, $this->insertValue['characterName']);
        $this->form .= $this->createLocalizationFormRow($characterName, true, 'characterName','characterNameInfo');
    }

    /**
     * Очистка значений формы
     */
    protected function clearInsertValueArray() {
        parent::clearInsertValueArray();
        $this->insertValue['title'] = null;
        $this->insertValue['fendomTitle'] = null;
        $this->insertValue['characterName'] = null;
    }
    
    /**
     * Инициализация значений формы
     */
    protected function getInsertValueArray() {
        parent::getInsertValueArray();
        $this->insertValue['title'] = $this->getPostValue('title');
        $this->insertValue['characterName'] = $this->getPostValue('characterName');
        $this->insertValue['fendomTitle'] = $this->getPostValue('fendomTitle');
    }
    
    protected function checkAllValue() {
        return (
                $this->checkValue('title') && 
                $this->checkValue('characterName') &&
                $this->checkMainValue()
        );
    }
    
    protected function generateJRequestQueryArray() {
        parent::generateJRequestQueryArray();
        $query = "INSERT INTO `JRequestScene` SET ";
        $query .= "`id`='".$this->requestID."', ";
        if(isset($this->insertValue['fendomTitle']) && 
                $this->insertValue['fendomTitle']!=null && 
                $this->insertValue['fendomTitle']!="") {
            $query .= "`fendomTitle`='".$this->insertValue['fendomTitle']."', ";
        }
        $query .= "`title`='".$this->insertValue['title']."';";
        $this->addInQueryArray($query);
    }
}

?>