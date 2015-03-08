<?php
/**
 * Description of JiyuuFestRequest_ShowRequestUI
 *
 * @author maxim
 */
class JiyuuFestRequest_ShowRequestUI {
    
    protected $urlHelper;
    protected $localization;

    
    private $bannerDir = "./resources/Components/JiyuuFestRequest/JiyuuFest/banners/";
    private $fileDir = "./resources/Components/JiyuuFestRequest/Requests/";

    protected $errorBuffer;
    protected $HTML;
    
    protected $festData;
    protected $mainData;
    protected $usersData;
    protected $typeData;
    protected $expansionData;
    protected $informationData;


    public function __construct($festData,$mainData,$usersData,$typeData,$expansionData,$informationData) {
        $this->festData = $festData; 
        $this->mainData = $mainData;
        $this->usersData = $usersData;
        $this->typeData = $typeData;
        $this->expansionData = $expansionData;
        $this->informationData = $informationData;
        
        $this->urlHelper = new UrlHelper();
        $this->localization = new Localization("JiyuuFests");
        
        $this->generateHtml();
    }

    public function getHtml() {
        if(count($this->errorBuffer) == 0) {
            return $this->HTML;
        } else {
            $out = "<div class='JRequestError'>";
            foreach ($this->errorBuffer as $error) {
                $out .= "<div>".$error."</div>";
            }
            $out .= "</div>";
            return $out;
        }
    }
    
    public function get() {
        if(count($this->errorBuffer) == 0) {
            echo $this->HTML;
        } else {
            $out = "<div class='JRequestError'>";
            foreach ($this->errorBuffer as $error) {
                $out .= "<div>".$error."</div>";
            }
            $out .= "</div>";
            echo $out;
        }
    }
    
    private function generateHtml() {
        $this->HTML = '';
//        $this->HTML .= $this->generateFestsHtml();
        $this->HTML .= $this->generateRequestHtml();
    }


    private function generateFestsHtml() {
        if(isset($this->festData['festivalStart'])) {
            $festivalDay = new DateTime($this->festData['festivalStart']);
            $festivalDayText = $festivalDay->format('d M Y H:i');
        } else if(isset($this->festData['festivalDay'])) {
            $festivalDay = new DateTime($this->festData['festivalDay']);
            $festivalDayText = $festivalDay->format('d M Y');
        } else {
            $festivalDayText = $this->localization->getText("noFestDate");
        }
        $out = '';
        $out .= '<div class="FestElement '.$this->festData['fest'].'">';
            $out .= '<div class="FestElementHeder">';
                $out .= '<div class="FestElementHederTitle">';
                $out .= $this->festData['name'];
                $out .= '</div>';
                $out .= '<div class="FestElementHederSendRequestButton">';
                $out .= '<a href="'.$this->urlHelper->chengeParams(array($this->festData['fest'])).'">';
                $out .= $this->localization->getText("BackToFestList");
                $out .= '</a>';
                $out .= '</div>';
                if(isset($festivalDayText)) {
                    $out .= '<div class="FestElementHederFestDate">';
                    $out .= $this->festData['venue'].": ".$festivalDayText;
                    $out .= '</div>';
                }
            $out .= '</div>';
            
            $IMG_URL = $this->bannerDir.$this->festData['fest'].".png";
            if(!file_exists($IMG_URL)) {
                $IMG_URL = $this->bannerDir.$this->festData['fest'].".jpg";
                if(!file_exists($IMG_URL)) {
                    $IMG_URL = null;
                }
            }
            if($IMG_URL!=null) {
                $out .= '<div class="FestElementBanner" style="background: url('.$IMG_URL.') no-repeat;"></div>';
            }
            $out .= '<div class="FestElementInfo">';
                $out .= '<div class="FestElementInfoData">';
                    $out .= '<div class="FestElementInfoDataBlock">';
                        $out .= '<div class="FestElementInfoDataHeder">'.$this->localization->getText("Intramural").'</div>';
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Intramural_Start"),$this->festData['filingRequest_Intramural_Start']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Intramural_Stop"),$this->festData['filingRequest_Intramural_Stop']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Intramural_End"),$this->festData['filingRequest_Intramural_End']);
                    $out .= '</div>';
                    $out .= '<div class="FestElementInfoDataBlock">';
                        $out .= '<div class="FestElementInfoDataHeder">'.$this->localization->getText("Extramural").'</div>';
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Extramural_Start"),$this->festData['filingRequest_Extramural_Start']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Extramural_Stop"),$this->festData['filingRequest_Extramural_Stop']);
                        $out .= $this->generateFestInfoData($this->localization->getText("filingRequest_Extramural_End"),$this->festData['filingRequest_Extramural_End']);
                    $out .= '</div>';
                $out .= '</div>';
                $out .= '<div class="FestElementDescription">';
                    $out .= '<div class="FestElementDescriptionHeder">'.$this->localization->getText("FestElementDescriptionHeder").'</div>';
                    $out .= '<div class="FestElementDescriptionText">'.$this->festData['description'].'</div>';
                $out .= '</div>';
                $out .= '<div class="clear"></div>';
            $out .= '</div>';
        $out .= '</div>';
        return $out;
    }

    private function generateFestInfoData($text,$date,$fullDate = true) {
        $dateText = new DateTime($date);
        $out = '<div class="FestElementInfoDataArea">';
        $out .= '<div class="infoType">'.$text.'</div>';
        if($fullDate) {
            $out .= '<div class="infoData">'.$dateText->format('d M Y H:i').'</div>';
        } else {
            $out .= '<div class="infoData">'.$dateText->format('d M Y').'</div>';
        }
        $out .= '</div>';
        return $out;
    }
    
    private function generateRequestHtml() {
        $out = '';
        $out .= '<div class="RequestElement '.$this->mainData['request'].'">';
            $out .= '<div class="RequestElementHeder">';
                $out .= '<div class="RequestElementID">';
                $out .= $this->mainData['request'];
                $out .= '</div>';
                $EDIT_URL = $this->urlHelper->chengeParams(array($this->festData['fest'],'editRequest',$this->mainData['request']));
                $DELETE_URL = $this->urlHelper->chengeParams(array($this->festData['fest'],'deleteRequest',$this->mainData['request']));
                $out .= '<div class="RequestElementEditButton"><a href="'.$EDIT_URL.'">&#9998;</a></div>';
                $out .= '<div class="RequestElementDeleteButton"><a href="'.$DELETE_URL.'">&#215;</a></div>';
                
                $out .= '<div class="RequestElementStatus '.$this->mainData['status'].'">';
                $out .= $this->mainData['statusName'];
                $out .= '</div>';
                
                $out .= '<div class="RequestElementContest">';
                $out .= $this->mainData['contestText'];
                $out .= '</div>';
                
                $out .= '<div class="RequestElementType">';
                $out .= $this->mainData['typeName'];
                $out .= '</div>';
                
                $out .= '<div class="RequestElementFest">';
                $out .= '<a href="'.$this->urlHelper->chengeParams(array($this->festData['fest'])).'">';
                $out .= $this->festData['name'];
                $out .= '</a>';
                $out .= '</div>';
            $out .= '</div>';
            $out .= '<div class="RequestElementInformation">';
                $createdFor = '<a href="'.$this->urlHelper->pageUrl('accounts',array($this->mainData['createdFor'])).'" target="_blanck">';
                $createdFor .= $this->mainData['createdForNickname'];
                $createdFor .= '</a>';
                $out .= $this->generateRequestInformationElementHtml('RequestCreatedFor',$createdFor);
                $created = new DateTime($this->mainData['created']);
                $out .= $this->generateRequestInformationElementHtml('RequestCreated',$created->format('d M Y H:i'));
                $changed = new DateTime($this->mainData['changed']);
                $out .= $this->generateRequestInformationElementHtml('RequestChanged',$changed->format('d M Y H:i'));
                if($this->mainData['durationMin'] !== null && $this->mainData['durationMin'] !== '') {
                    $duration = $this->mainData['durationMin'];
                    $duration .= ":";
                    $duration .= $this->mainData['durationSec'];
                    $out .= $this->generateRequestInformationElementHtml('duration',$duration);
                }
            
            $out .= '</div>';
            $out .= $this->generateRequestUsersBlocksHtml();
        $out .= '</div>';
        return $out;
    }
    
    private function generateRequestInformationElementHtml($textKey,$value) {
        $out = '';
        $out .= '<div class="RequestElementInformationElement">';
            $out .= '<div class="RequestElementInformationElementText">';
                $out .= $this->localization->getText($textKey);
            $out .= '</div>';
            $out .= '<div class="RequestElementInformationElementValue">';
                $out .= $value;
            $out .= '</div>';
        $out .= '</div>';
        return $out;
    }
    
    private function generateRequestUsersBlocksHtml() {
        $out = '';
        $out .= '<table class="RequestElementUsers">';
            $out .= '<tr class="RequestElementUsersElement">';
                $out .= '<td class="RequestElementUsersElementData">';
                $out .= '</td>';
                $out .= '<td class="RequestElementUsersElementData">';
                    $out .= $this->localization->getText('RequestMember');
                $out .= '</td>';
                if($this->typeData['characterName']>0) {
                    $out .= '<td class="RequestElementUsersElementData">';
                        $out .= $this->localization->getText('CharacterName');
                    $out .= '</td>';
                }
                if($this->typeData['photo']>0) {
                    $out .= '<td class="RequestElementUsersElementData">';
                        $out .= $this->localization->getText('SuitPhoto');
                    $out .= '</td>';
                }
                if($this->typeData['original']>0) {
                    $out .= '<td class="RequestElementUsersElementData">';
                        $out .= $this->localization->getText('OriginalImage');
                    $out .= '</td>';
                }
                $out .= '<td class="RequestElementUsersElementData"></td>';
                $out .= '<td class="RequestElementUsersElementData"></td>';
                $out .= '<td class="RequestElementUsersElementData"></td>';
                
                
            $out .= '</tr>';
        foreach ($this->usersData as $user) {
            $out .= '<tr class="RequestElementUsersElement">';
                if($user['confirmed']>0) {
                    $out .= '<td class="RequestElementUsersElementData RequestElementUsersElementConfirmed YES">';
//                    $out .= '&#10004;';
                    $out .= '</td>';
                } else {
                    $out .= '<td class="RequestElementUsersElementData RequestElementUsersElementConfirmed NO">';
//                    $out .= '&#10006;';
                    $out .= '</td>';
                }
                $out .= '<td class="RequestElementUsersElementData RequestElementUsersElementNickname">';
                $out .= '<a href="'.$this->urlHelper->pageUrl('accounts',array($user['nickname'])).'" target="_blanck">';
                $out .= $user['nickname'];
                $out .= '</a>';
                $out .= '</td>';
                
                if($this->typeData['characterName']>0) {
                    $out .= '<td class="RequestElementUsersElementData RequestElementUsersElementCharacterName">';
                    $out .= $user['characterName'];
                    $out .= '</td>';
                }
                
                $fileDir = $this->fileDir.$this->festData['fest']."/".$this->mainData['request']."/".$user['user']."/";
                
                if($this->typeData['photo']>0) {
                    $out .= '<td class="RequestElementUsersElementData RequestElementUsersElementPhoto">';
                    if(file_exists($fileDir.'photo_s.jpg')) {
                        $out .= '<img class="RF_UserPromoIMG" src="'.$fileDir.'photo_s.jpg">';
                    } else if(file_exists($fileDir.'photo_s.png')) {
                        $out .= '<img class="RF_UserPromoIMG" src="'.$fileDir.'photo_s.png">';
                    }
                    $out .= '</td>';
                }
                if($this->typeData['original']>0) {
                    $out .= '<td class="RequestElementUsersElementData RequestElementUsersElementOriginal">';
                    if(file_exists($fileDir.'original_s.jpg')) {
                        $out .= '<img class="RF_UserPromoIMG" src="'.$fileDir.'original_s.jpg">';
                    } else if(file_exists($fileDir.'original_s.png')) {
                        $out .= '<img class="RF_UserPromoIMG" src="'.$fileDir.'original_s.png">';
                    }
                    $out .= '</td>';
                }
                $out .= '<td class="RequestElementUsersElementConfirmed"><a href="javascript:alert(\'В разработке\');">&#10004;</a></td>';
                $out .= '<td class="RequestElementUsersElementEdit"><a href="javascript:alert(\'В разработке\');">&#9998;</a></td>';
                $out .= '<td class="RequestElementUsersElementDelete"><a href="javascript:alert(\'В разработке\');">&#215;</a></td>';
            $out .= '</tr>';
        }
        $count = 5;
        if($this->typeData['characterName']>0) {
            $count++;
        }
        if($this->typeData['photo']>0) {
            $count++;
        }
        if($this->typeData['original']>0) {
            $count++;
        }
        
        $userUm1 = $this->checkUserAmount();
        $userUm2 = $this->mainData['numberOfParticipants'];
        if($userUm1 < $userUm2) {
            $out .= '<tr class="RequestElementUsersElement">';
            $out .= '<td class="RequestElementUsersElement_AddUser" colspan="'.$count.'">';
            $out .= '<a href="'.$this->urlHelper->chengeParams(array($this->festData['fest'],'addRequestUser',$this->mainData['request'])).'">';
            $out .= 'Добавить пользователя  ('.$userUm1.'+1 / '.$userUm2.')';
            $out .= '</a>';
            $out .= '</td>';
            $out .= '</tr>';
        }
        
        
        $out .= '</table>';
        $out .= '<div class="clear"></div>';
        return $out;
    }
    
    private function checkUserAmount() {
        global $_SQL_HELPER;
        $query = "SELECT count(`user`) as userAmount FROM `JiyuuFestRequestUsers` WHERE `request`='".$this->mainData['request']."';";
        $rezult = $_SQL_HELPER->select($query,1);
        return $rezult['userAmount'];
    }
}
