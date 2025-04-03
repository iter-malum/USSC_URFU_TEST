<?php



include("security.php");
include("security_level_check.php");
include("functions_external.php");
include("selections.php");

if(isset($_REQUEST["url"]) && ($_COOKIE["security_level"] == "1" || $_COOKIE["security_level"] == "2"))
{


    if($_COOKIE["security_level"] == "2")
    {


        $_SESSION = array();
        session_destroy();


        setcookie("admin", "", time()-3600, "/", "", false, false);
        setcookie("movie_genre", "", time()-3600, "/", "", false, false);
        setcookie("secret", "", time()-3600, "/", "", false, false);
        setcookie("top_security", "", time()-3600, "/", "", false, false);
        setcookie("top_security_nossl", "", time()-3600, "/", "", false, false);
        setcookie("top_security_ssl", "", time()-3600, "/", "", false, false);

    }

    switch($_REQUEST["url"])
    {
        case "1" :

            header("Location: #");
            break;

        case "2" :

            header("Location: http://www.linkedin.com/in/malikmesellem");
            break;

        case "3" :

            header("Location: #");
            break;

        case "4" :

            header("Location: http://www.mmeit.be/en");
            break;

        default :

            header("Location: login.php");
            break;

    }

}

if(isset($_REQUEST["url"]) && ($_COOKIE["security_level"] != "1" && $_COOKIE["security_level"] != "2"))
{
   


    
    header("Location: " . $_REQUEST["url"]);

    exit;

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

<title>USSC_stand - Unvalidated Redirects & Forwards</title>

</head>

<body>
    
<header>

<h1>USSC_stand</h1>

<h2>Самое безопасное приложение в мире!</h2>

</header>    

<div id="menu">
      
    <table>
        
        <tr>
            
            <td><a href="portal.php">Bugs</a></td>
            <td><a href="password_change.php">Change Password</a></td>
            <td><a href="user_extra.php">Create User</a></td>
            <td><a href="security_level_set.php">Set Security Level</a></td>
            <td><a href="reset.php" onclick="return confirm('All settings will be cleared. Are you sure?');">Reset</a></td>            
            <td><a href="credits.php">Credits</a></td>
            <td><a href="#" target="_blank">Blog</a></td>
            <td><a href="logout.php" onclick="return confirm('Are you sure you want to leave?');">Logout</a></td>
            <td><font color="red">Welcome <?php if(isset($_SESSION["login"])){echo ucwords($_SESSION["login"]);}?></font></td>
            
        </tr>
        
    </table>   
   
</div> 

<div id="main">
    
    <h1>Unvalidated Redirects & Forwards (1)</h1>

    <p>Beam me up <?php if(isset($_SESSION["login"])){echo ucwords($_SESSION["login"]);} ?>...</p>

    <form action="<?php echo($_SERVER["SCRIPT_NAME"]);?>" method="GET">

<?php

if($_COOKIE["security_level"] == "1" || $_COOKIE["security_level"] == "2")
{

?>
        <select name="url">

            <option value="1">Blog</option>
            <option value="2">LinkedIn</option>
            <option value="3">Twitter</option>
            <option value="4">Company website</option>

        </select>
<?php

}

else
{

?>
        <select name="url">

            <option value="#">Blog</option>
            <option value="http://www.linkedin.com/in/malikmesellem">LinkedIn</option>
            <option value="#">Twitter</option>
            <option value="http://www.mmeit.be/en">Company website</option>

        </select>
<?php

}

?>

        <button type="submit" name="form" value="submit">Beam</button>

    </form>
    
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
    
<div id="security_level">
  
    <form action="<?php echo($_SERVER["SCRIPT_NAME"]);?>" method="POST">
        
        <label>Set your security level:</label><br />
        
        <select name="security_level">
            
            <option value="0">low</option>
            <option value="1">medium</option>
            <option value="2">high</option> 
            
        </select>
        
        <button type="submit" name="form_security_level" value="submit">Set</button>
        <font size="4">Current: <b><?php echo $security_level?></b></font>
        
    </form>   
    
</div>
    
<div id="bug">

    <form action="<?php echo($_SERVER["SCRIPT_NAME"]);?>" method="POST">
        
        <label>Choose your bug:</label><br />
        
        <select name="bug">
   
<?php


foreach ($bugs as $key => $value)
{
    
   $bug = explode(",", trim($value));
   




   
   echo "<option value='$key'>$bug[0]</option>";
 
}

?>


        </select>
        
        <button type="submit" name="form_bug" value="submit">Hack</button>
        
    </form>
    
</div>
      
</body>
    
</html>
