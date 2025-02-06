<?php session_start();
if (!isset($_SESSION['username'])) {
    header('Location: signin.php');
} elseif (null !== $_GET) {
    $orderid = sprintf('%08d', $_GET['orderid']);;
    $total = $_GET['total'];
} else {
    echo '<script>alert("An error occurred. Please try again.")</script><html><head><meta http-equiv="refresh" content="0;url=index.php"></head></html>';
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
    <link rel="stylesheet" href="stylesheets/success.css">
</head>
<body>
<?php include 'navbar.php';?>
    
    <div id="order-success">
        <h2>Order Confirmed!</h2>
        <p id="success-message">Your order has been successfully placed.</p>
    
        <div class="order-details">
            <h3>Order Details</h3>
            <p><strong>Order Number:</strong> #<?php echo $orderid;?></p>
            <p><strong>Date:</strong> <?php echo date("Y-m-d"); ?></p>
            <p><strong>Total Amount:</strong> $<?php echo $total;?></p>
        </div>
    
        <div class="navigation-buttons">
            <button onclick="window.location.href='index.php'">Back to Home</button>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>
</html>
