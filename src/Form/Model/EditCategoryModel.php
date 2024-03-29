<?php


namespace App\Form\Model;

use App\Entity\Category;
use Symfony\Component\Validator\Constraints as Assert;

class EditCategoryModel
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
     * @param Category|null $category
     * @return static
     */
    public static function makeFromCategory(?Category $category): self
    {
        $model = new self();
        if (!$category) {
            return $model;
        }

        $model->id = $category->getId();
        $model->title = $category->getTitle();

        return $model;
    }
}