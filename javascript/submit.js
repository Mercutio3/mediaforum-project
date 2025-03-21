document.addEventListener("DOMContentLoaded", function () {
    //Get HTML elements
    const reviewForm = document.getElementById("submission-form");
    const reviewPreview = document.getElementById("review-preview");

    //Handle 
    reviewForm.addEventListener("input", function () {
        //Get HTML form elements
        const mediaTitle = document.getElementById("media-title").value;
        const reviewTitle = document.getElementById("review-title").value;
        const rating = document.getElementById("rating").value;
        const reviewContent = document.getElementById("review-content").value;
        const mediaCreator = document.getElementById("media-creator").value;
        const mediaYear = document.getElementById("media-year").value;

        //Insert review details into HTML review preview for displaying
        reviewPreview.innerHTML = `
            <h4>${reviewTitle}</h4>
            <p><strong>Media:</strong> ${mediaTitle}</p>
            <p><strong>Creator:</strong> ${mediaCreator}</p>
            <p><strong>Year:</strong> ${mediaYear}</p>
            <p><strong>Rating:</strong> ${"*".repeat(rating)}${"-".repeat(5 - rating)}</p>
            <p><strong>Review:</strong> ${reviewContent}</p>
        `;
    });

    //Handle review submission button
    reviewForm.addEventListener("submit", function(event) {
        event.preventDefault();
        
        //Get HTML form elements
        const mediaTitle = document.getElementById("media-title").value;
        const reviewTitle = document.getElementById("review-title").value;
        const rating = document.getElementById("rating").value;
        const reviewContent = document.getElementById("review-content").value;
        const mediaCreator = document.getElementById("media-creator").value;
        const mediaYear = document.getElementById("media-year").value;

        //Case where a field is left blank
        if(!mediaTitle || !reviewTitle || !rating || !reviewContent || !mediaCreator || !mediaYear) {
            event.preventDefault();
            alert("Some required fields are missing.");
            return;
        }

        const formData = new FormData(reviewForm);

        fetch("../php/submit-review.php", {
            method: "POST",
            body: formData,
        })
        .then(response => {
            console.log("Raw response: ", response);
            return response.text();
        })
        .then(text => {
            console.log("Response text: ", text);
            try{
                const data = JSON.parse(text);
                if(data.success) {
                    alert("Review submitted!");
                    reviewForm.reset();
                    reviewPreview.innerHTML = "";
                } else {
                    alert("Something went wrong. Please try again.");
                }
            } catch (error){
                console.error("Failed to parse JSON: ", text);
                alert("An error occured. Please try again.");
            }
        })
        .catch(error => {
            console.error("Error: ", error);
            alert("An error occured. Please try again.");
        });
    });
});