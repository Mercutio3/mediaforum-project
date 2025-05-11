document.addEventListener("DOMContentLoaded", function () {
    const reviewGrid = document.querySelector(".profile-reviews-grid");
    console.log("Stats: ", userStats);

    //Attempt to delete a review
    async function deleteReview(reviewId){
        try {
            const response = await fetch("../php/review-delete.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    review_id: reviewId,
                }),
            });
            const data = await response.json();

            if(data.success) {
                const reviewElement = document.querySelector(`.profile-review-card[data-id="${reviewId}"]`);
                if(reviewElement) {
                    reviewElement.remove();
                }
            } else {
                alert(data.message || "Failed to delete.");
            }
        } catch (error) {
            console.error("Couldn't delete review: ", error);
            alert("Error. Try again.");
        }
    }

    //Handle delete review button clicking
    reviewGrid.addEventListener("click", function (event) {
        if(event.target.classList.contains("delete-review")) {
            const reviewElement = event.target.closest(".profile-review-card");
            const reviewId = parseInt(reviewElement.getAttribute("data-id"));

            if(confirm("Are you sure you want to delete this?")) {
                deleteReview(reviewId);
            }
        }
    })

    //Generate labels for charts
    function generateLabels() {
        const labels = [];
        for(let i = 6; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            labels.push(date.toISOString().split("T")[0]);
        }
        return labels;
    }

    //Map user data to labels for charts
    function mapDataToLabels(data, labels) {
        const counts = new Array(labels.length).fill(0);
        data.forEach(entry => {
            const index = labels.indexOf(entry.date);
            if(index !== -1){
                counts[index] = entry.likes_received || entry.likes_given || entry.reviews_posted || entry.comments_posted;
            }
        });
        return counts;
    }

    //Labels and data for "likes received" chart
    const labels = generateLabels();
    const likesReceivedData = {
        labels: labels,
        datasets: [{
            label: "Likes Received",
            data: mapDataToLabels(userStats.likesReceived, labels),
            backgroundColor: "rgba(255, 99, 132, 0.2)",
            borderColor: "rgba(255, 99, 132, 1)",
            borderWidth: 1
        }]
    };

    //Labels and data for "likes given" chart
    const likesGivenData = {
        labels: labels,
        datasets: [{
            label: "Likes Given",
            data: mapDataToLabels(userStats.likesGiven, labels),
            backgroundColor: "rgba(54, 162, 235, 0.2)",
            borderColor: "rgba(54, 162, 235, 1)",
            borderWidth: 1
        }]
    };

    //Labels and data for "reviews posted" chart
    const reviewsPostedData = {
        labels: labels,
        datasets: [{
            label: "Reviews Posted",
            data: mapDataToLabels(userStats.reviewsPosted, labels),
            backgroundColor: "rgba(75, 192, 192, 0.2)",
            borderColor: "rgba(75, 192, 192, 1)",
            borderWidth: 1
        }]
    };

    //Labels and data for "comments posted" chart
    const commentsPostedData = {
        labels: labels,
        datasets: [{
            label: "Comments Posted",
            data: mapDataToLabels(userStats.commentsPosted, labels),
            backgroundColor: "rgba(153, 102, 255, 0.2)",
            borderColor: "rgba(153, 102, 255, 1)",
            borderWidth: 1
        }]
    };

    //Labels and data for "comments received" chart
    const commentsReceivedData = {
        labels: labels,
        datasets: [{
            label: "Comments Received",
            data: mapDataToLabels(userStats.commentsReceived, labels),
            backgroundColor: "rgba(255, 159, 64, 0.2)",
            borderColor: "rgba(255, 159, 64, 1)",
            borderWidth: 1
        }]
    };

    
    createChart("likesReceivedChart", likesReceivedData);
    createChart("likesGivenChart", likesGivenData);
    createChart("reviewsPostedChart", reviewsPostedData);
    createChart("commentsPostedChart", commentsPostedData);
    createChart("commentsReceivedChart", commentsReceivedData);

    //Create a bar chart with labels and mapped data
    function createChart(canvasId, chartData){
        const ctx = document.getElementById(canvasId);
        if(!ctx) {
            console.error(`Canvas ID ${canvasId} not found!`);
            return;
        }

        try {
            new Chart(ctx, {
                type: "bar",
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio:false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: chartData.datasets[0].label
                        }
                    }
                }
            });
            console.log(`Chart for ${canvasId} created.`);
        } catch (error) {
            console.error(`Couldn't create chart for ${canvasId}: `, error);
        }
    }
});