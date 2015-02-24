<?php
/**
 * Description of JiyuuFestRequest_CreateRequest_VideoCosplay
 *
 * @author maxim
 */
class JiyuuFestRequest_CreateRequest_VideoCosplay extends JiyuuFestRequest_CreateRequest {
    
    protected function generateFormInputElementsHtml() {
        $out = parent::generateFormInputElementsHtml();
        // videoCosplayTitle
        $videoCosplayTitle = $this->inputHelper->textBox('videoCosplayTitle', 'videoCosplayTitle', 'videoCosplayTitle', 100, true, $this->getInsertData('videoCosplayTitle'));
        $out .= $this->inputHelper->createFormRow($videoCosplayTitle, true, $this->localization->getText("videoCosplayTitle"));
        // fendom
        $fendom = $this->inputHelper->textBox('fendom', 'fendom', 'fendom', 100, true, $this->getInsertData('fendom'));
        $out .= $this->inputHelper->createFormRow($fendom, true, $this->localization->getText("fendom"));
        // characters
        $characters = $this->inputHelper->textarea('characters', 'characters', 'characters', 1000, true, $this->getInsertData('characters'));
        $out .= $this->inputHelper->createFormRow($characters, true, $this->localization->getText("characters"));
        // musicTracks
        $musicTracks = $this->inputHelper->textarea('musicTracks', 'musicTracks', 'musicTracks', 1000, true, $this->getInsertData('musicTracks'));
        $out .= $this->inputHelper->createFormRow($musicTracks, true, $this->localization->getText("musicTracks"));
        // programs
        $programs = $this->inputHelper->textarea('programs', 'programs', 'programs', 1000, true, $this->getInsertData('programs'));
        $out .= $this->inputHelper->createFormRow($programs, true, $this->localization->getText("programs"));
        // videographer
        $videographer = $this->inputHelper->textBox('videographer', 'videographer', 'videographer', 100, true, $this->getInsertData('videographer'));
        $out .= $this->inputHelper->createFormRow($videographer, true, $this->localization->getText("videographer"));
        // video
        $video = $this->inputHelper->loadFiles('video', 'video', 'video', false, false, $this->mimeType['video']);
        $video_info = $this->localization->getText("loadFileNowOrLater")."<br><br>".$this->localization->getText("loadFile200MB");
        $out .= $this->inputHelper->createFormRow($video, true, $this->localization->getText("video"), $video_info);
        return $out;
    }
    
    
    protected function apdateOthersInsertData() {
        parent::apdateOthersInsertData();
        $this->insertData['videoCosplayTitle'] = $this->getPostValue('videoCosplayTitle');
        $this->insertData['fendom'] = $this->getPostValue('fendom');
        $this->insertData['characters'] = $this->getPostValue('characters');
        $this->insertData['musicTracks'] = $this->getPostValue('musicTracks');
        $this->insertData['programs'] = $this->getPostValue('programs');
        $this->insertData['videographer'] = $this->getPostValue('videographer');
        $this->insertData['video'] = $this->getPostValue('video');
    }
    
    
    protected function checkOthersInsertValue() {
        $videoCosplayTitle = $this->checkValue('videoCosplayTitle');
        $fendom = $this->checkValue('fendom');
        $characters = $this->checkValue('characters');
        $musicTracks = $this->checkValue('musicTracks');
        $programs = $this->checkValue('programs');
        $videographer = $this->checkValue('videographer');
        return $videoCosplayTitle && $fendom && $characters && $musicTracks && $programs && $videographer;
        
    }
    
    protected function mysqlInsertOthersData() {
        $query = "INSERT INTO `JiyuuFestRequest_VideoCosplay` SET ";
        $query .= "`request`='".$this->requestID."', ";
        $query .= "`videoCosplayTitle`='".$this->insertData['videoCosplayTitle']."', ";
        $query .= "`fendom`='".$this->insertData['fendom']."', ";
        $query .= "`characters`='".$this->insertData['characters']."', ";
        $query .= "`musicTracks`='".$this->insertData['musicTracks']."', ";
        $query .= "`programs`='".$this->insertData['programs']."', ";
        $query .= "`videographer`='".$this->insertData['videographer']."', ";
        $query = substr($query, 0, strlen($query)-2);
        $query .= ';';
        $this->SQL_HELPER->insert($query);
        $this->downloadFileHelper->uploadFile('video', 'video', null, null, '200MB');
        $amvFileName = $this->downloadFileHelper->getFileName();
        
        $query = "UPDATE `JiyuuFestRequest_VideoCosplay` SET ";
        $update = '';
        if(file_exists($this->fileDir.$amvFileName)) {
            $update .= "`video`='".$amvFileName."', ";
        }
        if($update!='') {
            $query .= $update;
            $query = substr($query, 0, strlen($query)-2);
            $query .= " where `request`='".$this->requestID."';";
            $this->SQL_HELPER->insert($query);
        }
    }
}
