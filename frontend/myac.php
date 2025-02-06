<?php session_start();
if (!isset($_SESSION['username'])) {
    header('Location: signin.php');
} else {
    $username = $_SESSION['username'];
    //Make requesr to the API
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
    if ($status == '0') {
        $fname = $response['fname'];
        $lname = $response['lname'];
        $email = $response['username'];
        $telcode = $response['telcode'];
        $tel = $response['tel'];
        $gender = $response['gender'];
        $country = $response['country'];
        $dob = $response['dob'];
        $mfa = $response['mfa'];
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
    <title>Sky Airlines</title>
    <link rel="icon" href="images/navbar-logo.png">
    <link rel="stylesheet" href="stylesheets/global.css">
    <link rel="stylesheet" href="stylesheets/myac.css">
</head>
<body>
<?php include 'navbar.php';?>
    <div id="manage-account">
    <h2>Manage My Account</h2>
    <div id="account-info">
        <h3>Personal Information</h3>
        <p><strong>First Name:</strong> <?php echo($fname)?></p>
        <p><strong>Last Name:</strong> <?php echo($lname)?></p>
        <p><strong>Email Address:</strong> <?php echo($email)?></p>
        <p><strong>Phone Number:</strong> +<?php echo($telcode . " " . $tel)?></p>
        <p><strong>Gender:</strong> <?php echo($gender)?></p>
        <p><strong>Country/Region:</strong> <?php echo($country)?></p>
        <p><strong>Date of Birth:</strong> <?php echo($dob)?></p>
    </div>
    <div id="account-info">
        <h3>Two-Factor Authentication (2FA)</h3>
        <p><strong>Status:</strong> <?php if ($mfa == "true"){echo "Enabled";} else {echo "Not Enabled";}?></p>
        <div class="mfa-buttons">
        <?php if ($mfa == "true") {?>
        <button onclick="window.location.href='requests_processing/disable2fa_process.php'">Disable Two-Factor Authentication</button>
        <?php } else {?>
        <button onclick="window.location.href='requests_processing/setup2fa_process.php'">Enable Two-Factor Authentication</button>
        <?php }?>
        </div>
    </div>
    <p id="delac">Notes: To delete your account, please contact customer service.</p>
    <div class="account-buttons">
        <button onclick="window.location.href='changeacinfo.php'">Change My Info</button>
        <button onclick="window.location.href='forgotpw.php?signout=true'">Change Password</button>
    </div>
    </div>
    <br>
    <?php include 'footer.php';?>
</body>
</html>
