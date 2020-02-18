
var host = 'http://email-system.test';

var Mailbox = (function () {
	let container = $('#mailboxes');
	let activeMailbox = null;

	var retrieveMailboxes = function () {
		let url = [host, 'mailboxes'].join('/');
		container.append(
			$('<div>')
				.css('padding-bottom', '10px')
				.addClass('text-center')
				.html($('<i class="fa fa-spinner fa-spin fa-3x">'))
		);
		container.load(url, function (e) {
			activeMailbox = container.find('li.active');
			bindEvents();
			updateMailboxList();
		});
	};

	var bindEvents = function () {
		container.on('click', '.inbox-nav li', mailboxClickHandler);
	};

	var mailboxClickHandler = function(e) {
		e.preventDefault();
		let currMb = $(e.currentTarget);
		console.log(e.currentTarget);
		let mbEncName = currMb.data('mailboxEncName');
		let currMbName = currMb.find('.mb-name').text();

		$('.inbox-head>h3').text(currMbName);
		activeMailbox.removeClass('active');
		currMb.addClass('active');
		activeMailbox = currMb;

		Message.retrieveMessages(1, mbEncName);
	};

	var getActiveMailbox = function() {
		return activeMailbox;
	};

	var updateMailboxList = function() {
		let url = [host, 'mailboxes'].join('/');
		$.ajax({
			url: url,
			method: 'GET',
			success: function (data) {
				container.html(data);
				container.find('.active').removeClass('active');
				activeMailbox.addClass('active');
			},
			complete: function () {
				setTimeout(updateMailboxList, 1000)
			}
		});
	};

	return {
		retrieveMailboxes: retrieveMailboxes,
		getActiveMailbox: getActiveMailbox,
		updateMailboxList: updateMailboxList
	}

})();
