const form = document.querySelector("form");

const firstnameInput = form.querySelector('input[name="firstname"]');
const lastnameInput = form.querySelector('input[name="lastname"]');
const emailInput = form.querySelector('input[name="email"]');
const passwordInput = form.querySelector('input[name="password1"]');
const confirmedPasswordInput = form.querySelector('input[name="password2"]');

function debounce(func) {
    let timeoutId;
    return function (...args) {
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, 1000);
    };
}

function isEmail(email) {
    return /\S+@\S+\.\S+/.test(email);
}

function isNameValid(name) {
    return name.length >= 2 && /^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ \-]+$/.test(name);
}


function isPasswordStrong(password) {
    return password.length >= 8 && /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/.test(password);
}

function arePasswordsSame(password, confirmedPassword) {
    return password === confirmedPassword;
}

function markValidation(element, condition, text) {
    const errorDisplay = element.nextElementSibling; 

    if (!condition) {
        element.classList.add('no-valid');
        if (errorDisplay) {
            errorDisplay.innerText = text;
        }
    } else {
        element.classList.remove('no-valid');
        if (errorDisplay) {
            errorDisplay.innerText = "";
        }
    }
}

const validateFirstName = () => {
    markValidation(firstnameInput, isNameValid(firstnameInput.value), "First name must have at least 2 characters and only contain letters!");
};

const validateLastName = () => {
    markValidation(lastnameInput, isNameValid(lastnameInput.value), "Last name must have at least 2 characters and only contain letters!");
};

const validateEmail = () => {
    markValidation(emailInput, isEmail(emailInput.value), "Invalid email format!");
};

const validatePasswordStrength = () => {
    markValidation(passwordInput, isPasswordStrong(passwordInput.value), "Password must be at least 8 characters long and contain at least 1 lowercase, uppercase, number and special character!");
    
    if (confirmedPasswordInput.value.length > 0) {
        validatePasswordMatch();
    }
};

const validatePasswordMatch = () => {
    const condition = arePasswordsSame(
        passwordInput.value,
        confirmedPasswordInput.value
    );
    markValidation(confirmedPasswordInput, condition, "Passwords must be the same!");
};

firstnameInput.addEventListener('keyup', debounce(validateFirstName));
lastnameInput.addEventListener('keyup', debounce(validateLastName));
emailInput.addEventListener('keyup', debounce(validateEmail));
passwordInput.addEventListener('keyup', debounce(validatePasswordStrength));
confirmedPasswordInput.addEventListener('keyup', debounce(validatePasswordMatch));