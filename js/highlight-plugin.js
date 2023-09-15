document.addEventListener('DOMContentLoaded', function() {
    // Look for the current hash in the URL
    const hash = window.location.hash.substring(1);

    console.log(hash);
    if (hash) {
        const pluginElement = document.getElementById(hash);

        if (pluginElement) {
            // Highlight the plugin row
            pluginElement.classList.add('highlight-plugin');

            // Scroll to the plugin row
            pluginElement.scrollIntoView();
        }
    }
});