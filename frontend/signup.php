<?php 
session_start();
if(isset($_SESSION['username'])){
    header('Location: index.php');
}
if(isset($_GET['error'])){
    if($_GET['error'] == 'userexists'){
        echo '<script>alert("The email address has already registered. Please sign in or retry with another email address.")</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="stylesheets/global.css">
    <link rel="stylesheet" href="stylesheets/signup.css">
</head>
<body>
<?php include 'navbar.php';?>
    <div id="signup-area">
        <h2>Create Your Account</h2>
        <form id="signup-form" action="requests_processing/signup_process.php" method="POST">
            <label for="first-name">First Name</label>
            <input type="text" id="first-name" name="first_name">
            
            <label for="last-name">Last Name</label>
            <input type="text" id="last-name" name="last_name">
            
            <label for="email">Email Address</label>
            <input type="email" id="signup-email" name="email" required>
            
            <label for="phone">Phone Number</label>
            <div class="phone-input">
                <select id="country-code" name="country_code" required>
                    <option value="852">+852 Hong Kong, PRC.</option>
                    <option value="853">+853 Macau, PRC.</option>
                </select>
                <input type="tel" id="phone-number" name="phone_number" required>
            </div>
            
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            
            <label for="country">Country/Region</label>
            <select id="country" name="country" required>
                <option value="Hong Kong, PRC.">Hong Kong, PRC.</option>
                <option value="Macau, PRC.">Macau, PRC.</option>
            </select>
            
            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" required max="<?php echo date('Y-m-d'); ?>">
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{12,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 12 or more characters">
            
            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm_password" required>
            
            <span id="error-message" style="color:red;display:none;font-weight:bold;">Passwords do not match.</span>
            
            <button type="submit" id="signup-button" disabled>Sign Up</button>
        </form>
    </div>

    <?php include 'footer.php';?>
</body>
<script src="scripts/signup.js">
</script>
</html>