<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tsuijie\PHPGrok\Grok;

class GrokTest extends TestCase
{
    
    public function testEcho()
    {
        $grok = new Grok;
        $grok->addPatternsFromPath();
        $this->assertEquals('ok', 'ok');
    }
}