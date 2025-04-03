<?php



include("security.php");
include("security_level_check.php");


$_SESSION = array();
session_destroy();


setcookie("admin", "", time()-3600, "/", "", false, false);
setcookie("movie_genre", "", time()-3600, "/", "", false, false);
setcookie("secret", "", time()-3600, "/", "", false, false);
setcookie("top_security", "", time()-3600, "/", "", false, false);
setcookie("top_security_nossl", "", time()-3600, "/", "", false, false);
setcookie("top_security_ssl", "", time()-3600, "/", "", false, false);

header("Location: login.php");

?>