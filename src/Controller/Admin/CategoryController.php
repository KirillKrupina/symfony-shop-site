<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\EditCategoryFormType;
use App\Form\Handler\CategoryFormHandler;
use App\Form\Model\EditCategoryModel;
use App\Repository\CategoryRepository;
use App\Utils\Manager\CategoryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller\Admin
 * @Route("/admin/category", name="admin_category_")
 */
class CategoryController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy(
            ['isDeleted' => false],
            ['id' => 'DESC']
        );
        return $this->render('admin/category/list.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/add', name: 'add')]
    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, CategoryFormHandler $categoryFormHandler, Category $category = null): Response
    {
        $editCategoryModel = EditCategoryModel::makeFromCategory($category);

        $form = $this->createForm(EditCategoryFormType::class, $editCategoryModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryFormHandler->processEditForm($editCategoryModel);

            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('admin_category_list');
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Category $category, CategoryManager $categoryManager): Response
    {
        $categoryManager->remove($category);

        $this->addFlash('warning', 'The category was deleted!');

        return $this->redirectToRoute('admin_category_list');
    }
}
