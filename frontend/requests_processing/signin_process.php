<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password']) && !isset($_POST['2fa'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        // Make request to the API
        $postdata = http_build_query(
            array(
                'username' => "$username",
                'password' => "$password"
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
        $result = file_get_contents('http://127.0.0.1:5000/api/login', false, $context);
        $response = json_decode($result, true);
        if ($response['status'] == '0') {
            session_start();
            $_SESSION['username'] = $username;
            echo '<html><head><meta http-equiv="refresh" content="0;url=../universalsuccess.php?success=signin"></head></html>';
        } elseif ($response['status'] == '9') {?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sign in - Sky Airlines</title>
            <link rel="icon" href="../images/navbar-logo.png">
            <link rel="stylesheet" href="../stylesheets/global.css">
            <link rel="stylesheet" href="../stylesheets/signin.css">
        </head>
        <body>
        <?php include '../navbar_2fa.php';?>
            <div id="signinarea">
            <h2>Two-Factor Authentication (2FA)</h2>
            <h3>Please use your mobile authenticator app to proceed.</h3>
            <form action="#" method="POST">
                <label for="password">One-time Password</label>
                <input type="password" id="signinarea-password" name="otp" required minlength="6" maxlength="6">
                <input type="hidden" name="username" value="<?php echo $username;?>">
                <button type="submit">Confirm</button>
            </form>
        </div>
        <?php include '../footer.php';?>
        </body>
        </html>
        <?php    
        } elseif ($response['status'] == '1') {
            header('Location: ../signin.php?error=incorrect');
        } else {
            echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../signin.php"></head></html>';
        }
} elseif (isset ($_POST['username']) && isset($_POST['otp'])) {
    $username = $_POST['username'];
    $otp = $_POST['otp'];
    // Make request to the API
    $postdata = http_build_query(
        array(
            'username' => "$username",
            'otp' => "$otp"
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
    $result = file_get_contents('http://127.0.0.1:5000/api/verify2fa', false, $context);
    $response = json_decode($result, true);
    if ($response['status'] == '0') {
        session_start();
        $_SESSION['username'] = $username;
        echo '<html><head><meta http-equiv="refresh" content="0;url=../universalsuccess.php?success=signin"></head></html>';
    } elseif ($response['status'] == '1') {
        echo '<script>alert("One time password is incorrect, please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../signin.php"></head></html>';
    } else {
        echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../signin.php"></head></html>';
    }
} else {
    header('Location: ../signin.php');
}
}
?>