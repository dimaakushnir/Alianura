(function () {

    const SITE_KEY = 'SITE_KEY'; // 🔴

    function showError(input, message) {
        clearError(input);

        input.classList.add('error');
        const error = input.nextElementSibling.closest('.form-contact__message');
        if(error){
            error.textContent = message;
        }

        // input.parentNode.appendChild(error);
    }

    function clearError(input) {
        input.classList.remove('error');
        const err = input.parentNode.querySelector('.form-error');
        if (err) err.remove();
    }

    function validateInput(input) {
        clearError(input);

        const value = input.value.trim();

        if (input.required && !value) {
            showError(input, 'This field is required.');
            return false;
        }

        // EMAIL
        if (input.type === 'email') {

            if (!value) {
                showError(input, 'Email cannot be empty');
                return false;
            }

            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!re.test(value)) {
                showError(input, 'Invalid email address');
                return false;
            }
        }

        // TEXT / TEXTAREA LENGTH
        if (input.minLength > 0 && value.length < input.minLength) {
            showError(input, `This field is required.`);
            return false;
        }

        return true;
    }


    function validateForm(form) {
        let valid = true;

        [...form.elements].forEach(el => {
            if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                if (!validateInput(el)) valid = false;
            }
        });

        return valid;
    }

    function initForm(formId, url, action) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!validateForm(form)) return;

            grecaptcha.ready(() => {
                grecaptcha.execute(SITE_KEY, { action }).then(token => {
                    let tokenInput = form.querySelector('[name="recaptcha_token"]');
                    if (!tokenInput) {
                        tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = 'recaptcha_token';
                        form.appendChild(tokenInput);
                    }
                    tokenInput.value = token;

                    const formData = new FormData(form);

                    fetch(url, {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert('Successfully sent!');
                                form.reset();

                                let dropdowns_form = form.querySelectorAll('.dropdown');

                                if(dropdowns_form){
                                    dropdowns_form.forEach(function (dropdown) {
                                        let dropdown_btn = dropdown.querySelector('.dropdown__btn');
                                        let dropdown_input_hidden = dropdown.querySelector('.dropdown__input-hidden');

                                        dropdown.classList.remove('selected');

                                        if(dropdown_input_hidden){
                                            dropdown_input_hidden.value = '';
                                        }

                                        if(dropdown_btn){
                                            let dropdown_btn_text = dropdown_btn.querySelector('.dropdown__btn-text');

                                            if(dropdown_btn_text){
                                                dropdown_btn_text.textContent = dropdown_btn_text.dataset.text;
                                            }
                                        }
                                    })
                                }

                            } else {
                                alert(data.message || 'Error');
                            }
                        })
                        .catch(() => {
                            alert('Server error');
                        });
                });
            });
        });
    }

    initForm('contactForm', 'php/send-contact.php', 'contact');
    initForm('subscribeForm', 'php/send-subscribe.php', 'subscribe');

})();

