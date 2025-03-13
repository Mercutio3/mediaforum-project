document.addEventListener("DOMContentLoaded", function () {
    //Get HTML elements
    const registerForm = document.getElementById("register-form");
    const registerError = document.getElementById("register-error");
    const registerSuccess = document.getElementById("register-success");

    //Ensure proper form submission
    function validateForm(username, email, password, confirmPassword){
        
        // Case where a field is left blank
        if(!username || !email || !password || !confirmPassword){
            return "Some fields are empty.";
        }

        // Case where passwords do not match
        if(password !== confirmPassword){
            return "Passwords must match.";
        }

        // Ensure e-mail format "example@example.example"
        if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
            return "Invalid e-mail address.";
        }

        return null;
    }

    //Display error
    function displayError(message){
        registerError.textContent = message;
        registerError.classList.remove("hidden");
        registerSuccess.classList.add("hidden");
    }

    //If all went well, redirect user to login page
    function displaySuccess(message){
        registerError.classList.add("hidden");
        registerSuccess.classList.remove("hidden");
        registerSuccess.textContent = message;

        //setTimeout(() => {
        //    window.location.href = "login.html";
        //}, 2000);
    }

    //Handle register form submission
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
                displaySuccess(data.message || "Signup successful. Check your email to verify your account.");
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