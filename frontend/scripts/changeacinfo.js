    const form = document.getElementById('signup-form');
    const inputs = form.querySelectorAll('input[required], select[required]');
    const signupButton = document.getElementById('signup-button');
    function validateForm() {
        let allFieldsFilled = true;
        inputs.forEach(input => {
            if (!input.value.trim()) {
                allFieldsFilled = false;
            }
        });
        signupButton.disabled = !allFieldsFilled;
    }
    inputs.forEach(input => {
        input.addEventListener('input', validateForm);
    });
    validateForm();

