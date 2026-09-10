<tr>
    <td>
        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">

            <tr>
                <td class="content-cell" align="center" style="padding-top: 24px;">
                    <table cellpadding="0" cellspacing="0" role="presentation" align="center">
                        <tr>
                            <td align="center" style="padding-bottom: 18px;">
                                <a href="{{ \App\Support\AppStore::url('email') }}"
                                   style="text-decoration: none; display: inline-block;">
                                    <img src="{{ asset('/assets/badges/App_Store_Badge_'.(app()->getLocale() === 'de' ? 'DE' : 'EN').'.png') }}"
                                         alt="Download on the App Store" width="120" style="display: block;">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <a href="https://instagram.com/getfytrr" style="text-decoration: none; display: inline-block; margin: 0 9px;">
                                    <img src="{{ asset('/assets/icons/social-instagram.png') }}" alt="Instagram" width="22" height="22" style="display: inline-block;">
                                </a>
                                <a href="https://www.tiktok.com/@fytrr_app" style="text-decoration: none; display: inline-block; margin: 0 9px;">
                                    <img src="{{ asset('/assets/icons/social-tiktok.png') }}" alt="TikTok" width="22" height="22" style="display: inline-block;">
                                </a>
                                <a href="https://www.linkedin.com/company/92827726" style="text-decoration: none; display: inline-block; margin: 0 9px;">
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
