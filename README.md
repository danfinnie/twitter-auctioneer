twitter-auctioneer
==================

lvtech 2013 hackathon project to host auctions with your friends via twitter

Setup
-----

1. Install node
2. Install stuff with npm `npm install user-stream mysql@2.0.0-alpha7`
3. Make a MySQL database and make a table in it:
    `create table bids (bid_id int auto_increment primary key, price decimal(5,2), twitter_user_id varchar(255), item varchar(255) );`
3. Copy prep_env.sh.dist to prep_env.sh and fill it out.  `source prep_env.sh`
4. `nodejs server.js`
