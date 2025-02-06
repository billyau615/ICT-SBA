<?php 
session_start();
if(isset($_SESSION['username'])){
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - Sky Airlines</title>
    <link rel="icon" href="images/navbar-logo.png">
    <link rel="stylesheet" href="stylesheets/global.css">
    <link rel="stylesheet" href="stylesheets/signin.css">
</head>
<body>
<?php include 'navbar.php';?>
    <div id="signinarea">
    <h2>Sign In</h2>
    <h3>Please sign in to continue.</h3>
    <form action="requests_processing/signin_process.php" method="POST">
        <label for="username">Email Address</label>
        <input type="text" id="signinarea-username" name="username" required> 
        <label for="password">Password</label>
        <input type="password" id="signinarea-password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <?php 
    if(isset($_GET['error'])){
        if($_GET['error'] == 'incorrect'){
            echo '<p style="color:red;font-weight:bold;font-size:15px;">Incorrect username or password. Please try again.</p>';
        }
    }
    ?>
    <p>Don't have an account? <a href="signup.php">Sign up</a></p>
    <p id="forgotpw"><a href="forgotpw.php">Forgot your password?</a></p>
</div>
<?php include 'footer.php';?>
</body>
</html>

