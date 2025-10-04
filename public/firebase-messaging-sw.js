// Import Firebase libraries for service workers
importScripts('https://www.gstatic.com/firebasejs/9.17.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.17.2/firebase-messaging-compat.js');

// Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyA7fvIfsXBy1TbfTagWrb4fpg6REV2rdbQ",
    authDomain: "file-management-170b4.firebaseapp.com",
    projectId: "file-management-170b4",
    storageBucket: "file-management-170b4.appspot.com",
    messagingSenderId: "689494579007",
    appId: "1:689494579007:web:993f30299950ccf056a58a",
    measurementId: "G-WG544F1YKK",
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Initialize Firebase Messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log("Received background message:", payload);

    const notificationTitle = payload.notification.title || "Background Notification";
    const notificationOptions = {
        body: payload.notification.body,
        icon: "/firebase-logo.png",
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
