<tr>
    <td>
        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">

            <tr>
                <td class="content-cell" align="center" style="padding-top: 24px;">
                    <table cellpadding="0" cellspacing="0" role="presentation">
                        <tr>
                            <td align="center" style="padding-bottom: 16px;">
                                <a href="https://instagram.com/getfytrr"
                                   style="text-decoration: none; display: inline-block;">
                                    <img src="https://cdn-icons-png.flaticon.com/512/174/174855.png" alt="Instagram"
                                         width="28" height="28" style="display: block;">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <a href="{{ config('app.app_store.ios.url') }}"
                                   style="text-decoration: none; display: inline-block;">
                                    <img src="{{asset("/assets/badges/App_Store_Badge_EN.svg")}}"
                                         alt="Download on the App Store" width="120" style="display: block;">
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
