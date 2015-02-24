<?php
include_once './components/JRequest/classes/JRequestApprovedList.php';

$jRequestList = new JRequestApprovedList();
echo $jRequestList->get();
?>