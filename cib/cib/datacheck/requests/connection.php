<?php
$servername = "localhost";
$username = "root";
//$password = "Akhuwat@321";
$password = "";
try {
    $conn = new PDO("mysql:host=$servername;dbname=cib", $username, $password);
    // set the PDO error mode to exception
    //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>
