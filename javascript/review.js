document.addEventListener("DOMContentLoaded", function () {
    const reviewTitle = document.getElementById("review-title");
    const reviewMediaType = document.getElementById("review-media-type");
    const reviewRating = document.getElementById("review-rating");
    const reviewSummary = document.getElementById("review-summary");
    const reviewPoster = document.getElementById("review-poster");
    const reviewLikes = document.getElementById("review-like-count");
    const reviewCommentCount = document.getElementById("review-comment-count");
    const commentList = document.getElementById("review-comments");
    const commentForm = document.getElementById("review-comment-form");

    function getReviewId() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get("id");
    }

    const reviews = [
        {
            id: 1,
            title: "Review 1",
            mediaType: "book",
            rating: 4,
            summary: "Review text.",
            poster: "Mercutio33",
            likes: 23,
            comments: [
                { id: 1, user: "Mercutio44", content: "hola", timestamp: "10 seconds ago"},
                { id: 2, user: "Mercutio55", content: "adios", timestamp: "4 days ago"},
            ],
        },
        {
            id: 2,
            title: "Review 2",
            mediaType: "movie",
            rating: 3,
            summary: "review text 2",
            poster: "Chungus22",
            likes: 42,
            comments: [
                { id: 1, user: "Mercutio44", content: "adios", timestamp: "2 days ago"},
            ],
        }
    ];

    function displayReviewDetails(review){
        reviewTitle.textContent = review.title;
        reviewMediaType.textContent = review.mediaType;
        reviewRating.textContent = `Rating: ${"*".repeat(review.rating)}${"-".repeat(5-review.rating)}`;
        reviewSummary.textContent = review.summary;
        reviewPoster.textContent = review.poster;
        reviewPoster.href = `user.html?username=${review.poster}`;
        reviewLikes.textContent = review.likes;
        reviewCommentCount.textContent = review.comments.length;
    }

    function displayComments(comments) {
        commentList.innerHTML = comments.map(comment => `
            <article class="comment">
                <p>${comment.content}</p>
                <footer>
                    <span>Posted by <a href="user.html?username=${comment.user}">${comment.user}</a></span>
                    <span>${comment.timestamp}</span>
                </footer>
            </article>
        `).join("");
    }

    const reviewId = getReviewId();
    const review = reviews.find(r => r.id === parseInt(reviewId));

    if(review) {
        displayReviewDetails(review);
        displayComments(review.comments);
    } else {
        reviewTitle.textContent = "Review not found.";
    }

    commentForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const commentText = document.getElementById("comment-text").value;

        if(!commentText) {
            alert("Enter comment!");
            return;
        }

        const newComment = {
            id: review.comments.length + 1,
            user: "CurrentUser",
            content: commentText,
            timestamp: "Just now",
        };

        review.comments.push(newComment);
        displayComments(review.comments);

        reviewCommentCount.textContent = review.comments.length;

        commentForm.reset();
    });
});