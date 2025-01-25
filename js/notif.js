        Notification.requestPermission()
        .then(permission => {
            if (permission === 'granted') {
                Notification.permission = 'granted';
                new Notification("hi")
            } else if (permission === 'denied') {
                Notification.permission = 'denied';
            }
        })