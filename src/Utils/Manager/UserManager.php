<?php


namespace App\Utils\Manager;


use App\Entity\Order;
use App\Entity\User;
use Doctrine\Persistence\ObjectRepository;

class UserManager extends AbstractBaseManager
{
    /**
     * @inheritDoc
     */
    public function getRepository(): ObjectRepository
    {
        $repository = $this->entityManager->getRepository(User::class);
        return $repository;
    }

    /**
     * @param User|object $user
     */
    public function remove(object $user)
    {
        $user->setIsDeleted(true);
        $this->save($user);
    }
}