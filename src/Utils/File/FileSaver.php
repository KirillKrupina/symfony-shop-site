<?php

namespace App\Utils\File;

use App\Utils\Filesystem\FilesystemWorker;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileSaver
{
    /**
     * In some contexts, such as URLs and file/directory names,
     * it's not safe to use any Unicode character
     * A slugger transforms a given string into another
     * string that only includes safe ASCII characters
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;
    /**
     * @var string
     */
    private string $uploadsTempDir;
    /**
     * @var FilesystemWorker
     */
    private FilesystemWorker $filesystemWorker;

    /**
     * FileSaver constructor.
     * @param SluggerInterface $slugger
     * @param FilesystemWorker $filesystemWorker
     * @param string $uploadsTempDir // global constant - services.yaml
     */
    public function __construct(SluggerInterface $slugger, FilesystemWorker $filesystemWorker, string $uploadsTempDir)
    {
        $this->slugger = $slugger;
        $this->uploadsTempDir = $uploadsTempDir;
        $this->filesystemWorker = $filesystemWorker;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string|null
     */
    public function saveUploadedFileIntoTemp(UploadedFile $uploadedFile)
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $saveFilename = $this->slugger->slug($originalFilename);
        $filename = sprintf('%s-%s.%s', $saveFilename, uniqid(), $uploadedFile->guessExtension());

        $this->filesystemWorker->createFolder($this->uploadsTempDir);

        try {
            $uploadedFile->move($this->uploadsTempDir, $filename);
        } catch (FileException $exception) {
            return null;
        }

        return $filename;
    }
}