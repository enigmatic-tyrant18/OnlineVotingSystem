<?php
$host = 'localhost';
$user = 'bhagatayush710@gmail.com'; 
$password = 'bhagatji'; 
$database = 'onlinevotingsystem';

$db = mysqli_connect($host, $user, $password, $database);
// Check the connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}