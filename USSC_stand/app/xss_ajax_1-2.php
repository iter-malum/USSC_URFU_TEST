<?php



include("security.php");
include("security_level_check.php");
include("functions_external.php");

function xss($data)
{
         
    switch($_COOKIE["security_level"])
    {
        
        case "0" :
            
            $data = no_check($data);            
            break;
        
        case "1" :
            
            $data = xss_check_4($data);
            break;
        
        case "2" : 
                       
            $data = xss_check_3($data);            
            break;
        
        default :
            
            $data = no_check($data);            
            break;   

    }       

    return $data;

}

if(isset($_GET["title"]))
{


    $movies = array("CAPTAIN AMERICA", "IRON MAN", "SPIDER-MAN", "THE INCREDIBLE HULK", "THE WOLVERINE", "THOR", "X-MEN");


    $title = $_GET["title"];


    header("Content-Type: text/xml; charset=utf-8");


    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>";


    echo "<response>";


    if(in_array(strtoupper($title), $movies))
        echo "Yes! We have that movie...";
    else if(trim($title) == "")
        echo "HINT: our master really loves Marvel movies :)";
    else
        echo xss($title) . "??? Sorry, we don't have that movie :(";


    echo "</response>";

}

else 
{
    
    echo "<font color=\"red\">Try harder :p</font>";
    
}

?>