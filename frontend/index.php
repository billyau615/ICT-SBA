<?php session_start()?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sky Airlines</title>
    <link rel="icon" href="images/navbar-logo.png">
    <link rel="stylesheet" href="stylesheets/global.css">
    <link rel="stylesheet" href="stylesheets/index.css">
</head>
<body>
<?php include 'navbar.php';?>
    <br><br>
    <!-- Slideshow -->
    <div id="slideshow-container">
        <div class="slides fade">
            <a href="5th-promo.php"><img src="images/index-promotion-img.png" style="width:100%;border-radius:16px;"></a>
        </div>
        <div class="slides fade">
            <video autoplay muted loop controls controlslist="timeline nodownload noplaybackrate noremoteplayback" disablepictureinpicture disableremoteplayback id="video">
                <source src="images/index-video.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <a class="prev" onclick="changeslide(-1)">&#10094;</a>
        <a class="next" onclick="changeslide(1)">&#10095;</a>
    </div>
    <div style="text-align:center">
        <span class="dot" onclick="setslide(1)"></span>
        <span class="dot" onclick="setslide(2)"></span>
    </div>
    <div id="flight-form-container">
    <h2>Book Flights</h2>
    <form action="requests_processing/createorder_process.php" method="GET" id="flight-search-form">
        <table id="form-table">
            <tr class="form-row">
                <td class="form-cell">
                    <label for="depart-from" class="form-label">Depart From</label>
                    <select id="depart-from" class="form-select" onchange="updateGoingTo()" name="departure" required>
                        <option value="" selected>Select airport</option>
                        <option value="HKG">Hong Kong SAR, PRC (HKG)</option>
                        <option value="PEK">Beijing, PRC (PEK)</option>
                        <option value="SHA">Shanghai, PRC (SHA)</option>
                        <option value="SZX">Shenzhen, PRC (SZX)</option>
                        <option value="TPE">Taipei, China (TPE)</option>
                        <option value="NRT">Tokyo, Japan (NRT)</option>
                        <option value="ICN">Seoul, South Korea (ICN)</option>
                        <option value="SIN">Singapore, Singapore (SIN)</option>
                        <option value="LAX">Los Angeles, USA (LAX)</option>
                        <option value="SFO">San Francisco, USA (SFO)</option>
                        <option value="LHR">London, United Kingdom (LHR)</option>
                        <option value="SYD">Sydney, Australia (SYD)</option>
                        <option value="CDG">Paris, France (CDG)</option>
                        <option value="FRA">Frankfurt, Germany (FRA)</option>
                    </select>
                </td>
                <td class="form-cell">
                    <label for="going-to" class="form-label">Going To</label>
                    <select id="going-to" class="form-select disabled-dropdown" name="arrival" disabled required>
                        <option value="" selected>Select airport</option>
                    </select>
                </td>
            </tr>
            <tr class="form-row">
                <td class="form-cell">
                    <label for="trip-type" class="form-label">Trip Type</label>
                    <select id="trip-type" class="form-select" onchange="toggleReturningDate()" name="triptype" required>
                        <option value="return" selected>Return</option>
                        <option value="one-way">One Way</option>
                    </select>
                </td>
                <td class="form-cell">
                    <label for="cabin-class" class="form-label">Cabin Class</label>
                    <select id="cabin-class" class="form-select" name="cabinclass" required>
                        <option value="economy">Economy</option>
                        <option value="business">Business</option>
                        <option value="first">First Class</option>
                    </select>
                </td>
            </tr>
            <tr class="form-row">
                <td class="form-cell">
                    <label for="num-adult" class="form-label">Number of Adults</label>
                    <select id="num-adult" class="form-select" name="numofadult" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                </td>
                <td class="form-cell">
                    <label for="num-child" class="form-label">Number of Children (Aged 12 or lower)</label>
                    <select id="num-child" class="form-select" name="numofchild" required>
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </td>
            </tr>
            <tr class="form-row">
                <td class="form-cell">
                    <label for="departing-on" class="form-label">Departing On</label>
                    <input type="date" id="departing-on" class="form-input" onchange="validateDates()" name="departingdate" required>
                </td>
                <td class="form-cell" id="returning-on-container">
                    <label for="returning-on" class="form-label">Returning On</label>
                    <input type="date" id="returning-on" class="form-input" onchange="validateDates()" name="returningdate">
                </td>
            </tr>
            <tr class="form-row">
                <td colspan="2">
                <div>
                    <div id="date-error" class="error-message"></div>
                </div>
                </td>
            </tr>
        </table>
        <div class="form-buttons">
            <button type="reset" class="form-btn reset-button">Reset</button>
            <button type="submit" class="form-btn submit-button" id="search-button" disabled>Search</button>
        </div>
    </form>
</div>
    <br><br>

    <?php include 'footer.php';?>
</body>
<script src="scripts/index-slideshow.js"></script>
<script src="scripts/index-searchflights.js"></script>
</html>
