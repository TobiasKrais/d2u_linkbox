(function () {
    function applyLinkboxDataStyles() {
        document.querySelectorAll('[data-linkbox-bg-color], [data-linkbox-bg-color-dark], [data-linkbox-border-color], [data-linkbox-border-color-dark], [data-linkbox-bg-image], [data-linkbox-hover-image]').forEach((element) => {
            if (element.dataset.linkboxBgColor) {
                element.style.setProperty('--linkbox-bg-color', element.dataset.linkboxBgColor);
            }
            if (element.dataset.linkboxBgColorDark) {
                element.style.setProperty('--linkbox-bg-color-dark', element.dataset.linkboxBgColorDark);
            }
            if (element.dataset.linkboxBorderColor) {
                element.style.setProperty('--linkbox-border-color', element.dataset.linkboxBorderColor);
            }
            if (element.dataset.linkboxBorderColorDark) {
                element.style.setProperty('--linkbox-border-color-dark', element.dataset.linkboxBorderColorDark);
            }
            if (element.dataset.linkboxBgImage) {
                element.style.setProperty('--linkbox-bg-image', 'url("' + element.dataset.linkboxBgImage + '")');
            }
            if (element.dataset.linkboxHoverImage) {
                element.style.setProperty('--linkbox-hover-image', 'url("' + element.dataset.linkboxHoverImage + '")');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyLinkboxDataStyles);
    } else {
        applyLinkboxDataStyles();
    }
})();
