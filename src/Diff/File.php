<?php

namespace Mlntn\Translation\Diff;

use Mlntn\Translation\Abstracts\Diff;

class File extends Diff
{

    protected $files = [];

    public function diff()
    {
        $files = $this->initDiffArray();

        foreach ($this->languages as $language => $path) {
            $language_files = glob("{$path}/*");

            foreach ($language_files as $file) {
                $f = $this->getLastInPath($file);

                $files[$language][] = $f;

                $this->files[$f] = $f;
            }
        }

        $this->files = array_values($this->files);

        return $this->cache = array_map(function ($list) {
            return array_values(array_diff($this->files, $list));
        }, $files);
    }

    /**
     * @param Translation[] $masters
     */
    public function fix($masters)
    {
        foreach ($this->cache as $language => $files) {
            foreach ($files as $file) {
                $master = $masters[$file]->getMaster();

                $this->writePhpFile($language, $file, $this->buildArray($master));
            }
        }
    }

    public function getFiles()
    {
        return $this->files;
    }

}