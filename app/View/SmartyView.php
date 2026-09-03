<?php

namespace App\View;

use Smarty\Exception;
use Smarty\Smarty;

/**
 * Thin wrapper around Smarty that renders templates to a string.
 */
final class SmartyView
{
    private readonly Smarty $smarty;

    /**
     * @param string $templateDir Directory containing .tpl template files.
     * @param string $compileDir Directory for Smarty's compiled templates (created if missing).
     * @param string $cacheDir Directory for Smarty's output cache (created if missing).
     */
    public function __construct(string $templateDir, string $compileDir, string $cacheDir)
    {
        $this->ensureDirectoriesExist($compileDir, $cacheDir);
        $this->smarty = $this->createSmarty($templateDir, $compileDir, $cacheDir);
    }

    /**
     * Renders a template to a string with the given variables assigned.
     *
     * @param string $template Template filename, relative to the template dir.
     * @param array<string, mixed> $data Variables to make available in the template.
     * @throws Exception
     */
    public function render(string $template, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        return $this->smarty->fetch($template);
    }

    /**
     * Creates any of the given directories that do not already exist.
     */
    private function ensureDirectoriesExist(string ...$dirs): void
    {
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }
    }

    /**
     * Builds and configures a Smarty instance with caching disabled.
     */
    private function createSmarty(string $templateDir, string $compileDir, string $cacheDir): Smarty
    {
        $smarty = new Smarty();
        $smarty->setTemplateDir($templateDir);
        $smarty->setCompileDir($compileDir);
        $smarty->setCacheDir($cacheDir);
        $smarty->caching = Smarty::CACHING_OFF;

        return $smarty;
    }
}
