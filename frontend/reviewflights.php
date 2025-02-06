<?php session_start();
if (!isset($_SESSION['username'])) {
    header('Location: signin.php');
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['orderid'])) {
        $username = $_SESSION['username'];
        $orderid = $_GET['orderid'];
        $postdata = http_build_query(
            array(
                'username' => "$username",
                'orderid' => "$orderid"
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
        $result = file_get_contents('http://localhost:5000/api/reviewflights', false, $context);
        $response = json_decode($result, true);
        if (isset($response['orderid'])) {
            $numofadult = $response['numofadult'];
            $numofchild = $response['numofchild'];
            if ($response['triptype'] == "return") {
                $total = (($response['departingprice_a'] + $response['returningprice_a']) * $numofadult )+ (($response['departingprice_c'] + $response['returningprice_c']) * $numofchild);
            } elseif ($response['triptype'] == "one-way") {
                $total = ($response['departingprice_a'] * $numofadult) + ($response['departingprice_c'] * $numofchild);
            }
        } else {
            echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=index.php"></head></html>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sky Airlines</title>
    <link rel="icon" href="images/navbar-logo.png">
    <link rel="stylesheet" href="stylesheets/global.css">
    <link rel="stylesheet" href="stylesheets/reviewflights.css">
</head>
<body>
<?php include 'navbar.php';?>
    <div id="progress-bar">
        <div class="step completed">Search Flights</div>
        <div class="step current">Review Flights</div>
        <div class="step">Passenger Information</div>
        <div class="step">Payment</div>
    </div>

    <div id="flight-results">
        <h2 id="RYF">Review Your Flights</h2>

        <div class="flight-section">
            <h3>Departing Flight</h3>
            <div class="flight-details">
                <p><strong>Flight Number:</strong> <?php echo $response['flight_number']?></p>
                <p><strong>Departure:</strong> <?php echo $response['src_long']?></p>
                <p><strong>Arrival:</strong> <?php echo $response['dst_long']?></p>
                <p><strong>Date:</strong> <?php echo $response['departingdate']?></p>
                <p><strong>Time:</strong> Departure: <?php echo $response['departingetd']?> | Arrival: <?php echo $response['departingeta']?></p>
                <p><strong>Cabin Class:</strong> <?php echo ucfirst($response['cabinclass'])?> Class</p>
                <p><strong>Price for adult (tax included):</strong> $<?php echo $response['departingprice_a']?></p>
                <p><strong>Price for child (tax included):</strong> $<?php echo $response['departingprice_c']?></p>
            </div>
        </div>
        <?php if ($response['triptype'] == "return") {
            $return = "true";
            ?>
        <div class="flight-section">
            <h3>Returning Flight</h3>
            <div class="flight-details">
                <p><strong>Flight Number:</strong> <?php if ($return == "true") {echo $response['flight_number_return'];}?></p>
                <p><strong>Departure:</strong> <?php if ($return == "true") echo $response['dst_long']?></p>
                <p><strong>Arrival:</strong> <?php if ($return == "true") echo $response['src_long']?></p>
                <p><strong>Date:</strong> <?php if ($return == "true") echo $response['returningdate']?></p>
                <p><strong>Time:</strong> Departure: <?php if ($return == "true") echo $response['returningetd']?> | Arrival: <?php if ($return == "true") echo $response['returningeta']?></p>
                <p><strong>Cabin Class:</strong> <?php if ($return == "true") echo ucfirst($response['cabinclass'])?> Class</p>
                <p><strong>Price for adult (tax included):</strong> $<?php if ($return == "true") echo $response['returningprice_a']?></p>
                <p><strong>Price for child (tax included):</strong> $<?php if ($return == "true") echo $response['returningprice_c']?></p>
            </div>
        </div>
        <?php } ?>

        <div class="total-price">
            <h3><?php echo $response['numofadult']?> Adult, <?php echo $response['numofchild']?> Child</h3>
            <h3>Total Price: $<?php echo $total?></h3>
        </div>
        <div class="total-price">
            <h3 id="hi">5th Anniversary Promotion will be applied after submitting passenger's information.</h3>
        </div>

        <div class="navigation-buttons">
                <button type="button" onclick="window.location.href='index.php#flight-form-container'">Back to Search Flights</button>
                <button type="button" onclick="window.location.href='passinfo.php?orderid=<?php echo $orderid?>'">Next: Passenger Information</button>
        </div>
    </div>

    <?php include 'footer.php';?>
</body>
</html>
