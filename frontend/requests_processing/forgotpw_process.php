<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        // Make request to the API
        $postdata = http_build_query(
            array(
                'username' => "$email"
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
        $result = file_get_contents('http://127.0.0.1:5000/api/forgotpassword', false, $context);
        header('Location: ../forgotpw.php?status=success');
    } else {
        echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../forgotpw.php"></head></html>';
    }
}