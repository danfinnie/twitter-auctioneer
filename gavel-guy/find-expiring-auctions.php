<?php

$dbh = new PDO("mysql:host=" . getenv('AUCTIONEER_MYSQL_HOST') . ";dbname=" . getenv('AUCTIONEER_MYSQL_DB'), getenv('AUCTIONEER_MYSQL_USER'), getenv('AUCTIONEER_MYSQL_PASSWORD'));
$last_action_timeout = 1; // 5*60*60

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
    ;
") as $row) {
    print_r($row);
}
