<?php

namespace PoOwAa\BrowserDetect\Contracts;

interface ParserInterface
{
    public function detect();

    public function parse($agent);
}
