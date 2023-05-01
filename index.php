<?php
session_start();

    include("connection.php");
    include("functions.php");


    $test_cars = ["Chevy V6", "Toyota Mustang", "Ferrari Audi"];
    $test_parts = ["Brakes", "Engine", "Headlights", "Taillights"];

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
            if(isset($_POST["logout"])){
                session_destroy();
                header("Location: index.php");
            }
    }

    $session_mode = "";
    $user_data = check_login($con);
    if(!empty($user_data))
    {
        //echo"User has an active session";
        $session_mode="member";
        $fname = $user_data['Fname'];
    }
    else{
        //echo"User does not have an active session";
        $session_mode="guest";
    }
    
?>
<!DOCTYPE html>
    <html>
        <head>
            <title>Autosource | Home</title>
            <link rel="stylesheet" type="text/css" href="style.css?v=2">
        </head>
        <body style="background-color: black;">
            <header id="home_header">
                <span id="logo">
                    <img src="Autosource-Logo.png" alt="Autosource Logo"/>
                </span>
                <?php if($session_mode == "guest"){ ?>
                    <p id="Home-Welcome-Text">
                        Welcome Guest, Take a look around!
                    </p>
                <?php }else{ 
                    echo"<p id='Home-Welcome-Text'>Welcome back $fname, how can we help?</p>";
                    }
                ?>
                <form method="POST" action="index.php" style="display: inline-block;">
                    <input type="text" name="search bar" id="search_bar" placeholder="Seach for a Part">
                    <input type="submit" name="search button" id="search_button" value="Search">
                </form>
                <?php if($session_mode == "guest"){ ?>
                    <a href="login.php">
                        <input type="submit" name="Log In" id="home_login_button" value="Log In">
                    </a>
                    <p id="have_an_acc" style="position:absolute;margin-left:71.5%;top:40%;width:100px;">
                        Have an account?
                    </p>
                    <a href="signup.php">
                        <input type="submit" name="Sign Up" id="home_signup_button" value="Sign Up">
                    </a>
                    <p id="have_an_acc" style="position:absolute;margin-left:78%;top:40%;width:100px;">
                        Create an account?
                    </p>
                <?php }else{ ?>
                    <p id="cars_and_parts_option"style="position:absolute;margin-left:57.2%;top:44%;">
                        Pick a vehicle
                    </p>
                    <p id="cars_and_parts_option" style="position:absolute;margin-left:66%;top:44%;">
                        Pick a part
                    </p>
                    <form>
                        <select id="car_dropdown" name="selected_car">
                            <option selected="selected">-</option>
                            <?php 
                                foreach($test_cars as $vehicle){
                                    echo "<option value='strtolower($vehicle)'>$vehicle</option>";
                                }
                            ?>
                        </select>
                        <select id="part_dropdown" name="selected_part">
                            <option selected="selected">-</option>
                            <?php 
                                foreach($test_parts as $part){
                                    echo "<option value='strtolower($part)'>$part</option>";
                                }
                            ?>
                        </select>
                    </form>
                    <a href="account.php" id="account_text">
                        Account
                    </a>
                    <form method="POST" action="index.php" style="display: inline-block;">
                        <input type="submit" name="logout" id="logout_button" value="Logout"/>
                    </form>
                    <?php } ?>
            </header>
            <div id="Home_Left_Container">
                <br>Welcome to Autosource Motor! &nbsp
                 ------------------------------<br>
                We are a website dedicated to helping you find parts 
                for your vehicle. We have a set of vehicles in our 
                inventory that you can check out, and find parts for.
                <br><br>
                You can also input your own vehicle under your own 
                Autosoure Motor account, and we can let you know the 
                parts we know fit your vehicle.<br><br><br>
                The parts we have to offer range from:
                <ul>
                    <li>Brakes</li>
                    <li>Engines</li>
                    <li>Alternators</li>
                    <li>Filters</li>
                    <li>Bulbs</li>
                </ul>
                And plenty more! <br><br> If you would like to check out our 
                part inventory go ahead and use the search bar above 
                to find what you are looking for. <br><br> If you would like to 
                check out our vehicle inventory, you can make an account
                and add any of those vehicles to your account as well.
            </div>
            <div id="Home_Main">

            </div>
                
            </div>
        </body>
    </html>
