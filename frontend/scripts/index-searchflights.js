const airportOptions = {
    "HKG": "Hong Kong SAR, PRC (HKG)",
    "PEK": "Beijing, PRC (PEK)",
    "SHA": "Shanghai, PRC (SHA)",
    "SZX": "Shenzhen, PRC (SZX)",
    "TPE": "Taipei, China (TPE)",
    "NRT": "Tokyo, Japan (NRT)",
    "ICN": "Seoul, South Korea (ICN)",
    "SIN": "Singapore, Singapore (SIN)",
    "LAX": "Los Angeles, USA (LAX)",
    "SFO": "San Francisco, USA (SFO)",
    "LHR": "London, United Kingdom (LHR)",
    "SYD": "Sydney, Australia (SYD)",
    "CDG": "Paris, France (CDG)",
    "FRA": "Frankfurt, Germany (FRA)"
};

function populateGoingTo(departFrom) {
    const goingToSelect = document.getElementById('going-to');
    goingToSelect.innerHTML = '<option value="">Select airport</option>';

    if (departFrom === 'HKG') {
        for (const [value, label] of Object.entries(airportOptions)) {
            if (value !== 'HKG') {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = label;
                goingToSelect.appendChild(option);
            }
        }
    } else {
        const option = document.createElement('option');
        option.value = 'HKG';
        option.textContent = airportOptions['HKG'];
        goingToSelect.appendChild(option);
    }
}

function updateGoingTo() {
    const departFromSelect = document.getElementById('depart-from');
    const selectedDepartFrom = departFromSelect.value;

    const goingToSelect = document.getElementById('going-to');
    if (selectedDepartFrom) {
        goingToSelect.disabled = false;
        goingToSelect.classList.remove('disabled-dropdown');
        populateGoingTo(selectedDepartFrom);
    } else {
        goingToSelect.disabled = true;
        goingToSelect.classList.add('disabled-dropdown');
        goingToSelect.innerHTML = '<option value="">Select airport</option>';
    }
    validateForm();
}

function toggleReturningDate() {
    const tripType = document.getElementById('trip-type').value;
    const returningOnContainer = document.getElementById('returning-on-container');
    const returningOnInput = document.getElementById('returning-on');

    if (tripType === 'one-way') {
        returningOnContainer.classList.add('hidden');
        returningOnInput.value = '';
    } else {
        returningOnContainer.classList.remove('hidden');
    }
    validateForm();
}

function validateDates() {
    const departingOn = document.getElementById('departing-on').value;
    const returningOn = document.getElementById('returning-on').value;
    const dateError = document.getElementById('date-error');
    const tripType = document.getElementById('trip-type').value;

    if (departingOn && returningOn && tripType !== 'one-way' && new Date(returningOn) < new Date(departingOn)) {
        dateError.textContent = 'Returning date cannot be earlier than the departing date.';
        return false;
    } else {
        dateError.textContent = '';
        return true;
    }
}

function validateForm() {
    const departFrom = document.getElementById('depart-from').value;
    const goingTo = document.getElementById('going-to').value;
    const departingOn = document.getElementById('departing-on').value;
    const tripType = document.getElementById('trip-type').value;
    const returningOn = document.getElementById('returning-on').value;
    const searchButton = document.getElementById('search-button');

    let isValid = true;

    if (!departFrom || !goingTo || !departingOn) {
        isValid = false;
    }

    if (tripType !== 'one-way' && !returningOn) {
        isValid = false;
    }

    if (!validateDates()) {
        isValid = false;
    }

    searchButton.disabled = !isValid;
}

document.getElementById('depart-from').addEventListener('change', updateGoingTo);
document.getElementById('going-to').addEventListener('change', validateForm);
document.getElementById('departing-on').addEventListener('change', validateForm);
document.getElementById('returning-on').addEventListener('change', validateForm);
document.getElementById('trip-type').addEventListener('change', toggleReturningDate);
document.getElementById('flight-search-form').addEventListener('reset', function() {
    const goingToSelect = document.getElementById('going-to');
    goingToSelect.disabled = true;
    goingToSelect.classList.add('disabled-dropdown');
    goingToSelect.innerHTML = '<option value="">Select airport</option>';
    clearErrorMessages();

    // Ensure the search button remains disabled after reset until valid input
    document.getElementById('search-button').disabled = true;
});

function clearErrorMessages() {
    document.getElementById('date-error').textContent = '';
    document.getElementById('search-button').disabled = false;
}

toggleReturningDate();
