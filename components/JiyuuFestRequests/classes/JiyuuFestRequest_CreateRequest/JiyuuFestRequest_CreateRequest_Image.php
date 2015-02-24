<?php

/**
 * Description of JiyuuFestRequest_CreateRequest_Image
 *
 * @author maxim
 */
class JiyuuFestRequest_CreateRequest_Image extends JiyuuFestRequest_CreateRequest {
    
    protected function generateFormInputElementsHtml() {
        $out = parent::generateFormInputElementsHtml();
        // imageTitle
        $imageTitle = $this->inputHelper->textBox('imageTitle', 'imageTitle', 'imageTitle', 100, true, $this->getInsertData('imageTitle'));
        $out .= $this->inputHelper->createFormRow($imageTitle, true, $this->localization->getText("imageTitle"));
        // image
        $image = $this->inputHelper->loadFiles('image', 'image', 'image', false, false, $this->mimeType['img']);
        $image_info = $this->localization->getText("loadFileNowOrLater")."<br><br>".$this->localization->getText("loadFile15MB");
        $out .= $this->inputHelper->createFormRow($image, true, $this->localization->getText("image"), $image_info);
        return $out;
    }
    
    
    protected function apdateOthersInsertData() {
        parent::apdateOthersInsertData();
        $this->insertData['imageTitle'] = $this->getPostValue('imageTitle');
        $this->insertData['image'] = $this->getPostValue('image');
    }
    
    
    protected function checkOthersInsertValue() {
        $imageTitle = $this->checkValue('imageTitle');
        return $imageTitle;
    }
    
    protected function mysqlInsertOthersData() {
        $query = "INSERT INTO `JiyuuFestRequest_Image` SET ";
        $query .= "`request`='".$this->requestID."', ";
        $query .= "`imageTitle`='".$this->insertData['imageTitle']."', ";
        $query = substr($query, 0, strlen($query)-2);
        $query .= ';';
        $this->SQL_HELPER->insert($query);
        $this->downloadImageHelper->uploadFile('image', 'image', null, null, '5MB',null,1920,1080,'default');
        $this->downloadImageHelper->makeMiniature('image_s', 200, 200, 'default');
        $imageFileName = $this->downloadFileHelper->getFileName();
        
        $query = "UPDATE `JiyuuFestRequest_Image` SET ";
        $update = '';
        if(file_exists($this->fileDir.$imageFileName)) {
            $update .= "`image`='".$imageFileName."', ";
        }
        if($update!='') {
            $query .= $update;
            $query = substr($query, 0, strlen($query)-2);
            $query .= " where `request`='".$this->requestID."';";
            $this->SQL_HELPER->insert($query);
        }
    }
}
