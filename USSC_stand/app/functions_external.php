<?php



function no_check($data)
{    
   
    return $data;
        
}

function email_check_1($data)
{
    
    return preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $data);
    
}

function email_check_2($data)
{
    
    return filter_var($data, FILTER_VALIDATE_EMAIL);  
     
}

function maili_check_1($data)
{


    $input = urldecode($data); 
        
    $input = str_replace("\n", "", $input);
    $input = str_replace("\r", "", $input);
    $input = str_replace("bcc:", "", $input);
    
    return $input;
    
}

function maili_check_2($data)
{


    $input = urldecode($data);
    
    $input = filter_var($input, FILTER_SANITIZE_EMAIL);
            
    return $input;
    
}

function url_check_1($data)
{


    $input = urldecode($data);
    
    $input = filter_var($input, FILTER_SANITIZE_URL);
            
    return $input;
    
}

function url_check_2($data)
{


    $input = urlencode($data);  
            
    return $input;
    
}

function xss_check_1($data)
{
    

    $input = str_replace("<", "&lt;", $data);
    $input = str_replace(">", "&gt;", $input);
    





    $input = urldecode($input);
    
    return $input;
    
}

function xss_check_2($data)
{
  

    
    return htmlentities($data, ENT_QUOTES);
    
}

function xss_check_3($data, $encoding = "UTF-8")
{







    
    return htmlspecialchars($data, ENT_QUOTES, $encoding);
       
}

function xss_check_4($data)
{
  



    
    return addslashes($data);
    
}

function xmli_check_1($data)
{


    $input = str_replace("(", "", $data);
    $input = str_replace(")", "", $input);
    $input = str_replace("=", "", $input);
    $input = str_replace("'", "", $input);
    $input = str_replace("[", "", $input);
    $input = str_replace("]", "", $input);
    $input = str_replace(":", "", $input);
    $input = str_replace(",", "", $input);
    $input = str_replace("*", "", $input);
    $input = str_replace("/", "", $input);
    $input = str_replace(" ", "", $input);
   
    return $input;
    
}

function ldapi_check_1($data)
{


    $input = str_replace("(", "", $data);
    $input = str_replace(")", "", $input);
    $input = str_replace("=", "", $input);
    $input = str_replace("&", "", $input);
    $input = str_replace("|", "", $input);
    $input = str_replace("*", "", $input);
    $input = str_replace(" ", "", $input);
   
    return $input;
    
}

function commandi_check_1($data)
{
    
    $input = str_replace("&", "", $data);
    $input = str_replace(";", "", $input);
    
    return $input;
    
}

function commandi_check_2($data)
{
   
    return escapeshellcmd($data);
    
}

function commandi_check_3($data)
{
    
    $input = str_replace("&", "", $data);
    $input = str_replace(";", "", $input);
    $input = str_replace("|", "", $input);
    
    return $input;
    
}

function sqli_check_1($data)
{
   
    return addslashes($data);
    
}

function sqli_check_2($data)
{
   
    return mysql_real_escape_string($data);
    
}

function sqli_check_3($link, $data)
{
   
    return mysqli_real_escape_string($link, $data);
    
}

function sqli_check_4($data)
{


    

    $input = str_replace("'", "''", $data);
   
    return $input;
    
}

function file_upload_check_1($file, $file_extensions  = array("asp", "aspx", "dll", "exe", "jsp", "php"), $directory = "images")
{
    
    $file_error = "";
    

    if($file["name"] == "")
    {
        
        $file_error = "Please select a file...";
        
        return $file_error;
        
    }
    

    switch($file["error"])
    

    
    {
        
        case 1 : $file_error = "Sorry, the file is too large. Please try again...";
                 break;
             
        case 2 : $file_error = "Sorry, the file is too large. Please try again...";
                 break;
             
        case 3 : $file_error = "Sorry, the file was only partially uploaded. Please try again...";
                 break;
             
        case 6 : $file_error = "Sorry, a temporary folder is missing. Please try again...";
                 break;
             
        case 7 : $file_error = "Sorry, the file could not be written. Please try again...";
                 break;
             
        case 8 : $file_error = "Sorry, a PHP extension stopped the file upload. Please try again...";
                 break;
             
    }
    
    if($file_error)
    {
        
        return $file_error;
        
    }
    

    $file_array = explode(".", $file["name"]);
    


    $file_extension = strtolower($file_array[count($file_array) - 1]);
    

    if(in_array($file_extension, $file_extensions))
    {
        
       $file_error = "Sorry, the file extension is not allowed. The following extensions are blocked: <b>" . join(", ", $file_extensions) . "</b>";
       
       return $file_error;
       
    }
    

    if(is_file("$directory/" . $file["name"]))
    {
        
        $file_error = "Sorry, the file already exists. Please rename the file...";      
        
    }
    
    return $file_error;
    
}

function file_upload_check_2($file, $file_extensions  = array("jpeg", "jpg", "png", "gif"), $directory = "images")
{
    
    $file_error = "";
    

    if($file["name"] == "")
    {
        
        $file_error = "Please select a file...";
        
        return $file_error;
        
    }
    

    switch($file["error"])
    

    
    {
        
        case 1 : $file_error = "Sorry, the file is too large. Please try again...";
                 break;
             
        case 2 : $file_error = "Sorry, the file is too large. Please try again...";
                 break;
             
        case 3 : $file_error = "Sorry, the file was only partially uploaded. Please try again...";
                 break;
             
        case 6 : $file_error = "Sorry, a temporary folder is missing. Please try again...";
                 break;
             
        case 7 : $file_error = "Sorry, the file could not be written. Please try again...";
                 break;
             
        case 8 : $file_error = "Sorry, a PHP extension stopped the file upload. Please try again...";
                 break;
             
    }
    
    if($file_error)
    {
        
        return $file_error;
        
    }
    

    $file_array = explode(".", $file["name"]);
    


    $file_extension = strtolower($file_array[count($file_array) - 1]);
    

    if(!in_array($file_extension, $file_extensions))
    {
        
       $file_error = "Sorry, the file extension is not allowed. Only the following extensions are allowed: <b>" . join(", ", $file_extensions) . "</b>";
       
       return $file_error;
       
    }
    

    if(is_file("$directory/" . $file["name"]))
    {
        
        $file_error = "Sorry, the file already exists. Please rename the file...";      
        
    }
    
    return $file_error;
    
}

function directory_traversal_check_1($data)
{


    
    $directory_traversal_error = "";  
    

    if(strpos($data, "../") !== false ||
       strpos($data, "..\\") !== false ||
       strpos($data, "/..") !== false ||
       strpos($data, "\..") !== false)
            
    {

        $directory_traversal_error = "Directory Traversal detected!";
    
    }
    
    /*
    else
    {
    
        echo "Good path!";
    
    }     
     */
    
    return $directory_traversal_error;

}

function directory_traversal_check_2($data)
{


    
    $directory_traversal_error = "";  
    

    if(strpos($data, "../") !== false ||
       strpos($data, "..\\") !== false ||
       strpos($data, "/..") !== false ||
       strpos($data, "\..") !== false ||
       strpos($data, ".") !== false)
            
    {

        $directory_traversal_error = "Directory Traversal detected!";
    
    }
    
    /*
    else
    {
    
        echo "Good path!";
    
    }     
     */
    
    return $directory_traversal_error;

}

function directory_traversal_check_3($user_path,$base_path = "")
{
    
    $directory_traversal_error = "";
    
    $real_base_path = realpath($base_path);



    $real_user_path = realpath($user_path);





    if(strpos($real_user_path, $real_base_path) === false)
    {
    
        $directory_traversal_error = "<font color=\"red\">An error occurred, please try again.</font>";
    
    }

    /*
    else
    {
    
        echo "Good path!";
    
    }     
     */
    
    return $directory_traversal_error;

}


function hpp_check_1($data)
{



   
    $query_string  = explode("&", $data);    

    $i = "";
    $param = array();
    $param_variables = array();

    foreach($query_string as $i)
    {

            $param = explode("=", $i);
            array_push($param_variables, $param[0]);

    }

    $count_unique = count(array_unique($param_variables));
    $count_total = count($param_variables);
    
    $hpp_detected = "";



    
    if($count_unique < $count_total)
    {

        $hpp_detected = "<font color=\"red\">HTTP Parameter Pollution detected!</font>";

    }

    return $hpp_detected;

}

function rlfi_check_1($data)
{


    $input = str_replace(chr(0), "", $data);


    $input = str_replace("../", "", $input);
    $input = str_replace("..\\", "", $input);
    $input = str_replace("/..", "", $input);
    $input = str_replace("\..", "", $input);

    return $input;

}

function random_string()
{
    
    $character_set_array = array();
    
    $character_set_array[] = array("count" => 3, "characters" => "abcdefghijklmnopqrstuvwxyz");
    $character_set_array[] = array("count" => 1, "characters" => "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
    $character_set_array[] = array("count" => 1, "characters" => "0123456789");

    $character_set_array[] = array("count" => 1, "characters" => "@#$+-?!");
    
    $temp_array = array();
    
    foreach($character_set_array as $character_set)
    {
        
        for($i=0; $i<$character_set["count"]; $i++)
        {
            
            $temp_array[] = $character_set["characters"][rand(0, strlen($character_set["characters"]) - 1)];
            
        }
        
    }
    
    shuffle($temp_array);
    
    return implode('', $temp_array);
}


function bin_sid_to_text($binsid)
{
    
    $hex_sid = bin2hex($binsid);
    $rev = hexdec(substr($hex_sid, 0, 2));// Gets the revision-part of the SID
    $subcount = hexdec(substr($hex_sid, 2, 2));// Gets count of sub-auth entries
    $auth = hexdec(substr($hex_sid, 4, 12));// SECURITY_NT_AUTHORITY
    $result = "$rev-$auth";
    

    /*
    for($x=0; $x<$subcount; $x++)
    {

        $subauth[$x] = hexdec(little_endian(substr($hex_sid, 16+($x*8), 8)));  // Gets all SECURITY_NT_AUTHORITY
        $result.= "-" . $subauth[$x];

    }
    */ 
    

    $result = hexdec(little_endian(substr($hex_sid, 16+(($subcount-1)*8), 8)));
        
    return $result;
     
}
 

function little_endian($hex)
{
     
    for($x = strlen($hex)-2; $x>=0; $x=$x-2)
    {

        $result.= substr($hex, $x, 2);

    }

    return $result;

}

?>