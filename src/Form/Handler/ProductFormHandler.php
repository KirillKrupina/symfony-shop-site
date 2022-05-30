<?php

namespace App\Form\Handler;

use App\Entity\Product;
use App\Utils\File\FileSaver;
use App\Utils\Manager\ProductManager;
use Symfony\Component\Form\FormInterface;

class ProductFormHandler
{

    /**
     * @var FileSaver
     */
    private FileSaver $fileSaver;
    /**
     * @var ProductManager
     */
    private ProductManager $productManager;

    public function __construct(ProductManager $productManager, FileSaver $fileSaver)
    {
        $this->fileSaver = $fileSaver;
        $this->productManager = $productManager;
    }

    public function processEditForm(Product $product, FormInterface $form)
    {
        // 1. Save product's changes
        // 2. Save uploaded file into temp folder
        // 3. Add image to product
        // 3.1. Get path of image folder
        // 3.2. Operation with ProductImage
        // 3.2.1. Resize and save image into folder. Size: BIG, MIDDLE, SMALL
        // 3.2.2. Create ProductImage and return it to Product
        // 3.3. Save Product with ProductImage

        $this->productManager->save($product);

        $newImageFile = $form->get('newImage')->getData();
        $tempImageFilename = $newImageFile ? $this->fileSaver->saveUploadedFileIntoTemp($newImageFile) : null;

        $this->productManager->updateProductImages($product, $tempImageFilename);

        $this->productManager->save($product);

        return $product;
    }
}