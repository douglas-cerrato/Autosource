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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" type="text/css">
    <title>Autosource | Sign Up</title>
</head>
<body style="background-color:#1B1A17;">
    <div id="SignUp_Menu">
        <div id="Inside_SignUp">
            <div id="Inside_Inside_SignUp">
                <p id="SignUp_Text">Sign Up for Autosource</p>
                <form method="post" action="signup.php">
                    <input type="text" id="SignUp_Input" name="fname" placeholder="First Name"><br><br>
                    <input type="text" id="SignUp_Input" name="lname" placeholder="Last Name"><br><br>
                    <input type="text" id="SignUp_Input" name="email" placeholder="Email address"><br><br>
                    <input type="text" id="SignUp_Input" name="phnum" placeholder="Phone Number"><br><br>
                    <input type="password" id="SignUp_Input" name="psswd" placeholder="Password"><br><br>
                    <input type="password" id="SignUp_Input" name="psswd2" placeholder="Re-type Password"><br><br>
                    <input type="submit" id="CreateAcc_Button" value="Create Account"><br>
                </form>
                <?php if ($error != "") { ?>
                    <p id="Error_Message">
                        <?php echo $error; ?>
                    </p>
                <?php } ?> 
            </div>
        </div>
    </div>
</body>
</html>