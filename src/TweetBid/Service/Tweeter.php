<?php
namespace TweetBid\Service;

use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;

class Tweeter
{
    private $twitter;
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
        $config = $container["config"];

        $this->twitter = new Client('https://api.twitter.com/{version}', array(
            'version' => '1.1',
            'ssl.certificate_authority' => 'system',
        ));

        $this->twitter->addSubscriber(new OauthPlugin(array(
            'consumer_key'    => $config->twitter->consumerKey,
            'consumer_secret' => $config->twitter->consumerSecret,
            'token'           => $config->gavel->token,
            'token_secret'    => $config->gavel->secret,
        )));
    }

    public function tweet($tweet) {
        $response = $this
            ->twitter
            ->post("statuses/update.json")
            ->addPostFields(array("status" => $tweet))
            ->send();

        return $response->isSuccessful();
    }
}
