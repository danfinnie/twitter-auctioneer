<?php
namespace TweetBid\Model;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="auctions")
 * @author Tim Lytle <tim@timlytle.net>
 */
class Auction
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    protected $id;
    
    /**
     * @Column(type="integer", nullable=true)
     * @var int
     */
    protected $amount;

    /**
     * @Column(type="string")
     * @var int
     */
    protected $item;
    
    /**
     * @Column(type="datetime", nullable=true)
     * @var int
     */
    protected $start;

    /**
     * @Column(type="datetime", nullable=true)
     * @var int
     */
    protected $end;

    /**
     * @ManyToOne(targetEntity="TweetBid\Model\User")
     **/
    protected $winner;

    /**
     * @ManyToOne(targetEntity="TweetBid\Model\User")
     **/
    protected $seller;
    
    /**
     * @OneToMany(targetEntity="TweetBid\Model\Bid", mappedBy="auction")
     * @var Doctrine\Common\Collections\ArrayCollection
     **/    
    protected $bids = array();

    /**
     * @Column(type="integer")
     * @var int
     */
    protected $state = false;
    
    public function __construct($item)
    {
        $this->item = $item;
        $this->bids = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getBids()
    {
        return $this->bids->toArray();
    }

    public function isNewAuction() {
        return $this->state === 0;
    }

    public function isAnnouncedAuction() {
        return $this->state === 1;
    }

    public function isContestedAuction() {
        return $this->state === 2;
    }

    public function isWonAuction() {
        return $this->state === 3;
    }

    public function isFinalizedAuction() {
        return $this->state === 4;
    }

    public function advanceState() {
        $this->state++;
    }

    public function getLastBid() {
        $lastBid = null;
        foreach ($this->getBids() as $bid) {
            if (!$lastBid || $bid->getTimestamp() > $lastBid->getTimestamp())
                $lastBid = $bid;
        }
        return $lastBid;
    }

    public function getBidders() {
        $bidders = array();

        foreach ($this->getBids() as $bid) {
            $bidder = $bid->getUser();

            if (!in_array($bidder, $bidders))
                $bidders[] = $bidder;
        }
    }
    
    //TODO: could do the fair warn validation here
    public function close()
    {
        $this->end = new \DateTime();
        $this->state = 4;
    }

    public function getItem() {
        return $this->item;
    }

    public function getSeller() {
        return $this->seller;
    }
}
