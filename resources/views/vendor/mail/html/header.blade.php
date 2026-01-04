@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ $url }}/assets/fytrr-logo-alt.png" class="logo" alt="Fytrr Logo" style="height: 40px; width: auto;">
@endif
</a>
</td>
</tr>

