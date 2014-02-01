<?php
	$keys = file(keys);
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

	$genPassword = generateRandomString();
?>

<html>
	<head>
	<title>Downloads</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link href='http://fonts.googleapis.com/css?family=Exo+2:400,300,300italic,700italic,700' rel='stylesheet' type='text/css' />
	<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css' />
	<link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css' />
	</head>
	<body>
		