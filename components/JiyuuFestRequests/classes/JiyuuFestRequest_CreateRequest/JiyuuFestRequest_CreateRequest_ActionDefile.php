<?php
/**
 * Description of JiyuuFestRequest_CreateRequest_ActionDefile
 *
 * @author maxim
 */
class JiyuuFestRequest_CreateRequest_ActionDefile extends JiyuuFestRequest_CreateRequest {
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
        $out = parent::generateFormInputElementsHtml();
        // defileType
        $defileTypeArray = $this->getDefileTypeArray();
        $defileType = $this->inputHelper->select('defileType', 'defileType', $defileTypeArray, true, $this->getInsertData('defileType'),'onChange="SelectDefileType()"');
        $out .= $this->inputHelper->createFormRow($defileType, true, $this->localization->getText("defileType"));
        // actionDefileTitle
        $defileTitle = $this->inputHelper->textBox('actionDefileTitle', 'actionDefileTitle', 'actionDefileTitle', 100, true, $this->getInsertData('actionDefileTitle'));
        $out .= $this->inputHelper->createFormRow($defileTitle, true, $this->localization->getText("actionDefileTitle"));
        // fendom
        $fendom = $this->inputHelper->textBox('fendom', 'fendom', 'fendom', 100, true, $this->getInsertData('fendom'));
        $out .= $this->inputHelper->createFormRow($fendom, true, $this->localization->getText("fendom"),null,'fendomRow');
        // kosbend
        $kosbend = $this->inputHelper->textBox('kosbend', 'kosbend', 'kosbend', 100, false, $this->getInsertData('kosbend'));
        $out .= $this->inputHelper->createFormRow($kosbend, false, $this->localization->getText("kosbend"));
        // demo & audition
        $demo = $this->inputHelper->loadFiles('demo', 'demo', 'demo', false, false, $this->mimeType['video']);
        $audition = $this->inputHelper->checkbox('audition', 'audition', 'audition', false, '1');
        $demo_audition = "<div>".$demo."</div><div>".$audition." ".$this->localization->getText("audition")."</div>";
        $demo_audition_info = $this->localization->getText("loadFileOrСheck")."<br>".$this->localization->getText("loadFile200MB");
        $out .= $this->inputHelper->createFormRow($demo_audition, false, $this->localization->getText("demo_audition"), $demo_audition_info);
        // audio & instrumental & audioInVideo
        $audio = $this->inputHelper->loadFiles('audio', 'audio', 'audio', false, false, $this->mimeType['audio']);
        $instrumental = $this->inputHelper->checkbox('instrumental', 'instrumental', 'instrumental', false, '1');
        $audioInVideo = $this->inputHelper->checkbox('audioInVideo', 'audioInVideo', 'audioInVideo', false, '1');
        $audio_instrumental_audioInVideo = "<div>".$audio."</div>".
                "<div>".$instrumental." ".$this->localization->getText("instrumental")."</div>".
                "<div>".$audioInVideo." ".$this->localization->getText("audioInVideo")."</div>";
        $out .= $this->inputHelper->createFormRow($audio_instrumental_audioInVideo, false, $this->localization->getText("audio_instrumental_audioInVideo"), $this->localization->getText("loadFileOrСheck"));
        // video & noVideo
        $video = $this->inputHelper->loadFiles('video', 'video', 'video', false, false, $this->mimeType['video']);
        $noVideo = $this->inputHelper->checkbox('noVideo', 'noVideo', 'noVideo', false, '1');
        $video_noVideo = "<div>".$video."</div><div>".$noVideo." ".$this->localization->getText("noVideo")."</div>";
        $video_noVideo_info = $this->localization->getText("loadFileOrСheck")."<br>".$this->localization->getText("loadFile200MB");
        $out .= $this->inputHelper->createFormRow($video_noVideo, false, $this->localization->getText("video_noVideo"), $video_noVideo_info);
        // collage
        $collage = $this->inputHelper->loadFiles('collage', 'collage', 'collage', false, false, $this->mimeType['img']);
        $collage_info = $this->localization->getText("loadFileNowOrLater")."<br><br>".$this->localization->getText("loadFile5MB");
        $out .= $this->inputHelper->createFormRow($collage, true, $this->localization->getText("collage"), $collage_info, 'collageRow');
        // explication
        $explication = $this->inputHelper->loadFiles('explication', 'explication', 'explication', false, false, $this->mimeType['doc']);
        $explication_info = $this->localization->getText("loadFile15MB");
        $out .= $this->inputHelper->createFormRow($explication, false, $this->localization->getText("explication"), $explication_info);
        return $out;
    }
    
    
    protected function apdateOthersInsertData() {
        $this->getDefileTypeData();
        parent::apdateOthersInsertData();
        $this->insertData['defileType'] = $this->getPostValue('defileType');
        $this->insertData['actionDefileTitle'] = $this->getPostValue('actionDefileTitle');
        $this->insertData['fendom'] = $this->getPostValue('fendom');
        $this->insertData['kosbend'] = $this->getPostValue('kosbend');
        
        $this->insertData['demo'] = $this->getPostValue('demo');
        $this->insertData['audition'] = $this->getPostValue('audition');
        
        $this->insertData['audio'] = $this->getPostValue('audio');
        $this->insertData['instrumental'] = $this->getPostValue('instrumental');
        $this->insertData['audioInVideo'] = $this->getPostValue('audioInVideo');
        
        $this->insertData['video'] = $this->getPostValue('video');
        $this->insertData['noVideo'] = $this->getPostValue('noVideo');
        
        $this->insertData['collage'] = $this->getPostValue('collage');
        $this->insertData['explication'] = $this->getPostValue('explication');
    }
    
    
    protected function checkOthersInsertValue() {
        $defileType = $this->checkValue('defileType');
        $defileTitle = $this->checkValue('actionDefileTitle');
        if($this->arrTypeData[$this->insertData['defileType']]['fendom']) {
            $fendom = $this->checkValue('fendom');
        } else {
            $fendom = true;
        }
        return $defileType && $defileTitle && $fendom;
    }
    
    protected function mysqlInsertOthersData() {
        $query = "INSERT INTO `JiyuuFestRequest_ActionDefile` SET ";
        $query .= "`request`='".$this->requestID."', ";
        $query .= "`actionDefileTitle`='".$this->insertData['actionDefileTitle']."', ";
        $query .= "`fendom`='".$this->insertData['fendom']."', ";
        $query .= "`kosbend`='".$this->insertData['kosbend']."', ";
        $query .= "`defileType`='".$this->insertData['defileType']."', ";
        if($this->insertData['audition']!==null && $this->insertData['audition']!='') {
            $query .= "`audition`='".$this->insertData['audition']."', ";
        }
        if($this->insertData['instrumental']!==null && $this->insertData['instrumental']!='') {
            $query .= "`instrumental`='".$this->insertData['instrumental']."', ";
        }
        if($this->insertData['audioInVideo']!==null && $this->insertData['audioInVideo']!='') {
            $query .= "`audioInVideo`='".$this->insertData['audioInVideo']."', ";
        }
        if($this->insertData['noVideo']!==null && $this->insertData['noVideo']!='') {
            $query .= "`noVideo`='".$this->insertData['noVideo']."', ";
        }
        $query = substr($query, 0, strlen($query)-2);
        $query .= ';';
        $this->SQL_HELPER->insert($query);
        $this->downloadFileHelper->uploadFile('demo', 'demo', null, null, '200MB');
        $demoFileName = $this->downloadFileHelper->getFileName();
        $this->downloadFileHelper->uploadFile('audio', 'audio', null, null, '15MB');
        $audioFileName = $this->downloadFileHelper->getFileName();
        $this->downloadFileHelper->uploadFile('video', 'video', null, null, '200MB');
        $videoFileName = $this->downloadFileHelper->getFileName();
        $this->downloadImageHelper->uploadFile('collage', 'collage', null, null, '5MB',null,1920,1080,'default');
        $this->downloadImageHelper->makeMiniature('collage_s', 200, 200, 'default');
        $collageFileName = $this->downloadFileHelper->getFileName();
        $this->downloadFileHelper->uploadFile('explication', 'explication', null, null, '15MB');
        $explicationFileName = $this->downloadFileHelper->getFileName();
        
        $query = "UPDATE `JiyuuFestRequest_Karaoke` SET ";
        $update = '';
        if(file_exists($this->fileDir.$demoFileName)) {
            $update .= "`demo`='".$demoFileName."', ";
        }
        if(file_exists($this->fileDir.$audioFileName)) {
            $update .= "`audio`='".$audioFileName."', ";
        }
        if(file_exists($this->fileDir.$videoFileName)) {
            $update .= "`video`='".$videoFileName."', ";
        }
        if(file_exists($this->fileDir.$collageFileName)) {
            $update .= "`collage`='".$collageFileName."', ";
        }
        if(file_exists($this->fileDir.$explicationFileName)) {
            $update .= "`explication`='".$explicationFileName."', ";
        }
        if($update!='') {
            $query .= $update;
            $query = substr($query, 0, strlen($query)-2);
            $query .= " where `request`='".$this->requestID."';";
            $this->SQL_HELPER->insert($query);
        }
    }
}
