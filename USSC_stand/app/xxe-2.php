<?php



include("security.php");
include("security_level_check.php");
include("connect_i.php");

$message = "";
$body = file_get_contents("php://input");


if($_COOKIE["security_level"] != "1" && $_COOKIE["security_level"] != "2")
{

    ini_set("display_errors",1);

    $xml = simplexml_load_string($body);




    $login = $xml->login;
    $secret = $xml->secret;

    if($login && $login != "" && $secret)
    {



        
        $sql = "UPDATE users SET secret = '" . $secret . "' WHERE login = '" . $login . "'";




        $recordset = $link->query($sql);

        if(!$recordset)
        {

            die("Connect Error: " . $link->error);

        }

        $message = $login . "'s secret has been reset!";

    }

    else
    {

        $message = "An error occured!";

    }

}


else
{



    $xml = simplexml_load_string($body);
    



    $login = $_SESSION["login"];
    $secret = $xml->secret;

    if($secret)
    {

        $secret = mysqli_real_escape_string($link, $secret);

        $sql = "UPDATE users SET secret = '" . $secret . "' WHERE login = '" . $login . "'";




        $recordset = $link->query($sql);

        if(!$recordset)
        {

            die("Connect Error: " . $link->error);

        }

        $message = $login . "'s secret has been reset!";

    }

    else
    {

        $message = "An error occured!"; 

    }

}

echo $message;

$link->close();

?>