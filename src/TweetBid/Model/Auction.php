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
     * @Column(type="boolean")
     * @var bool
     */
    protected $active = false;
    
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
}