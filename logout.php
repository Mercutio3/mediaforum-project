<?php
//Ends the session, effectively logging out.
//Access to all login-exclusive pages will be disabled.
session_start();
session_unset();
session_destroy();
header("Location: login.html");
exit();
?>