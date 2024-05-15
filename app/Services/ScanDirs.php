<?php

declare(strict_types=1);

namespace App\Services;

final class ScanDirs
{
    public function list_dir(string $path = './', array $ignore_names = []): object
    {
        $paths = ['dirs' => [], 'files' => []];
        $ignore = config('gpg')['files_for_ignore'];
        if (!empty($ignore_names)) {
            $ignore = array_merge($ignore, $ignore_names);
        }

        foreach (scandir($path) as $value) {
            if (!in_array($value, $ignore, true)) {
                $fPath = $path . DIRECTORY_SEPARATOR . $value;

                if (is_dir($fPath)) {
                    $subPaths = $this->list_dir($fPath, $ignore); // Рекурсивный вызов
                    $paths['dirs'][] = $fPath;
                    $paths['dirs'] = array_merge($paths['dirs'], $subPaths->dirs);
                    $paths['files'] = array_merge($paths['files'], $subPaths->files);
                } else {
                    $paths['files'][] = $fPath;
                }
            }
        }

        return (object) $paths;
    }
}
