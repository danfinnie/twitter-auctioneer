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
    This is what your database should be now:
    ```
    mysql> desc bids;
    +-------------------+--------------+------+-----+---------+----------------+
    | Field             | Type         | Null | Key | Default | Extra          |
    +-------------------+--------------+------+-----+---------+----------------+
    | bid_id            | int(11)      | NO   | PRI | NULL    | auto_increment |
    | price             | decimal(5,2) | YES  |     | NULL    |                |
    | twitter_user_id   | varchar(255) | YES  |     | NULL    |                |
    | date              | datetime     | YES  |     | NULL    |                |
    | auction_id        | int(11)      | YES  |     | NULL    |                |
    | twitter_user_name | varchar(255) | YES  |     | NULL    |                |
    +-------------------+--------------+------+-----+---------+----------------+

    mysql> desc auctions;
    +--------------------+--------------+------+-----+---------+----------------+
    | Field              | Type         | Null | Key | Default | Extra          |
    +--------------------+--------------+------+-----+---------+----------------+
    | auction_id         | int(11)      | NO   | PRI | NULL    | auto_increment |
    | price              | decimal(5,2) | YES  |     | NULL    |                |
    | item               | varchar(255) | YES  |     | NULL    |                |
    | start_date         | datetime     | YES  |     | NULL    |                |
    | end_date           | datetime     | YES  |     | NULL    |                |
    | winner_user_id     | varchar(255) | YES  |     | NULL    |                |
    | seller_user_id     | varchar(255) | YES  |     | NULL    |                |
    | notification_state | int(11)      | YES  |     | 0       |                |
    +--------------------+--------------+------+-----+---------+----------------+
    ```
3. Copy .env.dist to .env and fill it out.
3. Make sure the CURL PHP extension is installed.  Run `composer update`.
4. Run all servers: `foreman start`

Recent Changes
--------------

* prep_env.sh changed to .env (this is for foreman)
* Use Procfile for foreman (not necessary, can also just run the commands)
* Add notices for being outbid, new auction, and winning.
* Create an auction by tweeting `@TweetAuctioneer #sell #something`.

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

* @twitter_auctioneer I'll #bid $50 for the #bike.
* @twitter_auctioneer You selling a #bike?  I'm down to #bid $60.
* @zoey You won the #bike!

Fun New ORM Stuff
-----------------
* `php tools/doctrine.php`: the doctrine CLI tool
* `php tools/doctrine.php orm:validate-schema`: validate the mapping is valid and correct
* `php tools/doctrine.php orm:schema-tool:create`: create the database
* `php tools/doctrine.php orm:schema-tool:update`: update from mapping

Mapping doesn't match current database. Either current database should conform 
to Doctrine defaults, the annotations should be update to reflect the 
non-standard field names, or a DBA should be hired.