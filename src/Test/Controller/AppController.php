<?php

namespace Oka\InputHandlerBundle\Test\Controller;

use Oka\InputHandlerBundle\Annotation\AccessControl;
use Oka\InputHandlerBundle\Annotation\RequestContent;
use Oka\InputHandlerBundle\Test\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class AppController extends AbstractController
{
    /**
     * @AccessControl(version="v1", protocol="rest", formats="json")
     * @RequestContent(constraints="checkConstraints")
     */
    public function check(Request $request, $version, $protocol, array $requestContent): Response
    {
        return $this->json($requestContent);
    }

    /**
     * @AccessControl(version="v1", protocol="rest", formats="json")
     * @RequestContent(target="Oka\InputHandlerBundle\Test\Model\User", fields_alias={"password": "plainPassword"})
     */
    public function create(Request $request, $version, $protocol, User $user): Response
    {
        $user->password = $user->plainPassword;

        return $this->json($user);
    }

    /**
     * @AccessControl(version="v1", protocol="rest", formats="json")
     * @RequestContent(target="user", fields_alias={"password": "plainPassword"})
     */
    public function update(Request $request, $version, $protocol, User $user): Response
    {
        $user->password = $user->plainPassword;

        return $this->json($user);
    }

    private static function checkConstraints(): Assert\Collection
    {
        return new Assert\Collection([
            'email' => new Assert\Required(new Assert\Email()),
            'password' => new Assert\Required(new Assert\NotBlank()),
            'lastName' => new Assert\Optional(new Assert\NotBlank()),
            'firstName' => new Assert\Optional(new Assert\NotBlank()),
        ]);
    }
}
