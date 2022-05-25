<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\EditProductFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package App\Controller\Admin
 * @Route("/admin/product", name="admin_product_")
 */
class ProductController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(
            ['isDeleted' => false],
            ['id' => 'DESC'],
            50
        );
        return $this->render('admin/product/list.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/add', name: 'add')]
    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Product $product = null): Response
    {
        $form = $this->createForm(EditProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('admin_product_list');
        }

        return $this->render('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(): Response
    {

    }
}
