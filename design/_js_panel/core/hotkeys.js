$(document).ready(function() {

	$(document).bind('keydown', 'alt+f1', function (){
		$('#menu-catalog').focus();
	});
	
	switch(GCore.sCurrentController){
		case 'category':
			$(document).bind('keydown', 'ctrl+a', function (e){
				$('#add-category').click();
				return false; 
			});
			$(document).bind('keydown', 'ctrl+d', function (e){
				$('#delete-category').click();
				return false; 
			});
		break;
	}
});