document.addEventListener('DOMContentLoaded', function() {
    // Initialize core elements
    const otpInputs = document.querySelectorAll('.pwd-otp-input');
    const otpFinal = document.getElementById('pwd-otpFinal');
    const step1Form = document.getElementById('pwd-step1Form');
    const step2Form = document.getElementById('pwd-step2Form');
    const otpTimerElement = document.getElementById('pwd-otpTimer');
    
    // Track active timers
    const activeTimers = {
        otpTimer: null,
        resendTimer: null
    };

    // OTP Input Handling
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            updateFinalOtp();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
            
            for (let i = 0; i < pasteData.length && i < otpInputs.length; i++) {
                otpInputs[i].value = pasteData[i];
                if (i === pasteData.length - 1 && i < otpInputs.length - 1) {
                    otpInputs[i + 1].focus();
                }
            }
            updateFinalOtp();
        });
    });

    function updateFinalOtp() {
        otpFinal.value = Array.from(otpInputs).map(input => input.value).join('');
    }

    function formatTime(seconds) {
        const minutes = Math.floor(Math.max(0, seconds) / 60);
        const remainingSeconds = Math.max(0, seconds) % 60;
        return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
    }

    function clearTimers() {
        Object.values(activeTimers).forEach(timer => {
            if (timer) clearInterval(timer);
        });
        activeTimers.otpTimer = null;
        activeTimers.resendTimer = null;
    }

    window.changeEmail = function() {
        if (step1Form && step2Form) {
            step2Form.style.display = 'none';
            step1Form.style.display = 'block';
            otpInputs.forEach(input => {
                input.value = '';
                input.disabled = false;
            });
            updateFinalOtp();
            clearTimers();
            
            // Remove any existing alerts
            document.querySelectorAll('.pwd-alert').forEach(alert => alert.remove());
        }
    }

    // Initialize OTP Timer
    if (otpTimerElement && initialOtpExpiryRemaining > 0) {
        let timeRemaining = initialOtpExpiryRemaining;
        const timerContainer = otpTimerElement.closest('.pwd-timer-container');
        const resendContainer = document.querySelector('.pwd-resend-container');

        function handleOtpExpiration() {
            otpTimerElement.textContent = '00:00';
            timerContainer.style.display = 'none';
            resendContainer.style.display = 'block';
            
            // Disable inputs and verify button
            otpInputs.forEach(input => input.disabled = true);
            const verifyButton = document.querySelector('#pwd-otpForm button[type="submit"]');
            if (verifyButton) verifyButton.disabled = true;

            clearInterval(activeTimers.otpTimer);
            activeTimers.otpTimer = null;

            // Show expiry message if not already shown
            if (!document.querySelector('.pwd-alert.pwd-alert-error')) {
                const alertContainer = document.createElement('div');
                alertContainer.className = 'pwd-alert pwd-alert-error';
                alertContainer.innerHTML = '<ion-icon name="alert-circle"></ion-icon>OTP has expired! Please request a new one.';
                const otpForm = document.getElementById('pwd-otpForm');
                if (otpForm) otpForm.parentNode.insertBefore(alertContainer, otpForm);
            }
        }

        function updateOtpTimer() {
            if (timeRemaining > 0) {
                otpTimerElement.textContent = formatTime(timeRemaining);
                if (timeRemaining <= 10) {
                    timerContainer.classList.add('pwd-expiring');
                }
                timeRemaining--;
            } else {
                handleOtpExpiration();
            }
        }

        updateOtpTimer();
        activeTimers.otpTimer = setInterval(updateOtpTimer, 1000);
    }

    // Prevent form resubmission
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Handle form submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (this.id === 'pwd-otpForm' && otpFinal.value.length !== 6) {
                e.preventDefault();
                const alertContainer = document.createElement('div');
                alertContainer.className = 'pwd-alert pwd-alert-error';
                alertContainer.innerHTML = '<ion-icon name="alert-circle"></ion-icon>Please enter a complete 6-digit OTP.';
                
                document.querySelectorAll('.pwd-alert.pwd-alert-error').forEach(alert => alert.remove());
                this.parentNode.insertBefore(alertContainer, this);
                return;
            }

            this.querySelectorAll('button[type="submit"]').forEach(button => {
                button.disabled = true;
                const originalText = button.innerHTML;
                button.innerHTML = '<ion-icon name="hourglass-outline"></ion-icon>Processing...';
                
                setTimeout(() => {
                    if (!button.disabled) return;
                    button.disabled = false;
                    button.innerHTML = originalText;
                }, 30000);
            });
        });
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', clearTimers);
});