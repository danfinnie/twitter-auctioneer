<?php 
require_once __DIR__ . '/../../bootstrap.php';

//crazy simple router
with('/api', function () use ($container){
    //signin/sgnup are the same thing
    respond('POST', '/user', function (_Request $request, _Response $response) use ($container) {
        $config = $container['config']->twitter->toArray();
        $consumer = new Zend_Oauth_Consumer($config);  
        $token = $consumer->getRequestToken();
        
        $response->session('token', $token);
        $response->redirect($consumer->getRedirectUrl());
    });
    
    //twitter's callback
    respond('/twitter', function (_Request $request, _Response $response) use ($container) {
        $config = $container['config']->twitter->toArray();
        $consumer = new Zend_Oauth_Consumer($config);  
        $token = $consumer->getAccessToken(
                 $_GET,
                 $request->session('token')
             );
             
        //we don't actually do anythin with their twitter account other than this
        $client = $token->getHttpClient($config = $container['config']->twitter->toArray());
        $call = $client->setUri('https://api.twitter.com/1.1/account/verify_credentials.json')->request($client::GET);
        if($call->getStatus() != 200){
            $response->code(401);
            return;
        }
        
        $data = Zend_Json::decode($call->getBody());
        
        //check if user already exsists
        $em = $container['em'];
        $user = $em->getRepository('TweetBid\Model\User')->find($data['id_str']);
        if(!$user){
            $user = new TweetBid\Model\User($data['id_str'], $data['screen_name']);
            $em->persist($user);
        } else {
            $user->setName($data['screen_name']);
        }
        
        $em->flush();
        $response->session('user', $user);
        $response->redirect('/');
    });

    respond('GET', '/user', function (_Request $request, _Response $response) use ($container) {
        $user = $request->session('user');
        if(!empty($user)){
            $response->json(array('user' => $user->jsonSerialize()));
        } else {
            $response->code(401);
        }
    });
    
    respond('/payment', function (_Request $request, _Response $response) use ($container) {
        $user = $request->session('user');
        if(empty($user)){
            $response->code(401);
            return;
        }
        
        //does user have an account?
        if(!$user->getAccount()){
            $em = $container['em'];
            //pull user from db
            $user = $em->getRepository('TweetBid\Model\User')->find($user->getId());
            $account = \Balanced\Marketplace::mine()->createBuyer(null, $request->param('card'), null, $user->getName());
            $user->setAccount($account->uri);
            $em->flush();
            $response->session('user', $user);
        } else {
            $account = \Balanced\Account::get($user->getAccount());
            $account->addCard($request->param('card'));
        }
        
        $response->redirect('/api/user');
    });
});

dispatch();
