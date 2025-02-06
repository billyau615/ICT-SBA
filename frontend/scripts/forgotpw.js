document.addEventListener('DOMContentLoaded', function() {
    const pw = document.getElementById('pw');
    const pw2 = document.getElementById('pw2');
    const errorMessage = document.getElementById('error-message');
    const resetBtn = document.getElementById('resetbtn');

    function checkPasswords() {
        if (pw.value === '' || pw2.value === '') {
            errorMessage.style.display = 'none';
            resetBtn.disabled = true;
        } else if (pw.value !== pw2.value) {
            errorMessage.style.display = 'inline';
            resetBtn.disabled = true;
        } else {
            errorMessage.style.display = 'none';
            resetBtn.disabled = false;
        }
    }

    pw.addEventListener('input', checkPasswords);
    pw2.addEventListener('input', checkPasswords);
});
