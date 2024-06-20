<?php
$host='Localhost';
$user='root';
$pass='';
$db='allegro-2';
try{
    $polaczenie= new PDO("mysql:host={$host};dbname={$db};charset=utf8",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
}
catch(PDOException $e){
    exit('Bład servera');
}