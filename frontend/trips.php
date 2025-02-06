<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: signin.php');
    exit;
} else {
    $username = $_SESSION['username'];

    $postdata = http_build_query(array('username' => $username));
    $opts = array('http' =>
        array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );

    $context = stream_context_create($opts);
    $result = file_get_contents('http://localhost:5000/api/getupcomingtrips', false, $context);
    
    if ($result === FALSE) {
        echo '<script>alert("An error occurred while fetching trips. Please try again.")</script>';
        echo '<html><head><meta http-equiv="refresh" content="0;url=index.php"></head></html>';
        exit;
    }

    $response = json_decode($result, true);

    if ($response['status'] == '0') {
        $trips = $response['trips'];
    } else {
        echo '<script>alert("An error occurred: ' . $response['message'] . '")</script>';
        echo '<html><head><meta http-equiv="refresh" content="0;url=index.php"></head></html>';
        exit;
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
    <link rel="stylesheet" href="stylesheets/trips.css">
</head>
<body>
<?php include 'navbar.php';?>

<div id="my-trips">
    <h2>Upcoming Trips</h2>
    
    <?php if (!empty($trips)): ?>
        <?php foreach ($trips as $trip): 
            // Obtain info of the flight
            $flight_id = $trip['flight_number'];
            $postdata = http_build_query(array('flight_number' => $flight_id));
            $opts = array('http' =>
                array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
                )
            );
            $context = stream_context_create($opts);
            $result = file_get_contents('http://localhost:5000/api/getflightdetails', false, $context);
            $response = json_decode($result, true);
            ?>
            <div class="trip-info">
                <div id="flight-section">
                    <p><strong>Flight Number:</strong> <?php echo ($trip['flight_number']); ?></p>
                    <p><strong>Date:</strong> <?php echo ($trip['date']); ?></p>
                    <p><strong>Departure:</strong> <?php echo ($response['src_long']); ?></p>
                    <p><strong>Destination:</strong> <?php echo ($response['dst_long']); ?></p>
                    <p><strong>Time:</strong> Departure: <?php echo ($response['etd']);?> | Arrival: <?php echo ($response['eta']);?></p>
                    <p><strong>Passenger Names:</strong> <?php echo ($trip['passenger_names']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="trip-info">
        <div id="flight-section">
        <p><strong>No upcoming trips found.</strong></p>
        </div>
        </div>
    <?php endif; ?>
    <div id="support-message">
            <p>If you want to cancel or make changes to your order, please contact our customer service.</p>
            <button onclick="window.location.href='cs.php'">Contact Customer Service</button>
        </div>  
</div>
<?php include 'footer.php';?>
</body>
</html>
