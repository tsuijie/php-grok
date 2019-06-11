<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tsuijie\PHPGrok\Grok;
use Exception;

class GrokTest extends TestCase
{

    public function testCompile()
    {
        $g = new Grok;
        $g->addPatternsFromPath();
        $pattern = $g->compile('%{COMMONAPACHELOG}');

        $this->assertEquals('(((?<clientip>(?:((?:(((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?)|((?<![0-9])(?:(?:[0-1]?[0-9]{1,2}|2[0-4][0-9]|25[0-5])[.](?:[0-1]?[0-9]{1,2}|2[0-4][0-9]|25[0-5])[.](?:[0-1]?[0-9]{1,2}|2[0-4][0-9]|25[0-5])[.](?:[0-1]?[0-9]{1,2}|2[0-4][0-9]|25[0-5]))(?![0-9]))))|(\b(?:[0-9A-Za-z][0-9A-Za-z-]{0,62})(?:\.(?:[0-9A-Za-z][0-9A-Za-z-]{0,62}))*(\.?|\b)))) (?<ident>(([a-zA-Z][a-zA-Z0-9_.+-=:]+)@(\b(?:[0-9A-Za-z][0-9A-Za-z-]{0,62})(?:\.(?:[0-9A-Za-z][0-9A-Za-z-]{0,62}))*(\.?|\b)))|(([a-zA-Z0-9._-]+))) (?<auth>(([a-zA-Z][a-zA-Z0-9_.+-=:]+)@(\b(?:[0-9A-Za-z][0-9A-Za-z-]{0,62})(?:\.(?:[0-9A-Za-z][0-9A-Za-z-]{0,62}))*(\.?|\b)))|(([a-zA-Z0-9._-]+))) \[(?<timestamp>((?:(?:0[1-9])|(?:[12][0-9])|(?:3[01])|[1-9]))/(\b(?:[Jj]an(?:uary|uar)?|[Ff]eb(?:ruary|ruar)?|[Mm](?:a|ä)?r(?:ch|z)?|[Aa]pr(?:il)?|[Mm]a(?:y|i)?|[Jj]un(?:e|i)?|[Jj]ul(?:y)?|[Aa]ug(?:ust)?|[Ss]ep(?:tember)?|[Oo](?:c|k)?t(?:ober)?|[Nn]ov(?:ember)?|[Dd]e(?:c|z)(?:ember)?)\b)/((?>\d\d){1,2}):((?!<[0-9])((?:2[0123]|[01]?[0-9])):((?:[0-5][0-9]))(?::((?:(?:[0-5]?[0-9]|60)(?:[:.,][0-9]+)?)))(?![0-9])) ((?:[+-]?(?:[0-9]+))))\] "(?:(?<verb>\b\w+\b) (?<request>\S+)(?: HTTP/(?<httpversion>(?:((?<![0-9.+-])(?>[+-]?(?:(?:[0-9]+(?:\.[0-9]+)?)|(?:\.[0-9]+)))))))?|(?<rawrequest>.*?))" (?<response>(?:((?<![0-9.+-])(?>[+-]?(?:(?:[0-9]+(?:\.[0-9]+)?)|(?:\.[0-9]+)))))) (?:(?<bytes>(?:((?<![0-9.+-])(?>[+-]?(?:(?:[0-9]+(?:\.[0-9]+)?)|(?:\.[0-9]+))))))|-)))', $pattern);
    }

    public function testMatch()
    {
        $g = new Grok;
        $g->addPatternsFromPath();
        $array = $g->match('%{COMMONAPACHELOG}', '83.149.9.216 - - [17/May/2015:10:05:03 +0000] "GET /presentations/logstash-monitorama-2013/images/kibana-search.png HTTP/1.1" 200 203023 "http://semicomplete.com/presentations/logstash-monitorama-2013/" "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.77 Safari/537.36"');

        $this->assertEquals('GET', $array['verb']);
    }

    public function testInvalidPatternName()
    {
        $this->expectExceptionMessage('Failed to compile, pattern name [FOO] not exist.');

        $g = new Grok;
        $g->addPatternsFromPath();
        $pattern = $g->compile('%{FOO}');
    }

    public function testInvalidMatch()
    {
        $g = new Grok;
        $g->addPatternsFromPath();
        $array = $g->match('%{COMMONAPACHELOG}', 'foobar');

        $this->assertEquals(0, count($array));
    }

    public function testLoadInvalidDirectory()
    {
        $this->expectExceptionMessage('Cannot load patterns, target path [/foo/bar] not exist.');

        $g = new Grok;
        $g->addPatternsFromPath('/foo/bar');
    }
}