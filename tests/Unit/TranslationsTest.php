<?php

use Illuminate\Support\Arr;

/**
 * Keys of a translation group file, ignoring the locale-specific
 * "attributes" and "custom" maps of the validation file.
 *
 * @return list<string>
 */
function translationKeys(string $locale, string $group): array
{
    return collect(Arr::dot(require lang_path("{$locale}/{$group}.php")))
        ->keys()
        ->reject(fn (string $key) => preg_match('/^(attributes|custom)(\.|$)/', $key))
        ->values()
        ->all();
}

it('defines the same :dataset keys in English and Arabic', function (string $group) {
    expect(translationKeys('ar', $group))->toEqualCanonicalizing(translationKeys('en', $group));
})->with(['auth', 'messages', 'pagination', 'passwords', 'stock', 'validation']);

it('keeps every placeholder in the Arabic messages', function () {
    $english = Arr::dot(require lang_path('en/messages.php'));
    $arabic = Arr::dot(require lang_path('ar/messages.php'));

    foreach ($english as $key => $line) {
        preg_match_all('/:\w+/', $line, $expected);
        preg_match_all('/:\w+/', $arabic[$key], $actual);

        expect($actual[0])->toEqualCanonicalizing($expected[0], "messages.{$key}");
    }
});

it('has an Arabic translation for every interface string used in views', function () {
    $arabic = json_decode(file_get_contents(lang_path('ar.json')), true, flags: JSON_THROW_ON_ERROR);
    $missing = [];

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('views')));

    foreach ($files as $file) {
        if (! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }

        preg_match_all('/__\(([\'"])(.+?)\1/', file_get_contents($file->getPathname()), $matches);

        foreach ($matches[2] as $key) {
            $key = stripslashes($key);
            $isGroupKey = str_contains($key, '.') && ! str_contains($key, ' ');

            if (! $isGroupKey && ! array_key_exists($key, $arabic)) {
                $missing[] = $key;
            }
        }
    }

    expect(array_values(array_unique($missing)))->toBe([]);
});

it('defines every message key used in the code in both locales', function () {
    $missing = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(app_path())) as $file) {
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

    expect($missing)->toBe([]);
});
