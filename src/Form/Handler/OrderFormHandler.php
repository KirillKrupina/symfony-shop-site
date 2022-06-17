<?php


namespace App\Form\Handler;


use App\Entity\Order;
use App\Utils\Manager\OrderManager;

class OrderFormHandler
{
    /**
     * @var OrderManager
     */
    private OrderManager $orderManager;

    /**
     * CategoryFormHandler constructor.
     * @param OrderManager $orderManager
     */
    public function __construct(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    /**
     * @param Order $order
     * @return Order|null
     * @throws \Exception
     */
    public function processEditForm(Order $order): ?Order
    {
        $this->orderManager->save($order);
        return $order;
    }
}