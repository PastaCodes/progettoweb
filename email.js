window.addEventListener('load', () => {
    const emailContents = JSON.parse(document.getElementById('email-contents').innerText);
    const emailBlob = new Blob([emailContents], { type: 'application/xhtml+xml' });
    const emailUrl = URL.createObjectURL(emailBlob);
    while (true) {
        const userConsent = window.confirm('[DEV-MODE-ONLY] Would you like to open the simulated email?');
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
        window.alert('[DEV-MODE-ONLY] Please allow popups then click OK.');
    }
});