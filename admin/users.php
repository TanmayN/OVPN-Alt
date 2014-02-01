<?php
	
	$installed = strpos(shell_exec("scripts/installcheck.sh"), "e");
	
	if (!$installed) {
		echo("OpenVPN isn't installed.<br>");
		echo("<a href=\"install.php\">Click to go to the installer.</a>");
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
		<h3>Add User</h3>
		<p>Add a user here, please fill out the form below.</p>
		<?php
			if (!$_POST['username'] && !$_POST['deletuser']) {
				echo("<form action=\"users.php\" method=\"post\">");
				echo("<p>Username: <input type=\"form\" name=\"username\"><br>");
				echo("<input type=\"submit\" value=\"Add User\">");
				echo("</form>");
			} 
			elseif ($_POST['deleteuser']) {
				echo("<form action=\"users.php\" method=\"post\">");
				echo("<p>Username: <input type=\"form\" name=\"username\"><br>");
				echo("<input type=\"submit\" value=\"Add User\">");
				echo("</form>");
			}
			else {
				$output = shell_exec("sudo scripts/adduser.sh " . $_POST['username']);
				sleep(3);
				echo("<p>User added!</p>");
			}
		?>
		<br><br>
		<h3>Remove User</h3>
		<p>Enter the user to remove below.</p>
		<form action="users.php" method="post">
			<?php
				if (!$_POST['deleteuser'] && !$_POST['username']) {
					echo("<p>Username: <input type=\"form\" name=\"username\"><br>");
					echo("<input type=\"submit\" value=\"Remove User\">");
					echo("<input type=\"hidden\" name=\"deleteuser\" value=\"foo\">");
					echo("</form>");		
				}
				elseif ($_POST['deleteuser'] && !$_POST['username']) {
					echo("<p>Username: <input type=\"form\" name=\"username\"><br>");
					echo("<input type=\"submit\" value=\"Remove User\">");
					echo("<input type=\"hidden\" name=\"deleteuser\" value=\"foo\">");
					echo("</form>");
					echo("<p style=\"color:red\;\">ERROR: You didn't enter a username.</p>");
				}
				elseif ($_POST['username'] && !$_POST['deleteuser']) {
					echo("<p>Username: <input type=\"form\" name=\"username\"><br>");
					echo("<input type=\"submit\" value=\"Remove User\">");
					echo("<input type=\"hidden\" name=\"deleteuser\" value=\"foo\">");
					echo("</form>");
				}
				elseif ($_POST['deleteuser'] && $_POST['username']) {
					$output = shell_exec("scripts/removeuser.sh " . $_POST['username']);
					sleep(3);
					echo("<p>User removed!</p>");
				}
			?>
		</center>
	</body>
</html>