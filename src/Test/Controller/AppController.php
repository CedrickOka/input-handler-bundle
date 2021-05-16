<?php

namespace Oka\InputHandlerBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Oka\InputHandlerBundle\Annotation\AccessControl;
use Oka\InputHandlerBundle\Annotation\RequestContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 *
 */
class AppController extends AbstractController
{
    /**
     * @AccessControl(version="v1", protocol="rest", formats="json")
     * @RequestContent(constraints="indexConstraints", formats="json")
     */
    public function __invoke(Request $request, $version, $protocol, array $requestContent): Response
    {
        return new Response(json_encode($requestContent), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
    
    private static function indexConstraints(): Assert\Collection
    {
        return new Assert\Collection([
            'email' => new Assert\Required(new Assert\Email()),
            'password' => new Assert\Required(new Assert\NotBlank()),
            'lastName' => new Assert\Optional(new Assert\NotBlank()),
            'firstName' => new Assert\Optional(new Assert\NotBlank())
        ]);
    }
}
