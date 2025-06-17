document.addEventListener('DOMContentLoaded', () => {
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            let valid = true;
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '';
                }
            });
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });

    // Confirm checkout
    const checkoutButton = document.querySelector('form button.btn');
    if (checkoutButton) {
        checkoutButton.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to complete the purchase?')) {
                e.preventDefault();
            }
        });
    }
});