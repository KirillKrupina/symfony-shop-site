<?php


namespace App\Utils\Manager;


use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\User;
use App\Utils\StaticStorage\OrderStaticStorage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Monolog\DateTimeImmutable;

class OrderManager extends AbstractBaseManager
{
    /**
     * @var CartManager
     */
    private CartManager $cartManager;

    /**
     * OrderManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param CartManager $cartManager
     */
    public function __construct(EntityManagerInterface $entityManager, CartManager $cartManager)
    {
        parent::__construct($entityManager);
        $this->cartManager = $cartManager;
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): ObjectRepository
    {
        $repository = $this->entityManager->getRepository(Order::class);
        return $repository;
    }

    /**
     * @param string $sessionId
     * @param User $user
     * @throws \Exception
     */
    public function createOrderFromCartBySessionId(string $sessionId, User $user)
    {
        $cartRepository = $this->cartManager->getRepository();
        $cart = $cartRepository->findOneBy([
            'sessionId' => $sessionId
        ]);
        if ($cart) {
            $this->createOrderFromCart($cart, $user);
        }
    }

    /**
     * @param Cart|object $cart
     * @param User $user
     * @throws \Exception
     */
    public function createOrderFromCart(Cart $cart, User $user)
    {
        $order = new Order();

        $order->setOwner($user);
        $order->setStatus(OrderStaticStorage::ORDER_STATUS_CREATED);
        $orderTotalPrice = 0;

        $cartProducts = $cart->getCartProducts()->getValues();
        /**
         * @var CartProduct $cartProduct
         */
        foreach ($cartProducts as $cartProduct) {
            $product = $cartProduct->getProduct();

            $orderProduct = new OrderProduct();
            $orderProduct->setAppOrder($order);
            $orderProduct->setQuantity($cartProduct->getQuantity());
            $orderProduct->setPricePerOne($product->getPrice());
            $orderProduct->setProduct($product);

            $orderTotalPrice += $orderProduct->getQuantity() * $orderProduct->getPricePerOne();

            $order->addOrderProduct($orderProduct);

            $this->entityManager->persist($orderProduct);
            $this->entityManager->remove($cartProduct);
        }

        $order->setTotalPrice($orderTotalPrice);
        $order->setUpdatedAt(new \DateTimeImmutable());

        $this->save($order);

        $this->cartManager->remove($cart);

    }

    /**
     * @param object $entity
     * @throws \Exception
     */
    public function save(object $entity)
    {
        /**
         * @var Order $entity
         */
        $entity->setUpdatedAt(new \DateTimeImmutable());
        parent::save($entity);
    }

    /**
     * @param Order $order
     */
    public function calculateOrderTotalPrice(Order $order)
    {
        $orderTotalPrice = 0;
        /**
         * @var OrderProduct $orderProduct
         */
        foreach ($order->getOrderProducts()->getValues() as $orderProduct) {
            $orderTotalPrice += $orderProduct->getQuantity() * $orderProduct->getPricePerOne();
        }
        $order->setTotalPrice($orderTotalPrice);
    }

    /**
     * @param Order|object $order
     * @throws \Exception
     */
    public function remove(object $order)
    {
        $order->setIsDeleted(true);
        $this->save($order);
    }
}