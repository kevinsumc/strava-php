function showForm(formId) {
    document.querySelectorAll(".form-box").forEach((form) => form.classList.remove("active"));
    document.getElementById(formId).classList.add("active");
}
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
    } else {
        input.type = "password";
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
    }
}
document.getElementById('show-passwords').addEventListener('change', function(e) {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (e.target.checked) {
        password.type = 'text';
        confirmPassword.type = 'text';
    } else {
        password.type = 'password';
        confirmPassword.type = 'password';
    }
});