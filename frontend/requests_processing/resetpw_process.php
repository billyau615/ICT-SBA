<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST["pw"];
    $resetid = $_POST["resetid"];
    $postdata = http_build_query(
        array(
            'password' => "$password",
            'resetid' => "$resetid"
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
    $result = file_get_contents('http://localhost:5000/api/resetpassword', false, $context);
    $response = json_decode($result, true);
    if ($response['status'] == '0') {
        echo '<html><head><meta http-equiv="refresh" content="0;url=../universalsuccess.php?success=resetpw"></head></html>';
    } else {
        echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../resetpw.php?resetid='.$resetid.'"></head></html>';
    }
} else {
    header('Location: ../resetpw.php');
}
?>