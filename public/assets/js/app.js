document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-auto-dismiss="true"]').forEach(function (alertElement) {
        setTimeout(function () {
            var alert = bootstrap.Alert.getOrCreateInstance(alertElement);
            alert.close();
        }, 4500);
    });

    var yearElement = document.getElementById('currentYear');

    if (yearElement) {
        yearElement.textContent = new Date().getFullYear();
    }
});
