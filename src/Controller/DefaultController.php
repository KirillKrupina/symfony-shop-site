<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\EditProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        // getDoctrine is deprecated
        $entityManager = $this->getDoctrine()->getManager();
        $productList = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('main/default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /*
     * Test method
    #[Route('/product-add', name: 'product_add')]
    public function productAdd(): Response
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
    */

    #[Route('/edit-product/{id}', methods: ['GET', 'POST'], name: 'edit_product', requirements: ['id' => '\d+'])]
    #[Route('/add-product', methods: ['GET','POST'], name: 'add_product')]
    public function editProduct(Request $request, EntityManagerInterface $entityManager, int $id = null): Response
    {
        if (!$id) {
            $product = new Product();
        } else {
            $product = $entityManager->getRepository(Product::class)->find($id);
        }
        $form = $this->createForm(EditProductFormType::class, $product);

        /*  Receive request from form.
            Takes the POST’ed data from the previous request, processes it,
            and runs any validation (checks integrity of expected versus received data)
        */
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get data from form
            $data = $form->getData();
            // Prepare object to insert
            $entityManager->persist($product);
            // Update DB
            $entityManager->flush();

            return $this->redirectToRoute('edit_product', ['id' => $product->getId()]);
        }
        return $this->render('main/default/edit_product.html.twig', [
            'controller_name' => 'DefaultController',
            'form' => $form->createView()
        ]);
    }
}
