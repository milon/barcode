<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\WrongCheckDigitException;
use PHPUnit\Framework\TestCase;


class WrongCheckDigitExceptionTest extends TestCase
{
    public function testExceptionExtendsLogicException(): void
    {
        $e = new WrongCheckDigitException();
        $this->assertInstanceOf(\LogicException::class, $e);
    }

    public function testExceptionWithActualAndExpectedSetsMessage(): void
    {
        $e = new WrongCheckDigitException(1, 2);
        $this->assertStringContainsString('Expected 2 get 1', $e->getMessage());
    }

    public function testExceptionWithoutArgsHasEmptyMessage(): void
    {
        $e = new WrongCheckDigitException();
        $this->assertEmpty($e->getMessage());
    }
}
