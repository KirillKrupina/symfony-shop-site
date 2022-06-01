<?php

namespace App\Utils\Filesystem;

use Symfony\Component\Filesystem\Filesystem;

class FilesystemWorker
{
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $folder
     */
    public function createFolder(string $folder)
    {
        if (!$this->filesystem->exists($folder)) {
            $this->filesystem->mkdir($folder);
        }
    }

    /**
     * @param string $filePath
     */
    public function removeFile(string $filePath)
    {
        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
        }
    }
}
