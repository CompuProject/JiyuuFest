<script type="text/javascript">
$(function() {
	$("#search").keyup(function(){
		var search = $("#search").val();
					
		$.ajax({
			type: "POST",
			url: "UserSearch.php",
			data: {"search": search},
			cache: false,						
			success: function(response){
				$("#resSearch").html(response);
			}
		});
		return false;
				
	});
});
</script>
<?php
include_once './components/Users/classes/Account.php';
global $USER_ACCOUNT;
$USER_ACCOUNT = new Account();
?>