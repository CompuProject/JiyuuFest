<?php
class JRequestPhoto extends JRequest{
    
    public function __construct() {
        $this->jRequestType = "Photo";
        $this->durationYes = false;
        $this->kosbendTitleYes = false;
        parent::__construct();
    }
    
    protected function generateOthernessForm() {
        // title        
        $title = $this->inputHelper->textBox('title', 'title', 'title', 200, true, $this->insertValue['title']);
        $this->form .= $this->createLocalizationFormRow($title, true, 'titlePhoto');
        // photographer        
        $photographer = $this->inputHelper->textBox('photographer', 'photographer', 'photographer', 200, true, $this->insertValue['photographer']);
        $this->form .= $this->createLocalizationFormRow($photographer, true, 'photographer');
    }

    /**
     * Очистка значений формы
     */
    protected function clearInsertValueArray() {
        parent::clearInsertValueArray();
        $this->insertValue['title'] = null;
        $this->insertValue['photographer'] = null;
    }
    
    /**
     * Инициализация значений формы
     */
    protected function getInsertValueArray() {
        parent::getInsertValueArray();
        $this->insertValue['title'] = $this->getPostValue('title');
        $this->insertValue['photographer'] = $this->getPostValue('photographer');
    }
    
    /*public function getForm() {
        return "Форма заявки в разработке, прием заявок будет открыт позже";
    }*/
    
    protected function checkAllValue() {
        return (
                $this->checkValue('title') && 
                $this->checkValue('photographer') &&
                $this->checkMainValue()
        );
    }
    
    protected function generateJRequestQueryArray() {
        parent::generateJRequestQueryArray();
        $query = "INSERT INTO `JRequestPhoto` SET ";
        $query .= "`id`='".$this->requestID."', ";
        $query .= "`title`='".$this->insertValue['title']."',";
        $query .= "`photographer`='".$this->insertValue['photographer']."';";
        $this->addInQueryArray($query);
    }
}

?>