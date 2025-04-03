<?php


function get_tickets_stock($title)
{

	include("connect.php");        
	$sql = "SELECT tickets_stock FROM movies WHERE title = '" . $title . "'";
	$recordset = mysql_query($sql, $link);
        $row = mysql_fetch_array($recordset);
        mysql_close($link);
	return $row["tickets_stock"];

}


require("soap/nusoap.php");


$server = new nusoap_server();


$server->configureWSDL("*** USSC_stand Movie Service ***", "urn:movie_service");



$server->register("get_tickets_stock",

array("title" => "xsd:string"),

array("tickets_stock" => "xsd:integer"),

"urn:tickets_stock",
"urn:tickets_stock#get_tickets_stock");

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : "";

$server->service($HTTP_RAW_POST_DATA);

?>