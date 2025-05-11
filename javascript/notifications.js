document.addEventListener("DOMContentLoaded", function (){
    const notificationList = document.getElementById("notifications");
    const filters = document.querySelectorAll(".button-filter");
    const noNotificationsMessage = document.getElementById("no-notifications");

    //Get notifications from database
    async function getNotifications() {
        try {
            const response = await fetch("../php/notification-fetch.php");
            const data = await response.json();

            if(data.success) {
                console.log("Fetched Notification: ", data.notifications);
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

    //Insert notification details into HTML notification article for displaying
    function displayNotifications(notifications, filter = "all") {
        const filteredNotifications = notifications.filter(notification => {
            if(filter === "all") return true;
            return notification.type === filter;
        });

        console.log("Filtered notifications: ", filteredNotifications);

        if(filteredNotifications.length === 0){
            noNotificationsMessage.classList.remove("hidden");
        } else {
            noNotificationsMessage.classList.add("hidden");
            notificationList.innerHTML = filteredNotifications.map(notification => `
                <article class="notification ${notification.is_read ? "read" : "unread"}" data-id="${notification.id}">
                    <div class="notification-content">
                        <p><strong>${notification.source_username}</strong> ${notification.type === "like" ? "liked" : "commented on"} your review: <a href="review.html?id=${notification.review_id}">${notification.review_title}</a></p>
                        <span class="timestamp">${new Date(notification.created_at).toLocaleString()}</span>
                    </div>
                    <button class="mark-as-read">${notification.is_read ? "Mark as Unread" : "Mark as Read"}</button>
                </article>
            `).join("");
        }
    }

    //Toggle notification "read" status
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

    //Change filter button CSS
    filters.forEach(button => {
        button.addEventListener("click", function () {
            filters.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");
            const filter = this.getAttribute("data-filter")
            getNotifications().then(notifications => {
                displayNotifications(notifications, filter);
            });
        });
    });

    //Get notifications and display them
    getNotifications().then(notifications => {
        displayNotifications(notifications);
    });

    //Handle "mark as read" button clicking
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