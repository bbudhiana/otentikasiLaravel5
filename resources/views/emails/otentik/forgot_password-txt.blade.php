Hi @if (strlen($username) > 0) {{$username}} @endif,

Forgot your password, huh? No big deal.
To create a new password, just follow this link:

{{ url('/otentik/reset_password/' . $user_id . '/' . $new_pass_key, $parameters = array(), $secure = null) }}


You received this email, because it was requested by a {{ $site_name }} user. This is part of the procedure to create a new password on the system. If you DID NOT request a new password then please ignore this email and your password will remain the same.


Thank you,
The {{ $site_name }} Team