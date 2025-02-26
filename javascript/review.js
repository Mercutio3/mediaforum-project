document.addEventListener("DOMContentLoaded", function () {
    const reviewTitle = document.getElementById("review-title");
    const reviewMediaType = document.getElementById("review-media-type");
    const reviewMediaTitle = document.getElementById("review-media-title");
    const reviewMediaCreator = document.getElementById("review-media-creator");
    const reviewMediaYear = document.getElementById("review-media-year");
    const reviewRating = document.getElementById("review-rating");
    const reviewSummary = document.getElementById("review-summary");
    const reviewTags = document.getElementById("review-tags");
    const reviewCreatedAt = document.getElementById("review-created-at");
    const reviewImage = document.getElementById("review-image");
    const reviewPoster = document.getElementById("review-poster");
    const reviewLikes = document.getElementById("review-like-count");
    const reviewCommentCount = document.getElementById("review-comment-count");
    const commentList = document.getElementById("review-comments");
    const commentForm = document.getElementById("review-comment-form");

    function getReviewId() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get("id");
    }

    async function fetchReview(reviewId){
        try{
            const response = await fetch(`../php/review.php?id=${reviewId}`);
            const data = await response.json();

            if(data.success){
                return data;
            } else {
                console.error("Could not fetch review:", data.message);
                return null;
            }
        } catch(error) {
            console.error("Error fetching review:", error);
            return null;
        }
    }

    function displayReviewDetails(review){
        console.log("Review details:", review);
        reviewTitle.textContent = review.title;
        reviewMediaType.textContent = `Media Type: ${review.media_type}`;
        reviewMediaTitle.textContent = `Media Title: ${review.media_title}`;
        reviewMediaCreator.textContent = `Creator: ${review.media_creator}`;
        reviewMediaYear.textContent = `Year: ${review.media_year}`;
        reviewRating.textContent = `Rating: ${"*".repeat(review.rating)}${"-".repeat(5-review.rating)}`;
        reviewSummary.textContent = review.summary;
        reviewTags.textContent = `Tags: ${review.tags || "No tags"}`;
        reviewCreatedAt.textContent = `Posted on: ${new Date(review.created_at).toLocaleString()}`;
        reviewPoster.textContent = review.username;
        reviewPoster.href = `user.html?username=${review.username}`;
        reviewLikes.textContent = review.likes || 0;
        reviewCommentCount.textContent = review.comments ? review.comments.length : 0;

        if(reviewImage){
            if(review.image_url){
                reviewImage.src = review.image_url;
                reviewImage.alt = `Cover image for ${review.media_title}`;
            } else {
                reviewImage.style.display = "none";
            }
        } else {
            console.error("Reviewimge element not found in DOM.");
        }
        
    }

    function displayComments(comments) {
        console.log("Displaying comments:", comments);
        if (comments && comments.length > 0){
            commentList.innerHTML = comments.map(comment => `
                <article class="comment">
                    <p>${comment.content}</p>
                    <footer>
                        <span>Posted by <a href="user.html?username=${comment.username}">${comment.username}</a></span>
                        <span>${new Date(comment.created_at).toLocaleString()}</span>
                    </footer>
                </article>
            `).join("");
        } else {
            commentList.innerHTML = "<p>No comments yet.</p>";
        }  
    }

    const reviewId = getReviewId();
    if(reviewId) {
        fetchReview(reviewId).then(data => {
            if(data) {
                displayReviewDetails(data.review);
                displayComments(data.comments);
            } else {
                reviewTitle.textContent = "Reviwe not found.";
            }
        });
    } else {
        reviewTitle.textContent = "Invalid Review ID.";
    }

    commentForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        const commentText = document.getElementById("comment-text").value;

        if(!commentText) {
            alert("Enter comment!");
            return;
        }

        try {
            const response = await fetch("../php/comment.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    review_id: reviewId,
                    user_id: 1,
                    content: commentText,
                }),
            });
            const data = await response.json();

            if(data.success){
                const reviewData = await fetchReview(reviewId);
                if(reviewData) {
                    displayComments(reviewData.comments);
                    reviewCommentCount.textContent = reviewData.comments.length;
                }
            } else {
                alert("Couldn't submit comment. Please try again.");
            }
        } catch (error){
            console.error("Error submitting comment:", error);
            alert("Error occurred. Try again.");
        }

        commentForm.reset();
    });
});