<?php

namespace Oc;

use Oc\GlobalContext\GlobalContext;
use OcLegacy\Template\LegacyTemplateTrait;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as FrameworkController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractController
 */
abstract class AbstractController extends FrameworkController
{
    use LegacyTemplateTrait;

    /**
     * Fetches the global context from the master request.
     *
     * @return GlobalContext
     */
    public function getGlobalContext()
    {
        $requestStack = $this->get('request_stack');

        $masterRequest = $requestStack->getMasterRequest();

        if ($masterRequest === null) {
            throw new RuntimeException('No master request found.');
        }

        /**
         * @var GlobalContext
         */
        $globalContext = $masterRequest->get('global_context');

        if ($globalContext === null) {
            throw new RuntimeException('Global context not found on master request');
        }

        return $globalContext;
    }

    /**
     * Sets the container.
     *
     * There is no container available in the constructor of a controller, so we override setContainer() and use this
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null.
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        if ($container === null) {
            return;
        }

        $requestStack = $container->get('request_stack');
        /**
         * @var Request
         */
        $masterRequest = $requestStack->getMasterRequest();
        if ($masterRequest) {
            $this->setTarget($masterRequest->getUri());
        }
    }

    /**
     * @param string $message
     */
    protected function addErrorMessage($message)
    {
        $this->addFlash('error', $message);
    }

    /**
     * @param string $message
     */
    protected function addSuccessMessage($message)
    {
        $this->addFlash('success', $message);
    }

    /**
     * @param string $message
     */
    protected function addInfoMessage($message)
    {
        $this->addFlash('info', $message);
    }
}
