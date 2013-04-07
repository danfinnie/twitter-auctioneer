<?php
use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;

require_once __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('UTC');

$container = new Pimple();

$container['config'] = $container->share(function($c)
{
    //TODO: set application enviroment someplace
    $config = new Zend_Config_Ini(__DIR__ . '/config.ini', 'production'); 
    return $config;
});

$container['em'] = $container->share(function($c){
    //don't do this
    $cache = new \Doctrine\Common\Cache\ArrayCache;
    
    $config = new Configuration;
    $config->setMetadataCacheImpl($cache);
    $driverImpl = $config->newDefaultAnnotationDriver(realpath(__DIR__ . '/src/TweetBid/Model'));
    $config->setMetadataDriverImpl($driverImpl);
    $config->setQueryCacheImpl($cache);
    $config->setProxyDir('/tmp'); //don't do this either
    $config->setProxyNamespace('TweetBid\Proxies');    
    
    //yeah, not exactily optimized
    $config->setAutoGenerateProxyClasses(true);

    $connectionOptions = $c['config']->db->toArray();
    
    $em = EntityManager::create($connectionOptions, $config);    
    return $em;
});

$container["tweeter"] = new TweetBid\Service\Tweeter($container);

$container["gavel"] = new TweetBid\Service\Gavel($container);

// Is this used?
$container['session'] = $container->share(function ($c){
    $session = new Zend_Session_Namespace('tweetbid');
    return $session;
});

//setup balanced
Httpful\Bootstrap::init();
RESTful\Bootstrap::init();
Balanced\Bootstrap::init();

Balanced\Settings::$api_key = $container['config']->balanced->key;
