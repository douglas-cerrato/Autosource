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
    <html>
        <head>
            <title>Autosource | Log in</title>
            <link rel="stylesheet" type="text/css" href="style.css?v=1">
        </head>

        <body style="background-color:black">
            <div id="login-top-bar">
                <span id="logo">
                    <img src="Autosource-Logo.png" alt="Autosource Logo">
                </span>
                <a href="signup.php">
                    <button id="signup-button">Sign Up</button>
                </a>
            </div>
            <div id="Login-Bar">
                <br><br><br><br>Log in to Autosource Motors<br><br><br><br>
                <form method="POST" action="login.php">
                    <input type="text" id="text" name="email" placeholder="Email address"><br><br>
                    <input type="password" id="text" name="psswd" placeholder="Password"><br><br>
                    <input type="submit" id="login_button" value="Log in"><br>
                </form>
                <a id="info_links" href="index.php">Home</a>
                <a id="info_links" href="#">About</a>
                <a id="info_links" href="#">Contact</a>
            </div>
        </body>
    </html>