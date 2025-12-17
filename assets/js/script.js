document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('nav ul');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            navMenu.classList.toggle('show');
        });
    }
    
    // Job filters
    const filterForm = document.querySelector('.job-filters');
    
    if (filterForm) {
        const filterInputs = filterForm.querySelectorAll('select, input');
        
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    }
    
    // Tab functionality
    const tabs = document.querySelectorAll('.tab');
    
    if (tabs.length > 0) {
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                const tabContents = document.querySelectorAll('.tab-content');
                
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        // Skip the search form
        if (form.classList.contains('search-bar')) {
            return;
        }
        
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (field.value.trim() === '') {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    });
    
    

    // Password strength meter logic (Updated for clarity)
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(passwordInput => {
        // Check if the input is for a password that requires strength check (e.g., in registration)
        if (passwordInput && (passwordInput.id === 'password' || passwordInput.id === 'new_password')) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                // Find the closest strength meter in the DOM structure
                const strengthMeterContainer = this.closest('.form-group').querySelector('.password-strength');
                
                if (strengthMeterContainer) {
                    let strength = 0;
                    const strengthMeterFill = strengthMeterContainer.querySelector('.strength-meter-fill');
                    const strengthText = strengthMeterContainer.querySelector('.strength-text');
                    
                    // Criteria for strength check
                    if (password.length >= 8) strength++; // Min length
                    if (password.match(/[a-z]+/)) strength++; // Lowercase
                    if (password.match(/[A-Z]+/)) strength++; // Uppercase
                    if (password.match(/[0-9]+/)) strength++; // Numbers
                    if (password.match(/[$@#&!%*?]+/)) strength++; // Special characters

                    // Update strength meter visual
                    const strengthLevels = ['Password strength', 'Weak', 'Medium', 'Strong', 'Very Strong', 'Unique'];
                    const colors = ['#eee', '#ff4d4d', '#ffa64d', '#ffff4d', '#4dff4d', '#2ecc71'];
                    
                    const level = Math.min(strength, 5); // Cap at 5 for 'Unique'
                    
                    if (strengthText) strengthText.textContent = strengthLevels[level];
                    if (strengthMeterFill) {
                        strengthMeterFill.style.width = (level * 20) + '%';
                        strengthMeterFill.style.backgroundColor = colors[level];
                    }
                }
            });
        }
    });

    // Toggle password visibility - MODIFIED to use data-target for robustness
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordField = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});