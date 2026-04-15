{{-- Service worker for Firebase Cloud Messaging (compat API). --}}
importScripts('https://www.gstatic.com/firebasejs/{{ $version }}/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/{{ $version }}/firebase-messaging-compat.js');

firebase.initializeApp(@json($firebaseConfig));

firebase.messaging();
