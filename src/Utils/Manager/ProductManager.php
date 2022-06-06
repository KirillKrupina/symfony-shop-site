<?php


namespace App\Utils\Manager;


use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ProductManager extends AbstractBaseManager
{
    /**
     * @var string
     */
    private string $productImagesDir;
    /**
     * @var ProductImageManager
     */
    private ProductImageManager $productImageManager;

    /**
     * ProductManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param ProductImageManager $productImageManager
     * @param string $productImagesDir // global constant - services.yaml
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ProductImageManager $productImageManager,
        string $productImagesDir
    )
    {
        parent::__construct($entityManager);

        $this->productImagesDir = $productImagesDir;
        $this->productImageManager = $productImageManager;
    }

    /**
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository
    {
        $repository = $this->entityManager->getRepository(Product::class);
        return $repository;
    }

    /**
     * @param object $product
     */
    public function remove(object $product)
    {
        $product->setIsDeleted(true);
        $this->save($product);
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getProductImagesDir(Product $product)
    {
        $dir = sprintf('%s/%s', $this->productImagesDir, $product->getId());
        return $dir;
    }

    /**
     * @param Product $product
     * @param string|null $tempImageFilename
     * @return Product
     */
    public function updateProductImages(Product $product, string $tempImageFilename = null): Product
    {
        if (!$tempImageFilename) {
            return $product;
        }

        $productDir = $this->getProductImagesDir($product);

        $productImage = $this->productImageManager->saveImageForProduct($productDir, $tempImageFilename);
        $productImage->setProduct($product);
        $product->addProductImage($productImage);

        return $product;
    }
}