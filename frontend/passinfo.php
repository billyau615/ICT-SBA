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
    <link rel="stylesheet" href="stylesheets/passinfo.css">
</head>
<body>
<?php include 'navbar.php';?>
    
    <div id="progress-bar">
        <div class="step completed">Search Flights</div>
        <div class="step completed">Review Flights</div>
        <div class="step current">Passenger Information</div>
        <div class="step">Payment</div>
    </div>

    <div id="flightinfo">
        <h2 id="YF">Selected Flights</h2>
        <div id="flight-section">
            <div id="flight-details">
                <p><strong><?php echo $response['flight_number']?>: </strong><?php echo $response['src_long']?> to <?php echo $response['dst_long']?></p>
                <p><strong>Date:</strong> <?php echo $response['departingdate']?></p>
                <p><strong>Time:</strong> Departure: <?php echo $response['departingetd']?> | Arrival: <?php echo $response['departingeta']?></p><br>

                <?php if ($response['triptype'] == "return") { 
                    $return = "true"?>
                <p><strong><?php if ($return == "true") {echo $response['flight_number_return'];}?>: </strong><?php if ($return == "true") {echo $response['dst_long'];}?> to <?php if ($return == "true") {echo $response['src_long'];}?></p>
                <p><strong>Date:</strong> <?php if ($return == "true") {echo $response['returningdate'];}?></p>
                <p><strong>Time:</strong> Departure: <?php if ($return == "true") {echo $response['returningetd'];}?> | Arrival: <?php if ($return == "true") {echo $response['returningeta'];}?></p>
                <?php } ?>

            </div>
        </div>
        <div id="total-price">
            <h3><?php echo $numofadult?> Adult, <?php echo $numofchild?> Child</h3>
            <h3>Total Price: $<?php echo $total?></h3>
        </div>
        <div id="total-price">
            <h3 id="hi">5th Anniversary Promotion will be applied after submitting passenger's information.</h3>
        </div>
    </div>

    <div id="passenger-info-form">
        <h2>Passenger Information</h2>
        <form action="requests_processing/submitpassinfo_process.php" method="post">
        <?php
        if (isset($numofadult) && isset($numofchild)) {
            // Display Adult Passenger Forms
            for ($i = 1; $i <= $numofadult; $i++) {
                if ($_GET['autofill'] == 'true' && $i == 1) {
                    $postdata = http_build_query(
                        array(
                            'username' => "$username"
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
                    $result = file_get_contents('http://localhost:5000/api/get_user_info', false, $context);
                    $response = json_decode($result, true);
                    $fname = $response['fname'];
                    $lname = $response['lname'];
                    $telcode = $response['telcode'];
                    $tc852 = '';
                    $tc853 = '';
                    if ($telcode == '852') {
                        $tc852 = 'selected';
                    } elseif ($telcode == '853') {
                        $tc853 = 'selected';
                    }
                    $tel = $response['tel'];
                    $email = $response['username'];
                    $dob = $response['dob'];
                    echo "<div class='passenger'>";
                    echo "<h3>Passenger $i</h3>";
                    echo "<label for='a{$i}_fname'>First Name:</label>";
                    echo "<input type='text' id='a{$i}_fname' name='a{$i}_fname' value={$fname} required><br>";
                    echo "<label for='a{$i}_lname'>Last Name:</label>";
                    echo "<input type='text' id='a{$i}_lname' name='a{$i}_lname' value={$lname} required><br>";
                    echo "<label for='a{$i}_dob'>Date of Birth:</label>";
                    echo "<input type='date' id='a{$i}_dob' name='a{$i}_dob' max='" . date('Y-m-d') . "' value={$dob} required><br>";
                    echo "<label for='a{$i}_telcode'>Phone Number</label>";
                    echo "<div class='phone-input'>";
                    echo "<select id='a{$i}_telcode' name='a{$i}_telcode' required>";
                    echo "<option value='+852' {$tc852}>+852 Hong Kong, PRC.</option>";
                    echo "<option value='+853' {$tc853}>+853 Macau, PRC.</option>";
                    echo "</select>";
                    echo "<input type='tel' id='a{$i}_tel' name='a{$i}_tel' value={$tel} required><br>";
                    echo "</div>";
                    echo "<label for='a{$i}_email'>Email Address:</label>";
                    echo "<input type='email' id='a{$i}_email' name='a{$i}_email' value={$email} required>";
                } else {
                echo "<div class='passenger'>";
                echo "<h3>Passenger $i</h3>";
                echo "<label for='a{$i}_fname'>First Name:</label>";
                echo "<input type='text' id='a{$i}_fname' name='a{$i}_fname' required><br>";
                echo "<label for='a{$i}_lname'>Last Name:</label>";
                echo "<input type='text' id='a{$i}_lname' name='a{$i}_lname' required><br>";
                echo "<label for='a{$i}_dob'>Date of Birth:</label>";
                echo "<input type='date' id='a{$i}_dob' name='a{$i}_dob' max='" . date('Y-m-d') . "' required><br>";
                echo "<label for='a{$i}_telcode'>Phone Number</label>";
                echo "<div class='phone-input'>";
                echo "<select id='a{$i}_telcode' name='a{$i}_telcode' required>";
                echo "<option value='+852'>+852 Hong Kong, PRC.</option>";
                echo "<option value='+853'>+853 Macau, PRC.</option>";
                echo "</select>";
                echo "<input type='tel' id='a{$i}_tel' name='a{$i}_tel' required><br>";
                echo "</div>";
                echo "<label for='a{$i}_email'>Email Address:</label>";
                echo "<input type='email' id='a{$i}_email' name='a{$i}_email' required>";
                if ($i == 1 && !isset($_GET['autofill'])) {
                    echo "<div class='af'><a href='passinfo.php?orderid={$orderid}&autofill=true'>Autofill My Information</a></div>";
                }
                echo "<br><br></div>";}
            }

            // Display Child Passenger Forms
            for ($i = 1; $i <= $numofchild; $i++) {
                $childIndex = $numofadult + $i;  // This adjusts the numbering for display
                echo "<div class='passenger'>";
                echo "<h3>Passenger $childIndex (Child aged under 13)</h3>";
                echo "<label for='c{$i}_fname'>First Name:</label>";
                echo "<input type='text' id='c{$i}_fname' name='c{$i}_fname' required><br>";
                echo "<label for='c{$i}_lname'>Last Name:</label>";
                echo "<input type='text' id='c{$i}_lname' name='c{$i}_lname' required><br>";
                echo "<label for='c{$i}_dob'>Date of Birth:</label>";
                echo "<input type='date' id='c{$i}_dob' name='c{$i}_dob' max='" . date('Y-m-d') . "' required><br>";
                echo "<label for='c{$i}_telcode'>Phone Number</label>";
                echo "<div class='phone-input'>";
                echo "<select id='c{$i}_telcode' name='c{$i}_telcode' required>";
                echo "<option value='+852'>+852 Hong Kong, PRC.</option>";
                echo "<option value='+853'>+853 Macau, PRC.</option>";
                echo "</select>";
                echo "<input type='tel' id='c{$i}_tel' name='c{$i}_tel' required><br>";
                echo "</div>";
                echo "<label for='c{$i}_email'>Email Address:</label>";
                echo "<input type='email' id='c{$i}_email' name='c{$i}_email' required><br><br>";
                echo "</div>";
            }
        }
        ?>
        <input type="hidden" name="orderid" value="<?php echo htmlspecialchars($orderid);?>" />
        <input type="hidden" name="numofadult" value="<?php echo htmlspecialchars($numofadult);?>" />
        <input type="hidden" name="numofchild" value="<?php echo htmlspecialchars($numofchild);?>" />
            <div class="navigation-buttons">
                <button type="button" onclick="window.location.href='reviewflights.php?orderid=<?php echo $orderid?>'">Back to Review Flights</button>
                <button type="submit">Next: Payments</button>
            </div>
        </form>
    </div>
    </div>
    <?php include 'footer.php';?>
</body>
</html>
