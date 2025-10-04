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

// Register the service worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/firebase-messaging-sw.js')
        .then((registration) => {
            console.log('Service Worker registered with scope:', registration.scope);
        })
        .catch((error) => {
            console.error('Service Worker registration failed:', error);
        });
}
