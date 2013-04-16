
/**
 * Module dependencies.
 */

var express = require('express')
  , routes = require('./routes')
  , user = require('./routes/user')
  , http = require('http')
  , faye = require('faye')
  , path = require('path');

var redis = require("redis");
var redisClient = redis.createClient();

var app = express();

var bayeux     = new faye.NodeAdapter({mount: '/bindtv', timeout: 20});

// all environments
app.set('port', 80);
app.set('views', __dirname + '/views');
app.set('view engine', 'jade');
app.use(express.favicon());
app.use(express.logger('dev'));
app.use(express.bodyParser());
app.use(express.methodOverride());
app.use(app.router);
app.use(express.static(path.join(__dirname, 'public')));

// development only
if ('development' == app.get('env')) {
  app.use(express.errorHandler());
}

app.get('/', routes.index);
// app.get('/users', user.list);

var server = http.createServer(app).listen(app.get('port'), function(){
  console.log('Express server listening on port ' + app.get('port'));
});

bayeux.attach(server);


redisClient.on("error", function (err) {
    console.log("Error " + err);
});
 
redisClient.on("connect", testRedis);
 
function testRedis() {
    // Set a value
    redisClient.set("string key", "Redis Successfully Connected", function (err, reply) {
        console.log(reply.toString());
    });
    // Get a value
    redisClient.get("string key", function (err, reply) {
        console.log(reply.toString());
    });
}

// Process faye message
bayeux.getClient().subscribe('/screencast/*', function(message) {
  var pushType = message.push_type;

  if ('32' == pushType) {
    //Set a value
    redisClient.set(message.tv_channel + '_' + message.user_id, "1", function (err, reply) {
        console.log(reply.toString());
    });
  } else if ('33' == pushType) {
    redisClient.del(message.tv_channel + '_' + message.user_id, function (err, reply) {
        console.log(reply.toString());
    });
  
  }
// "31"：TV端用于绑定帐户的消息，同时消息体内带有user id.
// "32"：TV向手机发送绑定确认消息。
// "33"：TV端用于解除绑定帐户的消息，同时消息体内带有user id.
// “41”：云端视频推送消息，同时消息体内带有视频id. 

//   var strMessage = JSON.stringify(message);
//   console.log('test: ' + strMessage);

});

bayeux.bind('subscribe', function(clientId, channel) {
  console.log('[  SUBSCRIBE] ' + clientId + ' -> ' + channel);
});

bayeux.bind('unsubscribe', function(clientId, channel) {
  console.log('[UNSUBSCRIBE] ' + clientId + ' -> ' + channel);
});

bayeux.bind('disconnect', function(clientId) {
  console.log('[ DISCONNECT] ' + clientId);
});


app.get('/api', function(req, res){
 var tv_channel = req.param('tv_channel');
 var user_id = req.param('user_id');

 var app_key = req.get('app_key');
 
 if ('ijoyplus_android_0001bj' == app_key) {
 	redisClient.get(tv_channel + '_' + user_id, function (err, reply) {
 	
 	    	console.log('[Status2] ' + reply.toString());
        	console.log('[Status3] ' + err.toString());
    	if (!err) {
    	    if (reply != null) {
    	        res.json({ status: reply.toString() });
    	    } else {
    	        res.json({ status: '0' });
    	    }
    	} else {
    	    res.json({"res_code":"30001","res_desc":"Redis Server return error."})
    	}

 	});
 } else {
 	res.json({"res_code":"10006","res_desc":"Source paramter (appkey) is missing or invalid"})
 }
 


});