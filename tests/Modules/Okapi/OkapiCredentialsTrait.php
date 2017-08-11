<?php

namespace OcTest\Modules\Okapi;

trait OkapiCredentialsTrait
{
    public function getOkapiUrl()
    {
        return getenv('URL') . '/okapi';
    }

    public function getConsumerKey()
    {
        return 'yT2eV9xhwTuHKWVKxdWZ';
    }

    /**
     * @return OkapiClient
     */
    public function createOkapiClient()
    {
        return new OkapiClient($this->getOkapiUrl(), $this->getConsumerKey());
    }
}
