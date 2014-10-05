<?php

namespace Townspot\Authentication\Adapter;

class ForceLogin implements \Zend\Authentication\Adapter\AdapterInterface
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @param $user
     */
    function __construct($user)
    {
        $this->user = $user;
    }

    public function authenticate()
    {
        return new \Zend\Authentication\Result(
            \Zend\Authentication\Result::SUCCESS,
            $this->user->getId()
        );
    }
}