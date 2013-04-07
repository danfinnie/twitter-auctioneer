<?php
namespace TweetBid\Service;
use Doctrine\ORM\EntityManager;

class Auction
{
    protected $fair = 'PT30S';
    
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->fair = new \DateInterval($this->fair);
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
    
    //it's assumed this will be called at a consistant inerval, perhaps based
    //on auction activity (but for now, something like 15 seconds)
    public function manageAuction(\TweetBid\Model\Auction $auction)
    {
        //has something new and intersting happened?
        if($auction->isActive()){
            //announce the current high bid, mention the last few bidders
            
            $auction->isActive(false);
            $this->em->flush();
        }
        
        //has it started? (kind of a hack for now)
        if(!$this->getHighBid($auction)){
            return; //nothin to do
        }
        
        //should we close this (gave fair warning, and waited)?
        if($this->getHighBid($auction)->getTimestamp()->add($this->fair)->add($this->fair) < new \DateTime()){
            //close that puppy and tweet out the winner
            $auction->close();

            //also, charge the sucker
            $bid = $this->getHighBid($auction);
            $account = \Balanced\Account::get($bid->getUser()->getAccount());
            $account->debit($bid->getAmount());
            
            $this->em->flush();
        }
        
        //should we warn people ?
        if($this->getHighBid($auction)->getTimestamp()->add($this->fair) < new \DateTime()){
            //tweet out a fair warning
        }
    }
}