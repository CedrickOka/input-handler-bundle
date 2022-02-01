<?php

namespace Oka\InputHandlerBundle\Test\Model;

use Symfony\Component\Validator\Constraints as Assert;

class User
{
    /**
     * @Assert\Email()
     *
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $plainPassword;
}
