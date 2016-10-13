function validateForm(formSelector) {
	valid = true;

	$(formSelector + ' input.required').each(function() {
		if($(this).val() == "") {
			valid = false;
		}
	});

	$(formSelector + ' select.required').each(function() {
		option = $(this).find(":selected").val();
		if(option == "") {
			valid = false;
		}
	});

	return valid;
}