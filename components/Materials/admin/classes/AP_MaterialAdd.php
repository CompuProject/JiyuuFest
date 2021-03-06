<?php

class AP_MaterialAdd extends AdminPanel_ComponentPanelUI_Element_Add {
    
    private $dir = './resources/Components/Materials/';

    protected function getElementID() {
        return $this->insertValue['alias'];
    }
    
    /**
     * Получение значений
     */
    protected function getAllValue() {
        parent::getAllValue();
        $this->insertValue['alias'] = parent::getPostValue('alias');
        $this->insertValue['created'] = parent::getPostValue('created');
        $this->insertValue['lastChange'] = parent::getPostValue('lastChange');
        $this->insertValue['showTitle'] = parent::getPostValue('showTitle');
        $this->insertValue['showCreated'] = parent::getPostValue('showCreated');
        $this->insertValue['showChange'] = parent::getPostValue('showChange');
        if(isset($_POST['title']) && $_POST['title']!=null && $_POST['title']!="") {
            foreach ($_POST['title'] as $key => $value) {
                $this->insertValue['title'][$key] = parent::getMysqlText($value);
            }
        }
        if(isset($_POST['text']) && $_POST['text']!=null && $_POST['text']!="") {
            foreach ($_POST['text'] as $key => $value) {
                $this->insertValue['text'][$key] = parent::getMysqlText($value);
            }
        }
        if(isset($_POST['categories']) && $_POST['categories']!=null && $_POST['categories']!="") {
            $this->insertValue['categories'] = $_POST['categories'];
        } else {
            $this->insertValue['categories'] = array();
        }
    }
    
    /**
     * Проверка значений
     */
    protected function checkAllValue() {         
        parent::checkAllValue();
        $error = false;
        if(!$this->checkValue('alias',"/^[A-Za-z0-9_-]{3,200}+$/u")) {
            $error = true;
            $this->checkAllValueErrors[] = "Разрешены латинские буквы, цифры и щаник тире и нижнее подчеркивание";
        }
        if(!$this->checkAlias()) {
            $error = true;
            $this->checkAllValueErrors[] = "Такой псевдоним уже используется";
        }
        if(!$this->checkValue('created',"/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/")) {
            $error = true;
            $this->checkAllValueErrors[] = "Заполните поле в формате ГГГГ-ММ-ДД чч:мм:сс";
        }
        if(!$this->checkValue('lastChange',"/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/")) {
            $error = true;
            $this->checkAllValueErrors[] = "Заполните поле в формате ГГГГ-ММ-ДД чч:мм:сс";
        }
        if(!$this->checkValue('showTitle',"/^[0-1]{1}$/")) {
            $error = true;
            $this->checkAllValueErrors[] = "Выберите значение";
        }
        if(!$this->checkValue('showCreated',"/^[0-1]{1}$/")) {
            $error = true;
            $this->checkAllValueErrors[] = "Выберите значение";
        }
        if(!$this->checkValue('showChange',"/^[0-1]{1}$/")) {
            $error = true;
            $this->checkAllValueErrors[] = "Выберите значение";
        }
        if (!isset($_FILES['fileImage'])) {
            $error = true;
            $this->checkAllValueErrors[] = "Загрузите изображение";
        }
        $local = false;
        foreach ($this->langArray as $langData) {
            if(isset($_POST['title'][$langData['lang']]) && $_POST['title'][$langData['lang']]!=null && $_POST['title'][$langData['lang']]!="" &&
                isset($_POST['text'][$langData['lang']]) && $_POST['text'][$langData['lang']]!=null && $_POST['text'][$langData['lang']]!="") {
                $local = true;
            }
        }
        if(!$local){
            $error = true;
            $this->checkAllValueErrors[] = "Хотя бы для одного языка должны быть заполнены текстовые поля";
        }
        return !$error;
    }
    
    /**
     * Установка значений ввода по умолчанию
     */
    protected function setDefaltInput() { 
        parent::setDefaltInput();
        $this->insertValue['alias'] = parent::getOriginalPostValue('alias');
        $this->insertValue['created'] = date("Y-m-d h:i:s");
        $this->insertValue['lastChange'] = date("Y-m-d h:i:s");
        $this->insertValue['showTitle'] = "1";
        $this->insertValue['showCreated'] = "0";
        $this->insertValue['showChange'] = "0";
        $this->insertValue['title'] = array();
        $this->insertValue['text'] = array();
        foreach ($this->langArray as $langData) {
            $this->insertValue['title'][$langData['lang']] = "";
            $this->insertValue['text'][$langData['lang']] = "";
        }
        if(isset($_POST['title']) && $_POST['title']!=null && $_POST['title']!="") {
            foreach ($_POST['title'] as $key => $value) {
                $this->insertValue['title'][$key] = $value;
            }
        }
        if(isset($_POST['text']) && $_POST['text']!=null && $_POST['text']!="") {
            foreach ($_POST['text'] as $key => $value) {
                $this->insertValue['text'][$key] = $value;
            }
        }
        
        if(isset($_POST['categories']) && $_POST['categories']!=null && $_POST['categories']!="") {
            $this->insertValue['categories'] = $_POST['categories'];
        } else {
            $this->insertValue['categories'] = array();
        }
        $this->originalInsertValue = $this->insertValue;
    }
    
    /**
     * Выполнение вставки в таблицы
     */
    protected function insertExecute() {
        parent::insertExecute();
        $queryMaterials = "INSERT INTO `Materials` SET ";
        $queryMaterials .= "`alias`='".$this->insertValue['alias']."', ";
        $queryMaterials .= "`created`='".$this->insertValue['created']."', ";
        $queryMaterials .= "`lastChange`='".$this->insertValue['lastChange']."', ";
        $queryMaterials .= "`showTitle`='".$this->insertValue['showTitle']."', ";
        $queryMaterials .= "`showCreated`='".$this->insertValue['showCreated']."', ";
        $queryMaterials .= "`showChange`='".$this->insertValue['showChange']."';";
        $queryMaterials_Lang = array();
        foreach ($this->langArray as $langData) {
            if(isset($this->insertValue['title'][$langData['lang']]) && $this->insertValue['title'][$langData['lang']]!=null && 
                    $this->insertValue['title'][$langData['lang']]!="" && isset($this->insertValue['text'][$langData['lang']]) && 
                    $this->insertValue['text'][$langData['lang']]!=null && $this->insertValue['text'][$langData['lang']]!="") {
                $query = "INSERT INTO `Materials_Lang` SET ";
                $query .= "`material`='".$this->insertValue['alias']."', ";
                $query .= "`lang`='".$langData['lang']."', ";
                $query .= "`title`='".$this->insertValue['title'][$langData['lang']]."', ";
                $query .= "`text`='".$this->insertValue['text'][$langData['lang']]."';";
                $queryMaterials_Lang[] = $query;
            }
        }
        $queryMaterialsInCategories = array();
        if($this->insertValue['categories']!=null) {
            foreach ($this->insertValue['categories'] as $category) {
                $query = "INSERT INTO `MaterialsInCategories` SET ";
                $query .= "`material`='".$this->insertValue['alias']."', ";
                $query .= "`category`='".$category."';";
                $queryMaterialsInCategories[]=$query;
            }
        }
        $this->SQL_HELPER->insert($queryMaterials);
        foreach($queryMaterials_Lang as $queryMaterials_Lg) {
            $this->SQL_HELPER->insert($queryMaterials_Lg);
        }
        foreach($queryMaterialsInCategories as $queryMaterialsInCategory) {
            $this->SQL_HELPER->insert($queryMaterialsInCategory);
        }
        $this->uploadFile();
    }
    
    /**
     * Генерация форм ввода не зависимых от языка
     */
    protected function getInputBlocks() { 
        $html = parent::getInputBlocks();
        // alias
        $alias = $this->inputHelper->paternTextBox('alias', 'alias', 'alias', 200, true, 'Латиница, цифры и знаки - и _', '[A-Za-z0-9_-]{3,200}', $this->originalInsertValue['alias']);
        $html .= $this->inputHelper->createFormRow($alias, true, 'Alias');
        // created
        $created = $this->inputHelper->paternTextBox('created', 'created', 'created', 25, true, 'ГГГГ-ММ-ДД чч:мм:сс', '[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}', $this->originalInsertValue['created']);
        $html .= $this->inputHelper->createFormRow($created, true, 'Создано');
        // created
        $lastChange = $this->inputHelper->paternTextBox('lastChange', 'lastChange', 'lastChange', 25, true, 'ГГГГ-ММ-ДД чч:мм:сс', '[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}', $this->originalInsertValue['lastChange']);
        $html .= $this->inputHelper->createFormRow($lastChange, true, 'Изменено');
        // showTitle
        $showTitle = $this->inputHelper->select('showTitle', 'showTitle', $this->yes_no, true, $this->originalInsertValue['showTitle']);
        $html .= $this->inputHelper->createFormRow($showTitle, true, 'Показывать заголовок');
        // showCreated
        $showCreated = $this->inputHelper->select('showCreated', 'showCreated', $this->yes_no, true, $this->originalInsertValue['showCreated']);
        $html .= $this->inputHelper->createFormRow($showCreated, true, 'Показывать дату создания');
        // showChange
        $showChange = $this->inputHelper->select('showChange', 'showChange', $this->yes_no, true, $this->originalInsertValue['showChange']);
        $html .= $this->inputHelper->createFormRow($showChange, true, 'Показывать дату изменения');
        // image
        $image = '<input type="file" class="fileImage" name="fileImage" id="fileImage">';
        $html .= $this->inputHelper->createFormRow($image, true, 'Изображение');
        // categories
        $categories = $this->inputHelper->getChekBoxGroup('categories', 'categories', $this->getMaterialsCategories(), false, $this->originalInsertValue['categories']);
        $html .= $this->inputHelper->createFormRow($categories, false, 'Категории');
        return $html;
    }
    
    /**
     * Генерация форм ввода зависимых от языка
     */
    protected function getInputLangBlocks($lang) {
        $html = parent::getInputLangBlocks($lang);
        // title
        $title = $this->inputHelper->textBox('title['.$lang.']', 'title', 'title', 200, false, $this->originalInsertValue['title'][$lang]);
        $html .= $this->inputHelper->createFormRow($title, false, 'Заголовок');
        // text
        $text = $this->inputHelper->textarea('text['.$lang.']', 'text', 'text', 50000, false, $this->originalInsertValue['text'][$lang]);
        $html .= $this->inputHelper->createFormRow($text, false, 'Текст');
        return $html;
    }
    
    /* Новые функции */
    
    private function uploadFile() {
        if (isset($_POST['AP_Submit']) && $_POST['AP_Submit'] != '' && $_POST['AP_Submit'] != null) {
            // Проверяем загружен ли файл
            if(is_uploaded_file($_FILES["fileImage"]["tmp_name"])) {
              // Если файл загружен успешно, перемещаем его из временной директории в конечную
                move_uploaded_file($_FILES["fileImage"]["tmp_name"], $this->dir.$this->insertValue['alias'].".png");
            } else {
               echo "Ошибка загрузки файла";
            }
        } else {
            echo "Выберите файл";
        }
    }
    
    private function getMaterialsCategories() {
        $categories = array();
        $query = "SELECT * FROM  `MaterialsCategories`;";
        $result = $this->SQL_HELPER->select($query);
        foreach ($result as $key => $value) {
            $categories[$key]['text']=$this->getMaterialCategoriesDataText($value['alias']);
            $categories[$key]['value']=$value['alias'];
            $categories[$key]['checked']="0";
        }
        return $categories;
    }
    
    private function checkAlias() {
        $result = array();
        if(isset($_POST['alias']) && $_POST['alias']!=null && $_POST['alias']!="") {
            $query = "SELECT * FROM `Materials` WHERE `alias`='".$_POST['alias']."';";
            $result = $this->SQL_HELPER->select($query,1);
        }
        return $result == null;
    }
    
    private function getMaterialCategoriesDataText($category) {
        $title = "";
        $this->langHelper = new LangHelper("MaterialsCategories_Lang","lang","category",$category,$this->thisLang);
        $this->langType = $this->langHelper->getLangType();
        if($this->langType != -1){
            $langData = $this->langHelper->getLangData();
            $title = $langData["name"];
        }
        return $title;
    } 
}