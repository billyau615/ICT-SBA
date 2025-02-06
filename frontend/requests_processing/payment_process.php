<?php session_start();
if (!isset($_SESSION['username'])) {
    header('Location: signin.php');
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orderid'])) {
        $username = $_SESSION['username'];
        $response = $_POST;
        $response['username'] = $username;
        $postdata = http_build_query($response);
        $orderid = $_POST['orderid'];
        $total = $_POST['total'];
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents('http://localhost:5000/api/submitpayment', false, $context);
        $response = json_decode($result, true);
        if ($response['status'] == '0') {
            echo '<html><head><meta http-equiv="refresh" content="0;url=../success.php?orderid='.$response['flight_id'].'&total='.$total.'"></head></html>';
            exit();
        } elseif ($response['status'] == '1') {
            echo '<script>alert("Credit Card information invalid, please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../payment.php?orderid='. $orderid .'&total='.$total.'"></head></html>';
            exit();
        } else {
            echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../index.php"></head></html>';
            exit();
        }
    } else {
        echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../index.php"></head></html>';
    }
}
?>