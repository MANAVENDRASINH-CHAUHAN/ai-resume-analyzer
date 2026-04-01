(function () {
    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    async function fetchJson(url) {
        var response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Could not refresh dashboard data.');
        }

        return response.json();
    }

    function highlightChange(element) {
        if (!element) {
            return;
        }

        element.classList.remove('live-updated');
        void element.offsetWidth;
        element.classList.add('live-updated');
    }

    function updateText(root, selector, value) {
        var element = root.querySelector(selector);

        if (!element) {
            return;
        }

        var textValue = String(value);

        if (element.textContent.trim() !== textValue) {
            element.textContent = textValue;
            highlightChange(element);
        }
    }

    function updateLastUpdated(root, value) {
        root.querySelectorAll('[data-dashboard-last-updated]').forEach(function (element) {
            element.textContent = 'Last updated: ' + value;
        });
    }

    function renderCandidateRecentResumes(root, items) {
        var tbody = root.querySelector('[data-dashboard-recent-resumes]');

        if (!tbody) {
            return;
        }

        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No resumes uploaded yet. Use the upload button to get started.</td></tr>';

            return;
        }

        tbody.innerHTML = items.map(function (item) {
            return '' +
                '<tr>' +
                    '<td><a href="' + escapeHtml(item.resume_url) + '" class="fw-semibold text-dark">' + escapeHtml(item.file_name) + '</a></td>' +
                    '<td>' + escapeHtml(item.job_role) + '</td>' +
                    '<td><span class="badge rounded-pill ' + escapeHtml(item.analysis_status_badge_class) + '">' + escapeHtml(item.analysis_status_label) + '</span></td>' +
                    '<td>' + escapeHtml(item.total_score) + '%</td>' +
                    '<td>' + escapeHtml(item.uploaded_at) + '</td>' +
                '</tr>';
        }).join('');
    }

    function renderAdminRecentResumes(root, items) {
        var tbody = root.querySelector('[data-dashboard-recent-resumes]');

        if (!tbody) {
            return;
        }

        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No resumes found.</td></tr>';

            return;
        }

        tbody.innerHTML = items.map(function (item) {
            return '' +
                '<tr>' +
                    '<td>' + escapeHtml(item.candidate_name) + '</td>' +
                    '<td>' + escapeHtml(item.file_name) + '</td>' +
                    '<td><span class="badge rounded-pill ' + escapeHtml(item.analysis_status_badge_class) + '">' + escapeHtml(item.analysis_status_label) + '</span></td>' +
                    '<td>' + escapeHtml(item.total_score) + '</td>' +
                '</tr>';
        }).join('');
    }

    function startCandidateDashboardPolling(root) {
        var url = root.dataset.statsUrl;
        var isLoading = false;

        async function refresh() {
            if (isLoading) {
                return;
            }

            isLoading = true;

            try {
                var data = await fetchJson(url);

                updateText(root, '[data-dashboard-stat="total_resumes"]', data.total_resumes);
                updateText(root, '[data-dashboard-stat="completed_analyses"]', data.completed_analyses);
                updateText(root, '[data-dashboard-stat="pending_analyses"]', data.pending_analyses);
                updateText(root, '[data-dashboard-stat="average_score"]', data.average_score + '%');
                renderCandidateRecentResumes(root, data.recent_resumes || []);
                updateLastUpdated(root, data.last_updated || new Date().toLocaleString());
            } catch (error) {
                console.error('Candidate dashboard refresh failed.', error);
            } finally {
                isLoading = false;
            }
        }

        refresh();
        setInterval(refresh, 5000);
    }

    function startAdminDashboardPolling(root) {
        var url = root.dataset.statsUrl;
        var isLoading = false;

        async function refresh() {
            if (isLoading) {
                return;
            }

            isLoading = true;

            try {
                var data = await fetchJson(url);

                updateText(root, '[data-dashboard-stat="total_users"]', data.total_users);
                updateText(root, '[data-dashboard-stat="total_candidates"]', data.total_candidates);
                updateText(root, '[data-dashboard-stat="total_admins"]', data.total_admins);
                updateText(root, '[data-dashboard-stat="active_job_roles"]', data.active_job_roles);
                updateText(root, '[data-dashboard-stat="total_resumes"]', data.total_resumes);
                updateText(root, '[data-dashboard-stat="completed_analyses"]', data.completed_analyses);
                updateText(root, '[data-dashboard-stat="pending_analyses"]', data.pending_analyses);
                renderAdminRecentResumes(root, data.recent_resumes || []);
                updateLastUpdated(root, data.last_updated || new Date().toLocaleString());
            } catch (error) {
                console.error('Admin dashboard refresh failed.', error);
            } finally {
                isLoading = false;
            }
        }

        refresh();
        setInterval(refresh, 5000);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var candidateDashboard = document.querySelector('[data-candidate-dashboard]');
        var adminDashboard = document.querySelector('[data-admin-dashboard]');

        if (candidateDashboard) {
            startCandidateDashboardPolling(candidateDashboard);
        }

        if (adminDashboard) {
            startAdminDashboardPolling(adminDashboard);
        }
    });
})();
