@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@elseif (str_contains(trim($slot), 'Fitness'))
<img src="{{ $url }}/fytrr-logo.svg" class="logo" alt="{{ trim($slot) }} Logo" style="height: 50px; width: auto;">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>

