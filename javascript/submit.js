document.addEventListener("DOMContentLoaded", function () {
    const reviewForm = document.getElementById("submission-form");
    const reviewPreview = document.getElementById("review-preview");

    reviewForm.addEventListener("submit", function(event) {
        const mediaTitle = document.getElementById("media-title").value;
        const reviewTitle = document.getElementById("review-title").value;
        const rating = document.getElementById("rating").value;
        const reviewContent = document.getElementById("review-content").value;

        if(!mediaTitle || !reviewTitle || !rating || !reviewContent) {
            event.preventDefault();
            alert("Some required fields are missing.");
        }
    });

    reviewForm.addEventListener("input", function () {
        const mediaTitle = document.getElementById("media-title").value;
        const reviewTitle = document.getElementById("review-title").value;
        const rating = document.getElementById("rating").value;
        const reviewContent = document.getElementById("review-content").value;

        reviewPreview.innerHTML = `
            <h4>${reviewTitle}</h4>
            <p><strong>Media:</strong> ${mediaTitle}</p>
            <p><strong>Rating:</strong> ${"*".repeat(rating)}${"-".repeat(5 - rating)}</p>
            <p><strong>Review:</strong> ${reviewContent}</p>
        `;
    });

    reviewForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = new FormData(reviewForm);

        fetch("/submit-review", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert("Review submitted!");
                reviewForm.reset();
                reviewPreview.innerHTML = "";
            } else {
                alert("Something went wrong. Please try again.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occured. Please try again.")
        });
    });
});