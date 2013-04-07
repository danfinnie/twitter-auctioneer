<?php
namespace TweetBid\Model;

/**
 * @Entity
 * @Table(name="users")
 * @author Tim Lytle <tim@timlytle.net>
 */
class User
{
    /**
     * @Id @Column(type="string")
     * @var string
     */
    protected $id;
    
    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;
    
    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    protected $account;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }   
    
    public function getName()
    {
        return $this->name;
    }
     
    public function setAccount($account)
    {
        $this->account = $account;
    }
    
    public function getAccount()
    {
        return $this->account;
    }
    
    public function isActive()
    {
        return !empty($this->token);
    }
    
    public function jsonSerialize()
    {
        $return =  array(
        	'id' => $this->id,
            'name' => $this->name,
            'account' => $this->account
        );
        
        
        
        return $return;
    }
}