<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
<form class="JFRequestForm" name="JFRequestForm" 
      action="./test.php" 
      enctype="multipart/form-data" method="post" 
      accept-charset="UTF-8" autocomplete="on">
    <input type="checkbox" class="deletFile" name="deletFile[]" value="111.mp3" id="deletFile" autocomplete="off">
    <input type="checkbox" class="deletFile" name="deletFile[]" value="222.mp3" id="deletFile" autocomplete="off">
    <input type="checkbox" class="deletFile" name="deletFile[]" value="333.mp3" id="deletFile" autocomplete="off">
    <input type="checkbox" class="deletFile" name="deletFile[]" value="444.mp3" id="deletFile" autocomplete="off">
    <input type="checkbox" class="deletFile" name="deletFile[]" value="555.mp3" id="deletFile" autocomplete="off">
    <input class="JFRequestFormButton" type="submit" name="JFRequestFormSubmit" value="Подать заявку">
</form>
<pre>
<?php
if(isset($_POST['JFRequestFormSubmit'])) {
    var_dump($_POST);
}
?>
</pre>
</body>
</html>
