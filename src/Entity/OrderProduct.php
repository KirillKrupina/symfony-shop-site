<?php

namespace App\Entity;

use App\Repository\OrderProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Class OrderProduct
 * @package App\Entity
 * @ApiResource(
 *     collectionOperations = {
 *          "get" = {
 *             "normalization_context" = {"groups" = "order_product:list"}
 *           },
 *          "post" = {
 *              "security" = "is_granted('ROLE_ADMIN')",
 *              "normalization_context" = {"groups" = "order_product:list:write"}
 *          }
 *     },
 *     itemOperations={
 *     "get"={},
 *     "delete"={
 *          "security"="is_granted('ROLE_ADMIN')"
 *          }
 *     }
 * )
 */
#[ORM\Entity(repositoryClass: OrderProductRepository::class)]
class OrderProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['order_product:list', 'order_product:item', 'order:list', 'order:item'])]
    private $id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderProducts', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private $appOrder;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'orderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['order:list', 'order:item'])]
    private $product;

    #[ORM\Column(type: 'integer')]
    #[Groups(['order_product:list', 'order_product:item', 'order:list', 'order:item'])]
    private $quantity;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    #[Groups(['order_product:list', 'order_product:item', 'order:list', 'order:item'])]
    private $pricePerOne;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppOrder(): ?Order
    {
        return $this->appOrder;
    }

    public function setAppOrder(?Order $appOrder): self
    {
        $this->appOrder = $appOrder;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPricePerOne(): ?string
    {
        return $this->pricePerOne;
    }

    public function setPricePerOne(string $pricePerOne): self
    {
        $this->pricePerOne = $pricePerOne;

        return $this;
    }
}
