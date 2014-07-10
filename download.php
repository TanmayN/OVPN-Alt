<?php
	$installed = strpos(shell_exec("admin/scripts/installcheck.sh"), 'e');

	if (!$installed) {
		die("OpenVPN is not installed, please contact your administrator.");
	}
?>
<html>
	<head>
		<title>Add and Remove Users</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link href='http://fonts.googleapis.com/css?family=Exo+2:400,300,300italic,700italic,700' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css' />
		<script type="text/javascript">
		</script>
	</head>
	<body>
		<center>
			<h3>Download Config</h3>
			<p>Note: You will need a username and password to use this config. It does not work without one.</p>
			<a href="configs/ovpn-<?php echo shell_exec("hostname"); ?>.tar.gz"><button type="button">Download</button></a>
		</center>
	</body>
</html>