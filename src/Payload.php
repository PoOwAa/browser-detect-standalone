<?php

namespace PoOwAa\BrowserDetect;

use PoOwAa\BrowserDetect\Contracts\PayloadInterface;

class Payload implements PayloadInterface
{
    private $agent;

    private $store = [];

    public function __construct($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @inheritdoc
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @inheritdoc
     */
    public function getValue($key)
    {
        if (array_key_exists($key, $this->store)) {
            return $this->store[$key];
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function setValue($key, $value)
    {
        if ($value !== null) {
            $this->store[$key] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return array_merge($this->store, [
            'userAgent' => $this->agent,
        ]);
    }
}
