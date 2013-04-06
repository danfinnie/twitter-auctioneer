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
 
$last_bidders_stmt = $dbh->prepare("SELECT twitter_user_name FROM bids WHERE auction_id=? ORDER BY price DESC");
$mark_sent_stmt = $dbh->prepare("UPDATE auctions SET reminders_sent=1 WHERE auction_id=?");

foreach($dbh->query("
    SELECT *
    FROM auctions
    JOIN (SELECT max(bids.date) as last_action, auctions.auction_id
        FROM auctions
        JOIN bids
        ON bids.auction_id = auctions.auction_id
        GROUP BY auctions.auction_id) maxers
    ON maxers.auction_id = auctions.auction_id
    WHERE last_action < NOW() - $last_action_timeout
    AND winner_user_id IS NULL
    AND reminders_sent = 0
;") as $row) {
    $last_bidders_stmt->bindValue(1, $row['auction_id']);
    $last_bidders_stmt->execute();

    // Do this in PHP because doing it in MySQL changes the order.
    $last_bidders_arr = array_slice(array_unique($last_bidders_stmt->fetchAll(PDO::FETCH_COLUMN)), 1, 3);

    if (count($last_bidders_arr) > 0) {
        $last_bidders = '@' . implode(" @", $last_bidders_arr);
        $tweet = "$last_bidders Going once on the #$row[item].  Bid now or forever hold your peace!";

        echo $tweet . "\n";
        
        $twitter
            ->post("statuses/update.json")
            ->addPostFields(array("status" => $tweet))
            ->send();

        $mark_sent_stmt->execute(array($row['auction_id']));
    } else {
        echo "Not enough people to remind about #$row[item], no. $row[auction_id]\n";
    }
}
