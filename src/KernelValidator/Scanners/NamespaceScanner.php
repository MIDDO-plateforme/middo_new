<?php

namespace App\KernelValidator\Scanners;

use App\KernelValidator\Core\ValidatorInterface;
use App\KernelValidator\Core\ValidationResult;
use App\KernelValidator\Core\ValidationError;
use App\KernelValidator\Core\PathNormalizer;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class NamespaceScanner implements ValidatorInterface
{
    public function __construct(
        private string $srcPath,
        private string $baseNamespace = 'App'
    ) {}

    public function validate(): ValidationResult
    {
        $result = new ValidationResult();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->srcPath)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = PathNormalizer::normalizePath($file->getPathname());

            // Cas spécial Symfony
            if (basename($path) === 'Kernel.php') {
                continue;
            }

            $content = file_get_contents($path);

            if (!preg_match('/namespace\s+([^;]+);/', $content, $match)) {
                continue;
            }

            $namespace = PathNormalizer::normalizeNamespace($match[1]);

            $relative = str_replace(PathNormalizer::normalizePath($this->srcPath) . '/', '', $path);
            $relativeDir = dirname($relative);

            if ($relativeDir === '.' || $relativeDir === '') {
                $expectedNamespace = $this->baseNamespace;
            } else {
                $expectedNamespace = $this->baseNamespace . '\\' . str_replace('/', '\\', $relativeDir);
            }

            $expectedNamespace = PathNormalizer::normalizeNamespace($expectedNamespace);

            if ($namespace !== $expectedNamespace) {
                $result->errors[] = new ValidationError(
                    "Namespace mismatch: \"$namespace\" (found) vs \"$expectedNamespace\" (expected)",
                    $path
                );
            }
        }

        return $result;
    }
}
