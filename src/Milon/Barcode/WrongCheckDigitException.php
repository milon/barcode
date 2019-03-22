<?php

namespace Milon\Barcode;

use Exception;

class WrongCheckDigitException extends \LogicException {

	/**
	 * WrongCheckDigitException constructor.
	 * @param int|null       $actual
	 * @param int|null       $expected
	 * @param Exception      $code
	 * @param Exception|NULL $previous
	 */
	public function __construct($actual = NULL, $expected = NULL, $code = 0, \Exception $previous = NULL) {

		$message = NULL;
		if ($actual && $expected) {
			$message = 'Expected ' . $expected . ' get ' . $actual;
		}

		parent::__construct($message, $code, $previous);
	}


}