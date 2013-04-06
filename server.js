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

//create stream
stream.stream();

//listen stream data
stream.on('data', function(data) {
    // Only handle tweets
    if (!data.user || !data.text)
        return

    var twitter_user_id = data.user.id_str;
    var price = /\$([\d\.]+)/.exec(data.text)[1];
    var item = data.entities.hashtags[0].text;

    console.log({user: twitter_user_id, price: price, item: item});

    connection.query('INSERT INTO bids (twitter_user_id, price, item) VALUES (?, ?, ?)', [twitter_user_id, price, item], function(err, results) {
        console.log(results);
    });
});
