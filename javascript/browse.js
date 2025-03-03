document.addEventListener("DOMContentLoaded", function() {
    // Get HTML elements
    const filterForm = document.getElementById("browse-filter-form");
    const reviewGrid = document.querySelector(".browse-review-grid");

    // Get reviews from database
    async function fetchReviews() {
        try {
            const response = await fetch("../php/browse.php");
            const data = await response.json();

            if (data.success) {
                return data.reviews;
            } else {
                console.error("Failed to fetch reviews: ", data.message);
                return [];
            }
        } catch (error) {
            console.error("Error fetching: ", error);
            return [];
        }
    }

    // Insert review details into HTML review card article for displaying
    function displayReviews(reviews) {
        reviewGrid.innerHTML = reviews.map(review => `
            <article class="browse-review-card">
                <h3><a href="review.html?id=${review.id}">${review.title}</a></h3>
                <p class="media-type">${review.media_type}</p>
                <p class="rating">Rating: ${"*".repeat(review.rating)}${"-".repeat(5 - review.rating)}</p>
                <p class="summary">${review.summary}</p>
                <footer>
                    <span>Posted by <a href="../profile.php?user_id=${review.user_id}">${review.username}</a></span>
                    <span>Likes: ${review.likes || 0}</span>
                    <span>Comments: ${review.comments || 0}</span>
                </footer>
            </article>
        `).join("");
    }

    // Get and display reviews
    fetchReviews().then(reviews => {
        displayReviews(reviews);
    });

    // Handle "apply filters"
    filterForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        const mediaType = document.getElementById("media-type").value;
        const genre = document.getElementById("genre").value;
        const sortBy = document.getElementById("sort-by").value;

        const reviews = await fetchReviews();

        const filteredReviews = reviews.filter(review => {
            return (mediaType === "all" || review.media_type === mediaType) && (genre === "all" || review.genre === genre);
        });

        if(sortBy === "date") {
            filteredReviews.sort((a,b) => new Date(b.created_at) - new Date(a.created_at));
        } else if(sortBy === "likes") {
            filteredReviews.sort((a,b) => (b.likes || 0) - (a.likes || 0));
        } else if(sortBy === "rating") {
            filteredReviews.sort((a,b) => b.rating - a.rating);
        }

        displayReviews(filteredReviews);
    });
});