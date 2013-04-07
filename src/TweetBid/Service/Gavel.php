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

            $em->flush(); // In case we timeout during script execution.
        }
    }

    private function handleNewAuction($auction) {
        $item = $auction->getItem();
        $seller = $auction->getSeller()->getName();

        $auction->advanceState();
        $this->tweeter->tweet("We have a #$item from @$seller for sale!");
    }

    private function handleAnnouncedAuction($auction) {
        $lastBid = $auction->getLastBid();
        if ($this->bidIsPastTimeout($lastBid)) {
            $bidders = $auction->getBidders();
            $this->tweeter->tweet
        }
    }


    private function handleContestedAuction($auction) {

    }

    private function handleWonAuction($auction) {

    }

    private function bidIsPastTimeout($bid) {
       return $bid->getTimestamp->diff(new \DateTime()) > $this->auctionTimeout();
    }
}
