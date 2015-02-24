<?php
/**
 * Description of JiyuuFestRequest_CreateRequest_Photo
 *
 * @author maxim
 */
class JiyuuFestRequest_CreateRequest_Photo extends JiyuuFestRequest_CreateRequest {
    
    protected function generateFormInputElementsHtml() {
        $out = parent::generateFormInputElementsHtml();
        // photoTitle
        $photoTitle = $this->inputHelper->textBox('photoTitle', 'photoTitle', 'photoTitle', 100, false, $this->getInsertData('photoTitle'));
        $out .= $this->inputHelper->createFormRow($photoTitle, false, $this->localization->getText("photoTitle"));
        // fendom
        $fendom = $this->inputHelper->textBox('fendom', 'fendom', 'fendom', 100, true, $this->getInsertData('fendom'));
        $out .= $this->inputHelper->createFormRow($fendom, true, $this->localization->getText("fendom"));
        // characters
        $characters = $this->inputHelper->textarea('characters', 'characters', 'characters', 1000, true, $this->getInsertData('characters'));
        $out .= $this->inputHelper->createFormRow($characters, true, $this->localization->getText("characters"));
        // photographer
        $photographer = $this->inputHelper->textBox('photographer', 'photographer', 'photographer', 100, true, $this->getInsertData('photographer'));
        $out .= $this->inputHelper->createFormRow($photographer, true, $this->localization->getText("photographer"));
        // $photo
        $photo1 = $this->inputHelper->loadFiles('photo1', 'photo1', 'photo1', false, false, $this->mimeType['img']);
        $photo2 = $this->inputHelper->loadFiles('photo2', 'photo2', 'photo2', false, false, $this->mimeType['img']);
        $photo3 = $this->inputHelper->loadFiles('photo3', 'photo3', 'photo3', false, false, $this->mimeType['img']);
        $photo4 = $this->inputHelper->loadFiles('photo4', 'photo4', 'photo4', false, false, $this->mimeType['img']);
        $photo = '<div>'.$photo1.'</div><div>'.$photo2.'</div><div>'.$photo3.'</div><div>'.$photo4.'</div>';
        $photo_info = $this->localization->getText("loadFileNowOrLater")."<br><br>".$this->localization->getText("loadFile15MB");
        $out .= $this->inputHelper->createFormRow($photo, true, $this->localization->getText("image"), $photo_info);
        return $out;
    }
    
    
    protected function apdateOthersInsertData() {
        parent::apdateOthersInsertData();
    }
    
    
    protected function checkOthersInsertValue() {
        
    }
    
    protected function mysqlInsertOthersData() {
        
    }
}
