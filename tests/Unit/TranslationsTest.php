<?php

namespace Tests\Unit;

use Illuminate\Support\Arr;
use Tests\TestCase;

class TranslationsTest extends TestCase
{
    public function test_arabic_and_english_group_files_define_the_same_keys(): void
    {
        foreach (['auth', 'messages', 'pagination', 'passwords', 'stock', 'validation'] as $group) {
            $en = array_keys(Arr::dot(require lang_path("en/{$group}.php")));
            $ar = array_keys(Arr::dot(require lang_path("ar/{$group}.php")));

            // The attribute and custom maps are locale-specific and may differ.
            $filter = fn (array $keys) => array_values(array_filter(
                $keys,
                fn ($key) => ! preg_match('/^(attributes|custom)(\.|$)/', $key),
            ));

            $this->assertEqualsCanonicalizing($filter($en), $filter($ar), "lang/*/{$group}.php keys differ");
        }
    }

    public function test_arabic_translations_keep_every_placeholder(): void
    {
        $en = Arr::dot(require lang_path('en/messages.php'));
        $ar = Arr::dot(require lang_path('ar/messages.php'));

        foreach ($en as $key => $line) {
            preg_match_all('/:\w+/', $line, $expected);
            preg_match_all('/:\w+/', $ar[$key], $actual);

            $this->assertEqualsCanonicalizing($expected[0], $actual[0], "messages.{$key}");
        }
    }

    public function test_every_ui_string_used_in_views_has_an_arabic_translation(): void
    {
        $json = json_decode(file_get_contents(lang_path('ar.json')), true, flags: JSON_THROW_ON_ERROR);

        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views')));
        $missing = [];

        foreach ($files as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            preg_match_all('/__\(([\'"])(.+?)\1/', file_get_contents($file->getPathname()), $matches);

            foreach ($matches[2] as $key) {
                $key = stripslashes($key);
                if (! str_contains($key, '.') || str_contains($key, ' ')) {
                    if (! array_key_exists($key, $json)) {
                        $missing[] = $key;
                    }
                }
            }
        }

        $this->assertSame([], array_values(array_unique($missing)));
    }

    public function test_message_keys_used_in_code_exist(): void
    {
        $missing = [];

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(app_path())) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            preg_match_all("/__\\('(messages\\.[\\w.]+)'/", file_get_contents($file->getPathname()), $matches);

            foreach ($matches[1] as $key) {
                foreach (['en', 'ar'] as $locale) {
                    if (! trans()->has($key, $locale, false)) {
                        $missing[] = "{$locale}: {$key}";
                    }
                }
            }
        }

        $this->assertSame([], $missing);
    }
}
