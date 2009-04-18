$().ready( function() {
	$("form").submit( function() {
		$(this).find(":input[type='submit']").attr("disabled", "disabled");
	});
});