<?php session_start()?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sky Airlines</title>
    <link rel="icon" href="images/navbar-logo.png">
    <link rel="stylesheet" href="stylesheets/global.css">
    <link rel="stylesheet" href="stylesheets/cs.css">
</head>
<body>
<?php include 'navbar.php';?>
    <div id="container">
        <h1>Contact Us</h1><br>
        <p id="intro">Have a question or need help? We are here to assist you. You can reach us through the following methods:</p><br>
        <div id="contact-options">
            <div class="contact-box">
                <h2>Chat with Us</h2>
                <hr>
                <p>Chat with us on WhatsApp (+852 8888 8888) at anytime by clicking <a href="https://example.com">here</a>.<br><br><br><br><br><br><br></p>
            </div>
            <div class="contact-box">
                <h2>Call Us</h2>
                <hr>
                <p>You can reach us by calling our hotline in our office hours (9am-5pm).<br><br>Hong Kong, China: +852 8888 8888<br>Mainland China: +86 (888) 888-8888<br>Macau, China: +853 8888 8888<br><br>*Oversea calling charges may apply.</p>
            </div>
        </div>
        <div id="contactform-box">
            <h2>Send us a support ticket</h2>
            <hr>
            <div id="contactform-container">
            <form action="requests_processing/supportticket.php" method="POST">
                <label for="name">Full name:</label>
                <input type="text" id="name" name="name" required>
                <label for="email">Email address:</label>
                <input type="email" id="email" name="email" required>
                <label for="subject">Order ID (if applicable):</label>
                <input type="text" id="subject" name="subject" required>
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="10" required></textarea>
                <p>We will reply you within 24 hours.</p>
                <button type="submit">Submit</button>
            </form>
            </div>
        </div>
    </div>
    <?php include 'footer.php';?>
</body>
</html>