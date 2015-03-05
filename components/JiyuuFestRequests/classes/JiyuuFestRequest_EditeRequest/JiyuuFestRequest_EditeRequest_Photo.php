<?php
/**
 * Description of JiyuuFestRequest_EditeRequest_Photo
 *
 * @author maxim
 */
class JiyuuFestRequest_EditeRequest_Photo extends JiyuuFestRequest_EditeRequest {
    
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
        $photo = parent::getFileUrl('photo1').'<div>'.$photo1.'</div>'
                .parent::getFileUrl('photo2').'<div>'.$photo2.'</div>'
                .parent::getFileUrl('photo3').'<div>'.$photo3.'</div>'
                .parent::getFileUrl('photo4').'<div>'.$photo4.'</div>';
        $photo_info = $this->localization->getText("loadFileNowOrLater")."<br><br>".$this->localization->getText("loadFile15MB");
        $out .= $this->inputHelper->createFormRow($photo, true, $this->localization->getText("image"), $photo_info);
        return $out;
    }
    
    
    protected function apdateOthersInsertData() {
        parent::apdateOthersInsertData();
        $this->insertData['photoTitle'] = $this->getPostValue('photoTitle');
        $this->insertData['fendom'] = $this->getPostValue('fendom');
        $this->insertData['characters'] = $this->getPostValue('characters');
        $this->insertData['photographer'] = $this->getPostValue('photographer');
        $this->insertData['photo1'] = $this->getPostValue('photo1');
        $this->insertData['photo2'] = $this->getPostValue('photo2');
        $this->insertData['photo3'] = $this->getPostValue('photo3');
        $this->insertData['photo4'] = $this->getPostValue('photo4');
    }
    
    
    protected function checkOthersInsertValue() {
        $fendom = $this->checkValue('fendom');
        $characters = $this->checkValue('characters');
        $photographer = $this->checkValue('photographer');
        return $fendom && $characters && $photographer;
    }
    
    protected function mysqlInsertOthersData() {
        $query = "UPDATE `JiyuuFestRequest_Photo` SET ";
//        $query .= "`request`='".$this->requestID."', ";
        $query .= "`photoTitle`='".$this->insertData['photoTitle']."', ";
        $query .= "`fendom`='".$this->insertData['fendom']."', ";
        $query .= "`characters`='".$this->insertData['characters']."', ";
        $query .= "`photographer`='".$this->insertData['photographer']."', ";
        $query = substr($query, 0, strlen($query)-2);
        $query .= " WHERE `request`='".$this->requestID."';";
        $this->SQL_HELPER->insert($query);
        parent::deletFiles();
        
//        $this->downloadFileHelper->uploadFile('photo1', 'photo1', null, null, '15MB');
//        $photo1 = $this->downloadFileHelper->getFileName();
//        
//        $this->downloadFileHelper->uploadFile('photo2', 'photo2', null, null, '15MB');
//        $photo2 = $this->downloadFileHelper->getFileName();
//        
//        $this->downloadFileHelper->uploadFile('photo3', 'photo3', null, null, '15MB');
//        $photo3 = $this->downloadFileHelper->getFileName();
//        
//        $this->downloadFileHelper->uploadFile('photo4', 'photo4', null, null, '15MB');
//        $photo4 = $this->downloadFileHelper->getFileName();
        
        $this->downloadImageHelper->uploadFile('photo1', 'photo1', null, null, '15MB',null,1920,1080,'default');
        $this->downloadImageHelper->makeMiniature('photo1_s', 200, 200, 'default');
        $photo1 = $this->downloadImageHelper->getFileName();
        
        $this->downloadImageHelper->uploadFile('photo2', 'photo2', null, null, '15MB',null,1920,1080,'default');
        $this->downloadImageHelper->makeMiniature('photo2_s', 200, 200, 'default');
        $photo2 = $this->downloadImageHelper->getFileName();
        
        $this->downloadImageHelper->uploadFile('photo3', 'photo3', null, null, '15MB',null,1920,1080,'default');
        $this->downloadImageHelper->makeMiniature('photo3_s', 200, 200, 'default');
        $photo3 = $this->downloadImageHelper->getFileName();
        
        $this->downloadImageHelper->uploadFile('photo4', 'photo4', null, null, '15MB',null,1920,1080,'default');
        $this->downloadImageHelper->makeMiniature('photo4_s', 200, 200, 'default');
        $photo4 = $this->downloadImageHelper->getFileName();
        
        
        $query = "UPDATE `JiyuuFestRequest_Photo` SET ";
        $update = '';
        if(file_exists($this->fileDir.$photo1)) {
            $update .= "`photo1`='".$photo1."', ";
        }
        if(file_exists($this->fileDir.$photo2)) {
            $update .= "`photo2`='".$photo2."', ";
        }
        if(file_exists($this->fileDir.$photo3)) {
            $update .= "`photo3`='".$photo3."', ";
        }
        if(file_exists($this->fileDir.$photo4)) {
            $update .= "`photo4`='".$photo4."', ";
        }
        if($update!='') {
            $query .= $update;
            $query = substr($query, 0, strlen($query)-2);
            $query .= " where `request`='".$this->requestID."';";
            $this->SQL_HELPER->insert($query);
        }
        echo $query;
    }
}
