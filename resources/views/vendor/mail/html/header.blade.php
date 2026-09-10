@props(['url'])
<tr>
<td class="header">
<table class="header-card" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="header-inner">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ $url }}/assets/email-logo.png" class="logo" alt="fytrr" style="height: 32px; width: auto;">
@endif
</a>
</td>
</tr>
</table>
</td>
</tr>
