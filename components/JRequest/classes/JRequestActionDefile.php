<?php
class JRequestActionDefile extends JRequest{
    
    public function __construct() {
        $this->jRequestType = "ActionDefile";
        $this->maxNumberOfParticipants = 6;
        $this->characterNameYes=true;
        parent::__construct();
        
    }
    
    protected function generateOthernessForm() {
        // title        
        $title = $this->inputHelper->textBox('title', 'title', 'title', 200, true, $this->insertValue['title']);
        $this->form .= $this->createLocalizationFormRow($title, true, 'titleActionDefile');
        
        // fendomTitle        
        $fendom = $this->inputHelper->textBox('fendomTitle', 'fendomTitle', 'fendomTitle', 200, true, $this->insertValue['fendomTitle']);
        $this->form .= $this->createLocalizationFormRow($fendom, true, 'fendomTitle');
    }

    /**
     * Очистка значений формы
     */
    protected function clearInsertValueArray() {
        parent::clearInsertValueArray();
        $this->insertValue['title'] = null;
        $this->insertValue['fendomTitle'] = null;
    }
    
    /**
     * Инициализация значений формы
     */
    protected function getInsertValueArray() {
        parent::getInsertValueArray();
        $this->insertValue['title'] = $this->getPostValue('title');
        $this->insertValue['fendomTitle'] = $this->getPostValue('fendomTitle');
    }
    
    protected function checkAllValue() {
        return (
                $this->checkValue('title') && 
                $this->checkValue('characterName') &&
                $this->checkValue('fendomTitle') && 
                $this->checkMainValue()
        );
    }
    
    protected function generateJRequestQueryArray() {
        parent::generateJRequestQueryArray();
        $query = "INSERT INTO `JRequestActionDefile` SET ";
        $query .= "`id`='".$this->requestID."', ";
        $query .= "`fendomTitle`='".$this->insertValue['fendomTitle']."', ";
        $query .= "`title`='".$this->insertValue['title']."';";
        $this->addInQueryArray($query);
    }
}

?>