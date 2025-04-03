<?php



include("security.php");
include("security_level_check.php");
include("admin/settings.php");

$bugs = file("bugs.txt");

if(isset($_POST["form_bug"]) && isset($_POST["bug"]))
{
        
            $key = $_POST["bug"]; 
            $bug = explode(",", trim($bugs[$key]));
            


            
            header("Location: " . $bug[1]);
            
            exit;
   
}
 
if(isset($_POST["form_security_level"]) && isset($_POST["security_level"]))    
{
    
    $security_level_cookie = $_POST["security_level"];
    
    switch($security_level_cookie)
    {

        case "0" :

            $security_level_cookie = "0";
            break;

        case "1" :

            $security_level_cookie = "1";
            break;

        case "2" :

            $security_level_cookie = "2";
            break;

        default : 

            $security_level_cookie = "0";
            break;

    }

    if($evil_bee == 1)
    {

        setcookie("security_level", "666", time()+60*60*24*365, "/", "", false, false);

    }
    
    else        
    {
      
        setcookie("security_level", $security_level_cookie, time()+60*60*24*365, "/", "", false, false);
        
    }

    header("Location: ba_insecure_login.php");
    
    exit;

}

if(isset($_COOKIE["security_level"]))
{

    switch($_COOKIE["security_level"])
    {
        
        case "0" :
            
            $security_level = "low";
            break;
        
        case "1" :
            
            $security_level = "medium";
            break;
        
        case "2" :
            
            $security_level = "high";
            break;

        case "666" :

            $security_level = "666";
            break;
        
        default : 
            
            $security_level = "low";
            break;

    }
    
}

else
{
     
    $security_level = "not set";
    
}

$message = "";




if(isset($_REQUEST["secret"]))   
{ 
        
    if($_REQUEST["secret"] == "hulk smash!")
    {
        
        $message = "<font color=\"green\">The secret was unlocked: HULK SMASH!</font>";
        
    }
    
    else        
    {

        $message = "<font color=\"red\">Still locked... Don't lose your temper Bruce!</font>";

    }
    
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

<title>USSC_stand - Broken Authentication</title>

<script language="javascript">   

function unlock_secret()
{

    var USSC_stand = "bash update killed my shells!"

    var a = USSC_stand.charAt(0);  var d = USSC_stand.charAt(3);  var r = USSC_stand.charAt(16);
    var b = USSC_stand.charAt(1);  var e = USSC_stand.charAt(4);  var j = USSC_stand.charAt(9);
    var c = USSC_stand.charAt(2);  var f = USSC_stand.charAt(5);  var g = USSC_stand.charAt(4);
    var j = USSC_stand.charAt(9);  var h = USSC_stand.charAt(6);  var l = USSC_stand.charAt(11);
    var g = USSC_stand.charAt(4);  var i = USSC_stand.charAt(7);  var x = USSC_stand.charAt(4);
    var l = USSC_stand.charAt(11); var p = USSC_stand.charAt(23); var m = USSC_stand.charAt(4);
    var s = USSC_stand.charAt(17); var k = USSC_stand.charAt(10); var d = USSC_stand.charAt(23);
    var t = USSC_stand.charAt(2);  var n = USSC_stand.charAt(12); var e = USSC_stand.charAt(4);
    var a = USSC_stand.charAt(1);  var o = USSC_stand.charAt(13); var f = USSC_stand.charAt(5);
    var b = USSC_stand.charAt(1);  var q = USSC_stand.charAt(15); var h = USSC_stand.charAt(9);
    var c = USSC_stand.charAt(2);  var h = USSC_stand.charAt(2);  var i = USSC_stand.charAt(7);
    var j = USSC_stand.charAt(5);  var i = USSC_stand.charAt(7);  var y = USSC_stand.charAt(22);
    var g = USSC_stand.charAt(1);  var p = USSC_stand.charAt(4);  var p = USSC_stand.charAt(28);
    var l = USSC_stand.charAt(11); var k = USSC_stand.charAt(14);
    var q = USSC_stand.charAt(12); var n = USSC_stand.charAt(12);
    var m = USSC_stand.charAt(4);  var o = USSC_stand.charAt(19);

    var secret = (d + "" + j + "" + k + "" + q + "" + x + "" + t + "" +o + "" + g + "" + h + "" + d + "" + p);

    if(document.forms[0].passphrase.value == secret)
    { 
              

        location.href="<?php echo($_SERVER["SCRIPT_NAME"]); ?>?secret=" + secret;

    }
    
    else
    {
        

        location.href="<?php echo($_SERVER["SCRIPT_NAME"]); ?>?secret=";
                
    }

}	

</script>

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

    <h1>Broken Auth. - Insecure Login Forms</h1>

    <p>Enter the correct passphrase to unlock the secret.</p>

    <form>   

        <p><label for="name">Name:</label><font color="white">brucebanner</font><br />
        <input type="text" id="name" name="name" size="20" value="brucebanner" /></p> 

        <p><label for="passphrase">Passphrase:</label><br />
        <input type="password" id="passphrase" name="passphrase" size="20" /></p>

        <input type="button" name="button" value="Unlock" onclick="unlock_secret()" /><br />

    </form>

    </br >    
    <?php echo $message;?>    
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