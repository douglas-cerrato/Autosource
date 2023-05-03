<?php
session_start();

    include("connection.php");
    include("functions.php");

    
    $session_mode = "";
    //Checks session for user credentials, if valid then returns query of member table  
    $user_data = check_login($con);
    //Checks if user is logged in

    //$test_cars = ["Chevy V6", "Toyota Mustang", "Ferrari Audi"];
    //Query to grab all vehicles under vehicle, filtering out any repeating vehicles
    $vehicle_inventory_query = "SELECT Year, Make, Model FROM vehicle GROUP BY Year, Make, Model";
    $vehicle_result = mysqli_query($con, $vehicle_inventory_query);
    $vehicle_table = array();
    //Makes an array of every vehicle bases on Year, Make and Model
    while ($row = mysqli_fetch_array($vehicle_result))
    {
        $vehicle_table[]= array(
            'Year' => $row['Year'],
            'Make' => $row['Make'],
            'Model' => $row['Model']
        );
    }
    /*
    Query Used to Find All Members from Vehicle that have more than one vehicle:
    SELECT m.*
    FROM member m
    INNER JOIN (
    SELECT OwnerEmail, COUNT(*) as vehicle_count
    FROM vehicle
    GROUP BY OwnerEmail
    HAVING COUNT(*) > 1
    ) v ON m.Email = v.OwnerEmail;

    */

    $test_parts = ["Brakes", "Engine", "Headlights", "Taillights"];

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
            if(isset($_POST["logout"])){
                session_destroy();
                header("Location: index.php");
            }
            if(isset($_POST["add_vehicle"])){
                $VehicleChoice = $_POST["vehicle_inventory"];
                if($VehicleChoice == "---"){
                    header("Location: index.php");
                }else{    
                    list($year, $make, $model) = explode('|', $VehicleChoice);
                    $vin = generate_vin(); // generate a 17 character VIN
                    $current_date = date('Y-m-d'); //Current date to add to owns
                    $add_car_query = "INSERT INTO vehicle 
                        VALUES ('$vin', $year, '$make', '$model', '$user_data[Email]')";
                    mysqli_query($con, $add_car_query);
                    $add_to_owns = "INSERT INTO owns
                        VALUES ('$user_data[Email]', '$vin', '$current_date', NULL)";
                    mysqli_query($con, $add_to_owns);
                }
            }
            if(isset($_POST["rm_vehicle_button"])){
                //RemovedVehicle is the VIN of the car the user chose
                $RemovedVehicle = $_POST["rm_vehicle"];
                if($RemovedVehicle == "---"){
                    header("Location: index.php");
                }else{
                    $rm_from_owns = "DELETE FROM owns WHERE VehVin = '{$RemovedVehicle}' ";
                    mysqli_query($con, $rm_from_owns);
                    $rm_from_vehicle = "DELETE FROM vehicle WHERE VIN = '{$RemovedVehicle}' ";
                    mysqli_query($con, $rm_from_vehicle);
                }
            }
    }

    if(!empty($user_data))
    {
        //echo"User has an active session";
        $session_mode="member";
        $fname = $user_data['Fname'];
        //Query to grab users owned vehicles for drop down 
        $carinfo_query = "SELECT * FROM vehicle WHERE OwnerEmail = '{$user_data['Email']}'";
        $CarResult = mysqli_query($con, $carinfo_query);
        $VehicleInfo = array();
        $OwnedCars = array();
        while ($row =  mysqli_fetch_assoc($CarResult)){
            $VehicleInfo[] = array(
                'VIN' => $row['VIN'],
                'Year' => $row['Year'],
                'Make' => $row['Make'],
                'Model' => $row['Model']
            );
        }
    }
    else{
        //echo"User does not have an active session";
        //User set to guest mode
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
                    <form method="POST">
                        <select id="car_dropdown" name="selected_car">
                            <option selected="selected">-</option>
                            <?php  foreach($VehicleInfo as $vehicle){ ?>
                                <option value="<?php echo $vehicle['Year'] . '|' . $vehicle['Make'] . '|' . $vehicle['Model']; ?>">
                                <?php echo $vehicle['Year'] . ' ' . $vehicle['Make'] . ' ' . $vehicle['Model']; ?>
                            </option>
                            <?php } ?>
                        </select>
                        <select id="part_dropdown" name="selected_part">
                            <option selected="selected">-</option>
                            <?php 
                                foreach($test_parts as $part){
                                    echo "<option value='$part'>$part</option>";
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
                You can go ahead and make an Autosource Motor account
                and save our vehicles to your account, and we can wide provide range of parts
                for your liking.<br><br><br>
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
            <div id="Home_Right">  
                <p style="font-size: 105%; line-height: 115%;">Here you can see what vehicles we have parts for. 
                    If you would like to add a vehicle to your account inventory, you may go 
                    ahead and do so!<br>
                    -----------------------------------------------
                </p>
                <form method="POST">
                    <select id="vehicle_dropdown" name="vehicle_inventory">
                        <option selected="selected">---</option>
                        <?php foreach($vehicle_table as $vehicle){ ?>
                            <option value="<?php echo $vehicle['Year'] . '|' . $vehicle['Make'] . '|' . $vehicle['Model']; ?>">
                            <?php echo $vehicle['Year'] . ' ' . $vehicle['Make'] . ' ' . $vehicle['Model']; ?>
                        </option>
                        <?php } ?>
                    </select>
                    <?php if($session_mode == "member"){ ?>
                        <button type="submit" id="add_vehicle" name="add_vehicle">Add vehicle</button>
                    <?php } ?>
                </form>
                <?php if($session_mode == "guest"){ ?>
                    <a href="login.php">
                        <button id="add_vehicle">Add vehicle</button>
                    </a>
                <?php } ?>   
                <p style="position:absolute; top:35%;font-size: 105%; line-height: 120%;">
                    Below you can remove any vehicle you have attached to your account!
                    Don't worry though, any one you can be added right back. <br>
                        -----------------------------------------------
                </p>
                <form method="POST">
                    <select id="rm_vehicle_dropdown" name="rm_vehicle">
                        <option selected="selected">---</option>
                        <?php foreach($VehicleInfo as $vehicle){?>
                            <option value="<?php echo $vehicle["VIN"] ?>"><?php echo $vehicle['Year'] . ' ' . $vehicle['Make'] . ' ' . $vehicle['Model']; ?></option>
                        <?php } ?>
                    </select>
                    <?php if($session_mode == "member"){ ?>
                        <button type="submit" id="rm_vehicle" name="rm_vehicle_button">Remove Vehicle</button>
                    <?php } ?>
                </form>
                <?php if($session_mode == "guest"){ ?>
                    <a href="login.php">
                        <button id="rm_vehicle">Remove Vehicle</button>
                    </a>
                <?php } ?>
            </div>
        </body>
    </html>
