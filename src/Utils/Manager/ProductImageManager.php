<?php


namespace App\Utils\Manager;


use App\Entity\ProductImage;
use App\Utils\File\ImageResizer;
use App\Utils\Filesystem\FilesystemWorker;
use Doctrine\ORM\EntityManagerInterface;

class ProductImageManager
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var FilesystemWorker
     */
    private FilesystemWorker $filesystemWorker;
    /**
     * @var string
     */
    private string $uploadsTempDir;
    /**
     * @var ImageResizer
     */
    private ImageResizer $imageResizer;

    /**
     * ProductImageManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param FilesystemWorker $filesystemWorker
     * @param ImageResizer $imageResizer
     * @param string $uploadsTempDir // global constant - services.yaml
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FilesystemWorker $filesystemWorker,
        ImageResizer $imageResizer,
        string $uploadsTempDir
    )
    {
        $this->entityManager = $entityManager;
        $this->filesystemWorker = $filesystemWorker;
        $this->uploadsTempDir = $uploadsTempDir;
        $this->imageResizer = $imageResizer;
    }

    /**
     * @param string $productDir
     * @param string|null $tempImageFilename
     * @return ProductImage|null
     */
    public function saveImageForProduct(string $productDir, string $tempImageFilename = null)
    {
        if (!$tempImageFilename) {
            return null;
        }

        $this->filesystemWorker->createFolder($productDir);

        $filenameId = uniqid();
        $imageSmallParams = [
            'width' => 60, // pixel
            'height' => null,
            'newFolder' => $productDir,
            'newFileName' => sprintf('%s_%s.jpg', $filenameId, 'small')
        ];
        $imageSmall = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFilename, $imageSmallParams);

        $imageMiddleParams = [
            'width' => 430, // pixel
            'height' => null,
            'newFolder' => $productDir,
            'newFileName' => sprintf('%s_%s.jpg', $filenameId, 'middle')
        ];
        $imageMiddle = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFilename, $imageMiddleParams);

        $imageBigParams = [
            'width' => 800, // pixel
            'height' => null,
            'newFolder' => $productDir,
            'newFileName' => sprintf('%s_%s.jpg', $filenameId, 'big')
        ];
        $imageBig = $this->imageResizer->resizeImageAndSave($this->uploadsTempDir, $tempImageFilename, $imageBigParams);

        $productImage = new ProductImage();
        $productImage->setFilenameSmall($imageSmall);
        $productImage->setFilenameMiddle($imageMiddle);
        $productImage->setFilenameBig($imageBig);

        return $productImage;
    }

    /**
     * @param ProductImage $productImage
     * @param string $productImagesDir
     */
    public function removeImage(ProductImage $productImage, string $productImagesDir)
    {
        $smallFilePath = $productImagesDir . '/' . $productImage->getFilenameSmall();
        $this->filesystemWorker->removeFile($smallFilePath);

        $middleFilePath = $productImagesDir . '/' . $productImage->getFilenameMiddle();
        $this->filesystemWorker->removeFile($middleFilePath);

        $bigFilePath = $productImagesDir . '/' . $productImage->getFilenameBig();
        $this->filesystemWorker->removeFile($bigFilePath);

        $product = $productImage->getProduct();
        $product->removeProductImage($productImage);

        $this->entityManager->flush();
    }
}