Hi @if (strlen($username) > 0) {{ $username }} @endif,

You have changed your email address for {{ $site_name }}.
Follow this link to confirm your new email address:

{{ url('/reset_email/' . $user_id . '/' . $new_email_key, $parameters = array(), $secure = null) }}


Your new email: {{ $new_email }}


You received this email, because it was requested by a {{ $site_name }} user. If you have received this by mistake, please DO NOT click the confirmation link, and simply delete this email. After a short time, the request will be removed from the system.


Thank you,
The {{ $site_name }} Team