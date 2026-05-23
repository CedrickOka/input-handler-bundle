<?php

namespace Oka\InputHandlerBundle\Test\Controller;

use Oka\InputHandlerBundle\Annotation\AccessControl;
use Oka\InputHandlerBundle\Annotation\RequestContent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class AppController extends AbstractController
{
    #[AccessControl(protocol: 'rest', version: 'v1', formats: ['json'])]
    #[RequestContent(constraints: 'itemConstraints')]
    public function __invoke(Request $request, $version, $protocol, array $requestContent): Response
    {
        return $this->json($requestContent);
    }

    private static function itemConstraints(): Assert\Collection
    {
        return new Assert\Collection([
            'email' => new Assert\Required(new Assert\Email()),
            'password' => new Assert\Required(new Assert\NotBlank()),
            'lastName' => new Assert\Optional(new Assert\NotBlank()),
            'firstName' => new Assert\Optional(new Assert\NotBlank()),
        ]);
    }
}
