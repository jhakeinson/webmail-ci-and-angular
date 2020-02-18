
var Form = (function () {
	let sendForm = $('#sendMsgData');

	var initForm = function () {
		bindEvents();
	};

	var bindEvents = function() {
		sendForm.on('submit', formSubmitHandler);
		sendForm.on('change', 'input[type="file"]', fileInputChangeHandler);
		sendForm.on('click', '#clearUploads', clearUploadsClickHandler);
		sendForm.on('click', '.attachment-badge .btn-del-atch', deleteAtchBtnClickHandler);
	};

	var fileInputChangeHandler = function(e) {
		e.preventDefault();

		let attachmenrBox = $('.attachment-box');

		let files = e.target.files;
		let filesArr = Array.prototype.slice.call(files);

		filesArr.forEach(function (f) {
			let formData = new FormData();
			formData.append('file', f);

			let delBtn = '<i data-file="'+f.name+'" class="fa fa-spinner fa-spin btn-atch"></i>';
			attachmenrBox.append('<span class="badge attachment-badge">' + delBtn + f.name + '</span>');
			$.ajax({
				url: host + '/attachment/upload',
				type: 'POST',
				data: formData,
				success: function (data) {
					attachmenrBox.find('[data-file="'+f.name+'"]')
						.removeClass()
						.addClass('fa fa-times-circle btn-atch btn-del-atch');
				},
				error: function (jqXHR) {
					Notification.notifText('File upload failed.');
					Notification.show('error');
				},
				cache: false,
				contentType: false,
				processData: false
			})
		})
	};

	var formSubmitHandler = function(e) {
		e.preventDefault();

		let formData = new FormData(this);
		formData.delete('files[]');

		let sendBtn = sendForm.find('.btn-send');

		sendBtn.html('<i class="fa fa-spinner fa-spin">');
		sendBtn.attr('disabled', 'disabled');

		$.ajax({
			url: host + '/message/send',
			type: 'POST',
			data: formData,
			success: function (data) {
				sendBtn.html('Send');
				sendBtn.removeAttr('disabled');
				$('#myModal').modal('hide');
				resetForm();
				Notification.notifText(data.message);
				Notification.show('info');
			},
			error: function (jqXHR) {
				sendBtn.html('Send');
				sendBtn.removeAttr('disabled');
				$('#myModal').modal('hide');
				sendForm.reset();
				Notification.notifText('Failed to send message.');
				Notification.show('error');
			},
			cache: false,
			contentType: false,
			processData: false
		});
	};

	var deleteAtchBtnClickHandler = function (e) {
		e.preventDefault();

		var self = this;

		var file = $(this).data('file');

		$(self).removeClass().addClass('fa fa-spinner fa-spin btn-atch');
		$.ajax({
			url: host + '/attachment/remove?file=' + file,
			method: 'DELETE',
			success: function(data) {
				$(self).parent().remove();
			},
			error: function () {
				Notification.notifText('File deletion failed.');
				Notification.show('error');
				$(this).removeClass().addClass('fa fa-times-circle btn-atch btn-del-atch');
			}
		});
	};

	var clearUploadsClickHandler = function() {
		resetForm();
	};

	var resetForm = function() {
		sendForm[0].reset();
		sendForm.find('.attachment-box').empty();
	};

	var getForm = function() {
		return sendForm;
	};

	return {
		initForm: initForm,
		getForm: getForm,
		resetForm: resetForm
	}
})();
