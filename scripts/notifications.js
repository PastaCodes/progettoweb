export function addNotification(title, content) {
    fetch(`api?title=${title}&content=${content}`, {
        method: 'PUT',
        credentials: 'include',
        headers: {
            'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
        }
    });
}

export function deleteNotification(id) {
    fetch(`api?delete_notification=${notificationId}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: {
            'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
        }
    });
}
