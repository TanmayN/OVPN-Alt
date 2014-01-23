<?php

if (strpos(shell_exec("ls /etc"),'openvpn') === true) {

echo "OpenVPN is already installed.";
exit();

}
?>
<html>
	<head>
		<title>OpenVPN Installer</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link href='http://fonts.googleapis.com/css?family=Exo+2:400,300,300italic,700italic,700' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css' />
		<script type="text/javascript">
		function delayedRedirect(){
			window.location = "/done.html"
		}
		</script>
	</head>
	<?php
	if (isset($_POST['install'])) {
		echo "<body onLoad=\"setTimeout('delayedRedirect()', 240000)\">";
	}
	else {
		echo "<body>";
	}
	?>
		<center>
			<h3>Welcome to the installer.</h3>
			<p>To install, click the button below.</p>
			<form action="install.php" method="post">
				<input type="submit" name="install" value="Install">
			</form>
	
		</center>
	</body>
</html>
<?php
if (isset($_POST['install'])) {

shell_exec("scripts/startinstall.sh > /dev/null");
echo("<center><p>Installation started, allow up to 5 minutes for completion, this page will redirect once it's done.</p></center>");

}
?>