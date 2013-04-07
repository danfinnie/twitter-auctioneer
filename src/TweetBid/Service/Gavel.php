<?php
namespace TweetBid\Service;
use Doctrine\ORM\EntityManager;

class Gavel
{
    protected $em;
    protected $tweeter;
    protected $auctionTimeout;
    
    public function __construct($container)
    {
        $this->em = $container['em'];
        $this->tweeter = $container['tweeter'];
        $this->auctionTimeout = new \DateInterval('PT'.$container['config']->gavel->auctionTimeout.'S');
    }
    
    public function gavel() {
        $auctionRepo = $this->em->getRepository('TweetBid\Model\Auction');
        foreach($auctionRepo->findAll() as $auction) {
            if ($auction->isNewAuction())
                $this->handleNewAuction($auction);
            elseif ($auction->isAnnouncedAuction())
                $this->handleAnnouncedAuction($auction);
            elseif ($auction->isContestedAuction())
                $this->handleContestedAuction($auction);
            elseif ($auction->isWonAuction())
                $this->handleWonAuction($auction);

            $this->em->flush(); // In case we timeout during script execution.
        }
    }

    private function handleNewAuction($auction) {
        $item = $auction->getItem();
        $seller = $auction->getSeller()->getName();

        $auction->advanceState();
        $this->tweeter->tweet("We have a #$item from @" . "$seller for sale!");
    }

    private function handleAnnouncedAuction($auction) {
        $lastBid = $auction->getLastBid();
        //no one bid
        if(!$lastBid){
            return;
        }
        
        $auction->advanceState();
        $this->tweeter->tweet("Now @" . $lastBid->getUser()->getName() . " is the high bidder for #" . $auction->getItem() . " at $" . $lastBid->getAmount()/100);
    }

    private function handleContestedAuction($auction) {
        $lastBid = $auction->getLastBid();
        
        if($lastBid->getTimestamp()->add($this->auctionTimeout)->add($this->auctionTimeout) < new \DateTime()){
            //close that puppy and tweet out the winner
            $auction->close();
            
            //also, charge the sucker
            $account = \Balanced\Account::get($lastBid->getUser()->getAccount());
            $account->debit($lastBid->getAmount());
            
            $this->tweeter->tweet("Sold! @" . $lastBid->getUser()->getName() . " won #" . $auction->getItem());
            return;
        }
        
        if ($lastBid->getTimestamp()->add($this->auctionTimeout) < new \DateTime()) {
            $bidders = $auction->getBidders();
            
            //$last_bidder_idx = array_search($lastBid->getUser(), $bidders);
            //unset($bidders[$last_bidder_idx]);
            $tweet_str = "Going once #" . $auction->getItem();
            foreach($bidders as $bidder){
                if($bidder == $lastBid->getUser()){
                    continue;
                }
                
                if(strlen($tweet_str) + 2 + strlen($bidder->getName())){
                    continue;
                }
                
                $tweet_str .= ' @' . $bidder->getName();
            }
            $this->tweeter->tweet($tweet_str);
        }        
    }

    private function handleWonAuction($auction) {

    }

    private function bidIsPastTimeout($bid) {
       return $bid->getTimestamp()->add($this->auctionTimeout) < new \DateTime();
    }
}
