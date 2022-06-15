<?php

namespace App\Controller\Main;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EmbedController extends AbstractController
{
    public function showLastProducts(ProductRepository $productRepository, int $productsCount = null, int $categoryId = null): Response
    {
        $params = [];
        if ($categoryId) {
            $params = array_merge(['category' => $categoryId]);
        }

        $products = $productRepository->findBy(
            $params,
            ['id' => 'DESC'],
            $productsCount
        );
        return $this->render('main/_embed/_last_products.html.twig', [
            'products' => $products
        ]);
    }

    public function showCategories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy(
            [],
            ['title' => 'DESC']
        );
        return $this->render('main/_embed/_menu/_categories_menu.html.twig', [
            'categories' => $categories
        ]);
    }
}
