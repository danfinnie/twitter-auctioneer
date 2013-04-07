<?php
namespace TweetBid\Model;

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
    protected $price;

    /**
     * @Column(type="string")
     * @var int
     */
    protected $item;
    
    /**
     * @Column(type="datetime")
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
     * @Column(type="boolean")
     * @var bool
     */
    protected $active = false;
    
    public function __construct(Seller $seller, $item)
    {
        $this->seller = $seller;
        $this->idem = $item;
    }
}