<?php

	$installed = strpos(shell_exec("scripts/installcheck.sh"), 'e');
	$dir = shell_exec('pwd');

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
			window.location = "<?php echo $dir; ?>/admin/installdone.php";
		}
		</script>
	</head>
	<?php
		if ($installed == false	&& isset($_POST['install'])) {	
			echo("<body onLoad=\"setTimeout('delayedRedirect()', 180000)\">");
		}
		else {
			echo("<body>");
		}
	?>
		<center>
			<h3>Welcome to the installer.</h3>
			<p>To install, click the button below.</p>
			<?php
				if (!isset($_POST['install'])) {
					echo "<form action=\"install.php\" method=\"post\">";
					echo "<input type=\"submit\" name=\"install\" value=\"Install\">";
					echo "</form>";
				}
			?>
		</center>
	</body>
</html>
<?php
if (isset($_POST['install'])) {

	if ($installed !== false) {
		echo "<center><p>Error, OpenVPN is already installed.</p></center>";
		echo "<center><a href=\"uninstall.php\">Click here to uninstall it.</a></center>";
	}
	else {
		shell_exec("scripts/startinstall.sh > /dev/null");
		echo("<center><p>Installation started, allow up to 5 minutes for completion, this page will redirect once it's done.</p></center>");
	}
}
?>
