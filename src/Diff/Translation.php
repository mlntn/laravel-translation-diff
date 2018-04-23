<?php

namespace Mlntn\Translation\Diff;

use Mlntn\Translation\Abstracts\Diff;

class Translation extends Diff
{

    protected $file;

    protected $master = [];

    protected $original = [];

    public function __construct($languages, $file)
    {
        $this->file = $file;

        parent::__construct($languages);
    }

    public function diff()
    {
        $translations = $this->initDiffArray();

        foreach ($this->languages as $language => $path) {
            if (file_exists("{$path}/{$this->file}") === false) {
                unset($translations[$language]);
                continue;
            }

            $contents = include("{$path}/{$this->file}");

            $this->original[$language] = $contents;

            $translation = $this->getArrayKeys($contents);

            $translations[$language] = $translation;

            $this->master = $this->mergeArrays($this->master, $translation);
        }

        return $this->cache = array_filter(array_map(function ($list) {
            return array_values($this->diffArrayKeys($this->master, $list));
        }, $translations));
    }

    public function fix()
    {
        foreach ($this->cache as $language => $missing) {
            $this->writePhpFile($language, $this->file, $this->buildArray($this->master, $this->original[$language]));
        }
    }

    protected function diffArrayKeys($array1, $array2)
    {
        $diff = [];

        foreach ($array1 as $key => $value) {
            if (isset($array2[$key]) === false) {
                $diff[$key] = $key;
            } else {
                if (is_array($value)) {
                    $array_diff = $this->diffArrayKeys($array1[$key], $array2[$key]);

                    if (empty($array_diff) === false) {
                        $diff[$key] = $array_diff;
                    }
                }
            }
        }

        return $diff;
    }

    protected function mergeArrays($array1, $array2)
    {
        foreach ($array2 as $key => $value) {
            $array1[$key] = $value;

            if (isset($array1[$key]) && is_array($value)) {
                $array1[$key] = $this->mergeArrays($array1[$key], $array2[$key]);
            }
        }

        return $array1;
    }

    protected function getArrayKeys($array)
    {
        $return = [];

        foreach ($array as $key => $value) {
            $return[$key] = is_array($value) ? $this->getArrayKeys($value) : $key;
        }

        return $return;
    }

    public function getMaster()
    {
        return $this->master;
    }

}