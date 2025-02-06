<?php session_start()?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sky Airlines</title>
    <link rel="icon" href="images/navbar-logo.png">
    <link rel="stylesheet" href="stylesheets/global.css">
    <link rel="stylesheet" href="stylesheets/sitemap.css">
</head>
<body>
<?php include 'navbar.php';?>
    <br>
    <h1 id="title">Site Map</h1>
    <div id="faq-container">
        <p class="faq-section-title">Flight Booking</p>
        <div class="faq-item">
            <a href="index.php#flight-form-container"><button class="faq-question">Search Flights</button></a>
        </div>
        <div class="faq-item">
            <a href="trips.php"><button class="faq-question">Upcoming Trips</button></a>
        </div>
        <p class="faq-section-title">Information and Contact Us</p>
        <div class="faq-item">
            <a href="aboutus.php"><button class="faq-question">About Us</button></a>
        </div>
        <div class="faq-item">
            <a href="faq.php"><button class="faq-question">Frequently Asked Questions (FAQ)</button></a>
        </div>
        <div class="faq-item">
            <a href="cs.php"><button class="faq-question">Customer Service</button></a>
        </div>
        <div class="faq-item">
            <a href="5th-promo.php"><button class="faq-question">Celebrate Sky Airlines' 5th Anniversary with Exclusive Savings</button></a>
        </div>
        <p class="faq-section-title">User Account</p>
        <div class="faq-item">
            <a href="signin.php"><button class="faq-question">Sign In/Sign Up</button></a>
        </div>
        <div class="faq-item">
            <a href="myac.php"><button class="faq-question">Manage My Account</button></a>
        </div>
    </div>
    <br><br>

    <?php include 'footer.php';?>
</body>
<script src="scripts/index-slideshow.js"></script>
<script src="scripts/index-searchflights.js"></script>
</html>
