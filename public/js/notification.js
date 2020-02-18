
var Notification = (function() {

	let notifBar = $('#notifBar');
	let info = '#33FF33';
	let error = '#FF0000';

	var show = function(notifType = 'info', autoFadeOut = true) {
		let notifColor = '';

		if(notifType === 'info')
			notifColor = info;
		if(notifType === 'error')
			notifColor = error;

		notifBar
			.css('background-color', notifColor)
			.fadeIn('slow');

		if(autoFadeOut) {
			hide(3000);
		}
	};

	var hide = function(timeout) {
		setTimeout(() => {notifBar.fadeOut('slow')}, timeout);
	}

	var notifText = function(notifMsg = 'Hello, there!') {
		notifBar.html(notifMsg);
	}

	return {
		notifText: notifText,
		show: show,
		hide: hide
	}
})();
