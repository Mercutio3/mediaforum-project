document.addEventListener("DOMContentLoaded", function() {
    const filterForm = document.getElementById("browse-filter-form");
    const reviewGrid = document.querySelector(".browse-review-grid");

    const reviews = [
        { title: "Review 1", mediaType: "book", rating: 4, summary: "summary book", poster: "Mercutio33", likes: 33, comments: 22},
        { title: "Review 2", mediaType: "movie", rating: 3, summary: "summary movie", poster: "Olaf Scholz", likes: 2, comments: 6},
        { title: "Review 3", mediatype: "tv-show", rating: 5, summary: "summary show", poster: "Brian", likes: 139, comments: 0},
    ]

    function displayReviews(filteredReviews) {
        reviewGrid.innerHTML = filteredReviews.map(review => `
            <article class="browse-review-card">
                <h3><a href="review.html">${review.title}</a></h3>
                <p class="media-type">${review.mediaType}</p>
                <p class="rating">Rating: ${"*".repeat(review.rating)}${"-".repeat(5 - review.rating)}</p>
                <p class="summary">${review.summary}</p>
                <footer>
                    <span>Posted by <a href="user.html">${review.poster}</a></span>
                    <span>Likes: ${review.likes}</span>
                    <span>Comments: ${review.comments}</span>
                </footer>
            </article>
        `).join("");
    }

    displayReviews(reviews);

    filterForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const mediaType = document.getElementById("media-type").value;
        const genre = document.getElementById("genre").value;
        const sortBy = document.getElementById("sort-by").value;

        const filteredReviews = reviews.filter(review => {
            return (mediaType === "all" || review.mediaType === mediaType) && (genre === "all" || review.genre === genre);
        });

        if(sortBy === "date") {
            filteredReviews.sort((a,b) => new Date(b.date) - new Date(a.date));
        } else if(sortBy === "likes") {
            filteredReviews.sort((a,b) => b.likes - a.likes);
        } else if(sortBy === "rating") {
            filteredReviews.sort((a,b) => b.rating - a.rating);
        }

        displayReviews(filteredReviews);
    });
});