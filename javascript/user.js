document.addEventListener("DOMContentLoaded", function () {
    const userPfp = document.getElementById("user-pfp");
    const userName = document.getElementById("username");
    const userBio = document.getElementById("user-bio");
    const userReviewCount = document.getElementById("user-review-count");
    const userLikeCount = document.getElementById("user-like-count");
    const userCommentCount = document.getElementById("user-comment-count");
    const userReviews = document.getElementById("user-review-list");

    function getUsername(){
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get("username");
    }

    const users = [
        {
            username: "GordonFreeman",
            pfp: "images/profile1.jpg",
            bio: "User bio. HL3.",
            reviewCount: 3,
            likeCount: 4,
            commentCount: 5,
            reviews: [
                { id: 1, title: "Review Title 1", mediaType: "book", rating: 2, summary: "book sumamry 1", likes: 33, comments: 1 },
                { id: 2, title: "Review Title 2", mediaType: "movie", rating: 4, summary: "movie sumamry 1", likes: 1, comments: 31 },
            ],
        },
        {
            username: "GloopyPoopy",
            pfp: "images/profile2.jpg",
            bio: "Poopity scoop.",
            reviewCount: 5,
            likeCount: 6,
            commentCount: 7,
            reviews: [
                { id: 3, title: "Review Title 3", mediaType: "movie", rating: 5, summary: "best movie ever", likes: 2, comments: 0 },
            ],
        },
    ];

    function displayUserDetails(user){
        userPfp.src = user.pfp;
        userName.textContent = user.username;
        userBio.textContent = user.bio;
        userReviewCount.textContent = user.reviewCount;
        userLikeCount.textContent = user.likeCount;
        userCommentCount.textContent = user.commentCount;
    }

    function displayUserReviews(reviews){
        userReviews.innerHTML = reviews.map(review => `
            <article class="user-review-card">
                <h3><a href="review.html?id=${review.id}">${review.title}</a></h3>
                <p class="media-type">${review.mediaType}</p>
                <p class="rating">Rating: ${"*".repeat(review.rating)}${"-".repeat(5 - review.rating)}</p>
                <p class="summary">${review.summary}</p>
                <footer>
                    <span>Likes: ${review.likes}</span>
                    <span>Comments: ${review.comments}</span>
                </footer>
            </article>
        `).join("");
    }

    const username = getUsername();
    const user = users.find(u => u.username === username);

    if(user){
        displayUserDetails(user);
        displayUserReviews(user.reviews);
    } else {
        userName.textContent = "Invalid user."
    }
});