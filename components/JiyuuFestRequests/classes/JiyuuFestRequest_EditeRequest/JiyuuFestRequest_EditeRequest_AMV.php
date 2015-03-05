<?php
/**
 * Description of JiyuuFestRequest_CreateRequest_AMV
 *
 * @author maxim
 */
class JiyuuFestRequest_EditeRequest_AMV extends JiyuuFestRequest_EditeRequest {
    
    protected function generateFormInputElementsHtml() {
        $out = parent::generateFormInputElementsHtml();
        // amvTitle
        $amvTitle = $this->inputHelper->textBox('amvTitle', 'amvTitle', 'amvTitle', 100, true, $this->getInsertData('amvTitle'));
        $out .= $this->inputHelper->createFormRow($amvTitle, true, $this->localization->getText("amvTitle"));
        // fendom
        $fendom = $this->inputHelper->textBox('fendom', 'fendom', 'fendom', 100, true, $this->getInsertData('fendom'));
        $out .= $this->inputHelper->createFormRow($fendom, true, $this->localization->getText("fendom"));
        // musicTracks
        $musicTracks = $this->inputHelper->textarea('musicTracks', 'musicTracks', 'musicTracks', 1000, true, $this->getInsertData('musicTracks'));
        $out .= $this->inputHelper->createFormRow($musicTracks, true, $this->localization->getText("musicTracks"));
        // programs
        $programs = $this->inputHelper->textarea('programs', 'programs', 'programs', 1000, true, $this->getInsertData('programs'));
        $out .= $this->inputHelper->createFormRow($programs, true, $this->localization->getText("programs"));
        // amv
        $amv = parent::getFileUrl('amv')."<div>".$this->inputHelper->loadFiles('amv', 'amv', 'amv', false, false, $this->mimeType['video'])."</div>";
        $amv_info = $this->localization->getText("loadFileNowOrLater")."<br><br>".$this->localization->getText("loadFile200MB");
        $out .= $this->inputHelper->createFormRow($amv, true, $this->localization->getText("amv"), $amv_info);
        return $out;
    }
    
    
    protected function apdateOthersInsertData() {
        parent::apdateOthersInsertData();
        $this->insertData['amvTitle'] = $this->getPostValue('amvTitle');
        $this->insertData['fendom'] = $this->getPostValue('fendom');
        $this->insertData['musicTracks'] = $this->getPostValue('musicTracks');
        $this->insertData['programs'] = $this->getPostValue('programs');
        $this->insertData['amv'] = $this->getPostValue('amv');
    }
    
    
    protected function checkOthersInsertValue() {
        $amvTitle = $this->checkValue('amvTitle');
        $fendom = $this->checkValue('fendom');
        $musicTracks = $this->checkValue('musicTracks');
        $programs = $this->checkValue('programs');
        return $amvTitle && $fendom && $musicTracks && $programs;
    }
    
    protected function mysqlInsertOthersData() {
        $query = "UPDATE `JiyuuFestRequest_AMV` SET ";
//        $query .= "`request`='".$this->requestID."', ";
        $query .= "`amvTitle`='".$this->insertData['amvTitle']."', ";
        $query .= "`fendom`='".$this->insertData['fendom']."', ";
        $query .= "`musicTracks`='".$this->insertData['musicTracks']."', ";
        $query .= "`programs`='".$this->insertData['programs']."', ";
        $query = substr($query, 0, strlen($query)-2);
        $query .= " WHERE `request`='".$this->requestID."';";
        $this->SQL_HELPER->insert($query);
        parent::deletFiles();
        $this->downloadFileHelper->uploadFile('amv', 'amv', null, null, '200MB');
        $amvFileName = $this->downloadFileHelper->getFileName();
        
        $query = "UPDATE `JiyuuFestRequest_AMV` SET ";
        $update = '';
        if(file_exists($this->fileDir.$amvFileName)) {
            $update .= "`amv`='".$amvFileName."', ";
        }
        if($update!='') {
            $query .= $update;
            $query = substr($query, 0, strlen($query)-2);
            $query .= " where `request`='".$this->requestID."';";
            $this->SQL_HELPER->insert($query);
        }
    }
}
