window.addEventListener('load', () => {
    const emailBlob = new Blob([atob(emailContents)], { type: 'text/html' });
    const emailUrl = URL.createObjectURL(emailBlob);
    while (true) {
        const userConsent = window.confirm('[DEV-MODE-ONLY]\nRight now the site would have sent you an email.\nWould you like to open the simulated email?');
        if (!userConsent)
            break;
        const handle = window.open(emailUrl, target = '_blank');
        if (handle) {
            window.addEventListener('message', event => {
                if (event.data == 'revokeBlob')
                    URL.revokeObjectURL(emailUrl);
            });
            break;
        }
        window.alert('[DEV-MODE-ONLY]\nPlease allow popups and try again.');
    }
});