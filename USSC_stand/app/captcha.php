<?php



include("security.php");
include("security_level_check.php");
include("functions_external.php");

$captcha = random_string();
$_SESSION["captcha"] = $captcha;







$image = imagecreatefrompng("images/captcha.png");


$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
$orange = imagecolorallocate($image, 222, 77, 14);





$font = "fonts/arial.ttf";







imagettftext($image, 20, 0, 75, 38, $orange, $font, $captcha);
        

header ("Content-type: image/png");
imagepng($image);


imagedestroy($image)

?>