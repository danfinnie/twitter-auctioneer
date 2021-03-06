<?php
namespace TweetBid\Model;

/**
 * @Entity
 * @Table(name="bids")
 * @author Tim Lytle <tim@timlytle.net>
 */
class Bid
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    protected $id;
    
    /**
     * @Column(type="integer")
     * @var \DateTime
     */
    protected $amount;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    protected $timestamp;

    /**
     * @ManyToOne(targetEntity="TweetBid\Model\Auction")
     **/
    protected $auction;

    /**
     * @ManyToOne(targetEntity="TweetBid\Model\User")
     **/    
    protected $user;
    
    public function __construct(Auction $auction, User $user, $amount, \DateTime $timestamp)
    {
        $this->auction = $auction;
        $this->user = $user;
        $this->amount = $amount;
        $this->timestamp = $timestamp;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getAmount()
    {
        return $this->amount;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getTimestamp()
    {
        return clone $this->timestamp;
    }
    
    public function getAuction()
    {
        return $this->auction;
    }
}
