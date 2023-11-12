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
            font-size: 12px;
            color: grey;
            text-align: center;
            padding: 10px 20px;
        }
    </style>
</head>
<body>

    <div class="email-container">
        <img src="https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0" alt="Header Image" class="header-image">

        <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
            <tr>
                <td style="padding: 20px;">
                    {!! $content !!}
                    <p>Peace,</p>
                    <p><strong>{{ $subscriber->course->admin_name }}</strong><br>
                    <img src="https://i.postimg.cc/RVPwkBTj/junior-ferreira-7es-RPTt38n-I-unsplash.jpg" alt="Signature Image" style="width: 50px; height: 50px;"></p>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    Your Company Name<br>
                    Your Company Address<br>
                    <a href="unsubscribe-link">Unsubscribe</a>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
