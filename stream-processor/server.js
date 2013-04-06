var Stream = require('user-stream');
var mysql = require('mysql');

var stream = new Stream({
    consumer_key: process.env.AUCTIONEER_TWITTER_CONSUMER_KEY,
    consumer_secret: process.env.AUCTIONEER_TWITTER_CONSUMER_SECRET,
    access_token_key: process.env.AUCTIONEER_TWITTER_ACCESS_TOKEN,
    access_token_secret: process.env.AUCTIONEER_TWITTER_ACCESS_TOKEN_SECRET
});

var connection = mysql.createConnection({
    host: process.env.AUCTIONEER_MYSQL_HOST,
    user: process.env.AUCTIONEER_MYSQL_USER,
    password: process.env.AUCTIONEER_MYSQL_PASSWORD,
    database: process.env.AUCTIONEER_MYSQL_DB
});

/**
 * This thing shoves tweets to our bot into the database.
 */
stream.stream();
stream.on('data', function(data) {
    // Only handle tweets
    if (!data.user || !data.text)
        return

    console.log(data);

    var twitter_user_id = data.user.id_str;
    var twitter_user_name = data.user.screen_name;
    var price = /\$([\d\.]+)/.exec(data.text)[1];
    var hashtags = data.entities.hashtags.map(function(tag) { return tag.text });
    var bid_idx = hashtags.indexOf("bid");
    var item;

    if (bid_idx == -1 || hashtags.length < 2)
        return; // Need to specify #bid to bid.
    else if (bid_idx == 0)
        item = hashtags[1];
    else
        item = hashtags[0];

    console.log({user: twitter_user_id, price: price, item: item});

    connection.query('INSERT INTO bids (twitter_user_id, twitter_user_name, price, item, date) VALUES (?, ?, ?, ?, NOW())', [twitter_user_id, twitter_user_name, price, item], function(err, results) {
        console.log(results);
    });
});

/**
 * This thing periodically looks for finished auctions.
 */
var Y = function (F) {
 return (function (x) {
  return F(function (y) { return (x(x))(y);});
  })
        (function (x) {
  return F(function (y) { return (x(x))(y);});
  }) ;
} ;

Y(function(rec) {
    return function() {
        console.log("PONG");
        setTimeout(rec, 60*100);
    };
})();
