<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Complete Your Profile</title>
</head>

<body style="margin:0;padding:0;background-color:#f9f9f9;font-family:Arial,sans-serif;">
    <table style="border-spacing:0;width:100%;">
        <tr>
            <td align="center">
                <div class="container"
                    style="max-width:600px;margin:20px auto;background-color:#fff;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,.1);overflow:hidden;">
                    <!-- Header Section -->
                    <div class="header"
                        style="background:linear-gradient(135deg,#007bff,#6610f2);color:#fff;text-align:center;padding:40px 20px;">
                        <h1 style="margin:0;font-size:28px;">Complete Your Profile Today!</h1>
                        <p style="margin:5px 0 0;font-size:16px;opacity:.9;">Unlock the full potential of your job
                            search 🚀</p>
                    </div>

                    <!-- Content Section -->
                    <div class="content"
                        style="padding:20px;color:#333;line-height:1.6;font-size:16px; text-align:left">
                        <p style="margin:0 0 15px;">Hi <strong>{{NAME}}</strong>,</p>
                        <p style="margin:0 0 15px;">We noticed that your profile is almost complete—great job! But did
                            you know that profiles with 100% completion get <strong>[X]% more attention</strong> from
                            recruiters?</p>
                        <p style="margin:0 0 15px;">By completing your profile, you’ll:</p>
                        <ul style="margin:0 0 15px;padding-left:20px;">
                            <li style="margin-bottom:10px;">Boost your chances of being discovered by top employers.
                            </li>
                            <li style="margin-bottom:10px;">Receive personalized job recommendations tailored to your
                                skills.</li>
                            <li style="margin-bottom:10px;">Showcase your unique skills and experience in the best way
                                possible.</li>
                        </ul>
                        <p style="margin:0 0 15px;">It only takes a few minutes to update your profile and open the door
                            to exciting opportunities.</p>
                        <a href="<?php echo site_url('/?email={{USER_EMAIL}}&token={{TOKEN}}')?>" class="cta-button"
                            style="display:block;text-align:center;background-color:#007bff;color:#fff;text-decoration:none;padding:12px 20px;margin:20px 0;font-size:18px;font-weight:700;border-radius:6px;">Complete
                            My Profile Now</a>
                        <p style="margin:0 0 15px;">Don’t miss-out employers are looking for candidates just like you!
                        </p>
                    </div>

                    <!-- Footer Section -->
                    <div class="footer"
                        style="text-align:center;padding:20px;font-size:12px;color:#888;background-color:#f1f1f1;">
                        © <?php current_datetime( 'Y' ) ?> | GulfWorking.com | All rights reserved.<br>
                        <a href="mailto:support@yourcompany.com" style="color:#007bff;text-decoration:none;">Contact
                            Support</a>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>