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
    const likeButton = document.getElementById("like-button");

    let isLiked = false;

    //Get review ID
    function getReviewId() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get("id");
    }

    //Get review details
    async function fetchReview(reviewId){
        try{
            const response = await fetch(`../php/review.php?id=${reviewId}`);
            const data = await response.json();

            if(data.success){
                return data;
            } else {
                console.error("Could not fetch review: ", data.message);
                return null;
            }
        } catch(error) {
            console.error("Error fetching review: ", error);
            return null;
        }
    }

    //Check if user has already liked review
    async function checkIfLiked(reviewId){
        try {
            const response = await fetch(`../php/check-like.php?review_id=${reviewId}`);
            const data = await response.json();
            return data.liked;
        } catch (error) {
            console.error("Error checking like: ", error);
            return false;
        }
    }

    //Like 
    async function likeReview(reviewId){
        try {
            const response = await fetch(`../php/like.php?review_id=${reviewId}`, {
                method: "POST",
            });
            const data = await response.json();

            if(data.success) {
                isLiked = true;
                likeButton.textContent = "Unlike";
                updateLikeCount(1);
            } else {
                alert(data.message || "Failed to like.");
            }
        } catch (error) {
            console.error("Error liking:", error);
            alert("Error occured, try again.");
        }
    }

    //Unlike
    async function unlikeReview(reviewId){
        try {
            const response = await fetch(`../php/unlike.php?review_id=${reviewId}`, {
                method: "POST",
            });
            const data = await response.json();

            if(data.success) {
                isLiked = false;
                likeButton.textContent = "Like";
                updateLikeCount(-1);
            } else {
                alert(data.message || "Failed to unlike.");
            }
        } catch (error) {
            console.error("Error unliking:", error);
            alert("Error occured, try again.");
        }
    }

    //Update like count
    function updateLikeCount(change){
        const likeCount = parseInt(reviewLikes.textContent);
        reviewLikes.textContent = likeCount + change;
    }

    //Display review details
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
            console.error("reviewImage not found in DOM.");
        }
        
    }

    //Display comments
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
        fetchReview(reviewId).then(async data => {
            if(data) {
                displayReviewDetails(data.review);
                displayComments(data.comments);

                isLiked = await checkIfLiked(reviewId);
                likeButton.textContent = isLiked ? "Unlike" : "Like";
            } else {
                reviewTitle.textContent = "Reviwe not found.";
            }
        });
    } else {
        reviewTitle.textContent = "Invalid Review ID.";
    }

    //Handle like-button clicking
    likeButton.addEventListener("click", async function (event) {
        if(isLiked) {
            await unlikeReview(reviewId);
        } else {
            await likeReview(reviewId);
        }
    });

    //Handle comment submission
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