<?php



include("security.php");
include("security_level_check.php");
include("selections.php");

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

<title>USSC_stand - Credits</title>

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
            <td><font color="#ffb717">Credits</font></td>
            <td><a href="#" target="_blank">Blog</a></td>
            <td><a href="logout.php" onclick="return confirm('Are you sure you want to leave?');">Logout</a></td>
            <td><font color="red">Welcome <?php if(isset($_SESSION["login"])){echo ucwords($_SESSION["login"]);}?></font></td>

        </tr>

    </table>

</div>

<div id="main">

    <h1>Credits</h1>

    <p>O yeah... who am I? Well my name is Malik. I'm a security consultant working for my own company, <a href="http://www.mmeit.be/en" target="_blank">MME</a>.<br />
    We are specialized in Penetration Testing, Ethical Hacking, InfoSec Training, and Evil Bee Hunting.</p>

    <p>Download our <a href="http://goo.gl/uVBGnq" target="_blank">What is USSC_stand?</a> introduction tutorial, including free materials and exercises...<br />
    I'm also happy to give USSC_stand talks and workshops at your security convention or seminar!</p>

    <p>Need a training? We offer the following exclusive courses and workshops (on demand, at your location):

    <ul>

        <li>Attacking & Defending Web Apps with USSC_stand : 2-day Web Application Security course (<a href="http://goo.gl/ASuPa1" target="_blank">pdf</a>)</li>
        <li>Plant the Flags with USSC_stand : 4-hour offensive Web Application Hacking workshop (<a href="http://goo.gl/fAwCex" target="_blank">pdf</a>)</li>
        <li>Ethical Hacking Basics : 1-day Ethical Hacking course (<a href="http://goo.gl/09ccSf" target="_blank">pdf</a>)</li>
        <li>Ethical Hacking Advanced : 1-day comprehensive Ethical Hacking course (<a href="http://goo.gl/PHLnQF" target="_blank">pdf</a>)</li>
        <li>Windows Server 2012 Security : 2-day Windows Security course (<a href="http://goo.gl/4C0JfW" target="_blank">pdf</a>)</li>

    </ul>

    </p>

    <p>Special thanks to the Netsparker team!</p>

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