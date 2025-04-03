<?php



include("security.php");
include("security_level_check.php");
include("selections.php");
include("functions_external.php");
include("admin/settings.php");

$email = "";
$recipient = $smtp_recipient;
$subject = "USSC_stand - Mail Header Injection (SMTP)";
$message = "";


if(isset($_POST["form"]))
{


    if(!email_check_2($_POST["email"]) && ($_COOKIE["security_level"] == "1" || $_COOKIE["security_level"] == "2"))
    {

        $message = "<font color=\"red\">Enter a valid e-mail address!</font>";

    }

    else
    {


        if($smtp_server != "")
        {

            ini_set( "SMTP", $smtp_server);

            //Debugging


        }


        if($_COOKIE["security_level"] == "2")
        {

            $email = maili_check_2($_POST["email"]);




        }

        else
        {       

            $email = $_POST["email"];




        }


        $content =  "Content:\n\n";
        $content .= "Name: " . $_POST["name"] . "\n";
        $content .= "E-mail: " . $email . "\n";
        $content .= "Remarks: \n";
        $content .= $_POST["remarks"] . "\n\n";
        $content .= "Greets from USSC_stand!";


        $status = @mail($recipient, $subject, $content, "From: $email");

        if($status != true)
        {

            $message = "<font color=\"red\">An e-mail could not be sent...</font>";





        }

        else
        {

            $message = "<font color=\"green\">Your message has been sent to our master bee!</font>";





 
        }

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

<title>USSC_stand - Mail Header Injection</title>

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

    <h1>Mail Header Injection (SMTP)</h1>

    <p>E-mail us your questions at <?php echo $smtp_recipient?>.</p>

    <form action="<?php echo($_SERVER["SCRIPT_NAME"]);?>" method="POST">

        <p><label for="name">Name:</label><br />
        <input type="text" id="name" name="name"></p>
        <?php


        if($_COOKIE["security_level"] == "1" or $_COOKIE["security_level"] == "2")
        {

        ?>

        <p><label for="email">E-mail:</label><br />
        <input type="text" id="email" name="email"></p>
        <?php

        }
        else
        {

        ?>

        <p><label for="email">E-mail:</label><br />
        <textarea name="email"></textarea>
        <?php

        }

        ?>
        <p><label for="remarks">Remarks:</label><br />
        <textarea name="remarks" cols="50" rows="3" id="remarks"></textarea></p>

        <button type="submit" name="form" value="submit">Send</button><?php echo "&nbsp;&nbsp;" . $message;?>

    </form>

</div>

<div id="side">

    <a href="#" target="blank_" class="button"><img src="./images/twitter.png"></a>
    <a href="#" target="blank_" class="button"><img src="./images/linkedin.png"></a>
    <a href="#" target="blank_" class="button"><img src="./images/facebook.png"></a>
    <a href="#" target="blank_" class="button"><img src="./images/blogger.png"></a>

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