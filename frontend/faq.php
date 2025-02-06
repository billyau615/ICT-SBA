<?php session_start()?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sky Airlines</title>
    <link rel="icon" href="images/navbar-logo.png">
    <link rel="stylesheet" href="stylesheets/global.css">
    <link rel="stylesheet" href="stylesheets/faq.css">
</head>
<body>
<?php include 'navbar.php';?>

    <br>
    <div id="title">
        <h1>Frequently Asked Questions (FAQ)</h1>
        <h3>Find answers for common questions here.<h3>
        <h3>Still have something to ask? <a href="cs.php">Contact us.</a></h3><br>
    </div>
    <div id="faq-container">
        <p class="faq-section-title">Booking & Reservations</p>
        <div class="faq-item">
            <button class="faq-question">How can I book a flight?</button>
            <div class="faq-answer">
                <p>You can book a flight through our website, contacting our customer service or through travel agencies. Simply enter your travel details, select your preferred flight, and complete the payment process.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">Can I change or cancel my booking?</button>
            <div class="faq-answer">
                <p>Yes, you can change or cancel your booking by contacting our customer service department. Please note that changes or cancellations may be subject to fees depending on the fare type.
                </p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">What payment methods are accepted?</button>
            <div class="faq-answer">
                <p>We accept major credit and debit cards, and QR code payment depending on your country of origin. Details are available during the checkout process.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">What should I do if I don’t receive my booking confirmation?</button>
            <div class="faq-answer">
                <p>If you don’t receive your booking confirmation within 24 hours, please check your spam/junk folder. If it’s not there, contact our customer service for assistance.</p>
            </div>
        </div>
        <p class="faq-section-title">Baggage Information</p>
        <div class="faq-item">
            <button class="faq-question">What is the baggage allowance for my flight?</button>
            <div class="faq-answer">
                <p>Baggage allowance varies by route and fare type. Please check your booking details for specific allowances.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">What items are prohibited in checked baggage?</button>
            <div class="faq-answer">
                <p>Items like flammable liquids, explosives, lithium batteries, and corrosive materials are strictly prohibited in checked baggage.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">How do I report lost or damaged baggage?</button>
            <div class="faq-answer">
                <p>If your baggage is lost or damaged, report it immediately at the airport’s baggage service desk or via our customer service within 24 hours. We will assist you in tracking or compensating for your baggage.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">Can I carry sports equipment or oversized items?</button>
            <div class="faq-answer">
                <p>Yes, you can bring sports equipment or oversized items, but they may be subject to additional fees. Make sure to notify us by contacting customer service for arrangements.</p>
            </div>
        </div>
        <p class="faq-section-title">In-Flight Experience</p>
        <div class="faq-item">
            <button class="faq-question">What in-flight entertainment options are available?</button>
            <div class="faq-answer">
                <p>We offer a wide range of in-flight entertainment, including movies, TV shows, music, and games, available on personal screens or via our mobile app.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">Are meals provided on the flight?</button>
            <div class="faq-answer">
                <p>Meals are provided on all flights.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">Can I request a special meal?</button>
            <div class="faq-answer">
                <p>Yes, we offer a variety of special meals, including vegetarian, vegan, gluten-free, and halal options. Please request your meal by contacting our customer service department at least 24 hours before departure.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">Is Wi-Fi available on board?</button>
            <div class="faq-answer">
                <p>Wi-Fi is available on most of our aircraft for a small fee. You can purchase access during your flight through the in-flight entertainment system.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question">How do I upgrade my seat or class?</button>
            <div class="faq-answer">
                <p>You can upgrade your seat or class by contacting customer service. Upgrades are subject to availability and fees.</p>
            </div>
        </div>
    </div>
 
    <?php include 'footer.php';?>
</body>
<script type="text/javascript" src="scripts/faq.js"></script>  
</html>
