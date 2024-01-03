<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 100px 0 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            line-height: 1.5; /* Adjust line height */
        }
        .email-container {
            width: 100%;
            max-width: 600px;
        }
        .header-image {
            width: 100%;
            max-height: 100px;
            display: block;
        }
        .footer {
            font-size: 18px;
            color: grey;
            text-align: center;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
            <tr>
                <td>
                    {!! $content !!}
                    <p style="padding-top: 20px">Peace,</p>
                    <p><strong>{{ $subscriber->course->admin_name }}</strong><br>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    {{ $subscriber->course->name }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
