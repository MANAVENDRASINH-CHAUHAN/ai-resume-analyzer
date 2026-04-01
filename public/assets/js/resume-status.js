(function () {
    function getCsrfToken() {
        var tokenElement = document.querySelector('meta[name="csrf-token"]');

        return tokenElement ? tokenElement.getAttribute('content') : '';
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    async function fetchJson(url, options) {
        var response = await fetch(url, Object.assign({
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }, options || {}));

        var data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Request failed.');
        }

        return data;
    }

    function highlightChange(element) {
        if (!element) {
            return;
        }

        element.classList.remove('live-updated');
        void element.offsetWidth;
        element.classList.add('live-updated');
    }

    function updateBadge(element, badgeClass, label) {
        if (!element) {
            return;
        }

        element.className = 'badge rounded-pill ' + badgeClass;
        element.textContent = label;
        highlightChange(element);
    }

    function updateProgress(root, progressPercent) {
        var labelElement = root.querySelector('[data-progress-label]');
        var barElement = root.querySelector('[data-progress-bar]');

        if (labelElement) {
            labelElement.textContent = progressPercent + '%';
            highlightChange(labelElement);
        }

        if (barElement) {
            barElement.style.width = progressPercent + '%';
            barElement.setAttribute('aria-valuenow', progressPercent);
            highlightChange(barElement);
        }
    }

    function updateLastUpdated(root, value, selector) {
        var element = root.querySelector(selector || '[data-last-updated]');

        if (element) {
            element.textContent = 'Last updated: ' + value;
        }
    }

    function showFeedback(root, message, type) {
        var feedbackElement = root.querySelector('[data-analysis-feedback]') || root.querySelector('[data-resume-list-feedback]');

        if (!feedbackElement || !message) {
            return;
        }

        feedbackElement.className = 'alert border-0 shadow-sm';
        feedbackElement.classList.add(type === 'error' ? 'alert-danger' : 'alert-info');
        feedbackElement.classList.remove('d-none');
        feedbackElement.textContent = message;
    }

    function updateReportActions(root, data) {
        var reportActions = root.querySelector('[data-report-actions]');
        var reportLink = root.querySelector('[data-view-report-link]');
        var printLink = root.querySelector('[data-print-report-link]');
        var analyzeForm = root.querySelector('[data-analyze-form]');

        if (!reportActions) {
            return;
        }

        if (data.report_available) {
            reportActions.classList.remove('d-none');

            if (reportLink && data.report_url) {
                reportLink.setAttribute('href', data.report_url);
            }

            if (printLink && data.print_url) {
                printLink.setAttribute('href', data.print_url);
            }

            if (analyzeForm && analyzeForm.closest('[data-resume-row]')) {
                analyzeForm.classList.add('d-none');
            }
        } else {
            reportActions.classList.add('d-none');

            if (analyzeForm && analyzeForm.closest('[data-resume-row]')) {
                analyzeForm.classList.remove('d-none');
            }
        }
    }

    function updateAnalyzeButtonState(form, isLoading) {
        var button = form ? form.querySelector('[data-analyze-button]') : null;

        if (!button) {
            return;
        }

        if (!button.dataset.defaultLabel) {
            button.dataset.defaultLabel = button.textContent.trim();
        }

        button.disabled = isLoading;
        button.textContent = isLoading ? 'Starting Analysis...' : button.dataset.defaultLabel;
    }

    function updateResumeRow(row, data) {
        updateBadge(
            row.querySelector('[data-upload-status-badge]'),
            data.upload_status_badge_class,
            data.upload_status_label
        );
        updateBadge(
            row.querySelector('[data-analysis-status-badge]'),
            data.analysis_status_badge_class,
            data.analysis_status_label
        );
        updateProgress(row, data.progress_percent);
        updateReportActions(row, data);

        var scoreElement = row.querySelector('[data-total-score]');

        if (scoreElement) {
            scoreElement.textContent = data.total_score ?? '-';
        }
    }

    function updateResumeDetail(root, data) {
        updateBadge(
            root.querySelector('[data-upload-status-badge]'),
            data.upload_status_badge_class,
            data.upload_status_label
        );
        updateBadge(
            root.querySelector('[data-analysis-status-badge]'),
            data.analysis_status_badge_class,
            data.analysis_status_label
        );
        updateProgress(root, data.progress_percent);
        updateLastUpdated(root, data.updated_at || new Date().toLocaleString(), '[data-last-updated]');
        updateReportActions(root, data);

        if (data.report_available) {
            showFeedback(root, 'Analysis completed successfully. Report is now ready.', 'info');
        }
    }

    function startDetailPolling(root) {
        var url = root.dataset.statusUrl;
        var currentStatus = root.dataset.currentStatus;
        var isLoading = false;

        if (!url || currentStatus === 'completed' || currentStatus === 'error') {
            return;
        }

        async function refreshStatus() {
            if (isLoading) {
                return;
            }

            isLoading = true;

            try {
                var data = await fetchJson(url);
                updateResumeDetail(root, data);
                root.dataset.currentStatus = data.analysis_status;

                if (data.analysis_status === 'completed' || data.analysis_status === 'error') {
                    clearInterval(timer);

                    if (data.analysis_status === 'completed') {
                        setTimeout(function () {
                            window.location.reload();
                        }, 1200);
                    } else {
                        showFeedback(root, 'Analysis failed. Please review the resume text and try again.', 'error');
                    }
                }
            } catch (error) {
                console.error('Resume detail polling failed.', error);
            } finally {
                isLoading = false;
            }
        }

        var timer = setInterval(refreshStatus, 4000);
        refreshStatus();
    }

    function startListPolling(root) {
        var url = root.dataset.statusListUrl;
        var lastUpdatedElement = root.querySelector('[data-resume-list-last-updated]');
        var isLoading = false;

        if (!url) {
            return;
        }

        async function refreshStatuses() {
            if (isLoading) {
                return;
            }

            var rows = Array.prototype.slice.call(root.querySelectorAll('[data-resume-row]'));
            var ids = rows.map(function (row) {
                return row.dataset.resumeId;
            }).filter(Boolean);

            if (!ids.length) {
                return;
            }

            isLoading = true;

            try {
                var data = await fetchJson(url + '?ids=' + ids.join(','));

                (data.resumes || []).forEach(function (resumeData) {
                    var row = root.querySelector('[data-resume-row][data-resume-id="' + resumeData.id + '"]');

                    if (row) {
                        updateResumeRow(row, resumeData);
                    }
                });

                if (lastUpdatedElement) {
                    lastUpdatedElement.textContent = 'Last updated: ' + new Date().toLocaleString();
                }
            } catch (error) {
                console.error('Resume list polling failed.', error);
            } finally {
                isLoading = false;
            }
        }

        var timer = setInterval(function () {
            var activeRows = root.querySelectorAll('[data-resume-row] [data-analyze-form]:not(.d-none)').length;

            if (activeRows > 0 || root.querySelectorAll('[data-resume-row]').length > 0) {
                refreshStatuses();
            } else {
                clearInterval(timer);
            }
        }, 4000);

        refreshStatuses();
    }

    function bindAnalyzeForms(scope) {
        var forms = scope.querySelectorAll('[data-analyze-form]');

        forms.forEach(function (form) {
            if (form.dataset.bound === 'true') {
                return;
            }

            form.dataset.bound = 'true';

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                updateAnalyzeButtonState(form, true);

                try {
                    var data = await fetchJson(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        }
                    });

                    var resumeData = data.resume || {};
                    var detailRoot = form.closest('[data-resume-detail]');
                    var rowRoot = form.closest('[data-resume-row]');
                    var listRoot = form.closest('[data-resume-list]');

                    if (detailRoot) {
                        updateResumeDetail(detailRoot, resumeData);
                        detailRoot.dataset.currentStatus = resumeData.analysis_status;
                        showFeedback(detailRoot, data.message, 'info');
                        startDetailPolling(detailRoot);
                    }

                    if (rowRoot) {
                        updateResumeRow(rowRoot, resumeData);

                        if (listRoot) {
                            showFeedback(listRoot, data.message, 'info');
                        }
                    }
                } catch (error) {
                    var root = form.closest('[data-resume-detail]') || form.closest('[data-resume-list]');
                    showFeedback(root || scope, error.message, 'error');
                    console.error('Could not start analysis.', error);
                } finally {
                    updateAnalyzeButtonState(form, false);
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var detailRoot = document.querySelector('[data-resume-detail]');
        var listRoot = document.querySelector('[data-resume-list]');

        if (detailRoot) {
            bindAnalyzeForms(detailRoot);
            startDetailPolling(detailRoot);
        }

        if (listRoot) {
            bindAnalyzeForms(listRoot);
            startListPolling(listRoot);
        }
    });
})();
