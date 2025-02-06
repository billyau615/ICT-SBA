<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $country_code = $_POST['country_code'];
        $phone_number = $_POST['phone_number'];
        $gender = $_POST['gender'];
        $country = $_POST['country'];
        $dob = $_POST['dob'];
        $password = $_POST['password'];
        // Make request to the API
        $postdata = http_build_query(
            array(
                'username' => "$email",
                'password' => "$password",
                'fname' => "$first_name",
                'lname' => "$last_name",
                'telcode' => "$country_code",
                'tel' => "$phone_number",
                'gender' => "$gender",
                'dob' => "$dob",
                'country' => "$country"
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
        $result = file_get_contents('http://127.0.0.1:5000/api/signup', false, $context);
        $response = json_decode($result, true);
        if ($response['status'] == '0') {
            session_start();
            $_SESSION['username'] = $email;
            echo '<html><head><meta http-equiv="refresh" content="0;url=../universalsuccess.php?success=signup"></head></html>';
        } else if ($response['status'] == '1') {
            header('Location: ../signup.php?error=userexists');
        } else {
            echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../signup.php"></head></html>';
        }
} else {
    header('Location: ../signup.php');
}
?>