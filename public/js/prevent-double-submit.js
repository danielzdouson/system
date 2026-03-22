/**
 * Prevent double form submission
 * Automatically disables submit buttons after first click to prevent duplicates
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get all forms on the page
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Find all submit buttons in this form
            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            
            // Disable all submit buttons
            submitButtons.forEach(button => {
                button.disabled = true;
                
                // Add visual feedback
                const originalText = button.textContent || button.value;
                
                if (button.tagName === 'BUTTON') {
                    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                } else {
                    button.value = 'Processing...';
                }
                
                // Store original text for potential re-enable
                button.dataset.originalText = originalText;
            });
            
            // Re-enable buttons if form validation fails or submission is cancelled
            // This handles cases where the form doesn't actually submit
            setTimeout(() => {
                if (!form.checkValidity()) {
                    submitButtons.forEach(button => {
                        button.disabled = false;
                        if (button.tagName === 'BUTTON') {
                            button.innerHTML = button.dataset.originalText;
                        } else {
                            button.value = button.dataset.originalText;
                        }
                    });
                }
            }, 100);
        });
        
        // Re-enable buttons if there's an error during submission
        form.addEventListener('error', function() {
            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitButtons.forEach(button => {
                button.disabled = false;
                if (button.tagName === 'BUTTON') {
                    button.innerHTML = button.dataset.originalText || 'Submit';
                } else {
                    button.value = button.dataset.originalText || 'Submit';
                }
            });
        });
    });
    
    // Also prevent multiple clicks on any button with data-prevent-double-click attribute
    const preventDoubleClickButtons = document.querySelectorAll('[data-prevent-double-click]');
    preventDoubleClickButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.disabled) {
                e.preventDefault();
                return false;
            }
            
            this.disabled = true;
            const originalText = this.textContent || this.value;
            this.dataset.originalText = originalText;
            
            if (this.tagName === 'BUTTON') {
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            }
            
            // Re-enable after 3 seconds as a safety measure
            setTimeout(() => {
                this.disabled = false;
                if (this.tagName === 'BUTTON') {
                    this.innerHTML = originalText;
                }
            }, 3000);
        });
    });
});
