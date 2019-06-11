<?php

namespace Tsuijie\PHPGrok;

use Exception;

class Grok
{
    protected $patterns = [];

    /**
     * Match subject against given grok pattern.
     * @param $pattern
     * @param $subject
     * @return array
     */
    public function match($pattern, $subject) : array
    {
        $matches = [];
        $named = [];

        // In order for named group to work properly, we need php version >= 7.3.0, see https://github.com/php/php-src/pull/2044
        mb_ereg($this->compile($pattern), $subject, $matches);

        foreach($matches as $key => $item) {
            if (!is_int($key) and ($item !== false)) $named[$key] = $item;
        }

        return $named;
    }

    /**
     * compile given grok pattern to pure named oniguruma pattern.
     * example: %{PATTERN}
     * example with name: %{PATTERN:name}
     * example with value type (which does not serve any purpose here): %{PATTERN:name:int}
     * @param $pattern
     * @return string
     */
    public function compile($pattern) : string
    {
        preg_match_all('/%{(\w+(?::\w+(?::\w+)?)?)}/', $pattern, $matches);
        
        if (empty($matches[1])) return $pattern;

        foreach($matches[1] as $i => $item) {
            $item = explode(':', $item);
            if (empty($item)) continue;
            $key = $item[0];
            if (!isset($this->patterns[$key])) {
                throw new Exception("Failed to compile, pattern name [$key] not exist.");
            }
            $compiled = $this->compile($this->patterns[$key]);
            $name = isset($item[1]) ? $item[1] : null;
            $replace = $name ? "(?<$name>$compiled)" : "($compiled)";
            $pattern = str_replace($matches[0][$i], $replace, $pattern);
        }

        return $pattern;
    }

    /**
     * Note that duplicate patterns will be replaced without warning!
     * @param $name
     * @param $pattern
     */
    public function addPattern($name, $pattern) : void
    {
        $this->patterns[$name] = $pattern;
    }

    /**
     * Load pattern templates from file or directory.
     * Default patterns directory is used when path is set empty, see https://github.com/logstash-plugins/logstash-patterns-core
     * Note that duplicate patterns will be replaced without warning!
     * @param $path
     */
    public function addPatternsFromPath($path = '') : void
    {
        // default patterns directory is used when path is empty.
        if (empty($path)) $path = __DIR__ . '/patterns';

        // Load pattern file from single file, or directory with arbitrary depth.
        if (is_dir($path)) {
            $files = $this->iterateThroughDirectory($path);
        } else if (is_file($path)) {
            $files = [$path];
        } else {
            throw new Exception("Cannot load patterns, target path [$path] not exist.");
        }

        foreach ($files as $path) {
            $handle = fopen($path, "r");
            while (!feof($handle)) {
                $row = trim(fgets($handle));
                // ignore comment line
                if (empty($row) or substr($row, 0, 1) == '#') continue;
                $row = explode(' ', $row, 2);
                if (count($row) != 2) continue;
                $this->addPattern(trim($row[0]), trim($row[1]));
            }
            fclose($handle);
        }
    }

    /**
     * Iterate files from directories.
     * @param $dir
     * @return array
     */
    private function iterateThroughDirectory($dir) : array
    {
        $files = [];

        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file != '..' && $file != '.') {
                    if (is_dir($file)) {
                        $this->iterateThroughDirectory("$dir/$file");
                    } else {
                        $files[] = "$dir/$file";
                    }
                }
            }
        }

        closedir($handle);

        return $files;
    }
}