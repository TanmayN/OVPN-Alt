<?php

	include('../includes/config.inc.php');

	$installed = strpos(shell_exec($webdirectory . "admin/scripts/installcheck.sh"), 'e');
	
?>
<html>
	<head>
		<title>OpenVPN Uninstaller</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link href='http://fonts.googleapis.com/css?family=Exo+2:400,300,300italic,700italic,700' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css' />
		<script type="text/javascript">
		function delayedRedirect(){
			window.location = "/uninstalldone.php"
		}
		</script>
	</head>
	<?php
        if ($installed == true && isset($_POST['uninstall'])) {
            echo("<body onLoad=\"setTimeout('delayedRedirect()', 20000)\">");
        }
		else {
			echo("<body>");
		}
	?>
	<center>
			<h3>Welcome to the uninstaller.</h3>
			<p>To uninstall OpenVPN, click the button below.</p>
			<h3 style="color:red;">WARNING!</h3>
			<p style="color:red;">This will delete all your users and current config.</p>
			<?php
				if (!isset($_POST['uninstall'])) {
					echo "<form action=\"uninstall.php\" method=\"post\">";
					echo "<input type=\"submit\" name=\"uninstall\" value=\"Uninstall\">";
					echo "</form>";
				}
			?>
			<?php
			if (isset($_POST['uninstall'])) {
				if ($installed == false) {
					echo("<center><p>Error, OpenVPN is not installed.</p>");
					echo "<a href=\"install.php\">Click here to install it.</a></center>";
				}
			else {
					shell_exec("scripts/startuninstall.sh " . $webdirectory . "> /dev/null");
					echo("<center><p>Uninstall started, allow up to 30 seconds for completion, this page will redirect once it's done.</p></center>");
		
				}
			}
	
?>
		</center>
	</body>
</html>
