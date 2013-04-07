<?php 
require_once __DIR__ . '/../../bootstrap.php';

use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;

$em = $container['em'];
$user = $em->getRepository('TweetBid\Model\User')->find($data['id_str']);

