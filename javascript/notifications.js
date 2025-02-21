document.addEventListener("DOMContentLoaded", function (){
    const notificationList = document.getElementById("notifications");

    let notifications = [
        { id: 1, type: "like", user: "Mercutio33", content: "Review Title 1", timestamp: "30 minutes ago", read: false},
        { id: 2, type: "comment", user: "Olaf Scholz", content: "Review Title 2", timestamp: "50 minutes ago", read: false},
        { id: 3, type: "reply", user: "bunny", content: "Review Title 2", timestamp: "2 hours ago", read: true},
    ];

    function displayNotifications() {
        notificationList.innerHTML = notifications.map(notification => `
            <article class="notification ${notification.read ? "read" : "unread"}" data-id="${notification.id}">
                <div class="notification-content">
                    <p><strong>${notification.user}</strong> ${notification.type === "like" ? "liked" : notification.type === "comment" ? "commented on" : "replied to"} your review: <a href="review.html">${notification.conent}</a></p>
                    <span class="timestamp">${notification.timestamp}</span>
                </div>
                <button class="mark-as-read">${notification.read ? "Mark as Unead" : "Mark as Read"}</button>
            </article>
        `).join("");
    }

    displayNotifications();

    notificationList.addEventListener("click", function (event) {
        if(event.target.classList.contains("mark-as-read")) {
            const notificationElement = event.target.closest(".notification");
            const notificationId = parseInt(notificationElement.getAttribute("data-id"));

            const notification = notifications.find(n => n.id === notificationId);

            notification.read = !notification.read;

            displayNotifications();
        }
    });
});