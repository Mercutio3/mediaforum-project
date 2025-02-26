document.addEventListener("DOMContentLoaded", function () {
    const registerForm = document.getElementById("register-form");
    const registerError = document.getElementById("register-error");
    const registerSuccess = document.getElementById("register-success");

    function validateForm(username, email, password, confirmPassword){
        if(!username || !email || !password || !confirmPassword){
            return "Some fields are empty.";
        }

        if(password !== confirmPassword){
            return "Passwords must match.";
        }

        if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
            return "Invalid e-mail address.";
        }

        return null;
    }

    function displayError(message){
        registerError.textContent = message;
        registerError.classList.remove("hidden");
        registerSuccess.classList.add("hidden");
    }

    function displaySuccess(message){
        registerError.classList.add("hidden");
        registerSuccess.classList.remove("hidden");

        setTimeout(() => {
            window.location.href = "login.html";
        }, 2000);
    }

    registerForm.addEventListener("submit", function (event){
        event.preventDefault();

        const username = document.getElementById("username").value;
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirm-password").value;

        const error = validateForm(username, email, password, confirmPassword);
        if(error) {
            displayError(error);
            return;
        }

        const formData = {
            username,
            email,
            password,
        };

        fetch("../php/register.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                displaySuccess();
            } else {
                displayError(data.message || "Signup failed. Please try again.")
            }
        })
        .catch(error => {
            console.error("Error:", error);
            displayError("An error occured. Please try again.");
        });
    });
});