<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رمز التحقق</title>
</head>
<body style="font-family: Tahoma, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 500px; margin: auto; text-align: center;">
        <h2 style="color: #333;">رمز التحقق الخاص بك</h2>
        <p style="font-size: 28px; font-weight: bold; color: #007BFF;">{{ $otp }}</p>
        <p style="color: #555; font-size: 16px;">
            الرمز صالح لمدة <strong>5 دقائق فقط</strong>.<br>
            من فضلك لا تشاركه مع أي شخص للحفاظ على أمان حسابك.
        </p>
    </div>
</body>
</html>