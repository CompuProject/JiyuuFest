<?php
/**
 * Description of JiyuuFestRequestSendReview
 *
 * @author maxim
 */
class JiyuuFestRequestSendToReview {
    // помошники
    protected $SQL_HELPER;
    protected $urlHelper;
    // данные
    protected $requestID;
    
    
    public function __construct($requestID) {
        $this->requestID = $requestID;
        global $_SQL_HELPER;
        $this->SQL_HELPER = $_SQL_HELPER;
    }
}
