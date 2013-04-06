<?php

$dbh = new PDO("mysql:host=" . getenv('AUCTIONEER_MYSQL_HOST') . ";dbname=" . getenv('AUCTIONEER_MYSQL_DB'), getenv('AUCTIONEER_MYSQL_USER'), getenv('AUCTIONEER_MYSQL_PASSWORD'));

foreach($dbh->query('SELECT * from auctions') as $row) {
    print_r($row);
}
