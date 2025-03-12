document.addEventListener("DOMContentLoaded", function () {
    const reviewGrid = document.querySelector(".profile-reviews-grid");
    console.log("Stats: ", userStats);
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

    reviewGrid.addEventListener("click", function (event) {
        if(event.target.classList.contains("delete-review")) {
            const reviewElement = event.target.closest(".profile-review-card");
            const reviewId = parseInt(reviewElement.getAttribute("data-id"));

            if(confirm("Are you sure you want to delete this?")) {
                deleteReview(reviewId);
            }
        }
    })

    function generateLabels() {
        const labels = [];
        for(let i = 6; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            labels.push(date.toISOString().split("T")[0]);
        }
        return labels;
    }

    function mapDataToLabels(data, labels) {
        const counts = new Array(labels.length).fill(0);
        data.forEach(entry => {
            const index = labels.indexOf(entry.date);
            if(index !== -1){
                counts[index] = entry.likes_received || entry.likes_given || entry.reviews_posted;
            }
        });
        return counts;
    }

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

    createChart("likesReceivedChart", likesReceivedData);
    createChart("likesGivenChart", likesGivenData);
    createChart("reviewsPostedChart", reviewsPostedData);

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