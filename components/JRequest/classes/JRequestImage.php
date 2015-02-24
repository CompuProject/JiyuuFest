<?php
class JRequestImage extends JRequest{
    
    public function __construct() {
        $this->jRequestType = "Image";
        $this->durationYes = false;
        $this->kosbendTitleYes = false;
        parent::__construct();
    }
    
    protected function generateOthernessForm() {
        // title        
        $title = $this->inputHelper->textBox('title', 'title', 'title', 200, true, $this->insertValue['title']);
        $this->form .= $this->createLocalizationFormRow($title, true, 'titleImage');
    }

    /**
     * Очистка значений формы
     */
    protected function clearInsertValueArray() {
        parent::clearInsertValueArray();
        $this->insertValue['title'] = null;
    }
    
    /**
     * Инициализация значений формы
     */
    protected function getInsertValueArray() {
        parent::getInsertValueArray();
        $this->insertValue['title'] = $this->getPostValue('title');
    }
    
    /*public function getForm() {
        return "Форма заявки в разработке, прием заявок будет открыт позже";
    }*/
    
    protected function checkAllValue() {
        return (
                $this->checkValue('title') &&
                $this->checkMainValue()
        );
    }
    
    protected function generateJRequestQueryArray() {
        parent::generateJRequestQueryArray();
        $query = "INSERT INTO `JRequestImage` SET ";
        $query .= "`id`='".$this->requestID."', ";
        $query .= "`title`='".$this->insertValue['title']."';";
        $this->addInQueryArray($query);
    }
}

?>