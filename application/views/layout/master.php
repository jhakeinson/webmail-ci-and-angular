
<!doctype html>
<html lang="en" ng-app="ProjectListApp">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="A demo CRUD Website">
	<meta name="author" content="Jake I">

	<title>Webmail System</title>

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css'>

	<!-- Custom styles for this template -->
	<link href="./public/css/style.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">Webmail System</a>
		</div>
	</div>
</nav>

<main role="main">
	<?= $template; ?>
</main>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"><\/script>')</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- Custom Javascript
================================================== -->
<script src="<?= base_url('public/js/message.js'); ?>"></script>
<script src="<?= base_url('public/js/mailbox.js'); ?>"></script>
<script src="<?= base_url('public/js/form.js'); ?>"></script>
<script src="<?= base_url('public/js/notification.js'); ?>"></script>
<script src="<?= base_url('public/js/main.js'); ?>"></script>

</body>
</html>
