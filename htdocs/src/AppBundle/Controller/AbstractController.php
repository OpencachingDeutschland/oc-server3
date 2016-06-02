<?php

namespace AppBundle\Controller;

use AppBundle\Legacy\Traits\LegacyTemplateTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AbstractController extends Controller
{
    use LegacyTemplateTrait;

    /**
     * Sets the container.
     *
     * There is no container available in the constructor of a controller, so we override setContainer() and use this
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @return void
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $requestStack = $container->get('request_stack');
        /** @var \Symfony\Component\HttpFoundation\Request $masterRequest */
        $masterRequest = $requestStack->getMasterRequest();
        if ($masterRequest) {
            $this->setTarget($masterRequest->getUri());
        }
    }
}
