<?php


namespace App\Utils\ApiPlatform\Extension;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Product;
use Doctrine\ORM\QueryBuilder;

/**
 * Class FilterProductQueryExtension
 * @package App\Utils\ApiPlatform\Extension
 */
class FilterProductQueryExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{

    /**
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param string|null $operationName
     */
    // add a condition at the end of the request
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $this->andWhere($queryBuilder, $resourceClass);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $resourceClass
     */
    private function andWhere(QueryBuilder $queryBuilder, string $resourceClass)
    {
        if (Product::class !== $resourceClass) {
            return;
        }

        //Gets the root aliases of the query.
        // This is the entity aliases involved in the construction of the query
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(
            sprintf("%s.isDeleted = '0'", $rootAlias)
        );
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param QueryNameGeneratorInterface $queryNameGenerator
     * @param string $resourceClass
     * @param array $identifiers
     * @param string|null $operationName
     * @param array $context
     */
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        $this->andWhere($queryBuilder, $resourceClass);
    }
}