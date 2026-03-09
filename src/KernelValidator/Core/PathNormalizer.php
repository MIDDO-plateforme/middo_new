<?php

namespace App\KernelValidator\Core;

class PathNormalizer
{
    public static function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#/+#', '/', $path);
        return rtrim($path, '/');
    }

    public static function normalizeNamespace(string $namespace): string
    {
        $namespace = trim($namespace);
        $namespace = str_replace('/', '\\', $namespace);
        $namespace = preg_replace('#\\\\+#', '\\', $namespace);
        return trim($namespace, '\\');
    }

    public static function normalizeClass(string $class): string
    {
        return trim($class);
    }

    public static function expectedPath(string $srcPath, string $namespace, string $class, string $baseNamespace = 'App'): string
    {
        $srcPath = self::normalizePath($srcPath);
        $namespace = self::normalizeNamespace($namespace);
        $class = self::normalizeClass($class);

        $relative = str_replace($baseNamespace . '\\', '', $namespace);
        $relative = str_replace('\\', '/', $relative);
        $relative = trim($relative, '/');

        return $srcPath . '/' . $relative . '/' . $class . '.php';
    }
}
