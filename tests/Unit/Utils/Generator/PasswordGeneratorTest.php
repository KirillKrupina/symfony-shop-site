<?php

namespace App\Tests\Unit\Utils\Generator;

use PHPUnit\Framework\TestCase;
use App\Utils\Generator\PasswordGenerator;

/**
 * Class PasswordGenerator
 * @package App\Tests\Unit\Utils\Generator
 *
 * @group unit
 */
class PasswordGeneratorTest extends TestCase
{
    public function testGeneratePassword(): void
    {
        $password = PasswordGenerator::generatePassword(8);

        //compare what expected with the result
        self::assertSame(8, strlen($password));
    }
}
