<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head><title>Welcome to {{ $site_name }}!</title></head>
    <body>
        <div style="max-width: 800px; margin: 0; padding: 30px 0;">
            <table width="80%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="5%"></td>
                    <td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
                        <h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Welcome to {{ $site_name }}!</h2>
                        Thanks for joining {{ $site_name }}. We listed your sign in details below, make sure you keep them safe.<br />
                        To verify your email address, please follow this link:<br />
                        <br />
                        <big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="{{ url('otentik/activate', array('user_id'=>$user_id, 'key'=>$new_email_key)) }}" style="color: #3366cc;">Finish your registration...</a></b></big><br />
                        <br />
                        Link doesn't work? Copy the following link to your browser address bar:<br />
                <nobr>
                     {{ URL::to('/otentik/activate/' . $user_id . '/' . $new_email_key, $parameters = array(), $secure = null) }}
                </nobr><br />
                <br />
                Please verify your email within {{ $activation_period }} hours, otherwise your registration will become invalid and you will have to register again.<br />
                <br />
                <br />
                @if (strlen($username) > 0) Your username: {{ $username }}<br /> @endif
                Your email address: {{ $email }}<br />
                @if (isset($password)) Your password: {{ $password }}<br /> @endif
                <br />
                <br />
                Have fun!<br />
                The {{ $site_name }} Team
                </td>
                </tr>
            </table>
        </div>
    </body>
</html>