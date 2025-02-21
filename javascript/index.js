document.addEventListener("DOMContentLoaded", function () {
    const reviewGrid = document.querySelector(".trending-review-grid");

    const trendingReviews = [
        { title: "Review 1", mediaType: "book", rating: 4, summary: "Summary 1", poster: "Biggus Dickus", likes: 354, comments: 113},
        { title: "Review 1", mediaType: "book", rating: 4, summary: "Summary 1", poster: "Claudia Sheinbaum", likes: 214, comments: 56},
    ]

    function showTrendingReviews() {
        reviewGrid.innerHTML = trendingReviews.map(review => `
            <article class="trending-review-card">
                <h3><a href="review.html">${review.title}</a></h3>
                <p class="media-type">${review.mediaType}</p>
                <p class="rating">Rating: ${"*".repeat(review.rating)}${"-".repeat(5-review.rating)}</p>
                <p class="summary">${review.summary}</p>
                <footer>
                    <span>Posted by <a href="user.html">${review.author}</a></span>
                    <span>Likes: ${review.likes}</span>
                    <span>Comments: ${review.comments}</span>
                </footer>
            </article>
        `).join("");
    }

    showTrendingReviews();
});