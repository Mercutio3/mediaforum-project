document.addEventListener("DOMContentLoaded", function () {
    // Handle form submission for profile editing
    document.getElementById("edit-account-form").addEventListener("submit", function (event){
        event.preventDefault();
        const formData = new FormData(this);

        fetch("../php/edit-profile.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert("Profile updated successfully.");
            } else {
                alert(data.message || "Failed to update.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error. Please try again.");
        });
    });

    // Handle form submission for password changing
    document.getElementById("account-password-form").addEventListener("submit", function (event){
        event.preventDefault();
        const formData = new FormData(this);

        fetch("../php/change-password.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert("Password updated successfully!");
            } else {
                alert(data.message || "Could not update password.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error. Please try again.");
        });
    });

    // Handle form submission for account deletion
    document.getElementById("delete-account-button").addEventListener("click", function () {
        if(confirm("Are you sure? This is permanent!")){
            fetch("../php/delete-account.php", {
                method: "POST",
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    alert("Successful deletion.");
                    window.location.href = "login.html";
                } else {
                    alert(data.message || "Failed to delete.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error. Try again!");
            });
        }
    });
});