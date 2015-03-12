<?php
/**
 * Description of JiyuuFestRequest_EditeRequest_Defile
 *
 * @author maxim
 */
class JiyuuFestRequest_EditeRequest_Defile extends JiyuuFestRequest_EditeRequest {
    private $defileTypeData = array();
    private $arrTypeData = array();
    
    private function generateDefileTypeJSConfigArray() {
        $out = '';
        $out .= '<script type="text/javascript">';
        $out .= "var arrTypeData = {";
        foreach ($this->defileTypeData as $type) {
            $out .= $type['type'].": {fendom: '".$type['fendom']."',collage: '".$type['collage']."'}, ";
        }
        $out = substr($out, 0, strlen($out)-2);
        $out .= "};";
        $out .= '</script>';
        echo $out;
    }


    private function getDefileTypeData() {
        $this->arrTypeData = array();
        $query = "SELECT `type`, `name`, `description`, `fendom`, `collage` FROM `JiyuuFestRequest_DefileType` order by `sequence` asc;";
        $this->defileTypeData = $this->SQL_HELPER->select($query);
        foreach ($this->defileTypeData as $type) {
            $this->arrTypeData[$type['type']]['fendom'] = $type['fendom'] == '1';
            $this->arrTypeData[$type['type']]['collage'] = $type['collage'] == '1';
        }
        $this->generateDefileTypeJSConfigArray();
        
    }
    
    private function getDefileTypeArray() {
        $array = array();
        foreach ($this->defileTypeData as $key => $type) {
            if(isset($type['description']) && $type['description']!==null && $type['description']!='') {
                $description = " (".$type['description'].")";
            } else {
                $description = '';
            }
            $array[$key]['text'] = $type['name'].$description;
            $array[$key]['value'] = $type['type'];
        }
        return $array;
    }


    protected function generateFormInputElementsHtml() {
        $this->getDefileTypeData();
        $out = parent::generateFormInputElementsHtml();
        // defileType
        $defileTypeArray = $this->getDefileTypeArray();
        $defileType = $this->inputHelper->select('defileType', 'defileType', $defileTypeArray, true, $this->getInsertData('defileType'),'onChange="SelectDefileType()"');
        $out .= $this->inputHelper->createFormRow($defileType, true, $this->localization->getText("defileType"));
        // defileTitle
        $defileTitle = $this->inputHelper->textBox('defileTitle', 'defileTitle', 'defileTitle', 100, true, $this->getInsertData('defileTitle'));
        $out .= $this->inputHelper->createFormRow($defileTitle, true, $this->localization->getText("defileTitle"));
        // fendom
        $fendom = $this->inputHelper->textBox('fendom', 'fendom', 'fendom', 100, true, $this->getInsertData('fendom'));
        $out .= $this->inputHelper->createFormRow($fendom, true, $this->localization->getText("fendom"),null,'fendomRow');
        // kosbend
        $kosbend = $this->inputHelper->textBox('kosbend', 'kosbend', 'kosbend', 100, false, $this->getInsertData('kosbend'));
        $out .= $this->inputHelper->createFormRow($kosbend, false, $this->localization->getText("kosbend"));
        // audio
        $audio = parent::getFileUrl('audio')."<div>".$this->inputHelper->loadFiles('audio', 'audio', 'audio', false, false, $this->mimeType['audio'])."</div>";
        $audio_info = $this->localization->getText("loadFileNowOrLater")."<br><br>".$this->localization->getText("loadFile15MB");
        $out .= $this->inputHelper->createFormRow($audio, true, $this->localization->getText("audio"), $audio_info);
        return $out;
    }
    
    
    protected function apdateOthersInsertData() {
        parent::apdateOthersInsertData();
        $this->insertData['defileType'] = $this->getPostValue('defileType');
        $this->insertData['defileTitle'] = $this->getPostValue('defileTitle');
        $this->insertData['fendom'] = $this->getPostValue('fendom');
        $this->insertData['kosbend'] = $this->getPostValue('kosbend');
        
        $this->insertData['audio'] = $this->getPostValue('audio');
    }
    
    
    protected function checkOthersInsertValue() {
        $defileType = $this->checkValue('defileType');
        $defileTitle = $this->checkValue('defileTitle');
        if($this->arrTypeData[$this->insertData['defileType']]['fendom']) {
            $fendom = $this->checkValue('fendom');
        } else {
            $fendom = true;
        }
        return $defileType && $defileTitle && $fendom;
    }
    
    protected function mysqlInsertOthersData() {
        $query = "UPDATE `JiyuuFestRequest_Defile` SET ";
//        $query .= "`request`='".$this->requestID."', ";
        $query .= "`defileTitle`='".$this->insertData['defileTitle']."', ";
        $query .= "`fendom`='".$this->insertData['fendom']."', ";
        $query .= "`kosbend`='".$this->insertData['kosbend']."', ";
        $query .= "`defileType`='".$this->insertData['defileType']."', ";
        $query = substr($query, 0, strlen($query)-2);
        $query .= " WHERE `request`='".$this->requestID."';";
        $this->SQL_HELPER->insert($query);
        parent::deletFiles();
        $this->downloadFileHelper->uploadFile('audio', 'audio', null, null, '15MB');
        $audioFileName = $this->downloadFileHelper->getFileName();
        
        $query = "UPDATE `JiyuuFestRequest_Defile` SET ";
        $update = '';
        if(file_exists($this->fileDir.$audioFileName)) {
            $update .= "`audio`='".$audioFileName."', ";
        }
        if($update!='') {
            $query .= $update;
            $query = substr($query, 0, strlen($query)-2);
            $query .= " where `request`='".$this->requestID."';";
            $this->SQL_HELPER->insert($query);
        }
    }
}
