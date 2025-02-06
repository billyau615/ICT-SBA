<?php session_start();
if (!isset($_SESSION['username'])) {
    header('Location: signin.php');
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orderid'])) {
        $username = $_SESSION['username'];
        $response = $_POST;
        $response['username'] = $username;
        $postdata = http_build_query($response);
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents('http://localhost:5000/api/submitpassinfo', false, $context);
        $response = json_decode($result, true);
        if ($response['status'] == '0') {
            $discount_a = $response['discount_a'];
            $discount_c = $response['discount_c'];
            echo '<html><head><meta http-equiv="refresh" content="0;url=../payment.php?orderid='.$_POST['orderid'].'&discount_a='.$discount_a.'&discount_c='.$discount_c.'"></head></html>';
        } else {
            echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../index.php"></head></html>';
        }
    } else {
        echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../index.php"></head></html>';
    }
}
?>