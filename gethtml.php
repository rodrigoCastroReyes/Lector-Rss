<?php
	include('http.noheaders.php');
	
	header("Access-Control-Allow-Origin: *"); // Allow cross-domain
	header("Content-type: text/xml");
	
	$URL = $_GET["URL"];
	$params = parse_url($URL);
	
	$conn = new http($params["scheme"], $params["host"]);
	$html = $conn->get($params["path"]);
	
	echo $html;
?>