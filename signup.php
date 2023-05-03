<?php
session_start();
    
    include("connection.php");
    include("functions.php");
    
    $error = "";

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $fname = $_POST["fname"];
        $lname = $_POST["lname"];
        $email = $_POST["email"];
        $phnum = $_POST["phnum"];
        if($_POST["psswd"] == $_POST["psswd2"])
        {
            $psswd = $_POST["psswd"];
            if(!empty($fname) && !empty($lname) && !empty($email) && !empty($phnum) && !empty($psswd))
            {
                $query = "INSERT INTO member (Email,Fname,Lname,Passwd,PhoneNum) VALUES ('$email','$fname','$lname','$psswd','$phnum')";
                mysqli_query($con, $query);
                header("Location: login.php");
                die;
            }else{
                die("Could not create account in our database");
            }
        }else{
            $error = "Passwords did not match. Try again.";
        }

    }
?>
<!DOCTYPE html>
    <html>
        <head>
            <title>Autosource | Sign Up</title>
            <link rel="stylesheet" type="text/css" href="style.css?v=1">
        </head>

        <body style="background-color: black">
            <div id="signup-box">
                <span id="logo">
                    <img src="Autosource-Logo.png" alt="Autosource Logo" />
                </span>
                <br><br>Sign Up for Autosource<br><br><br><br>
                <form method="post" action="signup.php">
                    <input type="text" id="signup_text" name="fname" placeholder="First Name"><br><br>
                    <input type="text" id="signup_text" name="lname" placeholder="Last Name"><br><br>
                    <input type="text" id="signup_text" name="email" placeholder="Email address"><br><br>
                    <input type="text" id="signup_text" name="phnum" placeholder="Phone Number"><br><br>
                    <input type="password" id="signup_text" name="psswd" placeholder="Password"><br><br>
                    <input type="password" id="signup_text" name="psswd2" placeholder="Re-type Password"><br><br>
                    <input type="submit" id="createacc_button" value="Create Account"><br>
                </form>
                <?php if ($error != "") { ?>
                    <p id="error_message">
                        <?php echo $error; ?>
                    </p>
                <?php } ?> 
            </div>
        </body>
    </html>