document.addEventListener("DOMContentLoaded", function () {
    //Get HTML elements
    const loginForm = document.getElementById("login-form");
    const errorMessage = document.getElementById("login-error");

    //Show error message for when form is filled out incorrectly
    function displayError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove("hidden");
    }

    //Handle login form submission
    loginForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;

        //Case where some fields are left blank
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
                //Case where username doesn't exist or password is incorrect
                displayError(data.message || "Invalid username/password.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            displayError("Error. Please try again!");
        });
    });
});