// ================= HELPERS =================
function setError(input, message) {
    let small = input.parentElement.querySelector("small");

    if (!small) {
        small = document.createElement("small");
        input.parentElement.appendChild(small);
    }

    small.innerText = message;
    small.style.color = "red";
    input.style.border = "1px solid red";
}

function setSuccess(input) {
    let small = input.parentElement.querySelector("small");
    if (small) small.innerText = "";

    input.style.border = "1px solid green";
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function checkPasswordStrength(password) {
    let strength = 0;

    if (password.length >= 6) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    return strength;
}

// ================= REGISTER =================
const registerForm = document.querySelector("form[action='index.php?page=register']");

if (registerForm) {
    const prenom = registerForm.querySelector("input[name='prenom']");
    const nom = registerForm.querySelector("input[name='nom']");
    const email = registerForm.querySelector("input[name='email']");
    const password = registerForm.querySelector("input[name='password']");
    const confirmPassword = registerForm.querySelector("input[name='confirm_password']");

    // LIVE VALIDATION
    prenom.addEventListener("input", () => {
        if (prenom.value.trim().length < 2) {
            setError(prenom, "Prénom trop court");
        } else {
            setSuccess(prenom);
        }
    });

    nom.addEventListener("input", () => {
        if (nom.value.trim().length < 2) {
            setError(nom, "Nom trop court");
        } else {
            setSuccess(nom);
        }
    });

    email.addEventListener("input", () => {
        if (!isValidEmail(email.value.trim())) {
            setError(email, "Email invalide");
        } else {
            setSuccess(email);
        }
    });

    password.addEventListener("input", () => {
        let strength = checkPasswordStrength(password.value);

        if (strength < 2) {
            setError(password, "Mot de passe faible");
        } else if (strength === 2) {
            setError(password, "Mot de passe moyen");
        } else {
            setSuccess(password);
        }
    });

    confirmPassword.addEventListener("input", () => {
        if (confirmPassword.value !== password.value) {
            setError(confirmPassword, "Les mots de passe ne correspondent pas");
        } else {
            setSuccess(confirmPassword);
        }
    });

    // SUBMIT VALIDATION
    registerForm.addEventListener("submit", function (e) {
        let valid = true;

        if (prenom.value.trim().length < 2) {
            setError(prenom, "Prénom trop court");
            valid = false;
        }

        if (nom.value.trim().length < 2) {
            setError(nom, "Nom trop court");
            valid = false;
        }

        if (!isValidEmail(email.value.trim())) {
            setError(email, "Email invalide");
            valid = false;
        }

        if (password.value.length < 6) {
            setError(password, "Minimum 6 caractères");
            valid = false;
        }

        if (confirmPassword.value !== password.value) {
            setError(confirmPassword, "Les mots de passe ne correspondent pas");
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}

// ================= LOGIN =================
const loginForm = document.querySelector("form[action='index.php?page=login']");

if (loginForm) {
    const email = loginForm.querySelector("input[name='email']");
    const password = loginForm.querySelector("input[name='password']");

    loginForm.addEventListener("submit", function (e) {
        let valid = true;

        if (!isValidEmail(email.value.trim())) {
            setError(email, "Email invalide");
            valid = false;
        }

        if (password.value.length < 6) {
            setError(password, "Mot de passe incorrect");
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}