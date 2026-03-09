<?php

namespace App\KernelValidator\Scanners;

use App\KernelValidator\Core\ValidatorInterface;
use App\KernelValidator\Core\ValidationResult;
use App\KernelValidator\Core\ValidationError;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DoctrineEntityScanner implements ValidatorInterface
{
    public function __construct(private string $srcPath) {}

    public function validate(): ValidationResult
    {
        $result = new ValidationResult();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->srcPath)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') continue;

            $path = $file->getPathname();
            $content = file_get_contents($path);

            if (!str_contains($content, '#[ORM\Entity')) continue;

            if (!preg_match('/namespace\s+([^;]+);/', $content, $ns)) continue;
            if (!preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $cl)) continue;

            $fqcn = $ns[1] . '\\' . $cl[1];

            if (!class_exists($fqcn)) {
                $result->errors[] = new ValidationError(
                    "Entité Doctrine non autoloadable : $fqcn",
                    $path
                );
            }
        }

        return $result;
    }
}
