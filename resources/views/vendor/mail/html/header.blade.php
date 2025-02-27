@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim(string: $slot) === 'Laravel')
<img src="{{ asset('image/TTA logo.jpg') }}" alt="TTA Logo" class="logo" width="150">

@else
{{ $slot }}
@endif
</a>
</td>
</tr>

<!--https://laravel.com/img/notification-logo.png-->
