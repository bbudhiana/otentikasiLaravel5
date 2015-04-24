Welcome to {{ $site_name }},

Thanks for joining {{ $site_name }}. We listed your sign in details below, make sure you keep them safe.
To verify your email address, please follow this link:

{{ url('/otentik/activate/' . $user_id . '/' . $new_email_key, $parameters = array(), $secure = null) }}


Please verify your email within {{ $activation_period }} hours, otherwise your registration will become invalid and you will have to register again.
@if (strlen($username) > 0) @endif

Your username: {{ $username }}

Your email address: {{ $email }}
@if (isset($password))
Your password: {{ $password }}
@endif



Have fun!
The {{ $site_name }} Team