<?php

function check_login($con)
{
    if(isset($_SESSION['user_id']))
    {
        $id = $_SESSION['user_id'];
        $query = "SELECT * FROM member WHERE Email = '$id'";
        
        $result = mysqli_query($con, $query);
        if($result && mysqli_num_rows($result) > 0)
        {
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }
    return [];
}
function generate_vin() {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < 17; $i++) {
        $index = mt_rand(0, strlen($characters) - 1);
        $random_string .= $characters[$index];
    }
    return $random_string;
}