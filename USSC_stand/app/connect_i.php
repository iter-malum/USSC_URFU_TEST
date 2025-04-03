<?php



include("config.inc.php");


$link = new mysqli($server, $username, $password, $database);


if($link->connect_error)
{
    

    
    die("Тебе нужно инициализировать лабораторную. Перейди на /install.php " . $link->connect_error);   
   
}



?>