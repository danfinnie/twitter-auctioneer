var Stream = require('user-stream');

var stream = new Stream({
    consumer_key: process.env.AUCTIONEER_TWITTER_CONSUMER_KEY,
    consumer_secret: process.env.AUCTIONEER_TWITTER_CONSUMER_SECRET,
    access_token_key: process.env.AUCTIONEER_TWITTER_ACCESS_TOKEN,
    access_token_secret: process.env.AUCTIONEER_TWITTER_ACCESS_TOKEN_SECRET
});

//create stream
stream.stream();

//listen stream data
stream.on('data', function(json) {
  console.log(json);
});
