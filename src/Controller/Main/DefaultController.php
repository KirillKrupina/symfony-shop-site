<?php

namespace App\Controller\Main;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\Admin\EditProductFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'main_homepage')]
    public function index(): Response
    {
        return $this->render('main/default/index.html.twig', []);
    }
}
