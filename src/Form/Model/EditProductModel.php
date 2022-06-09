<?php


namespace App\Form\Model;


use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class EditProductModel
{
    /**
     * @var int|null
     */
    public ?int $id;

    /**
     * @Assert\NotBlank(message="Please enter a title")
     * @var string|null
     */
    public ?string $title;

    /**
     * @Assert\NotBlank(message="Please enter a price")
     * @Assert\GreaterThanOrEqual(value="0")
     * @var string|null
     */
    public ?string $price;

    /**
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/png"},
     *     mimeTypesMessage = "Please upload a valid image. The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}"
     * )
     * @var UploadedFile|null
     */
    public ?UploadedFile $newImage;

    /**
     *
     * @Assert\NotBlank(message="Please enter a quantity")
     * @var int|null
     */
    public ?int $quantity;

    /**
     * @var string|null
     */
    public ?string $description;

    /**
     * @Assert\NotBlank(message="Please choose a category")
     * @var Category
     */
    public Category $category;

    /**
     * @var boolean
     */
    public bool $isPublished;

    /**
     * @var boolean
     */
    public bool $isDeleted;

    public static function makeFromProduct(?Product $product): self
    {
        $model = new self();
        if (!$product) {
            return $model;
        }

        $model->id = $product->getId();
        $model->title = $product->getTitle();
        $model->price = $product->getPrice();
        $model->quantity = $product->getQuantity();
        $model->description = $product->getDescription();
        $model->isPublished = $product->getIsPublished();
        $model->isDeleted = $product->getIsDeleted();

        return $model;
    }

    public function getId()
    {
        return $this->id;
    }
}