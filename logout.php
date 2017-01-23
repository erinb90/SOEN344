<?php
session_start();
unset($_SESSION);
session_destroy();
// redirect user to login page
header("location:  index.php");
exit;