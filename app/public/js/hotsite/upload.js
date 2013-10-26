//home page after login
$(document).ready(function() {
	$('#uploadFile').change(function(){
		if($('#uploadFile').val() != '') {
			$('#postFile').submit();
		}
	})
});