<?php session_start();
if (!isset($_SESSION['username'])) {
    header('Location: signin.php');
} else {
    $postdata = http_build_query(
        array(
            'username' => $_SESSION['username']
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
    $result = file_get_contents('http://localhost:5000/api/disable2fa', false, $context);
    $response = json_decode($result, true);
    if ($response['status'] == '0') {
        echo '<script>alert("Two-Factor Authentication of your account is disabled.")</script><html><head><meta http-equiv="refresh" content="0;url=../myac.php"></head></html>';
    } else {
        echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../myac.php"></head></html>';
    }
}
?>