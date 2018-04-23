<?php

namespace Mlntn\Translation\Commands;

use Mlntn\Translation\Diff\File;
use Mlntn\Translation\Diff\Translation;
use Illuminate\Console\Command;

class CheckTranslations extends Command
{

    protected $signature = 'lang:diff {--fix}';

    protected $description = 'Ensure proper translation coverage';

    protected $languages = [];

    protected $files = [];

    public function handle()
    {
        $languages = $this->getLanguages();

        $file  = new File($languages);
        $files = $file->diff();

        $translations = [];

        foreach ($file->getFiles() as $f) {
            $translation[$f]  = new Translation($languages, $f);
            $translations[$f] = $translation[$f]->diff();
        }

        if ($this->option('fix')) {
            $file->fix($translation);

            foreach ($translation as $t) {
                $t->fix();
            }
        } else {
            $missing_files = [];

            foreach ($files as $language => $file) {
                foreach ($file as $f) {
                    $missing_files[] = [$language, $f];
                }
            }

            if (empty($missing_files)) {
                $this->output->success('No missing files');
            } else {
                $this->output->table(['Language', 'Missing file'], $missing_files);
            }

            $missing_translations = [];

            foreach ($translations as $file => $translation) {
                foreach ($translation as $language => $missing) {
                    foreach ($missing as $m) {
                        $missing_translations[] = [$file, $language, $m];
                    }
                }
            }

            if (empty($missing_translations)) {
                $this->output->success('No missing translations');
            } else {
                $this->output->table(['File', 'Language', 'Missing translation'], $missing_translations);
            }
        }
    }

    protected function getLangPath($append)
    {
        return resource_path('lang' . ($append ? DIRECTORY_SEPARATOR . $append : ''));
    }

    protected function getLanguages()
    {
        return collect(glob($this->getLangPath('*'), GLOB_ONLYDIR))
            ->map(function ($v) {
                return [
                    'lang' => $this->getLastInPath($v),
                    'path' => $v,
                ];
            })
            ->pluck('path', 'lang')
            ->toArray();
    }

    protected function getLastInPath($path)
    {
        $parts = explode(DIRECTORY_SEPARATOR, $path);

        return end($parts);
    }

}
