document.addEventListener("DOMContentLoaded", function (){
    const notificationList = document.getElementById("notifications");

    //Get notifications from database
    async function getNotifications() {
        try {
            const response = await fetch("../php/notification-fetch.php");
            const data = await response.json();

            if(data.success) {
                return data.notifications;
            } else {
                console.error("Couldn't fetch notifications: ", data.message);
                return [];
            }
        } catch (error) {
            console.error("Error fetching notifs.: ", error);
            return [];
        }
    }

    //Show notifications
    function displayNotifications(notifications) {
        notificationList.innerHTML = notifications.map(notification => `
            <article class="notification ${notification.is_read ? "read" : "unread"}" data-id="${notification.id}">
                <div class="notification-content">
                    <p><strong>${notification.source_username}</strong> ${notification.type === "like" ? "liked" : "commented on"} your review: <a href="review.html?id=${notification.review_id}">${notification.review_title}</a></p>
                    <span class="timestamp">${new Date(notification.created_at).toLocaleString()}</span>
                </div>
                <button class="mark-as-read">${notification.is_read ? "Mark as Unread" : "Mark as Read"}</button>
            </article>
        `).join("");
    }

    //Toggle notification read status
    async function toggleRead(notificationId, isRead){
        try {
            const response = await fetch("..php/mark-notification.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    notification_id: notificationId,
                    is_read: isRead ? 0 : 1,
                }),
            });
            const data = await response.json();

            if(!data.success){
                console.error("Failed to update notification.", data.message); 
            }
        } catch (error) {
            console.error("Error updating notification: ", error);
        }
    }

    //Fetch notifications and display them
    getNotifications().then(notifications => {
        displayNotifications(notifications);
    });

    //Handle "mark as read" button
    notificationList.addEventListener("click", function (event) {
        if(event.target.classList.contains("mark-as-read")) {
            const notificationElement = event.target.closest(".notification");
            const notificationId = parseInt(notificationElement.getAttribute("data-id"));

            notificationElement.classList.toggle("read");
            notificationElement.classList.toggle("unread");
            event.target.textContent = notificationElement.classList.contains("read") ? "Mark as Unread" : "Mark as Read";

            const isRead = notificationElement.classList.contains("read");
            toggleRead(notificationId, isRead);
        }
    });
});