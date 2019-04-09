/**
 * Created by kozhevnikov on 12.02.2016.
 */


var express = require('express');
var app = express();
var server = require('http').Server(app);
var io = require('socket.io')(server);
//var redis = require('socket.io-redis');
//io.adapter(redis({ host: 'localhost', port: 6379 }));
var redis = require('redis');
var redisClient = redis.createClient(6379, 'localhost');

redisClient.flushall(); //при старте серва сбрасываем всю историю
var counter = 0;

server.listen(8888);

console.log('listening on *:8888');

io.on('connection', function(socket){


  //  redisClient.lpush('messages', ['d', 'c']);
  //  redisClient.lrange('messages', 0, 99, function(err, reply) {
  //      reply.reverse().forEach(function(item, i, arr){
  //          console.log(i + ' => ' + item);
  //        //  socket.emit('chatMessage', item);
  //      })
  //  });
    redisClient.smembers('messages', function(err, reply){
        if(reply != null){
            reply.reverse().forEach(function(key){
                redisClient.hgetall(key, function(err, data){
                    socket.emit('chatInit', data);
                });
            });
        }
    });

 //   console.log('a user connected');

    socket.on('disconnect', function() {
        console.log('a user disconnected');
    });

    socket.on('chatMessage', function(msg, user){
        counter++;
        console.log('out message: ' + msg);
       // redisClient.flushall();
        var mKey = 'messages:' + counter + ':' + user['username'];
        redisClient.hmset(mKey, 'msg', msg, 'name', user['fullname']); // push into redis
        redisClient.sadd('messages', mKey);
        redisClient.hgetall(mKey, function(error, data){
            io.sockets.emit('chatMessage', data);
        });

      //  socket.send(msg);
    });


    function log() {
        var array = [">>> "];
        for(var i = 0; i < arguments.length; i++) {
            array.push(arguments[i]);
        }
        socket.emit('log', array);
    }

    socket.on('message', function (message) {
        console.log(message.type);
        log('Got message: ', message);
       // console.log(io.sockets.clients().length);
        socket.broadcast.emit('message', message); // should be room only
    });

    socket.on('create or join', function (room) {
        var numClients = io.sockets.clients(room).length;

        log('Room ' + room + ' has ' + numClients + ' client(s)');
        log('Request to create or join room', room);

        if(numClients == 0) {
            socket.join(room);
            socket.emit('created', room);
        }

        else if(numClients == 1) {
            io.sockets.in(room).emit('join', room);
            socket.join(room);
            socket.emit('joined', room);
        }

        else { // max two clients
            socket.emit('full', room);
        }

        socket.emit('emit(): client ' + socket.id + ' joined room ' + room);
        socket.broadcast.emit('broadcast(): client ' + socket.id + ' joined room ' + room);
    });

});

