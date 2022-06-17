<?php


namespace App\Utils\Manager;


use App\Entity\Cart;
use Doctrine\Persistence\ObjectRepository;

class CartManager extends AbstractBaseManager
{
    /**
     * @inheritDoc
     */
    public function getRepository(): ObjectRepository
    {
        $repository = $this->entityManager->getRepository(Cart::class);
        return $repository;
    }
}