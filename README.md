# php-grok
Logstash Grok compatible log parser for PHP

# Requirements

This simple code is made available by recent fix for php discussed here: https://github.com/php/php-src/pull/2044 .

Thus it only works for php version >= 7.3.0 .

The extension `mb-string` is also required obviously.

# Install

```
composer require tsuijie/php-grok
```

# Example

```
use Tsuijie\PHPGrok\Grok;

$g = new Grok;
$g->addPatternsFromPath();
$array = $g->match('%{COMMONAPACHELOG}', '0.0.0.0 - - [17/May/2015:10:05:03 +0000] "GET /presentations/logstash-monitorama-2013/images/kibana-search.png HTTP/1.1" 200 203023 "http://semicomplete.com/presentations/logstash-monitorama-2013/" "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.77 Safari/537.36"');

print_r($array);
```

# Tests

Run the tests using phpunit:

```
phpunit --configuration phpunit.xml.dist
```

# License

[MIT license](https://opensource.org/licenses/MIT).