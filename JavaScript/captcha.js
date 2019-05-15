function tryAgain(){
    alert('Ошибка при загрузке CAPTCHA. Проверьте интернет соединение и попробуйте позже.');
}

function disableButton(){
    validCAPTCHA = false;
    document.getElementById('submitButton').setAttribute('disabled', true);
}

function enableButton(){
    validCAPTCHA = true;
    checkAllFlags(); // проверка всех флагов(validate.js)
}

function onloadCallback(){
    // callback-функция для reCAPTCHA
    // срабатывает, когда скрипт reCAPTCHA полностью загрузится
    grecaptcha.render('captcha', {
        'sitekey' : '6Ld5EaEUAAAAACVsMFit1C2SVDcmuDoLjBl9YsSt',
        'callback' : 'enableButton', // срабатывает после успешного прохождения капчи
        'expired-callback' : 'disableButton', // срабатывает, если истекло время сессии reCAPTCHA
        'error-callback' : 'tryAgain' // срабатывает при ошибке
        });
}