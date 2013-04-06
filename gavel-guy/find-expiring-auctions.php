<?php

require '../vendor/autoload.php';

use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;

$dbh = new PDO("mysql:host=" . getenv('AUCTIONEER_MYSQL_HOST') . ";dbname=" . getenv('AUCTIONEER_MYSQL_DB'), getenv('AUCTIONEER_MYSQL_USER'), getenv('AUCTIONEER_MYSQL_PASSWORD'));
$last_action_timeout = 1; // 5*60*60

$twitter = new Client('https://api.twitter.com/{version}', array(
    'version' => '1.1',
    'ssl.certificate_authority' => 'system',
));
$twitter->addSubscriber(new OauthPlugin(array(
    'consumer_key'    => getenv('AUCTIONEER_TWITTER_CONSUMER_KEY'),
    'consumer_secret' => getenv('AUCTIONEER_TWITTER_CONSUMER_SECRET'),
    'token'           => getenv('AUCTIONEER_TWITTER_ACCESS_TOKEN'),
    'token_secret'    => getenv('AUCTIONEER_TWITTER_ACCESS_TOKEN_SECRET'),
)));
 

foreach($dbh->query("
    SELECT *
    FROM auctions
    JOIN (SELECT max(bids.date) as last_action, auctions.auction_id
    FROM auctions
        JOIN bids
        ON bids.item = auctions.item
        AND bids.date > auctions.start_date
        GROUP BY auctions.auction_id) maxers
    ON maxers.auction_id = auctions.auction_id
    WHERE last_action < NOW() - $last_action_timeout
    AND winner_user_id IS NULL
;") as $row) {
    // print_r($row);
    print_r ($twitter
        ->post("statuses/update.json")
        ->addPostFields(array("status" => "@danfinnie lolcats #$row[item]"))
        ->send());
}
