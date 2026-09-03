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

    /**
     * Converts a title into a lowercase, hyphenated ASCII slug.
     *
     * @param string $title Source title, may contain Cyrillic characters.
     * @return string A non-empty slug; falls back to "n-a" when the title has no alphanumeric content.
     */
    public function generate(string $title): string
    {
        $lower = mb_strtolower($title);
        $transliterated = strtr($lower, self::MAP);
        $ascii = preg_replace('/[^a-z0-9]+/u', '-', $transliterated);
        $trimmed = trim($ascii, '-');

        return $trimmed === '' ? 'n-a' : $trimmed;
    }

    /**
     * Generates a slug for the title, appending a numeric suffix until it no longer collides.
     *
     * @param string $title Source title to slugify.
     * @param callable(string): bool $exists Returns true when the candidate slug is already taken.
     * @return string A slug guaranteed not to satisfy $exists.
     */
    public function unique(string $title, callable $exists): string
    {
        $base = $this->generate($title);
        $slug = $base;
        $suffix = 2; // First collision becomes "-2" (the bare slug counts as the implicit "first" one).

        while ($exists($slug)) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
