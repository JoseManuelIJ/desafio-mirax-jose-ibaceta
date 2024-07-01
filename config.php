<?php
function connection(){
    //datos de la base de datos
    $serverName= "localhost";
    $database= "Mirax";
    $username= "root";
    $password= "password";
    //conexion con la base de datos
    $connection= mysqli_connect($serverName,$username,"password",$database);
    //en caso de tildes u otros elementos
    mysqli_set_charset($connection,"utf8");
    return $connection;
}


?>