<?php
session_start();

    include("connection.php");
    include("functions.php");

    $session_mode = "";
    $user_data = check_login($con); //Checks session for user credentials from DB to verify if they have logged in 
    $Search_Clicked = False; //Ignore this


    $vehicle_inventory_query = "SELECT Year, Make, Model FROM Vehicle GROUP BY Year, Make, Model";
    $vehicle_result = mysqli_query($con, $vehicle_inventory_query);
    $vehicle_table = array();
    while ($row = mysqli_fetch_array($vehicle_result)) //Makes an array of every vehicle bases on Year, Make and Model
    {
        $vehicle_table[]= array(
            'Year' => $row['Year'],
            'Make' => $row['Make'],
            'Model' => $row['Model']
        );
    }
    $part_category_query = "SELECT DISTINCT CategoryName from partscategory";
    $parts_result = mysqli_query($con, $part_category_query);
    $parts_array = array();
    while ($row = mysqli_fetch_array($parts_result))
    {
        $parts_array[]= $row['CategoryName'];
    }

    if(!empty($user_data))
    {
        $session_mode="member"; //User signed into an account with an active session 
        $fname = $user_data['Fname']; //User's name 
        //Query to grab users owned vehicles for drop down 
        $carinfo_query = "SELECT * FROM Vehicle WHERE OwnerEmail = '{$user_data['Email']}'";
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
        $session_mode="guest"; //User not signed in
    }

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
                $add_car_query = "INSERT INTO Vehicle 
                    VALUES ('$vin', $year, '$make', '$model', '$user_data[Email]')";
                mysqli_query($con, $add_car_query);
                $add_to_owns = "INSERT INTO Owns
                    VALUES ('$user_data[Email]', '$vin', '$current_date', NULL)";
                mysqli_query($con, $add_to_owns);
                header("Location: index.php");
                exit();
            }
        }
        if(isset($_POST["rm_vehicle_button"])){
            //RemovedVehicle is the VIN of the car the user chose
            $RemovedVehicle = $_POST["rm_vehicle"];
            if($RemovedVehicle == "---"){
                header("Location: index.php");
            }else{
                $rm_from_owns = "DELETE FROM Owns WHERE VehVin = '{$RemovedVehicle}' ";
                mysqli_query($con, $rm_from_owns);
                $rm_from_vehicle = "DELETE FROM Vehicle WHERE VIN = '{$RemovedVehicle}' ";
                mysqli_query($con, $rm_from_vehicle);
                header("Location: index.php");
                exit();
            }
        }
        if(isset($_POST["search_button"])){
            $Search_Clicked = True;
            $ChosenCarParts = array();
            if(isset($_POST["selected_car"]) && isset($_POST["selected_part"])){ //Checks if member has a car from their inventory and part selected
                $Selected_Car = $_POST["selected_car"];
                $Selected_Part = $_POST["selected_part"];

                if(($Selected_Car != "-") && ($Selected_Part != "-")){
                    $carParts = explode('|', $Selected_Car);
                    $year = $carParts[0];
                    $make = $carParts[1];
                    $model = $carParts[2];

                    $AllParts  = <<<EOD
                    SELECT fitsin.*, partscategory.CategoryName
                    FROM fitsin
                    INNER JOIN partscategory ON fitsin.PartSerialNum = partscategory.PartSerialNum
                    WHERE fitsin.VehYear = "$year"
                        AND fitsin.VehMake = "$make"
                        AND fitsin.VehModel = "$model"
                        AND partscategory.CategoryName = "$Selected_Part";
                    EOD;

                    $Searched_Parts_Results = mysqli_query($con, $AllParts);
                    while ($row =  mysqli_fetch_assoc($Searched_Parts_Results)){
                        $ChosenCarParts[] = $row;
                    }
                }
            /*
            //Query to find all parts from a certain vehicle under a certain category name
            SELECT fitsin.*, partscategory.CategoryName
            FROM fitsin
            INNER JOIN partscategory ON fitsin.PartSerialNum = partscategory.PartSerialNum
            WHERE fitsin.VehYear = "2022"
                AND fitsin.VehMake = "Toyota"
                AND fitsin.VehModel = "Camry"
                AND partscategory.CategoryName = "brakes";
            */
            }
            if(empty($ChosenCarParts)){
                if(isset($_POST["search_bar"])){
                    $SearchTerm = $_POST["search_bar"];
                    $query = "SELECT * FROM fitsin WHERE PartName LIKE '%$SearchTerm%'";
                    $result = mysqli_query($con, $query);
                    while($row = mysqli_fetch_assoc($result)){
                        $ChosenCarParts[] = $row;
                    }
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
        <link rel="stylesheet" type="text/css" href="style.css?v=1">
        <title>Autosource | Home</title>
    </head>
    <body style="background-color:#1B1A17;">
        <header id="header">
            <div id="Inside_Header">
                <form method="POST" action="index.php">
                    <input type="text" name="search_bar" id="search_bar" placeholder="Seach for a Part">
                    <input type="submit" name="search_button" id="search_button" value="Search">
                    <?php if($session_mode == 'member'){ ?>
                        <p id="Used_Car_and_Parts_Text">
                            Pick a Vehicle
                        </p>
                        <p id="Used_Car_and_Parts_Text">
                            Pick a Part
                        </p>
                        <select id="Car_Dropdown" name="selected_car">
                            <option selected="selected">-</option>
                            <?php  foreach($VehicleInfo as $vehicle){ ?>
                                <option value="<?php echo $vehicle['Year'] . '|' . $vehicle['Make'] . '|' . $vehicle['Model']; ?>">
                                <?php echo $vehicle['Year'] . ' ' . $vehicle['Make'] . ' ' . $vehicle['Model']; ?>
                            </option>
                            <?php } ?>
                        </select>
                        <select id="Part_Dropdown" name="selected_part">
                            <option selected="selected">-</option>
                            <?php 
                                foreach($parts_array as $part){
                                    echo "<option value='$part'>$part</option>";
                                }
                            ?>
                        </select>
                    <?php }?>    
                </form>
                <?php if($session_mode == "guest"){ ?>
                    <p id="Home_Welcome_Text">
                        Welcome Guest, Take a look around!
                    </p>
                    <div id="Guest_Buttons">
                        <p id="Login_Button_Text">Have an account?</p>
                        <a href="login.php">
                            <input type="submit" name="Log In" id="Home_Login_Button" value="Log In">
                        </a>
                        <p id="Signup_Button_Text">New User?</p>
                        <a href="signup.php">
                            <input type="submit" name="Sign Up" id="Home_Signup_Button" value="Sign Up">
                        </a>
                    </div>
                <?php }else{ 
                    echo"<p id='Home_Welcome_Text'>Welcome back $fname, how can we help?</p>"; ?>
                    <!-- <a href="account.php" id="AccountLink">Account</a> -->
                    <form method="POST">
                        <input id="Logout_Button" type="submit" name="logout" value="Logout"/>
                    </form>
                <?php }?>
            </div>
        </header>
        <div id="lower_half">
            <div id="Main_Left_Container">
                    <div id="Inside_Left_Container">
                        <br>Welcome to Autosource Motor! &nbsp<br><br>

                    We are a website dedicated to helping you find parts 
                    for your vehicle. We have a set of vehicles in our 
                    inventory that you can check out, and find parts for.
                    <br><br>
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
                </div>
            <div id="Main_Container">
                    <div id="Inside_Main_Container">
                        <?php if($Search_Clicked == False){ 
                        }elseif($Search_Clicked == True){
                            echo"<p id='SearchedResultsText'> Seach results retrieved: </p>";
                            if(empty($ChosenCarParts)){
                                echo"<p id='ErrorNoParts'>Uh Oh! Seems like we have no parts in stock for this vehicle!</p>";
                            }else{ 
                                foreach($ChosenCarParts as $result){
                                    $PriceQuery = "SELECT Price FROM Parts WHERE SerialNum = $result[PartSerialNum]";
                                    $QueryResult = mysqli_query($con, $PriceQuery);
                                    $Price = mysqli_fetch_assoc($QueryResult); ?>
                                    <div id="SearchedPartName"> Part Name <br> <?php echo"$result[PartName]" ?> </div>
                                    <div id="SearchedSerialNumber"> SerialNum <br> <?php echo"$result[PartSerialNum]" ?></div>
                                    <div id="SearchedPrice"> Price <br> <?php echo"$Price[Price]" ?></div>  
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            <div id="Main_Right_Container">
                <div id="Inside_Right_Container">
                    <p style="font-size: 1.9vh;font-family:'Lucida Console';top: 1vh;position: relative;font-weight:bold;">
                        Here you can see what vehicles we have parts for. 
                        If you would like to add a vehicle to your account inventory, you may go 
                        ahead and do so!<br>
                    </p>
                    <form method="POST" action="index.php">
                        <select id="Vehicle_Dropdown" name="vehicle_inventory">
                            <option selected="selected">---</option>
                            <?php foreach($vehicle_table as $vehicle){ ?>
                                <option value="<?php echo $vehicle['Year'] . '|' . $vehicle['Make'] . '|' . $vehicle['Model']; ?>">
                                <?php echo $vehicle['Year'] . ' ' . $vehicle['Make'] . ' ' . $vehicle['Model']; ?>
                            </option>
                            <?php } ?>
                        </select>
                        <?php if($session_mode == "member"){ ?>
                            <button type="submit" id="Add_Vehicle" name="add_vehicle">Add vehicle</button>
                        <?php } ?>
                    </form>
                    <?php if($session_mode == "guest"){ ?>
                        <a href="login.php">
                            <button id="Add_Vehicle_Guest">Add vehicle</button>
                        </a>
                    <?php } ?>   
                    <p style="position: relative;font-family:'Lucida Console';font-size: 1.9vh;font-weight: bold;">
                        Below you can remove any vehicle you have attached to your account!
                        Don't worry though, any one you can be added right back. <br>
                    </p>
                    <form method="POST" action="index.php">
                        <select id="Rm_Vehicle_Dropdown" name="rm_vehicle">
                            <option selected="selected">---</option>
                            <?php foreach($VehicleInfo as $vehicle){?>
                                <option value="<?php echo $vehicle["VIN"] ?>"><?php echo $vehicle['Year'] . ' ' . $vehicle['Make'] . ' ' . $vehicle['Model']; ?></option>
                            <?php } ?>
                        </select>
                        <?php if($session_mode == "member"){ ?>
                            <button type="submit" id="Rm_Vehicle" name="rm_vehicle_button">Remove Vehicle</button>
                        <?php } ?>
                    </form>
                    <?php if($session_mode == "guest"){ ?>
                        <a href="login.php">
                            <button id="Rm_Vehicle_Guest">Remove Vehicle</button>
                        </a>
                    <?php } ?>
                </div>
            </div>    
        </div>
    </body>
</html>