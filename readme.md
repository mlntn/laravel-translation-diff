This library finds missing translations in Laravel language files and can add them across all languages. You will need to complete the translation, but this tool removes the tedious manual comparing of language files across translations.

## Usage

**Compare language files across all translations and show a report of inconsistencies**

    php artisan lang:diff

**Compare language files across all translations and write the corrected files with ```// @todo``` for each translation that needs to be added**

    php artisan lang:diff --fix

