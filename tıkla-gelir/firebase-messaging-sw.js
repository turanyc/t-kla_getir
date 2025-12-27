importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

const firebaseConfig = {
  apiKey: "AIzaSyD184l78jHw1fwU70jmxLPOiHeZj3Y0",
  authDomain: "kral-kurye.firebaseapp.com",
  projectId: "kral-kurye",
  storageBucket: "kral-kurye.firebasestorage.app",
  messagingSenderId: "842745909494",
  appId: "1:842745909494:web:5b6257cc51f31235039cd7"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage(payload => {
  self.registration.showNotification(payload.notification.title, {
    body: payload.notification.body,
    icon: '/assets/logo192.png'
  });
});