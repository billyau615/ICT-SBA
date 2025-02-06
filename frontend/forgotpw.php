<?php
session_start();
if (isset($_GET['signout'])) {
    if ($_GET['signout'] == 'true') {
        session_unset();
        session_destroy();
}
}
if (isset($_SESSION['username'])) {
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="stylesheets/global.css">
    <link rel="stylesheet" href="stylesheets/forgotpw.css">
</head>
<body>
<?php include 'navbar.php';?>
<?php 
if(isset($_GET['status'])){ 
    if($_GET['status'] == 'success'){?>
    <div id="forgotpw-area">
        <h2>Request submitted</h2>
        <h3>We have sent you a link to reset your password if the provided email address is registered. <br><br>Please check your email inbox. The link is valid for 20 minutes.</h3>
    </div>
<?php }} elseif(isset($_GET['resetid'])) {
    $resetid = $_GET['resetid'];
    // Make request to the API
    $postdata = http_build_query(
        array(
            'resetid' => $resetid
        )
        );
    $opts = array('http' => 
        array(
            'method' => 'POST', 
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context = stream_context_create($opts);
    $result = file_get_contents('http://127.0.0.1:5000/api/checkresetid', false, $context);
    $response = json_decode($result, true);
    if ($response['status'] == '0') {?>
    <div id="forgotpw-area">
    <h2>Reset Your Password</h2>
    <form action="requests_processing/resetpw_process.php" method="POST">
            <label for="pw">New Password</label>
            <input type="password" id="pw" class="pw" name="pw" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{12,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 12 or more characters">
            <label for="pw2">Confirm New Password</label>
            <input type="password" id="pw2" class="pw" name="pw2" required>
            <input type="hidden" name="resetid" value="<?php echo($resetid) ?>"/>
            <span id="error-message" style="color:red;display:none;font-weight:bold;">Passwords do not match.</span>
            <button type="submit" id="resetbtn" disabled>Reset Password</button>
    </form>
    </div>
    <script src="scripts/forgotpw.js"></script>
<?php } elseif ($response['status'] == '1') {?>
    <div id="forgotpw-area">
        <h2>Invalid Link</h2>
        <h3>The link is invalid or has expired. You will be redirected to request a new reset link shortly.</h3>
    </div>
    <script>
        window.setTimeout(function(){
            window.location.href = "forgotpw.php";
        }, 5000);
    </script>
<?php }} else {?>
    <div id="forgotpw-area">
        <h2>Forgot Your Password?</h2>
        <h3>We will send you a link to reset your password.</h3>
        <form action="requests_processing/forgotpw_process.php" method="POST">
            <label for="email">Email address</label>
            <input type="email" id="forgotpw-email" name="email" required>
            <button type="submit" id="requestbtn">Send Reset Link</button>
        </form>
    </div>
<?php }?>
    <?php include 'footer.php';?>
</body>
</html>
