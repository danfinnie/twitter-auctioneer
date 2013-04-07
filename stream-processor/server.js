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
    database: process.env.AUCTIONEER_MYSQL_DB,
    debug: true
});

function handle_sell(data, sell_idx) {
    console.log("received a sell");
    var twitter_user_id = data.user.id_str;
    var twitter_user_name = data.user.screen_name;
    var item_idx = sell_idx == 0 ? 1 : 0;
    var item = data.entities.hashtags[item_idx].text;
    var timestamp = Date.parse(data.created_at);

    connection.query('INSERT INTO auctions (item, start_date, seller_user_id) VALUES (?, FROM_UNIXTIME(?), ?)', [item, timestamp/1000, twitter_user_id], function(err, results) {
        console.log(err);
        console.log(results);
    });
}

/**
 * This thing shoves tweets to our bot into the database.
 */
stream.stream();
stream.on('error', function(e) {
    console.log(e);
});
stream.on('data', function(data) {
    // Only handle tweets
    if (!data.user || !data.text)
        return;

    console.log(data);

    var twitter_user_id = data.user.id_str;
    var twitter_user_name = data.user.screen_name;

    var hashtags = data.entities.hashtags.map(function(tag) { return tag.text });
    var timestamp = Date.parse(data.created_at);
    var bid_idx = hashtags.indexOf("bid");
    var sell_idx = hashtags.indexOf("sell");
    var item;

    if (sell_idx > -1) {
        handle_sell(data, sell_idx);
        return;
    } else if (bid_idx == -1 || hashtags.length < 2)
        return; // Need to specify #bid to bid.
    else if (bid_idx == 0)
        item = hashtags[1];
    else
        item = hashtags[0];

    var price_matches = /\$([\d\.]+)/.exec(data.text)
    if (!price_matches || price_matches.length < 2)
        return; // Needs to have a price
    var price = price_matches[1];


    connection.query('SELECT * FROM auctions WHERE item = ? ORDER BY start_date DESC LIMIT 1', [item], function(err, results) {
        console.log(results);

        if (results.length == 0)
            return; // No such auction is running.

        if (results[0].price >= price)
            return; // Price must be monotonically increasing.

        var auction_id = results[0].auction_id;

        connection.query('INSERT INTO bids (twitter_user_id, twitter_user_name, price, auction_id, date) VALUES (?, ?, ?, ?, FROM_UNIXTIME(?))', [twitter_user_id, twitter_user_name, price, auction_id, timestamp/1000], function(err, results) {
            console.log(err);
            console.log(results);
        });

        connection.query('UPDATE auctions SET price = ?, notification_state = ? WHERE auction_id = ?', [price, 1, auction_id], function(err, results) {
            console.log(err);
            console.log(results);
        });
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
