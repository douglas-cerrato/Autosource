<?php

$dbhost = "localhost";
$dbstring = "autosource";

$con = mysqli_connect($dbhost, $dbstring,$dbstring,$dbstring);
if(!$con){
    echo mysqli_connect_error();
    die();
}
