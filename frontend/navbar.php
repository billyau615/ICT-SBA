
<ul id="navbar">
        <li id="navbar-icon"><a href="index.php"><img src="images/navbar-logo.png"></a></li>
        <?php if (isset($_SESSION['username'])) { ?>
            <li id="navbar-item">  
                <div id="navbar-support-dropdown">
                    <p id="navbar-support-dropdown-btn">My Account</p>
                    <div id="navbar-support-dropdown-menu">
                        <a href="myac.php">Manage Account</a>
                        <a href="requests_processing/signout_process.php">Sign Out</a>
                    </div>
                </div>
            </li>
        <?php } else { ?>
            <li id="navbar-item"><a href="signin.php">Sign In / Up</a></li>
        <?php } ?>
        <li id="navbar-item">  
            <div id="navbar-support-dropdown">
            <p id="navbar-support-dropdown-btn">Support</p>
                <div id="navbar-support-dropdown-menu">
                    <a href="faq.php">FAQ</a>
                    <a href="cs.php">Contact us</a>
                </div>
            </div>
        </li>
        <li id="navbar-item">  
            <div id="navbar-flights-dropdown">
            <p id="navbar-flights-dropdown-btn">Flights</p>
                <div id="navbar-flights-dropdown-menu">
                    <a href="index.php#flight-form-container">Book a trip</a>
                    <a href="trips.php">My trips</a>
                </div>
            </div>
        </li>
        <li id="navbar-item"><a href="aboutus.php">About Us</a></li>
    </ul>