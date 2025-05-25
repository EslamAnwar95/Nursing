
// server.js
const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const cors = require('cors');
const bodyParser = require('body-parser');

// 1. إعداد السيرفر
const app = express();
const server = http.createServer(app);

// 2. إعداد سوكيت IO
const io = socketIo(server, {
    cors: {
        origin: "*", // اسمح للكل يتصل
    }
});

app.use(cors());
app.use(bodyParser.json());

// 3. نقطة الاتصال من Laravel
app.post('/order-created', (req, res) => {
    const { provider_id, provider_type, order_id, patient_id } = req.body;

    console.log('📨 Laravel Triggered:', req.body);

    const roomName = `${provider_type}-${provider_id}`;

    // بث مباشر للممرض
    io.to(roomName).emit('orderCreated', {
        order_id,
        patient_id,
        provider_id,
        provider_type,
    });

    return res.sendStatus(200);
});

// 4. تعامل مع اتصال المستخدم
io.on('connection', (socket) => {
    console.log('🔌 User connected:', socket.id);

    socket.on('joinRoom', (roomId) => {
        socket.join(roomId);
        console.log(`🟢 Joined room: ${roomId}`);
    });

    socket.on('disconnect', () => {
        console.log('❌ User disconnected:', socket.id);
    });
});

// 5. شغل السيرفر
server.listen(3000, () => {
    console.log('🚀 Socket.io server running on http://localhost:3000');
});