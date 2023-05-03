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
    <html>
        <head>
            <title>Autosource | Account</title>
            <link rel="stylesheet" type="text/css" href="style.css?v=2">
        </head>
        <body style="background-color: black; color: white;">
            <div id="CenterContainer">
                <p style="font-size: 175%; position:absolute;left:13%;top:2%;"> 
                    Hello <?php echo $user_data['Fname']; ?>! How can we help you?
                </p>
                <p style="font-size: 175%; position:absolute;left:36%;top:16%;">
                    Information
                </p>
                <p style="font-weight:bolder;font-size:175%; position:absolute;left:5%;top:21%;line-height:200%;">
                    First Name: <?php echo $user_data['Fname']; ?> <br>
                    Last Name: <?php echo $user_data['Lname']; ?> <br>
                    Email: <?php echo $user_data['Email']; ?> <br>
                    Phone Number: <?php echo $user_data['PhoneNum']; ?> <br>
                </p>
                <p style="font-weight:bolder;font-size:175%; position:absolute;left:5%;top:55%;line-height:200%;">
                    Reset Password
                </p>
                <form method="POST" action="account.php">
                    <input type="password"  id="RetypePassword" name="Retype_Password" placeholder="Enter Current Password">
                    <input type="text"  id="NewPassword" name="New_Password" placeholder="Enter New Password">
                    <input type="password"  id="RetypeNewPassword" name="Retype_New_Password" placeholder="Retype New Password">
                    <input type="submit" id="PasswordResetButton" name="PasswordReset" value="Submit">
                    <input type="submit" id="AccountLogoutButton" name="logout" value="Log Out">
                </form>
                <a href="index.php">
                    <input type="button" id="BackHomeButton" value="Back Home">
                </a>
                <?php 
                    if($_SERVER['REQUEST_METHOD'] == "POST"){
                        if(isset($_POST["PasswordReset"])){
                            $EnteredPassword = $_POST["Retype_Password"];
                            $NewPassword = $_POST['New_Password'];
                            $RetypeNewPassword = $_POST['Retype_New_Password'];
                            if(!empty($_POST["Retype_Password"]) && !empty($_POST["New_Password"]) && !empty($_POST["Retype_New_Password"])){
                                //Check password from database and checks to see if it is correct
                                $CheckPass = "select Passwd from member where Email = '{$user_data['Email']}' ";
                                $Checking = mysqli_query($con, $CheckPass);
                                $StoredPasswd = "";
                                while($row =  mysqli_fetch_assoc($Checking)){
                                    $StoredPasswd = $row;
                                }
                                if($EnteredPassword != $StoredPasswd['Passwd']){
                                    echo"<p id='test'>Wrong Password</p>";
                                }elseif($NewPassword != $RetypeNewPassword){
                                    echo"<p id='test' style='left:3.5%;'>Passwords do not match!</p>";
                                }
                                else{
                                    $NewPasswdQuery = "UPDATE member SET Passwd = '{$NewPassword}' WHERE Email = '{$user_data['Email']}' ";
                                    mysqli_query($con, $NewPasswdQuery);
                                    echo"<p id='test' style='left:7.5%;'>New Password Set</p>";
                                }
                            }
                        }
                    }
                ?>
                
            </div>
        </body>
    </html>