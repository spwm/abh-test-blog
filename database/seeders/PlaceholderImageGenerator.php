<?php

namespace Database\Seeders;

/**
 * Generates a small set of solid-color placeholder JPEGs for seeded posts, reusing files across posts.
 */
final class PlaceholderImageGenerator
{
    private const COLORS = ['2f6fed', 'ed6a5a', '2ea44f', 'f2b134', '8e44ad'];

    /**
     * @param string $outputDir Directory the placeholder images are written to (created if missing).
     */
    public function __construct(private readonly string $outputDir)
    {
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0777, true);
        }
    }

    /**
     * Returns the filename of a placeholder image for $index, generating it on first use.
     *
     * @param int $index Post index; determines which of the reused placeholder files is returned.
     * @return string Filename, relative to the output directory.
     */
    public function generate(int $index): string
    {
        $filename = sprintf('post-placeholder-%d.jpg', $index % 5);
        $path = $this->outputDir . '/' . $filename;

        if (!is_file($path)) {
            $this->render($path, self::COLORS[$index % count(self::COLORS)]);
        }

        return $filename;
    }

    /**
     * Renders a single solid-color placeholder JPEG to $path.
     *
     * @param string $path Absolute file path to write the JPEG to.
     * @param string $hexColor Background color as a 6-digit hex string, no leading "#".
     */
    private function render(string $path, string $hexColor): void
    {
        $width = 800;
        $height = 450;
        $image = imagecreatetruecolor($width, $height);

        [$r, $g, $b] = sscanf($hexColor, '%02x%02x%02x');
        $background = imagecolorallocate($image, $r, $g, $b);
        imagefill($image, 0, 0, $background);

        $white = imagecolorallocate($image, 255, 255, 255);
        $text = 'Blog';
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        imagestring(
            $image,
            $font,
            (int) (($width - $textWidth) / 2),
            (int) (($height - $textHeight) / 2),
            $text,
            $white
        );

        imagejpeg($image, $path, 85);
        imagedestroy($image);
    }
}
