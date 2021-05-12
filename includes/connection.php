<?php
	define("DB_SERVER", "localhost");
	define("DB_USER", "root");
	define("DB_PASS", "");
	define("DB_NAME", "web_app");

	$con = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME,DB_USER,DB_PASS);
	
	?>