<?php  
session_start();

session_unset();
session_destroy();

header("Location: http://localhost/qr-code-login-system/");

?>