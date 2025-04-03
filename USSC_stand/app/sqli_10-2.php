<?php



include("security.php");
include("security_level_check.php");
include("functions_external.php");
include("connect.php");

function sqli($data)
{

    switch ($_COOKIE["security_level"])
    {

        case "0" :

            $data = no_check($data);
            break;

        case "1" :

            $data = sqli_check_1($data);
            break;

        case "2" :

            $data = sqli_check_2($data);
            break;

        default :

            $data = no_check($data);
            break;
    }

    return $data;
}

if(!empty($_GET["title"]))
{


    $title = $_GET["title"];


    $sql = "SELECT * FROM movies WHERE title LIKE '%" . sqli($title) . "%'";


    $recordset = mysql_query($sql, $link);


    if(mysql_num_rows($recordset) != 0)
    {

        while($row = mysql_fetch_array($recordset))
        {

            $movies[] = $row;

        }

    }

    else
    {

        $movies = array();

    }

}

else
{

    $movies = array();

}

header("Content-Type: text/json; charset=utf-8");


echo json_encode($movies);

?>