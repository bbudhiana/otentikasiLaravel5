<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head><title>Your new email address on {{ $site_name }}</title></head>
    <body>
        <div style="max-width: 800px; margin: 0; padding: 30px 0;">
            <table width="80%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="5%"></td>
                    <td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
                        <h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Your new email address on {{ $site_name }}</h2>
                        You have changed your email address for {{ $site_name }}.<br />
                        Follow this link to confirm your new email address:<br />
                        <br />
                        <big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="{{ url('reset_email', array('user_id'=>$user_id, 'key'=>$new_email_key)) }}" style="color: #3366cc;">Confirm your new email</a></b></big><br />
                        <br />
                        Link doesn't work? Copy the following link to your browser address bar:<br />
                <nobr>
                     {{ url('/reset_email/' . $user_id . '/' . $new_email_key, $parameters = array(), $secure = null) }}
                </nobr><br />
                <br />
                <br />
                Your email address: {{ $new_email }}<br />
                <br />
                <br />
                You received this email, because it was requested by a <a href="{{ url('/')}}" style="color: #3366cc;">{{ $site_name }}</a> user. If you have received this by mistake, please DO NOT click the confirmation link, and simply delete this email. After a short time, the request will be removed from the system.<br />
                <br />
                <br />
                Thank you,<br />
                The {{ $site_name }} Team
                </td>
                </tr>
            </table>
        </div>
    </body>
</html>