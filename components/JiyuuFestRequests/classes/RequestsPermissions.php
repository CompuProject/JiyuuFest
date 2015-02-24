<?php
/**
 * Description of RequestsPermissions
 *
 * @author maxim
 */
class RequestsPermissions {
    private $permissions;
    
    public function __construct() {
        global $_SQL_HELPER;
        $query = "SELECT * FROM `JiyuuFestRequestPermissions`;";
        $result = $_SQL_HELPER->select($query);
        foreach ($result as $element) {
            $this->permissions[$element['userType']][$element['timeFrame']][$element['status']]['showRequest'] = $element['showRequest'] > 0;
            $this->permissions[$element['userType']][$element['timeFrame']][$element['status']]['editRequest'] = $element['editRequest'] > 0;
            $this->permissions[$element['userType']][$element['timeFrame']][$element['status']]['editUsers'] = $element['editUsers'] > 0;
            $this->permissions[$element['userType']][$element['timeFrame']][$element['status']]['deleteRequest'] = $element['deleteRequest'] > 0;
            $this->permissions[$element['userType']][$element['timeFrame']][$element['status']]['changeStatus'] = $element['changeStatus'] > 0;
        }
    }
    
    public function getPermissions($userType, $timeFrame, $status, $key) {
        if(isset($this->permissions[$userType][$timeFrame][$status][$key])) {
            return $this->permissions[$userType][$timeFrame][$status][$key];
        } else {
            return false;
        }
    }
}
