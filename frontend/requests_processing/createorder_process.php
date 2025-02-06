<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../signin.php');
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET["departure"]) && isset($_GET["arrival"]) && isset($_GET["triptype"])) {
        $departure = $_GET["departure"];
        $arrival = $_GET["arrival"];
        $triptype = $_GET["triptype"];
        $cabinclass = $_GET["cabinclass"];
        //the two dates are in format YYYY-MM-DD
        $departingdate = $_GET["departingdate"];
        if ($triptype == 'return')
            $returningdate = $_GET["returningdate"];
        $numofadult = $_GET["numofadult"];
        $numofchild = $_GET["numofchild"];
        $postdata = http_build_query(
            array(
                'departure' => "$departure",
                'arrival' => "$arrival",
                'triptype' => "$triptype",
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
        $result = file_get_contents('http://127.0.0.1:5000/api/search_flights', false, $context);
        $response = json_decode($result, true);
        if ($response['status'] == '0') {
            $flight = $response['flight'];
            if ($triptype == 'return') {
                $flight_return = $response['flight_return'];
            } else {
                $flight_return = '';
                $returningdate = '';
            }
            $postdata = http_build_query(
                array(
                    'username' => $_SESSION['username'],
                    'flight' => "$flight",
                    'flight_return' => "$flight_return",
                    'triptype' => "$triptype",
                    'departingdate' => "$departingdate",
                    'returningdate' => "$returningdate",
                    'numofadult' => "$numofadult",
                    'numofchild' => "$numofchild",
                    'cabinclass' => "$cabinclass"
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
            $result = file_get_contents('http://127.0.0.1:5000/api/createorder', false, $context);
            $response = json_decode($result, true);
            $orderid = $response['orderid'];
            if ($response['status'] == '0') {
                echo "<html><head><meta http-equiv=\"refresh\" content=\"0;url=../reviewflights.php?orderid={$orderid}\"></head></html>";
            } elseif ($response['status'] == '1') {
                echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../index.php"></head></html>';
            } else {
                echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../index.php"></head></html>';
            }


        } elseif ($response['status'] == '1') {
            echo '<script>alert("There are no flights available at the moment.")</script><html><head><meta http-equiv="refresh" content="0;url=../index.php"></head></html>';
        } else {
            echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../index.php"></head></html>';
        }
    } else {
        echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../index.php"></head></html>';
    }   } else {
        echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=../index.php"></head></html>';
    }
?>