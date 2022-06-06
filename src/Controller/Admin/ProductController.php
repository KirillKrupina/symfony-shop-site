<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\EditProductFormType;
use App\Form\Handler\ProductFormHandler;
use App\Form\Model\EditProductModel;
use App\Repository\ProductRepository;
use App\Utils\Manager\ProductManager;
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
    public function edit(Request $request, ProductFormHandler $productFormHandler, Product $product = null): Response
    {
        $editProductModel = EditProductModel::makeFromProduct($product);

        $form = $this->createForm(EditProductFormType::class, $editProductModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productFormHandler->processEditForm($editProductModel, $form);

            return $this->redirectToRoute('admin_product_list');
        }

        $images = [];
        if ($product) {
            $images = $product->getProductImages()->getValues();
        }

        return $this->render('admin/product/edit.html.twig', [
            'images' => $images,
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Product $product, ProductManager $productManager): Response
    {
        $productManager->remove($product);
        return $this->redirectToRoute('admin_product_list');
    }
}
