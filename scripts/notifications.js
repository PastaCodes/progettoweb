export function addNotification(title, content, recipient = null) {
    fetch(`api?title=${title}&content=${content}${recipient ? `&recipient=${recipient}` : ''}`, {
        method: 'PUT',
        credentials: 'include',
        headers: {
            'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
        }
    });
}

export function deleteNotification(id) {
    fetch(`api?delete_notification=${id}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: {
            'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
        }
    });
}
