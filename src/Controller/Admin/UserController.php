<?php


namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\EditUserFormType;
use App\Form\Handler\UserFormHandler;
use App\Repository\UserRepository;
use App\Utils\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller\Admin
 * @Route("/admin/user", name="admin_user_")
 */
class UserController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy(
            ['isDeleted' => false],
            ['id' => 'DESC']
        );
        return $this->render('admin/user/list.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/add', name: 'add')]
    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, UserFormHandler $userFormHandler, User $user = null): Response
    {
        if (!$user) {
            $user = new User();
        }

        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userFormHandler->processEditForm($form);
            // After creation new user we can call verified method via EmailVerifier

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('admin_user_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', 'Something went wrong...');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function delete(User $user, UserManager $userManager): Response
    {
        $userManager->remove($user);

        $this->addFlash('warning', 'The category was deleted!');

        return $this->redirectToRoute('admin_category_list');
    }
}