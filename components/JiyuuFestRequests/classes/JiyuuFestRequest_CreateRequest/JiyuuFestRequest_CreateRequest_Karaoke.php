<?php
/**
 * Description of JiyuuFestRequest_CreateRequest_Karaoke
 *
 * @author maxim
 */
class JiyuuFestRequest_CreateRequest_Karaoke extends JiyuuFestRequest_CreateRequest{
    
    protected function generateFormInputElementsHtml() {
        $out = parent::generateFormInputElementsHtml();
        // songTitle
        $songTitle = $this->inputHelper->textBox('songTitle', 'songTitle', 'songTitle', 100, true, $this->getInsertData('songTitle'));
        $out .= $this->inputHelper->createFormRow($songTitle, true, $this->localization->getText("songTitle"));
        // artistSongs
        $artistSongs = $this->inputHelper->textBox('artistSongs', 'artistSongs', 'artistSongs', 100, true, $this->getInsertData('artistSongs'));
        $out .= $this->inputHelper->createFormRow($artistSongs, true, $this->localization->getText("artistSongs"));
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
        return $out;
    }
    
    protected function apdateOthersInsertData() {
        parent::apdateOthersInsertData();
        $this->insertData['songTitle'] = $this->getPostValue('songTitle');
        $this->insertData['artistSongs'] = $this->getPostValue('artistSongs');
        $this->insertData['kosbend'] = $this->getPostValue('kosbend');
        
        $this->insertData['demo'] = $this->getPostValue('demo');
        $this->insertData['audition'] = $this->getPostValue('audition');
        
        $this->insertData['audio'] = $this->getPostValue('audio');
        $this->insertData['instrumental'] = $this->getPostValue('instrumental');
        $this->insertData['audioInVideo'] = $this->getPostValue('audioInVideo');
        
        $this->insertData['video'] = $this->getPostValue('video');
        $this->insertData['noVideo'] = $this->getPostValue('noVideo');
    }
    
    protected function checkOthersInsertValue() {
        $songTitle = $this->checkValue('songTitle');
        $artistSongs = $this->checkValue('artistSongs');
        return $songTitle && $artistSongs;
    }
    
    protected function mysqlInsertOthersData() {
        $query = "INSERT INTO `JiyuuFestRequest_Karaoke` SET ";
        $query .= "`request`='".$this->requestID."', ";
        $query .= "`songTitle`='".$this->insertData['songTitle']."', ";
        $query .= "`artistSongs`='".$this->insertData['artistSongs']."', ";
        $query .= "`kosbend`='".$this->insertData['kosbend']."', ";
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
        if($update!='') {
            $query .= $update;
            $query = substr($query, 0, strlen($query)-2);
            $query .= " where `request`='".$this->requestID."';";
            $this->SQL_HELPER->insert($query);
        }
    }
}
