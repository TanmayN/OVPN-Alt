<?php

	$host = $_POST['host'] ;
	$port = $_POST['port'] ;

	$havekeyoutput = shell_exec("sudo /admin/scripts/havekey.sh");
	
	if (strpos($havekeyoutput, '.pub')) {
		 $havekey = true;
	}
	
	if ($_POST['check'] && $_POST['host'] && $_POST['port']) {
		//$scpoutput = shell_exec("sshpass -p 'cookies' scp -P " . $_POST['port'] . " scripts/removecookies.sh /root/.ssh/id_rsa.pub cookies@" . $_POST['host'] . ":/root/");
		$scpoutput = shell_exec("sudo sshpass -p 'cookies' scp -P " . $port . " /var/www/admin/scripts/removecookies.sh /root/.ssh/id_rsa.pub cookies@" . $host . ":/root/");
		sleep(5);
		
		if (strpos($scpoutput, 'denied')) {
			 $failed = "denied";
		}
		elseif (strpos($scpoutput, 'not resolve')) {
			 $failed = "resolve";
		}
		elseif (strpos($scpoutput, 'Connection refused')) {
			 $failed = "refused";
		}
		elseif (strpos($scpoutput, 'not found')) {
			shell_exec("sudo apt-get -y install sshpass");
			echo ("Please refresh the page (Say yes to \"Resend data\"");
		}
		else {
		
			shell_exec("sudo sshpass -p 'cookies' ssh -p " . $port . " cookies@" . $host . " -t 'mkdir -p /root/.ssh;chmod 700 /root/.ssh;touch /root/.ssh/authorized_keys;cat /root/id_rsa.pub >> /root/.ssh/authorized_keys'");
			sleep(5);
			shell_exec("sudo ssh -p " . $_POST['port'] . " root@" . $_POST['host'] . " -t 'chmod +x removecookies.sh; ./removecookies.sh'");
			sleep(5);

			
			shell_exec("sudo echo " . $_POST['host'] . "  " . $_POST['port'] . " >> /admin/data/hosts_and_ports");
			
			 $success = true;
			
		}
	}
		
?>
<html>
<head>
	<title>Add a remote server</title>
	<link href='http://fonts.googleapis.com/css?family=Exo+2:400,300,300italic,700italic,700' rel='stylesheet' type='text/css' />
	<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css' />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
	<body>
		<center>
			<h3>Add a remote host</h3>
			<p>Here, you can add a remote server.</p>
			<p>I haven't yet found a way to fully automate this securely (without directly passing the password through post), so there will be a few steps you'll have to do from SSH, but you can pretty much just copy/paste them.</p>
			<?php
				if (!$havekey) {
				
					$makekey = shell_exec("sudo ssh-keygen -q -t rsa -N '' -f /root/.ssh/id_rsa");
				
				}
			?>
			<p>SSH into your remote server as root, and do the following commands ONE BY ONE, in order.</p>
			<li class="finished"> useradd -m -o -u 0 -s /bin/bash cookies</li>
			<li class="finished"> echo "cookies:cookies" | chpasswd</li>
			<p>Then click the button below. (Note: This account will be deleted, don't worry. :P)</li>
			<?php
				if (!isset($_POST['check'])) {
					echo "<form action=\"addremote.php\" method=\"post\">";
					echo "Host: <input type=\"form\" name=\"host\"><br>";
					echo "Port: <input type=\"form\" name=\"port\" value=\"22\"><br>";
					echo "<input type=\"submit\" name=\"check\" value=\"Setup\">";
					echo "</form>";
				}
				elseif (!$_POST['host'] || !$_POST['port']) {
					echo "<form action=\"addremote.php\" method=\"post\">";
					echo "Host: <input type=\"form\" name=\"host\"><br>";
					echo "Port: <input type=\"form\" name=\"port\" value=\"22\"><br>";
					echo "<input type=\"submit\" name=\"check\" value=\"Setup\">";
					echo "</form>";
					echo "<p style=\"color:red\;\">ERROR: Fill out the form above..</p>";
				}
				elseif ( $failed == "denied") {
					echo "<p style=\"color:red\;\">ERROR: SSH Access Denied. Did you do all the commands correctly?</p>";
				}
				elseif ( $failed == "not resolve") {
					echo "<p style=\"color:red\;\"> ERROR: Could not resolve hostname, try entering the raw IP address.</p>";
				}
				elseif ( $failed == "refused") {
					echo "<p style=\"color:red\;\"> ERROR: Connection refused, is this the right SSH Port?</p>";
				}
				elseif ( $success) {
					echo "<p>Success! " . $_POST['host'] . " was added successfully!</p>";
				}
			?>
		</center>
	</body>
</html>