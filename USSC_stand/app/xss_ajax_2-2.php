<?php




include("security.php");
include("security_level_check.php");
include("functions_external.php");

if(isset($_GET["title"]))
{


    $movies = array("CAPTAIN AMERICA", "IRON MAN", "SPIDER-MAN", "THE INCREDIBLE HULK", "THE WOLVERINE", "THOR", "X-MEN");


    $title = $_GET["title"];

    if($_COOKIE["security_level"] == "2")
    {
        

        header("Content-Type: text/json; charset=utf-8");
        

        if(in_array(strtoupper($title), $movies))
        {
       

            $movies = array(
                "movies" => array(
                    array(
                        "response" => "Yes! We have that movie..."
                    )
                )
            );

        }

        else if(trim($title) == "")       
        {


            $movies = array(
                "movies" => array(
                    array(
                        "response" => "HINT: our master really loves Marvel movies :)"
                    )
                )
            );

        }

        else        
        {


            $movies = array(
                "movies" => array(
                    array(
                        "response" => xss_check_3($title) . "??? Sorry, we don't have that movie :("
                    )
                )
            );

        }



        echo json_encode($movies); 

    }

    else      
    {

        if($_COOKIE["security_level"] == "1")
        {


            header("Content-Type: text/json; charset=utf-8");

        }


        if(in_array(strtoupper($title), $movies))
            echo '{"movies":[{"response":"Yes! We have that movie..."}]}';
        else if(trim($title) == "")
            echo '{"movies":[{"response":"HINT: our master really loves Marvel movies :)"}]}';
         else
            echo '{"movies":[{"response":"' . $title . '??? Sorry, we don\'t have that movie :("}]}';

    }

}

else 
{
    
    echo "<font color=\"red\">Try harder :p</font>";
    
}


/*
$movies = array(
    "movies" => array(
        array(
            "title" => "Iron Man"
        ),
        array(
            "title" => "Captain America"
        )
    )
);
*/

?>