<?php


namespace App\Controller\Admin;


use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Form\Admin\EditOrderFormType;
use App\Form\Handler\OrderFormHandler;
use App\Repository\OrderRepository;
use App\Utils\Manager\OrderManager;
use App\Utils\StaticStorage\OrderStaticStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrderController
 * @package App\Controller\Admin
 * @Route("/admin/order", name="admin_order_")
 */
class OrderController extends AbstractController
{
#[Route('/list', name: 'list')]
    public function list(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(
            ['isDeleted' => false],
            ['id' => 'DESC']
        );
        return $this->render('admin/order/list.html.twig', [
            'orders' => $orders,
            'orderStatuses' => OrderStaticStorage::getOrderStatuses()
        ]);
    }

    #[Route('/add', name: 'add')]
    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, OrderFormHandler $orderFormHandler, Order $order = null, int $id = null): Response
    {
        if (!$order) {
            $order = new Order();
        }

        $form = $this->createForm(EditOrderFormType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderFormHandler->processEditForm($order);

            $this->addFlash('success', sprintf('Order #%s was saved!', $order->getId()));

            return $this->redirectToRoute('admin_order_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('warning', 'Something went wrong...');
        }

        return $this->render('admin/order/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Order $order, OrderManager $orderManager): Response
    {
        $orderManager->remove($order);

        $this->addFlash('warning', 'The order was deleted!');

        return $this->redirectToRoute('admin_order_list');
    }
}