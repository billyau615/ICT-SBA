<?php session_start();
if (!isset($_SESSION['username'])) {
    header('Location: signin.php');
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['orderid'])) {
        $username = $_SESSION['username'];
        $orderid = $_GET['orderid'];
        $discount_a = $_GET['discount_a'];
        $discount_c = $_GET['discount_c'];
        $discount = 0;
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
                $departingprice_a = $response['departingprice_a'];
                $departingprice_c = $response['departingprice_c'];
                $returningprice_a = $response['returningprice_a'];
                $returningprice_c = $response['returningprice_c'];
                $total = (($response['departingprice_a'] + $response['returningprice_a']) * $numofadult )+ (($response['departingprice_c'] + $response['returningprice_c']) * $numofchild);
                $discount = floor(($discount_a * $departingprice_a * 0.1) + ($discount_c * $departingprice_c * 0.1) + ($discount_a * $returningprice_a * 0.1) + ($discount_c * $returningprice_c * 0.1));
                $total = $total - $discount;
            } elseif ($response['triptype'] == "one-way") {
                $departingprice_a = $response['departingprice_a'];
                $departingprice_c = $response['departingprice_c'];
                $total = ($response['departingprice_a'] * $numofadult) + ($response['departingprice_c'] * $numofchild);
                $discount = floor(($discount_a * $departingprice_a * 0.1) + ($discount_c * $departingprice_c * 0.1));
                $total = $total - $discount;
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
    <link rel="stylesheet" href="stylesheets/payment.css">
</head>
<body>
<?php include 'navbar.php';?>
    
    <div id="progress-bar">
        <div class="step completed">Search Flights</div>
        <div class="step completed">Review Flights</div>
        <div class="step completed">Passenger Information</div>
        <div class="step current">Payment</div>
    </div>

    <div id="flightinfo">
        <h2 id="YF">Review your order</h2>
        <div id="flight-section">
            <h3>Departing Flight</h3>
            <div id="flight-details">
                <p><strong>Flight Number:</strong> <?php echo $response['flight_number']?></p>
                <p><strong>Departure:</strong> <?php echo $response['src_long']?></p>
                <p><strong>Arrival:</strong> <?php echo $response['dst_long']?></p>
                <p><strong>Date:</strong> <?php echo $response['departingdate']?></p>
                <p><strong>Time:</strong> Departure: <?php echo $response['departingetd']?> | Arrival: <?php echo $response['departingeta']?></p>
                <p><strong>Price for adult (tax included):</strong> $<?php echo $response['departingprice_a']?></p>
                <p><strong>Price for child (tax included):</strong> $<?php echo $response['departingprice_c']?></p>
            </div>
        </div>
        <?php if ($response['triptype'] == "return") { 
            $return = "True"; ?>
        <div id="flight-section">
            <h3>Returning Flight</h3>
            <div id="flight-details">
                <p><strong>Flight Number:</strong> <?php if ($return == "True"){echo $response['flight_number_return'];}?></p>
                <p><strong>Departure:</strong> <?php if ($return == "True"){echo $response['dst_long'];}?></p>
                <p><strong>Arrival:</strong> <?php if ($return == "True"){echo $response['src_long'];}?></p>
                <p><strong>Date:</strong> <?php if ($return == "True"){echo $response['returningdate'];}?></p>
                <p><strong>Time:</strong> Departure: <?php if ($return == "True"){echo $response['returningetd'];}?> | Arrival: <?php if ($return == "True"){echo $response['returningeta'];}?></p>
                <p><strong>Price for adult (tax included):</strong> $<?php if ($return == "True"){echo $response['returningprice_a'];}?></p>
                <p><strong>Price for child (tax included):</strong> $<?php if ($return == "True"){echo $response['returningprice_c'];}?></p>
            </div>
        </div>
        <?php } ?>

        <div id="total-price">
            <h3><?php echo $numofadult?> Adult, <?php echo $numofchild?> Child</h3>
            <h3>Subtotal: $<?php echo $total+$discount?></h3>
            <h3>Discount: $<?php echo $discount?></h3>
            <h3>Total Price: $<?php echo $total?></h3>
        </div>
    </div>

    <form action="requests_processing/payment_process.php" method="post">
    <div id="payment-form">
        <h2>Payment Information</h2>
        <hr>
        <h3>Billing Address</h3>
        <div class="form-group">
            <label for="address-line-1">Address Line 1</label>
            <input type="text" id="address-line-1" name="address-line-1" required>
        </div>
        <div class="form-group">
            <label for="address-line-2">Address Line 2</label>
            <input type="text" id="address-line-2" name="address-line-2" required>
        </div>
        <div class="form-group">
            <label for="countryregion">Country/Region</label>
            <select id="countryregion" name="countryregion" required>
                <option value="HK">Hong Kong, PRC.</option>
                <option value="MO">Macau, PRC.</option>
            </select>
        </div>
        <h3>Payment Method</h3>
        <div class="payment-method">
            <input type="radio" id="creditcard" name="payment-method" value="creditcard" required>
            <label for="creditcard">Credit Card</label>
        </div>
        <div class="cardcontainer">
        <div class="form-group">
            <label for="cardnumber">Card Number</label>
            <input type="text" class="check" id="cardnumber" name="cardnumber" placeholder="Card Number" pattern="\d{16}" maxlength="16" title="Enter a 16-digit card number">
        </div>
        <div class="form-group-container">
            <div class="form-group">
                <label for="expirydate">Expiry Date</label>
                <input type="text" class="check" id="expirydate" name="expirydate" placeholder="MM/YY" pattern="\d{2}/\d{2}" maxlength="5" title="Enter a valid expiry date in MM/YY format">
            </div>
            <div class="form-group">
                <label for="cvv">CVV</label>
                <input type="text" class="check" id="cvv" name="cvv" placeholder="CVV" pattern="\d{3,4}" maxlength="4" title="Enter a 3 or 4 digit CVV">
            </div>
        </div>
        </div>
        <div class="payment-method">
            <input type="radio" id="payu" name="payment-method" value="payu" required>
            <label for="payu">PayU</label>
        </div>

        <div class="navigation-buttons">
        <button type="button" onclick="window.location.href='passinfo.php'">Back to Passenger Information</button>
            <button type="submit">Submit Order</button>
        </div>
    </div>
    <input type="hidden" name="orderid" value="<?php echo $orderid?>">
    <input type="hidden" name="total" value="<?php echo $total?>">
    </form>

    <?php include 'footer.php';?>
</body>
<script src="scripts/payment.js"></script>
</html>
