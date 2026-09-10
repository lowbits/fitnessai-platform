@props(['campaign' => 'email'])
<tr>
    <td>
        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">

            <tr>
                <td class="content-cell" align="center" style="padding-top: 24px;">
                    <table cellpadding="0" cellspacing="0" role="presentation" align="center">
                        @php $badgeLocale = app()->getLocale() === 'de' ? 'DE' : 'EN'; @endphp
                        <tr>
                            <td align="center" style="padding-bottom: 18px;">
                                <a href="{{ \App\Support\AppStore::url('email-'.$campaign) }}"
                                   style="text-decoration: none; display: inline-block; margin: 0 4px;">
                                    <img src="{{ asset('/assets/badges/App_Store_Badge_'.$badgeLocale.'.png') }}"
                                         alt="Download on the App Store" width="120" style="display: inline-block;">
                                </a>
                                <a href="{{ route('download-app', ['locale' => app()->getLocale(), 'utm_source' => 'email', 'utm_medium' => 'notification', 'utm_campaign' => $campaign, 'utm_content' => 'android_waitlist']) }}"
                                   style="text-decoration: none; display: inline-block; margin: 0 4px;">
                                    <img src="{{ asset('/assets/badges/Android_Waitlist_'.$badgeLocale.'.png') }}"
                                         alt="Android waitlist" width="120" style="display: inline-block;">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <a href="{{ \App\Support\EmailLink::withUtm('https://instagram.com/getfytrr', $campaign, 'social_instagram') }}" style="text-decoration: none; display: inline-block; margin: 0 9px;">
                                    <img src="{{ asset('/assets/icons/social-instagram.png') }}" alt="Instagram" width="22" height="22" style="display: inline-block;">
                                </a>
                                <a href="{{ \App\Support\EmailLink::withUtm('https://www.tiktok.com/@fytrr_app', $campaign, 'social_tiktok') }}" style="text-decoration: none; display: inline-block; margin: 0 9px;">
                                    <img src="{{ asset('/assets/icons/social-tiktok.png') }}" alt="TikTok" width="22" height="22" style="display: inline-block;">
                                </a>
                                <a href="{{ \App\Support\EmailLink::withUtm('https://www.linkedin.com/company/92827726', $campaign, 'social_linkedin') }}" style="text-decoration: none; display: inline-block; margin: 0 9px;">
                                    <img src="{{ asset('/assets/icons/social-linkedin.png') }}" alt="LinkedIn" width="22" height="22" style="display: inline-block;">
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="content-cell" align="center">
                    {{ Illuminate\Mail\Markdown::parse($slot) }}
                </td>
            </tr>
        </table>
    </td>
</tr>
