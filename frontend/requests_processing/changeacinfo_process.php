<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: signin.php');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    $email = $_POST['email'];
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $telcode = $_POST['country_code'];
    $tel = $_POST['phone_number'];
    $gender = $_POST['gender'];
    $country = $_POST['country'];
    // Make request to the API
    $postdata = http_build_query(
        array(
            'username' => "$username",
            'email' => "$email",
            'fname' => "$fname",
            'lname' => "$lname",
            'telcode' => "$telcode",
            'tel' => "$tel",
            'gender' => "$gender",
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
    $result = file_get_contents('http://127.0.0.1:5000/api/change_user_info', false, $context);
    $response = json_decode($result, true);
    if ($response['status'] == '0') {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['username'] = $email;
        echo '<html><head><meta http-equiv="refresh" content="0;url=../universalsuccess.php?success=changeacinfo"></head></html>';
    } elseif ($response['status'] == '1') {
        echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../changeacinfo.php"></head></html>';
    } elseif ($response['status'] == '2') {
        echo '<script>alert("Your updated email address has been associated to another account. Please use another email address, or contact customer service. ")</script><html><head><meta http-equiv="refresh" content="0;url=../changeacinfo.php"></head></html>';
    } else {
        echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../changeacinfo.php"></head></html>';
    }
} else {
    echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../changeacinfo.php"></head></html>';
}
?>