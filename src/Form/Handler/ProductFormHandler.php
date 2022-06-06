<?php

namespace App\Form\Handler;

use App\Entity\Product;
use App\Form\Model\EditProductModel;
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

    /**
     * @param EditProductModel $editProductModel
     * @param FormInterface $form
     * @return Product|null
     */
    public function processEditForm(EditProductModel $editProductModel, FormInterface $form): ?Product
    {
        // 1. Save product's changes
        // 1.2. Create new Product
        // 1.3. Copy data from editProductModel to product
        // 2. Save uploaded file into temp folder
        // 3. Add image to product
        // 3.1. Get path of image folder
        // 3.2. Operation with ProductImage
        // 3.2.1. Resize and save image into folder. Size: BIG, MIDDLE, SMALL
        // 3.2.2. Create ProductImage and return it to Product
        // 3.3. Save Product with ProductImage

        $product = new Product();
        if (isset($editProductModel->id)) {
            $product = $this->productManager->find($editProductModel->id);
        }

        $product->setTitle($editProductModel->title);
        $product->setPrice($editProductModel->price);
        $product->setQuantity($editProductModel->quantity);
        $product->setDescription($editProductModel->description);
        $product->setIsPublished($editProductModel->isPublished);
        $product->setIsDeleted($editProductModel->isDeleted);

        $this->productManager->save($product);

        $newImageFile = $form->get('newImage')->getData();
        $tempImageFilename = $newImageFile ? $this->fileSaver->saveUploadedFileIntoTemp($newImageFile) : null;

        $this->productManager->updateProductImages($product, $tempImageFilename);

        $this->productManager->save($product);

        return $product;
    }
}