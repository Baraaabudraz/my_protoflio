<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New inquiry</title>
</head>
<body style="margin:0;padding:24px 12px;background:#f4f6fb;font-family:Arial,Helvetica,sans-serif;color:#16202e;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
        <tr>
            <td style="background:linear-gradient(135deg,#0379a8,#2563eb);background-color:#0379a8;padding:24px 28px;color:#ffffff;">
                <div style="font-size:13px;opacity:.85;">New inquiry from your portfolio</div>
                <div style="font-size:22px;font-weight:bold;margin-top:4px;">{{ $inquiry['name'] }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding:24px 28px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.6;">
                    <tr><td style="color:#5c6a7e;width:110px;padding:4px 0;">Email</td><td style="padding:4px 0;"><a href="mailto:{{ $inquiry['email'] }}" style="color:#0379a8;">{{ $inquiry['email'] }}</a></td></tr>
                    @if($inquiry['phone'])
                        <tr><td style="color:#5c6a7e;padding:4px 0;">Phone</td><td style="padding:4px 0;" dir="ltr">{{ $inquiry['phone'] }}</td></tr>
                    @endif
                    @if($inquiry['service'])
                        <tr><td style="color:#5c6a7e;padding:4px 0;">Service</td><td style="padding:4px 0;">{{ $inquiry['service'] }}</td></tr>
                    @endif
                    @if($inquiry['budget'])
                        <tr><td style="color:#5c6a7e;padding:4px 0;">Budget</td><td style="padding:4px 0;">{{ $inquiry['budget'] }}</td></tr>
                    @endif
                    <tr><td style="color:#5c6a7e;padding:4px 0;">Language</td><td style="padding:4px 0;">{{ $inquiry['locale'] === 'ar' ? 'Arabic' : 'English' }}</td></tr>
                </table>
                <div style="margin-top:18px;padding:16px 18px;background:#f4f6fb;border-radius:12px;font-size:15px;line-height:1.7;white-space:pre-line;" dir="auto">{{ $inquiry['message'] }}</div>
                <p style="margin:22px 0 0;">
                    <a href="mailto:{{ $inquiry['email'] }}" style="display:inline-block;background:#0379a8;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:10px;">Reply to {{ $inquiry['name'] }}</a>
                    <a href="{{ route('admin.messages') }}" style="display:inline-block;margin-left:10px;color:#0379a8;text-decoration:none;font-weight:bold;padding:12px 4px;">Open inbox</a>
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 28px;border-top:1px solid #e2e8f0;font-size:12px;color:#5c6a7e;">Message #{{ $messageId }} · Just hit reply to answer the client directly.</td>
        </tr>
    </table>
</body>
</html>
