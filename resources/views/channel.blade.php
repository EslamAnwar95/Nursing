<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>قناة الممرض</title>
</head>
<body>
    <h1>🩺 صفحة الممرض</h1>

    <script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
    <script>
        const socket = io('http://localhost:3000');

        const providerId = 7;
        const providerType = 'nurse';

        // لازم تتنفذ بعد الاتصال
        socket.on('connect', () => {
            socket.emit('joinRoom', `${providerType}-${providerId}`);
            console.log('✅ Joined room:', `${providerType}-${providerId}`);
        });

        socket.on('orderCreated', function (data) {
            console.log("🚨 Order Received:", data);
            alert("طلب جديد وصل! رقم الطلب: " + data.order_id);
        });
    </script>
</body>
</html>
