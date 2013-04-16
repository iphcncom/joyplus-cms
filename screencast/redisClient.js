var redis = require("redis");
var redisClient = redis.createClient();

redisClient.on("error", function (err) {
    console.log("Error " + err);
});
 
redisClient.on("connect", runSample);
 
function runSample() {
    // Set a value
    redisClient.set("string key", "Hello World", function (err, reply) {
        console.log(reply.toString());
    });
    // Get a value
    redisClient.get("string key", function (err, reply) {
        console.log(reply.toString());
    });
}
