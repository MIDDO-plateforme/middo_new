<?php

namespace App\KernelValidator\Scanners;

use App\KernelValidator\Core\ValidatorInterface;
use App\KernelValidator\Core\ValidationResult;
use App\KernelValidator\Core\ValidationError;
use App\KernelValidator\Core\PathNormalizer;

class ServiceScanner implements ValidatorInterface
{
    public function __construct(
        private string $srcPath,
        private string $servicesPath = 'config/services.yaml'
    ) {}

    public function validate(): ValidationResult
    {
        $result = new ValidationResult();

        $servicesFile = PathNormalizer::normalizePath($this->servicesPath);

        if (!file_exists($servicesFile) || is_dir($servicesFile)) {
            $result->warnings[] = new ValidationError(
                "services.yaml introuvable ou n'est pas un fichier",
                $servicesFile
            );
            return $result;
        }

        $yaml = file_get_contents($servicesFile);

        // REGEX PROPRE, SANS RETOUR À LA LIGNE
        preg_match_all('/App\\\

\[A-Za-z0-9_\\\\]

+/', $yaml, $matches);

        $declaredServices = array_unique($matches[0]);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->srcPath)
        );

        $foundClasses = [];

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') continue;

            $path = PathNormalizer::normalizePath($file->getPathname());
            $content = file_get_contents($path);

            if (!preg_match('/namespace\s+([^;]+);/', $content, $ns)) continue;
            if (!preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $cl)) continue;

            $namespace = PathNormalizer::normalizeNamespace($ns[1]);
            $class = PathNormalizer::normalizeClass($cl[1]);

            $fqcn = $namespace . '\\' . $class;
            $foundClasses[] = $fqcn;
        }

        foreach ($declaredServices as $service) {
            if (!in_array($service, $foundClasses)) {
                $result->warnings[] = new ValidationError(
                    "Service déclaré mais classe introuvable : $service",
                    $servicesFile
                );
            }
        }

        return $result;
    }
}
