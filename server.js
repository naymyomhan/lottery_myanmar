const app = require('express')();
const fs = require('fs');

const server = require('https').createServer({
    key: fs.readFileSync("/etc/ssl/key.pem"),
    cert: fs.readFileSync("/etc/ssl/cert.pem"),
    requestCert: true,
    rejectUnauthorized: false
}, app);

var io = require('socket.io')(server, {
    cors: { origin: '*', },
    path: '/mysocket',
    transports: ['polling', 'websocket'],
});

const Redis = require('ioredis');
var redis = new Redis();
const socket_port = 3030;

var users = [];

server.listen(socket_port, function () {
    console.log('listening to port ' + socket_port)
})

redis.subscribe('private-channel', function () {
    console.log('subscribed to private channel');
});

redis.on('message', function (channel, message) {
    message = JSON.parse(message);

    console.log(message.data.data);
    if (channel == 'private-channel') {
        let admin_id = message.data.data.new_message.admin_id;
        if (admin_id != null) {
            let data = message.data.data;
            let receiver_id = message.data.data.new_message.user_id;
            let event = message.event;
            // console.log(users);
            io.to(`${users[receiver_id]}`).emit(channel + ':' + event, data);
        } else {
            let data = message.data.data;
            let event = message.event;
            // console.log(users);
            io.emit('admin:' + channel + ':' + event, data);
        }
    }
});

io.on('connection', function (socket) {
    socket.on('user_connection', function (user_id) {
        users[user_id] = socket.id;
        // io.emit('user_join_chatroom',user_id);
        console.log("user join the chat rooom " + user_id);
    });

    socket.on('admin_connection', function () {
        io.emit('updateAdminStatus');
        console.log("admin connected ");
    });

    socket.on('disconnect', function () {
        // var socket_id=socket.id;
        // console.log(socket_id);

        // var user_id=users.indexOf(socket_id);
        // console.log(user_id);
        // io.emit('user_leave_chatroom',user_id);
    });
});
