<?php

namespace App\Controller\Main;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\Admin\EditProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'main_homepage')]
    public function index(Session $session): Response
    {
        // getDoctrine is deprecated. Will stay here for example
        // $entityManager = $this->getDoctrine()->getManager();
//        $categories = $entityManager->getRepository(Category::class)->findBy(
//            ['isDeleted' => false],
//            ['id' => 'DESC']
//        );

        // Set categories in session to use everywhere in templates.
        // Need fix in future if find best example to create a global var in twig
        // $session->set('session_categories', $categories);

        return $this->render('main/default/index.html.twig', []);
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

//    #[Route('/edit-product/{id}', methods: ['GET', 'POST'], name: 'edit_product', requirements: ['id' => '\d+'])]
//    #[Route('/add-product', methods: ['GET','POST'], name: 'add_product')]
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
