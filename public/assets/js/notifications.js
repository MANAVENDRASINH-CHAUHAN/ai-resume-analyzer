(function () {
    function getCsrfToken() {
        var tokenElement = document.querySelector('meta[name="csrf-token"]');

        return tokenElement ? tokenElement.getAttribute('content') : '';
    }

    async function fetchJson(url, options) {
        var response = await fetch(url, Object.assign({
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }, options || {}));

        if (!response.ok) {
            throw new Error('Could not fetch notification data.');
        }

        return response.json();
    }

    function updateBadge(badgeElement, count) {
        var unreadCount = Number(count || 0);

        badgeElement.textContent = unreadCount;
        badgeElement.classList.toggle('d-none', unreadCount === 0);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var badgeElement = document.querySelector('[data-notification-badge]');
        var triggerElement = document.querySelector('[data-notification-trigger]');

        if (!badgeElement || !triggerElement) {
            return;
        }

        var unreadUrl = badgeElement.dataset.url;
        var markReadUrl = triggerElement.dataset.markReadUrl;
        var isLoading = false;

        async function refreshBadge() {
            if (isLoading) {
                return;
            }

            isLoading = true;

            try {
                var data = await fetchJson(unreadUrl);
                updateBadge(badgeElement, data.unread_count);
            } catch (error) {
                console.error('Notification refresh failed.', error);
            } finally {
                isLoading = false;
            }
        }

        triggerElement.addEventListener('click', async function () {
            if (Number(badgeElement.textContent || 0) === 0) {
                return;
            }

            try {
                await fetchJson(markReadUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                });

                updateBadge(badgeElement, 0);
            } catch (error) {
                console.error('Could not mark notifications as read.', error);
            }
        });

        refreshBadge();
        setInterval(refreshBadge, 10000);
    });
})();
