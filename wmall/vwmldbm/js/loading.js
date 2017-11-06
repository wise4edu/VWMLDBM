window.onbeforeunload = function () { 
	$('#loading').show(); 
}

$(window).ready(function() {
	$('#loading').hide();
});	
