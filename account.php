<?php
session_start();

    include('functions.php');
    include('connection.php');

    $session_mode = "";
    //Uses the checks_login function with DB getting passed. Check_Login checks if a session is set, and if so it 
    //uses the $_SESSION['user_id'] to return the member table as user_data. We use this to communicate to other tables
    $user_data = check_login($con);

    if(empty($user_data)){
        header("Location: login.php");
    }

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if(isset($_POST["logout"])){
            session_destroy();
            header("Location: index.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="style.css?v=1">
        <title>Autosource | Account</title>
    </head>
    <body style="background-color:#1B1A17;">
        <div>
    </body>
</html>