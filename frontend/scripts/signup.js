const form = document.getElementById('signup-form');
const inputs = form.querySelectorAll('input[required], select[required]');
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirm-password');
const signupButton = document.getElementById('signup-button');
const errorMessage = document.getElementById('error-message');
function validateForm() {
    let allFieldsFilled = true;
    let passwordsMatch = password.value === confirmPassword.value;
    inputs.forEach(input => {
        if (!input.value) {
            allFieldsFilled = false;
        }
    });
    if (passwordsMatch) {
        errorMessage.style.display = 'none';
    } else {
        errorMessage.style.display = 'block';
    }
    signupButton.disabled = !allFieldsFilled || !passwordsMatch;
}
inputs.forEach(input => {
    input.addEventListener('input', validateForm);
});
password.addEventListener('input', validateForm);
confirmPassword.addEventListener('input', validateForm);