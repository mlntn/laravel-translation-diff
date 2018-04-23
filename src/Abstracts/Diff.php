<?php

namespace Mlntn\Translation\Abstracts;

abstract class Diff
{

    protected $languages = [];

    protected $cache;

    public function __construct($languages)
    {
        $this->languages = $languages;
    }

    protected function initDiffArray()
    {
        return array_combine(array_keys($this->languages), array_fill(0, count($this->languages), []));
    }

    protected function getLastInPath($path)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $path);

        return end($parts);
    }

    protected function buildArray($array, $values = [], $indent = '    ')
    {
        $return = "[\n";

        foreach ($array as $k => $v) {
            $return .= "{$indent}'{$k}' => ";

            if (is_array($v)) {
                $return .= $this->buildArray($v, $values[$k] ?? [], $indent . '    ');
            } else {
                $return .= "'";

                if (isset($values[$k])) {
                    $return .= str_replace("'", "\\'", $values[$k]);
                }

                $return .= "',";

                if (isset($values[$k]) === false) {
                    $return .= ' // @todo';
                }

                $return .= "\n";
            }
        }

        return $return . substr($indent, 4) . "],\n";
    }

    protected function writePhpFile($language, $file, $contents)
    {
        $php_contents = "<?php\n\nreturn " . trim($contents, " \n,") . ';';

        file_put_contents(resource_path("lang/{$language}/{$file}"), $php_contents);
    }

}