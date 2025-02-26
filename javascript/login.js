document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("login-form");
    const errorMessage = document.getElementById("login-error");

    function displayError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove("hidden");
    }

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        if(!username || !password){
            displayError("Please fill out all fields.");
            return;
        }

        fetch("../php/login.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({username, password}),
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                window.location.href = "index.html";
            } else {
                displayError(data.message || "Invalid username/password.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            displayError("Error. Please try again!");
        });
    });
});