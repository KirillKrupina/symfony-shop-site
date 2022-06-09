<?php


namespace App\Utils\Manager;


use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Persistence\ObjectRepository;

class CategoryManager extends AbstractBaseManager
{
    /**
     * @inheritDoc
     */
    public function getRepository(): ObjectRepository
    {
        $repository = $this->entityManager->getRepository(Category::class);
        return $repository;
    }

    /**
     * @param Category|object $category
     */
    public function remove(object $category)
    {
        $category->setIsDeleted(true);

        $attachedProducts = $category->getProducts()->getValues();

        /**
         * @var Product $product
         */
        foreach ($attachedProducts as $product) {
            $product->setCategory(null);
        }

        $this->save($category);
    }
}