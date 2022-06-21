<?php


namespace App\Form\Handler;


use App\Entity\User;
use App\Utils\Manager\UserManager;
use Symfony\Component\Form\Form;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFormHandler
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * CategoryFormHandler constructor.
     * @param UserManager $userManager
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(UserManager $userManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userManager = $userManager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @param Form $form
     * @return User|null
     */
    public function processEditForm(Form $form): ?User
    {
        $email = $form->get('email')->getData();
        $plainPassword = $form->get('plainPassword')->getData();
        $roles = $form->get('roles')->getData();

        /** @var User $user */
        $user = $form->getData();

        if (!$user->getId()){
            $user->setEmail($email);
        }

        if ($plainPassword) {
            $encodedPassword = $this->userPasswordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
        }

        if ($roles) {
            $user->setRoles($roles);
        }

        // Created user from admin panel always verified. Can change logic in the future mb
        $user->setIsVerified(true);

        $this->userManager->save($user);

        return $user;
    }
}