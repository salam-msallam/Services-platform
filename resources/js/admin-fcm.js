import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

function readJsonScript(id) {
    const el = document.getElementById(id);
    if (!el?.textContent?.trim()) {
        return null;
    }

    try {
        return JSON.parse(el.textContent);
    } catch {
        return null;
    }
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

async function registerFcm() {
    const raw = readJsonScript('firebase-web-config');
    if (!raw?.apiKey) {
        return;
    }

    const vapidKey = raw.vapidKey ?? '';
    const { vapidKey: _drop, ...firebaseConfig } = raw;

    if (!vapidKey) {
        console.warn('[admin-fcm] FIREBASE_WEB_VAPID_KEY is missing; cannot get FCM token.');
        return;
    }

    if (!('Notification' in window)) {
        return;
    }

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        return;
    }

    const swUrl = document.querySelector('meta[name="firebase-messaging-sw-url"]')?.getAttribute('content');
    if (!swUrl) {
        return;
    }

    const registration = await navigator.serviceWorker.register(swUrl, { scope: '/' });

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    onMessage(messaging, (payload) => {
        if (payload.notification?.title) {
            /* eslint-disable no-new */
            new Notification(payload.notification.title, {
                body: payload.notification.body,
            });
        }
    });

    const token = await getToken(messaging, {
        vapidKey,
        serviceWorkerRegistration: registration,
    });

    if (!token) {
        return;
    }

    const endpoint = document.querySelector('meta[name="admin-device-token-url"]')?.getAttribute('content');
    if (!endpoint) {
        return;
    }

    const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken(),
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            device_token: token,
            platform: 'web',
        }),
    });

    if (!response.ok) {
        console.warn('[admin-fcm] Failed to register device token', response.status);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        registerFcm().catch((e) => console.warn('[admin-fcm]', e));
    });
} else {
    registerFcm().catch((e) => console.warn('[admin-fcm]', e));
}
