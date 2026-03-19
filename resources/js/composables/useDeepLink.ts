import { ref } from 'vue';

/**
 * Try to open the app via custom URL scheme.
 * If the app is not installed, fall back to the App Store after a timeout.
 */
export function useDeepLink(appStoreUrl: string, scheme: string = 'fytrr') {
    const didOpenApp = ref(false);

    function openApp(path: string = '', queryParams: Record<string, string> = {}) {
        const params = new URLSearchParams(queryParams).toString();
        const query = params ? `?${params}` : '';
        const schemeUrl = `${scheme}://${path}${query}`;

        const onBlur = () => {
            didOpenApp.value = true;
            window.removeEventListener('blur', onBlur);
        };
        window.addEventListener('blur', onBlur);

        window.location.href = schemeUrl;

        // If the app didn't open after 1.5s, redirect to App Store
        setTimeout(() => {
            window.removeEventListener('blur', onBlur);
            if (!didOpenApp.value && !document.hidden) {
                window.location.href = appStoreUrl;
            }
        }, 1500);
    }

    return {
        openApp,
        didOpenApp,
    };
}
