<?php

namespace Milon\Barcode;

/**
 * Thrown when a barcode cannot be generated for the given code and type.
 */
class InvalidBarcodeException extends \InvalidArgumentException
{
    /**
     * @param string $type
     * @param string $code
     * @return self
     */
    public static function forEncodingFailure($type, $code)
    {
        return new self(self::encodingFailureMessage($type, $code));
    }

    /**
     * @param string $type
     * @return self
     */
    public static function forUnsupportedType($type)
    {
        return new self('Unsupported barcode type: ' . $type);
    }

    /**
     * Build a helpful message when barcode encoding fails.
     *
     * @param string $type
     * @param string $code
     * @return string
     */
    public static function encodingFailureMessage($type, $code)
    {
        $type = strtoupper((string) $type);
        $message = 'Unable to encode barcode of type ' . $type . ' for the given code.';

        switch ($type) {
            case 'C128A':
                $message .= ' C128A supports uppercase letters, digits, symbols, and control characters; lowercase letters are not allowed. Use C128B or C128 for mixed case.';
                break;
            case 'C128B':
                $message .= ' C128B supports ASCII characters 32-127 (letters, digits, and symbols).';
                break;
            case 'C128C':
                if (!ctype_digit((string) $code)) {
                    $message .= ' C128C supports digits only (0-9), encoded in pairs. Use C128 or C128B for alphanumeric codes.';
                } elseif ((strlen((string) $code) % 2) !== 0) {
                    $message .= ' C128C requires an even number of digits.';
                } else {
                    $message .= ' C128C supports digits only (0-9), encoded in pairs.';
                }
                break;
            case 'C128':
                $message .= ' The code contains characters that cannot be represented in Code 128.';
                break;
            case 'C39':
            case 'C39+':
                $message .= ' CODE 39 supports digits, uppercase letters A-Z, and - . $ / + % space. Use C39E for extended characters.';
                break;
            case 'S25':
            case 'S25+':
            case 'I25':
            case 'I25+':
            case 'MSI':
            case 'MSI+':
            case 'EAN2':
            case 'EAN5':
            case 'EAN8':
            case 'EAN13':
            case 'UPCA':
            case 'UPCE':
            case 'PHARMA':
            case 'PHARMA2T':
            case 'POSTNET':
            case 'PLANET':
                $message .= ' This barcode type requires a numeric code (digits only).';
                break;
            case 'DATAMATRIX':
            case 'PDF417':
            case 'QRCODE':
                $message .= ' The payload may be empty or exceed the maximum capacity for this symbology.';
                break;
        }

        return $message;
    }
}
