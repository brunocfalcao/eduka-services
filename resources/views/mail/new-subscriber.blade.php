<!DOCTYPE html>
<html>
<head>
    <title>Mastering Nova Newsletter</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f0f0f0; font-family: 'Consolas', 'Courier New', monospace;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 30px; background-color: #f0f0f0;">
        <tr>
            <td align="center">
                <!-- Logo Image -->
                <img src="https://via.placeholder.com/100x100.png?text=Mastering+Nova" alt="Mastering Nova Logo" style="width: 100px; height: auto; margin-bottom: 20px;">
                <!-- Content Table -->
                <table width="800" border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #ccc; background-color: #ffffff;"> <!-- Increased width from 600 to 800 -->
                    <tr>
                        <td align="center" style="padding: 20px; font-size: 24px;">
                            Mastering Nova
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px; text-align: left; line-height: 1.6;">
                            <p>Hey there!</p>
                            <p>Thank you for your interest in subscribing for news of my upcoming course, <b><strong>{{ $subscriber->course->name }}</strong></b>!</p>
                            <p>I will keep you updated about the launch date as soon as I have more clarity on it.</p>
                            <p>I am currently recording this course, so if you would like to see a topic covered, let me know and I'll see if I can add it too.</p>
                            <p>Best, <br/> Bruno</p>
                        </td>
                    </tr>
                </table>
                <!-- Footer Table -->
                <table width="800" border="0" cellspacing="0" cellpadding="0" style="margin-top: 1px;"> <!-- Adjusted width to match content table -->
                    <tr>
                        <td style="padding: 10px; font-size: 12px; text-align: center; color: #666; line-height: 1;">
                            <p style="margin-bottom: 4px;">Your email are kept private and never shared.</p>
                            <p><a href="https://{{ $subscriber->course->domain }}">{{ $subscriber->course->domain }}</a> - All rights reserved</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
