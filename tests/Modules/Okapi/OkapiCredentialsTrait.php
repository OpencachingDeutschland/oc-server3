<?php

namespace OcTest\Modules\Okapi;

trait OkapiCredentialsTrait
{
    public function getOkapiUrl()
    {
        $url = getenv('OKAPI_LOCAL_URL');
        if (!$url) {
            $url = getenv('URL');
        }
        return $url . '/okapi';
    }

    public function getConsumerKey()
    {
        $consumer_key = getenv('OKAPI_LOCAL_CONSUMER_KEY');
        if (!$consumer_key) {
            $consumer_key = 'yT2eV9xhwTuHKWVKxdWZ';
        }
        return $consumer_key;
    }

    /**
     * @return OkapiClient
     */
    public function createOkapiClient()
    {
        return new OkapiClient($this->getOkapiUrl(), $this->getConsumerKey());
    }
}
