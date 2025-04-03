<?php




include("config.inc.php");


$link = mysql_connect($server, $username, $password);


if(!$link)
{
    

    
    die("Could not connect to the server: " . mysql_error());
    
}


$database = mysql_select_db($database, $link);


if(!$database)
{
    

    
    die("Could not connect to the database: " . mysql_error()); 

}



?>