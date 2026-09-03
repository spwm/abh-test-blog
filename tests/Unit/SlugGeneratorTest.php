<?php

namespace Tests\Unit;

use App\Support\SlugGenerator;
use PHPUnit\Framework\TestCase;

final class SlugGeneratorTest extends TestCase
{
    public function test_transliterates_cyrillic_title(): void
    {
        $generator = new SlugGenerator();

        $this->assertSame('kak-nauchitsya-php', $generator->generate('Как научиться PHP'));
    }

    public function test_normalizes_spaces_and_special_characters(): void
    {
        $generator = new SlugGenerator();

        $this->assertSame('hello-world', $generator->generate('  Hello,   World!!!  '));
    }

    public function test_empty_title_falls_back_to_placeholder(): void
    {
        $generator = new SlugGenerator();

        $this->assertSame('n-a', $generator->generate('   ---   '));
    }

    public function test_unique_appends_suffix_when_slug_taken(): void
    {
        $generator = new SlugGenerator();
        $taken = ['hello-world', 'hello-world-2'];

        $slug = $generator->unique('Hello World', fn (string $slug) => in_array($slug, $taken, true));

        $this->assertSame('hello-world-3', $slug);
    }
}
