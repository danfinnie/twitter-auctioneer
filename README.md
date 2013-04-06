twitter-auctioneer
==================

lvtech 2013 hackathon project to host auctions with your friends via twitter

Server Setup
------------

1. Install node
2. Install stuff with npm `npm install user-stream mysql@2.0.0-alpha7`
3. Make a MySQL database and make a table in it:
    ```create table bids (bid_id int auto_increment primary key, price decimal(5,2), twitter_user_id varchar(255), item varchar(255), date datetime );
    create table auctions (auction_id int auto_increment primary key, price decimal(5,2), item varchar(255), start_date datetime, end_date datetime, winner_user_id varchar(255), seller_user_id varchar(255) );```
3. Copy prep_env.sh.dist to prep_env.sh and fill it out.  `source prep_env.sh`
3. Make sure the CURL PHP extension is installed.  Run `composer update` in the `gavel-guy` dir.
4. `nodejs server.js`

Database Stuff
--------------

This is what a pending auction looks like in the DB:
```
mysql> insert into auctions (item, start_date, end_date, seller_user_id) values ("bike", NOW(), NOW() + 60*60*2, "1330669213");
Query OK, 1 row affected (0.07 sec)

mysql> select * from auctions;
+------------+-------+------+---------------------+---------------------+----------------+----------------+
| auction_id | price | item | start_date          | end_date            | winner_user_id | seller_user_id |
+------------+-------+------+---------------------+---------------------+----------------+----------------+
|          1 |  NULL | bike | 2013-04-06 07:40:12 | 2013-04-06 16:47:41 | NULL           | 1330669213     |
+------------+-------+------+---------------------+---------------------+----------------+----------------+
1 row in set (0.02 sec)
```

Possible Tweets
---------------

@twitter_auctioneer I'll #bid $50 for the #bike.
@twitter_auctioneer You selling a #bike?  I'm down to #bid $60.
@zoey You won the #bike!
