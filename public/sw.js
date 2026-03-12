self.addEventListener('push', function (e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        //notifications aren't supported or permission not granted!
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        console.log('Push received:', msg);

        const title = msg.title || 'Ngekos.id';
        const options = {
            body: msg.body || 'Ada notifikasi baru untuk Anda.',
            icon: msg.icon || '/storage/logo/logo-icon.svg',
            badge: '/storage/logo/logo-icon.svg',
            data: {
                url: msg.data ? msg.data.url : '/'
            },
            actions: msg.actions || [],
            requireInteraction: true, // Notification stays until clicked
            vibrate: [200, 100, 200],
            tag: 'order-notification-' + Date.now() // Assign unique tag so they don't overwrite
        };

        e.waitUntil(
            self.registration.showNotification(title, options)
        );
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const urlToOpen = event.notification.data.url || '/';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function(clientList) {
            // Check if there is already a window open with this URL and focus it
            for (let i = 0; i < clientList.length; i++) {
                let client = clientList[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }
            // Otherwise, open a new window
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});
