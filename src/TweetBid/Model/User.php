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
     * @Column(type="string")
     * @var string
     */
    protected $token;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
    
    //TODO: implment this
    public function refresh(\TweetBid\Service\Twitter $twitter)
    {
        //fetch user data from twitter and update meta data
    }
    
    public function setToken($token)
    {
        $this->token = $token;
    }
    
    public function getToken()
    {
        return $this->token;
    }
    
    public function isActive()
    {
        return !empty($this->token);
    }
}