// Firebase modüler SDK (v9+)

import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";

import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging.js";



// SENİN CONFIG (değişmedi)

const firebaseConfig = {

  apiKey: "AIzaSyD184l78jHw1fwU70jmxLPOiHeZj3Y0",

  authDomain: "kral-kurye.firebaseapp.com",

  projectId: "kral-kurye",

  storageBucket: "kral-kurye.firebasestorage.app",

  messagingSenderId: "842745909494",

  appId: "1:842745909494:web:5b6257cc51f31235039cd7",

  measurementId: "G-B75WW9KS9H"

};



const app = initializeApp(firebaseConfig);

const messaging = getMessaging(app);



/* ----------  FCM TOKEN AL & SUNUCUYA GÖNDER  ---------- */

async function saveToken() {

  const token = await getToken(messaging, {

    vapidKey: "BB0k6OVcIDftJuxVAb6XTUVnGUUJCUlfAJCpPCgmfiGQcyO8b9Lhnwb3QbUv0K9OZe2QNmJYQmA6acbcIDcEYXY" // << VAPID (public)

  });

  if (token) {

    await fetch('api/save_fcm_token.php', {

      method: 'POST',

      headers: {'Content-Type': 'application/x-www-form-urlencoded'},

      body: 'token=' + encodeURIComponent(token)

    });

  }

}



/* ----------  FOREGROUND MESAJI  ---------- */

onMessage(messaging, payload => {

  new Notification(payload.notification.title, {

    body: payload.notification.body,

    icon: payload.notification.icon || '/assets/logo192.png'

  });

});



/* ----------  SAYFA YÜKLENİNCE TOKEN’I KAYDET  ---------- */

document.addEventListener('DOMContentLoaded', saveToken);