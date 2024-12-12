@component('mail::message')
# Contact Us Form

**From:** {{ $data['email'] }}

**Message:** <br>
{{ $data['description'] }}

@endcomponent