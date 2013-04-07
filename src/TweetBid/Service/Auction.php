<?php
namespace TweetBid\Service;
use Doctrine\ORM\EntityManager;

class Auction
{
    protected $fair = 30;
    
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * Try to close auction.
     */
    public function closeAuction(\TweetBid\Model\Auction $auction)
    {
        //ensure high bid was further than fair warning
        
    }
    
    public function getHighBid(\TweetBid\Model\Auction $auction)
    {
        //horrible way to do this, should be DQL
        $high = null;
        foreach($auction->getBids() as $bid){
            if(empty($high)){
                $high = $bid;
                continue;
            }
            
            if($bid->getAmount() > $high->getAmount()){
                $high = $bid;
                continue;
            }
            
            if(($bid->getAmount() == $high->getAmount()) AND ($bid->getTimestamp() < $high->getTimestamp())){
                $high = $bid;
                continue;
            }
        }
        
        return $high;
    }
    
    
}