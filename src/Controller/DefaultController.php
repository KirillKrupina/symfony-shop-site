<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $productList = $entityManager->getRepository(Product::class)->findAll();
        dd($productList);

        return $this->render('main/default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route('/product-add', name: 'product_add')]
    public function productAt(): Response
    {
        $product = new Product();
        $product->setTitle('Product '.rand(1, 100));
        $product->setDescription('Smth');
        $product->setPrice(10);
        $product->setQuantity(1);

        // Call Doctrine
        $entityManager = $this->getDoctrine()->getManager();
        // Prepare object to insert
        $entityManager->persist($product);
        // Update DB
        $entityManager->flush();

        return $this->redirectToRoute('homepage');
    }
}
