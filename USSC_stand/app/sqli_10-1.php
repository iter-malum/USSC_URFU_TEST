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
<script src="js/jquery-1.4.4.min.js"></script>

<title>USSC_stand - SQL Injection</title>

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

    <h1>SQL Injection (AJAX/JSON/jQuery)</h1>

    <form>

        <p>

        <label for="title">Search for a movie:</label>
        <input type="text" id="title" name="title" size="25">

        </p>

    </form>

    <table id="table_yellow">

        <tr height="30" bgcolor="#ffb717" align="center">

            <td width="200"><b>Title</b></td>
            <td width="80"><b>Release</b></td>
            <td width="140"><b>Character</b></td>
            <td width="80"><b>Genre</b></td>
            <td width="80"><b>IMDb</b></td>

        </tr>

    </table>

    <script>

        $("#title").keyup(function(){

            var search = {title: $("#title").val()};


            $.getJSON("sqli_10-2.php", search, function(data){
                init_table();

                var total = 0;
                $.each(data, function(key, val){
                    total++;
                    $("#table_yellow tr:last").after("<tr><td>" + val.title + "</td><td align='center'>" + val.release_year + "</td><td>" + val.main_character + "</td><td align='center'>" + val.genre + "</td><td align='center'><a href='http://www.imdb.com/title/" + val.imdb + "' target='_blank'>Link</a></td></tr>");
                });

                if (total == 0)
                {
                    $("#table_yellow tr:last").after("<tr height='30'><td colspan='5' width='580'>No movies were found!</td></tr>");
                }
            })

        });

        function init_table(){
            $("#table_yellow").html("<tr height='30' bgcolor='#ffb717' align='center'>" +
                    "<td width='200'><b>Title</b></td>" +
                    "<td width='80'><b>Release</b></td>" +
                    "<td width='140'><b>Character</b></td>" +
                    "<td width='80'><b>Genre</b></td>" +
                    "<td width='80'><b>IMDb</b></td>" +
                    "</tr>"
                    );
        }

    </script>

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