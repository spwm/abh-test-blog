<?php

namespace App\Support;

/**
 * Generates URL-friendly slugs from titles, transliterating Cyrillic to Latin.
 */
final class SlugGenerator
{
    private const MAP = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
        'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '',
        'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    ];

    public function generate(string $title): string
    {
        $lower = mb_strtolower($title);
        $transliterated = strtr($lower, self::MAP);
        $ascii = preg_replace('/[^a-z0-9]+/u', '-', $transliterated);
        $trimmed = trim($ascii, '-');

        return $trimmed === '' ? 'n-a' : $trimmed;
    }

    /**
     * @param callable(string): bool $exists
     */
    public function unique(string $title, callable $exists): string
    {
        $base = $this->generate($title);
        $slug = $base;
        $suffix = 2;

        while ($exists($slug)) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
