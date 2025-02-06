<?php session_start();
if (!isset($_SESSION['username'])) {
    header('Location: signin.php');
} else {
    $username = $_SESSION['username'];
    //Make request to the API
    $opts = array('http' => 
        array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query(array('username' => $username))
        )
    );
    $context = stream_context_create($opts);
    $result = file_get_contents('http://127.0.0.1:5000/api/get_user_info', false, $context);
    $response = json_decode($result, true);
    $status = $response['status'];
    if (isset($response['status']) && $response['status'] == '0') {
        $telcode_select = array(
            '852' => '',
            '853' => ''
        );
        $gender_select = array(
            'Male' => '',
            'Female' => ''
        );
        $country_select = array(
            'Hong Kong, PRC.' => '',
            'Macau, PRC.' => ''
        );
        $fname = $response['fname'];
        $lname = $response['lname'];
        $email = $response['username'];
        $telcode = $response['telcode'];
        $telcode_select[$telcode] = 'selected';
        $tel = $response['tel'];
        $gender = $response['gender'];
        $gender_select[$gender] = 'selected';
        $country = $response['country'];
        $country_select[$country] = 'selected';
        $dob = $response['dob'];
    } else {
        echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=index.php"></head></html>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Account Information</title>
    <link rel="stylesheet" href="stylesheets/global.css">
    <link rel="stylesheet" href="stylesheets/changeacinfo.css">
</head>
<body>
<?php include 'navbar.php';?>
    <div id="signup-area">
        <h2>Change Account Information</h2>
        <form id="signup-form" action="requests_processing/changeacinfo_process.php" method="POST">
            <label for="first-name">First Name</label>
            <input type="text" id="first-name" name="first_name" required value="<?php echo($fname)?>">
            <label for="last-name">Last Name</label>
            <input type="text" id="last-name" name="last_name" required value="<?php echo($lname)?>">
            
            <label for="email">Email Address</label>
            <input type="email" id="signup-email" name="email" required value="<?php echo($email)?>">
            
            <label for="phone">Phone Number</label>
            <div class="phone-input">
                <select id="country-code" name="country_code" required>
                    <option value="852" <?php echo($telcode_select[852])?>>+852 Hong Kong, PRC.</option>
                    <option value="853" <?php echo($telcode_select[853])?>>+853 Macau, PRC.</option>
                </select>
                <input type="tel" id="phone-number" name="phone_number" required value="<?php echo($tel)?>">
            </div>
            
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php echo($gender_select["Male"])?>>Male</option>
                <option value="Female" <?php echo($gender_select["Female"])?>>Female</option>
            </select>
            
            <label for="country">Country/Region</label>
            <select id="country" name="country" required>
                <option value="Hong Kong, PRC." <?php echo($country_select["Hong Kong, PRC."])?>>Hong Kong, PRC.</option>
                <option value="Macau, PRC." <?php echo($country_select["Macau, PRC."])?>>Macau, PRC.</option>
            </select>
            
            <button type="submit" id="signup-button" disabled>Confirm Changes</button>
        </form>
    </div>

    <?php include 'footer.php';?>
</body>
<script src="scripts/changeacinfo.js"></script>
</html>
