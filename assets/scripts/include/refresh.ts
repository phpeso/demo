export function tryRefresh() {
    const url = URL.parse(document.URL);
    if (url === null) {
        throw Error('Unable to parse document URL!');
    }
    const dt = new Date();
    const refreshed = url.searchParams.get('refresh');

    if (refreshed !== null && dt.getTime() - Number.parseInt(refreshed) < 60000) {
        // refreshed in less than a minute
        return;
    }

    url.searchParams.set('refresh', dt.getTime().toString());

    window.location.assign(url); // refresh
}

export function deleteRefresh() {
    const url = URL.parse(document.URL);

    if (url === null) {
        throw Error('Unable to parse document URL!');
    }

    if (url.searchParams.has('refresh')) {
        url.searchParams.delete('refresh');
        window.history.replaceState(null, '', url);
    }
}
