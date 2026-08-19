/**
 * AGASOBANUYE TV - Advanced Recovery Script
 * Path: include/forget.js
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Email Request Form State Loader
    const emailForm = document.getElementById('request_email_form');
    if (emailForm) {
        emailForm.addEventListener('submit', () => {
            const btn = document.getElementById('request_token_btn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner"></span> <span class="btn-label" style="margin-left: 8px;">Requesting...</span>';
            }
        });
    }

    // 2. Multi-box Verification Token Handling (OTP System)
    const boxes = document.querySelectorAll('.otp-single-box');
    const masterHiddenInput = document.getElementById('hidden_compiled_otp');
    const targetForm = document.getElementById('otp_verification_form');
    const submitBtn = document.getElementById('verify_otp_btn');

    if (boxes.length > 0) {
        boxes[0].focus();

        function compileOtpStringPayload() {
            let currentAccumulatedString = "";
            boxes.forEach(box => { currentAccumulatedString += box.value; });
            if (masterHiddenInput) {
                masterHiddenInput.value = currentAccumulatedString.toUpperCase();
            }
            
            if (submitBtn) {
                if (currentAccumulatedString.length === 6) {
                    submitBtn.removeAttribute('disabled');
                } else {
                    submitBtn.setAttribute('disabled', 'true');
                }
            }
        }

        boxes.forEach((box, idx) => {
            box.addEventListener('input', () => {
                box.value = box.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                compileOtpStringPayload();
                if (box.value.length === 1 && idx < boxes.length - 1) {
                    boxes[idx + 1].focus();
                }
            });

            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    if (box.value === '' && idx > 0) {
                        boxes[idx - 1].value = '';
                        boxes[idx - 1].focus();
                        compileOtpStringPayload();
                    } else {
                        box.value = '';
                        compileOtpStringPayload();
                    }
                }
            });

            // Advanced Clipboard Paste Support for Full Code Injection
            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').toUpperCase().replace(/[^A-Z0-9]/g, '');
                let pasteIndex = idx;
                for (let i = 0; i < pasteData.length && pasteIndex < boxes.length; i++) {
                    boxes[pasteIndex].value = pasteData[i];
                    pasteIndex++;
                }
                if (pasteIndex < boxes.length) {
                    boxes[pasteIndex].focus();
                } else {
                    boxes[boxes.length - 1].focus();
                }
                compileOtpStringPayload();
            });
        });

        if (targetForm) {
            targetForm.addEventListener('submit', (e) => {
                compileOtpStringPayload();
                if (masterHiddenInput && masterHiddenInput.value.length !== 6) {
                    e.preventDefault();
                    alert("Please enter all 6 characters of the verification token.");
                } else if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner"></span> <span class="btn-label" style="margin-left: 8px;">Verifying...</span>';
                }
            });
        }
    }

    // 3. Password Visibility Toggle & Real-time Strength Meter
    const passwordInput = document.getElementById('new_password');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const strengthBar = document.getElementById('strengthBar');

    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePasswordBtn.innerHTML = type === 'password' ? '<i class="fa-regular fa-eye"></i>' : '<i class="fa-regular fa-eye-slash"></i>';
        });
    }

    if (passwordInput && strengthBar) {
        passwordInput.addEventListener('input', () => {
            const val = passwordInput.value;
            let score = 0;
            if (val.length > 5) score++;
            if (val.length > 9) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const percentage = (score / 5) * 100;
            strengthBar.style.width = percentage + '%';

            if (score <= 2) {
                strengthBar.style.backgroundColor = '#ff4d4d';
            } else if (score <= 4) {
                strengthBar.style.backgroundColor = '#ffa500';
            } else {
                strengthBar.style.backgroundColor = '#2ecc71';
            }
        });
    }
});