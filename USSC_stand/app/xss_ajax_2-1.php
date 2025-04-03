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
<script src="js/json2.js"></script>

<title>USSC_stand - XSS</title>

</head>

<body onload="process()">
    
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

    <h1>XSS - Reflected (AJAX/JSON)</h1>

    <p>

    <label for="title">Search for a movie:</label>
    <input type="text" id="title" name="title">
    
    </p>

    <div id="result"></div>
    
    <script>
    

        var xmlHttp = createXmlHttpRequestObject(); 


        function createXmlHttpRequestObject() 
        {	

            var xmlHttp;

            if(window.ActiveXObject)
            {
                try
                {
                    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e)
                {
                    xmlHttp = false;
                }
            }

            else
            {
                try
                {
                    xmlHttp = new XMLHttpRequest();
                }
                catch (e)
                {
                    xmlHttp = false;
                }
            }

            if(!xmlHttp)
                alert("Error creating the XMLHttpRequest object.");
            else 
                return xmlHttp;
        }


        function process()
        {

            if(xmlHttp.readyState == 4 || xmlHttp.readyState == 0)
            {


                title = encodeURIComponent(document.getElementById("title").value);

                xmlHttp.open("GET", "xss_ajax_2-2.php?title=" + title, true);  

                xmlHttp.onreadystatechange = handleServerResponse;

                xmlHttp.send(null);
            }
            else

                setTimeout("process()", 1000);
        }


        function handleServerResponse()
        {

            if(xmlHttp.readyState == 4)
            {

                if(xmlHttp.status == 200)
                {

<?php

                if($_COOKIE["security_level"] == "2")
                {

?>
                    JSONResponse = JSON.parse(xmlHttp.responseText);
<?php

                }

                else
                {
?>
                    JSONResponse = eval("(" + xmlHttp.responseText + ")");
<?php

                    }

?>



                    result = JSONResponse.movies[0].response;

                    //for (var i=0; i<JSONResponse.movies.length; i++)



                    document.getElementById("result").innerHTML = result;

                    setTimeout("process()", 1000);
                } 

                else 
                {
                    alert("There was a problem accessing the server: " + xmlHttp.statusText);
                }
            }
        }

    </script>

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