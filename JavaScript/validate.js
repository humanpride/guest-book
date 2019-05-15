function clearValidationFeedback(input){
    // убирает стили для поля input и информацию о валидности данных
    input.classList.remove('is-valid');
    input.classList.remove('is-invalid');
    input.nextSibling.nextSibling.classList.remove('valid-feedback'); // ссылается на div, следующий после input
    input.nextSibling.nextSibling.classList.remove('invalid-feedback');
    input.nextSibling.nextSibling.innerHTML = '';
}

function invalidInput(input){
    // установка стилей для невалидных данных
    input.classList.remove('is-valid');
    input.classList.add('is-invalid');
    input.nextSibling.nextSibling.classList.add('invalid-feedback');
}

function validInput(input){
    // установка стилей для валидных данных
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
    input.nextSibling.nextSibling.classList.remove('invalid-feedback');
}

// Инициализация флагов. Если введены невалидные данные, пользователь не сможет отправить форму
var validUsername = false;
var validEmail = false;
var validUrl = true; // URL - необязательное поле, поэтому с пустым полем пропускаем
var validText = false;
var validCAPTCHA = false;

function clearFlags(){
    // Cброс флагов в начальное положение, кроме флага капчи
    validUsername = false;
    validEmail = false;
    validUrl = true;
    validText = false;
}

function checkAllFlags(){
    // проверка всех флагов на true и активация/дезактивация кнопки
    if (validUsername && validEmail && validUrl && validText && validCAPTCHA)
        document.getElementById('submitButton').removeAttribute('disabled');
    else
        document.getElementById('submitButton').setAttribute('disabled', true);
}

function validateUsername(){
    // проверка имени пользователя
    if (this.value == ''){
        validUsername = false;
        clearValidationFeedback(this);
        checkAllFlags();
        return;
    }

    if (this.value.search(/^[a-zA-Z0-9]+$/) == -1){
        validUsername = false;
        invalidInput(this);
        this.nextSibling.nextSibling.innerHTML = 'Имя пользователя должно состоять только из латинских букв и цифр';
        checkAllFlags();
    }
    else{
        validUsername = true;
        validInput(this);
        this.nextSibling.nextSibling.innerHTML = '';
        checkAllFlags();
    }
}

function validateEmail(){
    // проверка Email
    if (this.value == ''){
        validEmail = false;
        clearValidationFeedback(this);
        checkAllFlags();
        return;
    }

    if (this.value.search(/^[-._+a-z0-9]+@(?:[a-z0-9][-a-z0-9]*\.)+[a-z]{2,6}$/i) == -1) {
        validEmail = false;
        invalidInput(this);
        this.nextSibling.nextSibling.innerHTML = 'Введите корректный Email';
        checkAllFlags();
    }
	else{
        validEmail = true;
        validInput(this);
        this.nextSibling.nextSibling.innerHTML = '';
        checkAllFlags();
    }
}

function validateUrl(){
    // проверка поля homepage
    if (this.value == ''){
        validUrl = true; // пустое значение пропускаем, т.к. поле не обязательное
        clearValidationFeedback(this);
        checkAllFlags();
        return;
    }

    if (this.value.search(/^(https?:\/\/)?[a-z0-9]+([-.][a-z0-9]+)*\.[a-z]{2,5}(:\d{1,5})?(\/[\w.-]+\/?)*$/) == -1) {
        validUrl = false;
        invalidInput(this);
        this.nextSibling.nextSibling.innerHTML = 'Укажите корректный URL адрес';
        checkAllFlags();
    }
    else{
        validUrl = true;
        validInput(this);
        this.nextSibling.nextSibling.innerHTML = '';
        checkAllFlags();
    }
}

function validateText(){
    // проверка текста
    if (this.value == ''){
        validText = false;
        clearValidationFeedback(this);
        checkAllFlags();
        return;
    }

    if (this.value.search(/<([a-z]+)[^>]*>/) == -1) {
        validText = true;
        validInput(this);
        this.nextSibling.nextSibling.innerHTML = '';
        checkAllFlags();
    }
    else{
        validText = false;
        invalidInput(this);
        this.nextSibling.nextSibling.innerHTML = 'Текст сообщения не должен содержать HTML теги';
        checkAllFlags();
    }
}

document.getElementById('uname').onkeyup = validateUsername;
document.getElementById('email').onkeyup = validateEmail;
document.getElementById('url').onkeyup = validateUrl;
document.getElementById('text').onkeyup = validateText;