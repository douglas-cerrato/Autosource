<?php 
session_start();

    include("functions.php");
    include("connection.php");

    if($_SERVER['REQUEST_METHOD'] =="POST")
    {
        $email = $_POST['email'];
        $psswd = $_POST['psswd'];
    

        if(!empty($email) && !empty($psswd))
        {
            $query = "select * from member where Email = '$email' LIMIT 1";
            $result = mysqli_query($con, $query);

            if($result && mysqli_num_rows($result) > 0)
            {
                $user_data = mysqli_fetch_assoc($result);
                if($user_data['Passwd'] == $psswd)
                {
                    $_SESSION['user_id'] = $user_data['Email'];
                    header("Location: index.php");
                    die;
                }
            }

        }
    }
?>
<!DOCTYPE html> 
<html lang="en">
    <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" type="text/css" href="style.css?v=2">
            <title>Autosource | Log In</title>
        </head>
    <body style="background-color:#1B1A17;">
    <header id="header">
            <div id="Inside_Header">
                <a href="signup.php">
                    <button id="Login_Signup_Button">Sign Up</button>
                </a>
            </div>
        </header>
        <div id="lower_half">
            <div id="Login_Menu">
                <div id="Inside_Login_Menu">
                    <p id="Login_Message">Login to Autosource</p>
                    <form method="POST" action="login.php">
                        <input type="text" id="Login_Text" name="email" placeholder="Email address"><br><br>
                        <input type="password" id="Login_Text" name="psswd" placeholder="Password"><br><br>
                        <input type="submit" id="Login_Button" value="Log in"><br>
                    </form>
                </div>
            </div>
        </div>
        </body>    
</html>