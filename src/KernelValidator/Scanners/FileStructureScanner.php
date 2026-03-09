<?php

namespace App\KernelValidator\Scanners;

use App\KernelValidator\Core\ValidatorInterface;
use App\KernelValidator\Core\ValidationResult;
use App\KernelValidator\Core\ValidationError;
use App\KernelValidator\Core\PathNormalizer;

class FileStructureScanner implements ValidatorInterface
{
    public function __construct(
        private string $srcPath,
        private string $baseNamespace = 'App'
    ) {}

    public function validate(): ValidationResult
    {
        $result = new ValidationResult();

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->srcPath)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') continue;

            $path = PathNormalizer::normalizePath($file->getPathname());

            // Cas spécial Symfony : Kernel.php est toujours à la racine
            if (basename($path) === 'Kernel.php') continue;

            $content = file_get_contents($path);

            if (!preg_match('/namespace\s+([^;]+);/', $content, $ns)) continue;
            if (!preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $cl)) continue;

            $namespace = PathNormalizer::normalizeNamespace($ns[1]);
            $class = PathNormalizer::normalizeClass($cl[1]);

            $expected = PathNormalizer::expectedPath(
                $this->srcPath,
                $namespace,
                $class,
                $this->baseNamespace
            );

            $expected = PathNormalizer::normalizePath($expected);

            if (realpath($path) !== realpath($expected)) {
                $result->warnings[] = new ValidationError(
                    "Chemin non conforme PSR-4 pour $namespace\\$class",
                    $path
                );
            }
        }

        return $result;
    }
}
