<?php

namespace App\Shared\Domain\Service;

use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final readonly class FileManagerService
{
    private Filesystem $filesystem;
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->filesystem = new Filesystem();
        $this->projectDir = $projectDir;
    }

    public function ensureDirectoryExists(string $relativePath): ?string
    {
        $absolutePath = Path::join($this->projectDir, 'public', $relativePath);

        if (!$this->filesystem->exists($absolutePath)) {
            try {
                $this->filesystem->mkdir($absolutePath);
            } catch (Exception) {
                return null;
            }
        }

        return $absolutePath;
    }

    public function readFile(string $relativePath)
    {
        $filePath = Path::join($this->projectDir, 'public', $relativePath);
        if (!$this->filesystem->exists($filePath)) {
            return null;
        }
        return $this->filesystem->readFile($filePath);
    }

    public function removeFiles(string $path): void
    {
        if (!$this->filesystem->exists($path)) {
            return;
        }
        $this->filesystem->remove($path);
    }
}