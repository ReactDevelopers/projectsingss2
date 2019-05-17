/**
 * Register service worker
 */
if('serviceWorker' in navigator) {
    if (navigator.onLine === true) {
        navigator.serviceWorker
            .register('/contract-lodge-sw.js')
            .then(() => {
                console.log("Service Worker Registered");
            })
    }
}

window.addEventListener("load", () => {
    window.addEventListener("online", handleNetworkChange);
    window.addEventListener("offline", handleNetworkChange);
    // Check current network status on page load.
    handleNetworkChange();
});

/**
 * Function to enable/disable input elements in offline mode.
 */
function enableDisableInputs() {
    // Enable/disable inputs when online/offline
    if (navigator.onLine) {
        jQuery("input, select, textarea, button").prop("disabled", false);
    } else {
        jQuery("input, select, textarea, button").not("button[data-dismiss='modal'], #modal-notifications button, input[type='hidden']").prop("disabled", true);
    }
}

/**
 * Function to handle network change.
 */
function handleNetworkChange() {
    if (navigator.onLine) {
        document.body.classList.remove("offline");
    } else {
        document.body.classList.add("offline");
    }
    // Call to the function to enable/disable inputs in online/offline mode.
    enableDisableInputs();
    // Call to the function to enable/disable new entry links.
    enableDisableLinks();
}

/**
 * Function to disable new entry links
 */
function enableDisableLinks() {

    if (navigator.onLine) {
        jQuery('a[data-offline="disabled"]').removeClass("disabled-link");
    } else {
        jQuery('a[data-offline="disabled"]').addClass("disabled-link");
    }
}
