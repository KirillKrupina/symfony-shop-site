<?php


namespace App\Tests\Functional\ApiPlatform;


use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Tests\Utils\TestUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProductResourceTest
 * @package App\Tests\Functional\ApiPlatform
 *
 * @group functional
 */
class ProductResourceTest extends WebTestCase
{
    public const REQUEST_HEADERS = [
        'HTTP_ACCEPT' => 'application/ld+json',
        'CONTENT_TYPE' => 'application/json'
    ];

    private const URI_KEY = '/api/products';

    public function testGetProducts()
    {
        $client = self::createClient();

        $client->request('GET', self::URI_KEY, [], [], self::REQUEST_HEADERS);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetProduct()
    {
        $client = self::createClient();

        /**
         * @var Product $product
         */
        $product = self::getContainer()->get(ProductRepository::class)->findOneBy([]);

        $uri = self::URI_KEY . '/' . $product->getUuid();

        $client->request('GET', $uri, [], [], self::REQUEST_HEADERS);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateProduct()
    {
        $client = self::createClient();

        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'test_admin@mail.com']);

        $client->loginUser($user, 'main');

        $context = [
            'title' => 'New Product',
            'price' => '150',
            'quantity' => 5
        ];

        $client->request('POST', self::URI_KEY, [], [], self::REQUEST_HEADERS, json_encode($context));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}