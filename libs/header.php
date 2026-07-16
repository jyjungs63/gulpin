<?php

session_start();
if (isset($_SESSION['user'])) {
    echo "<meta name='user' content =" . $_SESSION['user'] . ">";
    echo "<meta name='authenticated' content=" .$_SESSION["authenticated"]. ">";
    echo "<meta name='name' content=" .$_SESSION["name"]. ">";
    echo "<meta name='role' content=" .$_SESSION["role"]. ">";
    echo "<meta name='confirm' content=" .$_SESSION["confirm"]. ">";
    echo "<meta name='location' content=" .$_SESSION["location"]. ">";
} 
else
{
    //header('location:../login/login.php');
}
?>