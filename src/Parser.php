<?php

namespace PoOwAa\BrowserDetect;

use League\Pipeline\Pipeline;
use PoOwAa\BrowserDetect\Contracts\ParserInterface;
use PoOwAa\BrowserDetect\Contracts\ResultInterface;

class Parser implements ParserInterface
{
    protected $runtime;

    protected $agent;

    public function __construct($agent = null)
    {
        $this->agent = is_null($agent) ? (array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown') : $agent;
        $this->runtime = [];
    }

    /**
     * Reflect calls to the result object
     *
     * @throws \PoOwAa\BrowserDetect\Exceptions\BadMethodCallException
     *
     * @param string $method
     * @param array $params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        $result = $this->detect();

        if (method_exists($result, $method)) {
            return call_user_func_array([$result, $method], $params);
        }

        throw new BadMethodCallException(sprintf('%s method does not exists!', $method, ResultInterface::class));
    }

    public function detect()
    {
        return $this->parse($this->agent);
    }

    public function parse($agent)
    {
        if (!isset($this->runtime[$agent])) {
            return $this->process($agent);
        }

        return $this->runtime[$key];
    }

    /**
     * Pipe the payload through the stages.
     *
     * @param  string $agent
     * @return ResultInterface
     */
    protected function process($agent)
    {
        $pipeline = new Pipeline([
            new Stages\UAParser,
            new Stages\MobileDetect,
            new Stages\CrawlerDetect,
            new Stages\DeviceDetector,
            new Stages\BrowserDetect,
        ]);

        return $pipeline->process(new Payload($agent));
    }
}
