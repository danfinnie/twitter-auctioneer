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
     * @var int
     */
    protected $price;

    /**
     * @Column(type="datetime")
     * @var int
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
    
    public function __construct(Auction $auction, User $user, $price, DateTime $timestamp)
    {
        $this->auction = $auction;
        $this->user = $user;
        $this->price = $price;
        $this->timestamp = $timestamp;
    }
}