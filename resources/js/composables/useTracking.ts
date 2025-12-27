export const useTracking = () => {
    /**
     * Track custom event in Plausible
     * @param eventName - Name of the event (e.g., 'Plan Generated')
     * @param props - Optional properties to attach to the event
     */
    const trackEvent = (eventName: string, props?: Record<string, any>) => {
        if (typeof window !== 'undefined' && window.plausible) {
            window.plausible(eventName, { props });
        }
    };

    /**
     * Track pageview manually (useful for SPA navigation)
     * @param url - Optional URL to track (defaults to current URL)
     */
    const trackPageview = (url?: string) => {
        if (typeof window !== 'undefined' && window.plausible) {
            window.plausible('pageview', { u: url });
        }
    };

    return {
        trackEvent,
        trackPageview,
    };
};

// TypeScript Declaration
declare global {
    interface Window {
        plausible?: (
            event: string,
            options?: { props?: Record<string, any>; u?: string },
        ) => void;
    }
}
