<?php
namespace TweetBid\Service;
use Doctrine\ORM\EntityManager;

class Gavel
{
    protected $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    
}