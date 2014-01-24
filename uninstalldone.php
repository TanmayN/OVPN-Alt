<html>
	<head>
		<title>Uninstall done</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link href='http://fonts.googleapis.com/css?family=Exo+2:400,300,300italic,700italic,700' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css' />
	</head>
	<body>
		<center>
			<h3>OpenVPN was uninstalled successfully.</h3>
			<a href="index.html">Return to Index</a>
			<br><br>
			<p>Output below:</p>
			<br>
			<?php
				shell_exec("sed -e 's/$/<br>/' -i typescript");
				$output = shell_exec("cat typescript");
				echo($output);
				shell_exec("sudo rm typescript");
			?>
		</center>
	</body>
</html>