<?php



include("connect_i.php");

$message = "";

if(isset($_GET["user"]) && isset($_GET["activation_code"]) )
{
    
    $login = $_GET["user"];
    $login = mysqli_real_escape_string($link, $login);    
    
    $activation_code = $_GET["activation_code"];
    $activation_code = mysqli_real_escape_string($link, $activation_code);               
                
    $sql = "SELECT * FROM users WHERE login = '" . $login . "' AND BINARY activation_code = '" . $activation_code . "'";
                



    $recordset = $link->query($sql);             
                             
    if(!$recordset)
    {

        die("Error: " . $link->error);

    }
                



                
    $row = $recordset->fetch_object();   
                                                                           
    if($row)
    {




                    
        $sql = "UPDATE users SET activation_code = NULL, activated = 1 WHERE login = '" . $login . "'";




        $recordset = $link->query($sql);

        if(!$recordset)
        {

            die("Error: " . $link->error);

        }
                    




        $message = "<font color=\"green\">User activated!</font>";

    }
                
    else
    {

        $message = "<font color=\"red\">User not or already activated!</font>";

    }

}

else

{
    
    $message = "<font color=\"red\">Not a valid input!</font>";

}

?>
<!DOCTYPE html>
<html>
    
<head>
        
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<!--<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Architects+Daughter">-->
<link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" media="screen" />
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />

<!--<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>-->
<script src="js/html5.js"></script>

<title>USSC_stand - User Activation</title>

</head>

<body>
    
<header>

<h1>USSC_stand</h1>

<h2>Самое безопасное приложение в мире!</h2>

</header>    

<div id="menu">
      
    <table>
        
        <tr>
            
            <td><a href="login.php">Login</a></td>
            <td><font color="#ffb717">User Activation</font></td>            
            
        </tr>
        
    </table>   
   
</div> 

<div id="main">

    <h1>User Activation</h1>

    <p><?php

    echo $message;

    $link->close();

    ?></p>

</div>
    
<div id="side">    
    
    <a href="#" target="blank_" class="button"><img src="./images/twitter.png"></a>
    <a href="#" target="blank_" class="button"><img src="./images/linkedin.png"></a>
    <a href="#" target="blank_" class="button"><img src="./images/facebook.png"></a>
    <a href="#" target="blank_" class="button"><img src="./images/blogger.png"></a>

</div>     
    
<div id="disclaimer">
          
    <p>USSC_stand is licensed under <a rel="license" href="#" target="_blank"><img style="vertical-align:middle" src="./images/cc.png"></a> &copy; 2014 MME BVBA / Follow <a href="#" target="_blank">@MME_IT</a> on Twitter and ask for our cheat sheet, containing all solutions! / Need an exclusive <a href="http://www.mmebvba.com" target="_blank">training</a>?</p>
   
</div>
    
<div id="bee">
    
    <img src="./images/USSC_logo.png">
    
</div>
      
</body>
    
</html>