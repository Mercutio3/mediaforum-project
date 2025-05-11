document.addEventListener("DOMContentLoaded", function () {
    const reviewGrid = document.querySelector(".trending-review-grid");

    //Get trending reviews
    async function getTrendingReviews() {
        try {
            const response = await fetch("../php/trending.php");
            const data = await response.json();

            if(data.success){
                return data.reviews;
            } else {
                console.error("Couldn't fetch trending reviews: ", data.message);
                return [];
            }
        } catch (error) {
            console.error("Error fetching trending reviews: ", error);
            return [];
        }
    }

    //Insert trending review details into HTML review card article for displaying
    function showTrendingReviews(reviews) {
        reviewGrid.innerHTML = reviews.map(review => `
            <article class="trending-review-card">
                <h3><a href="review.html?id=${review.id}">${review.title}</a></h3>
                <p class="media-type">${review.media_type}</p>
                <p class="rating">Rating: ${"*".repeat(review.rating)}${"-".repeat(5-review.rating)}</p>
                <p class="summary">${review.summary}</p>
                <footer>
                    <span>Posted by <a href="profile.php?user_id=${review.user_id}">${review.poster}</a></span>
                    <span>Likes: ${review.like_count}</span>
                    <span>Comments: ${review.comment_count}</span>
                </footer>
            </article>
        `).join("");
    }

    //Call display function
    getTrendingReviews().then(reviews => {
        showTrendingReviews(reviews);
    });

    document.querySelector('.media-scroller').addEventListener('mouseenter', () => {
        document.querySelector('.media-scroller').style.animationPlayState = 'paused';
      });
      
      document.querySelector('.media-scroller').addEventListener('mouseleave', () => {
        document.querySelector('.media-scroller').style.animationPlayState = 'running';
      });
});