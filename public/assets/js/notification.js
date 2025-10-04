// Initialize Firebase Messaging
const messaging = firebase.messaging();

// Request notification permission
async function requestNotificationPermission() {
    try {
        const permission = await Notification.requestPermission();
        if (permission === "granted") {
            console.log("Notification permission granted.");
            await handleFCMToken();
        } else {
            console.warn("Notification permission denied.");
        }
    } catch (error) {
        console.error("Error requesting notification permission:", error);
    }
}

// Handle incoming messages
messaging.onMessage((payload) => {
    console.log("Message received:", payload);

    // Display notification in the UI
    const notificationsList = document.getElementById("notificationsList");
    const newNotification = document.createElement("li");
    newNotification.textContent = payload.notification.body;
    notificationsList.appendChild(newNotification);
});

// Add event listener to request permission button
document.getElementById("requestPermissionButton").addEventListener("click", requestNotificationPermission);
