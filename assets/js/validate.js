function validateForm(formSelector) {
	valid = true;

	try {
		$(formSelector + ' input.required').each(function() {
			if($(this).val() == "") {
				valid = false;
			}
		});

		$(formSelector + ' textarea.required').each(function() {
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
	}
	catch(err) {
		valid = false;
		console.log(err);
	}

	return valid;
}