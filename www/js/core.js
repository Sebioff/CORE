var hasBeenSubmitted = false;

$().ready( function() {
	$("form").submit(function() {
		if (hasBeenSubmitted)
			return false;
		else {
			hasBeenSubmitted = true;
			// sadly we can't just disable the button, since disabled elements aren't submitted
			$(this).find(":input[type='submit']").addClass('core_gui_submittable_disabled');
		}
	});
});