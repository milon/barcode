<?php

namespace Milon\Barcode;

//============================================================+
// File name   : tcpdf_barcodes_2d.php
// Version     : 1.0.015
// Begin       : 2009-04-07
// Last Update : 2014-05-20
// Author      : Nicola Asuni - Tecnick.com LTD - www.tecnick.com - info@tecnick.com
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2009-2014 Nicola Asuni - Tecnick.com LTD
//
// This file is part of TCPDF software library.
//
// TCPDF is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// TCPDF is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with TCPDF.  If not, see <http://www.gnu.org/licenses/>.
//
// See LICENSE.TXT file for more information.
// -------------------------------------------------------------------
//
// Description : PHP class to creates array representations for
//               2D barcodes to be used with TCPDF.
//
//============================================================+
/**
 * @file
 * PHP class to creates array representations for 2D barcodes to be used with TCPDF.
 * @package com.tecnick.tcpdf
 * @author Nicola Asuni
 * @version 1.0.015
 */
/**
 * @class TCPDF2DBarcode
 * PHP class to creates array representations for 2D barcodes to be used with TCPDF (http://www.tcpdf.org).
 * @package com.tecnick.tcpdf
 * @version 1.0.015
 * @author Nicola Asuni
 */

use Milon\Barcode\QRcode;
use Milon\Barcode\Datamatrix;
use Milon\Barcode\PDF417;
use Illuminate\Support\Str;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DNS2D
 *
 * @author dinesh
 */
class DNS2D {

    /**
     * Array representation of barcode.
     * @protected
     */
    protected $barcode_array = false;

    /**
     * path to save png in getBarcodePNGPath
     * @var <type>
     */
    protected $store_path;

    /**
     * Return a SVG string representation of barcode.
     * <li>$arrcode['code'] code to be printed on text label</li>
     * <li>$arrcode['num_rows'] required number of rows</li>
     * <li>$arrcode['num_cols'] required number of columns</li>
     * <li>$arrcode['bcode'][$r][$c] value of the cell is $r row and $c column (0 = transparent, 1 = black)</li></ul>
     * @param $code (string) code to print
     * @param $type (string) type of barcode: <ul><li>DATAMATRIX : Datamatrix (ISO/IEC 16022)</li><li>PDF417 : PDF417 (ISO/IEC 15438:2006)</li><li>PDF417,a,e,t,s,f,o0,o1,o2,o3,o4,o5,o6 : PDF417 with parameters: a = aspect ratio (width/height); e = error correction level (0-8); t = total number of macro segments; s = macro segment index (0-99998); f = file ID; o0 = File Name (text); o1 = Segment Count (numeric); o2 = Time Stamp (numeric); o3 = Sender (text); o4 = Addressee (text); o5 = File Size (numeric); o6 = Checksum (numeric). NOTES: Parameters t, s and f are required for a Macro Control Block, all other parametrs are optional. To use a comma character ',' on text options, replace it with the character 255: "\xff".</li><li>QRCODE : QRcode Low error correction</li><li>QRCODE,L : QRcode Low error correction</li><li>QRCODE,M : QRcode Medium error correction</li><li>QRCODE,Q : QRcode Better error correction</li><li>QRCODE,H : QR-CODE Best error correction</li><li>RAW: raw mode - comma-separad list of array rows</li><li>RAW2: raw mode - array rows are surrounded by square parenthesis.</li><li>TEST : Test matrix</li></ul>
     * @param $w (int) Width of a single rectangle element in user units.
     * @param $h (int) Height of a single rectangle element in user units.
     * @param $color (string) Foreground color (in SVG format) for bar elements (background is transparent).
     * @return string SVG code.
     * @protected
     */
    public function getBarcodeSVG($code, $type, $w = 3, $h = 3, $color = 'black') {
        if (!$this->store_path) {
            $this->setStorPath(app('config')->get("barcode.store_path"));
        }
        //set barcode code and type
        $this->setBarcode($code, $type);
        // replace table for special characters
        $repstr = array("\0" => '', '&' => '&amp;', '<' => '&lt;', '>' => '&gt;');
        $svg = '<' . '?' . 'xml version="1.0" standalone="no"' . '?' . '>' . "\n";
        $svg .= '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' . "\n";
        $svg .= '<svg width="' . round(($this->barcode_array['num_cols'] * $w), 3) . '" height="' . round(($this->barcode_array['num_rows'] * $h), 3) . '" version="1.1" xmlns="http://www.w3.org/2000/svg">' . "\n";
        $svg .= "\t" . '<g id="elements" fill="' . $color . '" stroke="none">' . "\n";
        // print barcode elements
        $y = 0;
        // for each row
        for ($r = 0; $r < $this->barcode_array['num_rows']; ++$r) {
            $x = 0;
            // for each column
            for ($c = 0; $c < $this->barcode_array['num_cols']; ++$c) {
                if ($this->barcode_array['bcode'][$r][$c] == 1) {
                    // draw a single barcode cell
                    $svg .= "\t\t" . '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="' . $h . '" />' . "\n";
                }
                $x += $w;
            }
            $y += $h;
        }
        $svg .= "\t" . '</g>' . "\n";
        $svg .= '</svg>' . "\n";
        return $svg;
    }

    /**
     * Return an HTML representation of barcode.
     * <li>$arrcode['code'] code to be printed on text label</li>
     * <li>$arrcode['num_rows'] required number of rows</li>
     * <li>$arrcode['num_cols'] required number of columns</li>
     * <li>$arrcode['bcode'][$r][$c] value of the cell is $r row and $c column (0 = transparent, 1 = black)</li></ul>
     * @param $code (string) code to print
     * @param $type (string) type of barcode: <ul><li>DATAMATRIX : Datamatrix (ISO/IEC 16022)</li><li>PDF417 : PDF417 (ISO/IEC 15438:2006)</li><li>PDF417,a,e,t,s,f,o0,o1,o2,o3,o4,o5,o6 : PDF417 with parameters: a = aspect ratio (width/height); e = error correction level (0-8); t = total number of macro segments; s = macro segment index (0-99998); f = file ID; o0 = File Name (text); o1 = Segment Count (numeric); o2 = Time Stamp (numeric); o3 = Sender (text); o4 = Addressee (text); o5 = File Size (numeric); o6 = Checksum (numeric). NOTES: Parameters t, s and f are required for a Macro Control Block, all other parametrs are optional. To use a comma character ',' on text options, replace it with the character 255: "\xff".</li><li>QRCODE : QRcode Low error correction</li><li>QRCODE,L : QRcode Low error correction</li><li>QRCODE,M : QRcode Medium error correction</li><li>QRCODE,Q : QRcode Better error correction</li><li>QRCODE,H : QR-CODE Best error correction</li><li>RAW: raw mode - comma-separad list of array rows</li><li>RAW2: raw mode - array rows are surrounded by square parenthesis.</li><li>TEST : Test matrix</li></ul>
     * @param $w (int) Width of a single rectangle element in pixels.
     * @param $h (int) Height of a single rectangle element in pixels.
     * @param $color (string) Foreground color for bar elements (background is transparent).
     * @return string HTML code.
     * @protected
     */
    public function getBarcodeHTML($code, $type, $w = 10, $h = 10, $color = 'black') {
        if (!$this->store_path) {
            $this->setStorPath(app('config')->get("barcode.store_path"));
        }
        //set barcode code and type
        $this->setBarcode($code, $type);
        $html = '<div style="font-size:0;position:relative;width:' . ($w * $this->barcode_array['num_cols']) . 'px;height:' . ($h * $this->barcode_array['num_rows']) . 'px;">' . "\n";
        // print barcode elements
        $y = 0;
        // for each row
        for ($r = 0; $r < $this->barcode_array['num_rows']; ++$r) {
            $x = 0;
            // for each column
            for ($c = 0; $c < $this->barcode_array['num_cols']; ++$c) {
                if ($this->barcode_array['bcode'][$r][$c] == 1) {
                    // draw a single barcode cell
                    $html .= '<div style="background-color:' . $color . ';width:' . $w . 'px;height:' . $h . 'px;position:absolute;left:' . $x . 'px;top:' . $y . 'px;">&nbsp;</div>' . "\n";
                }
                $x += $w;
            }
            $y += $h;
        }
        $html .= '</div>' . "\n";
        return $html;
    }

    /**
     * Return a PNG image representation of barcode (requires GD or Imagick library).
     * <li>$arrcode['code'] code to be printed on text label</li>
     * <li>$arrcode['num_rows'] required number of rows</li>
     * <li>$arrcode['num_cols'] required number of columns</li>
     * <li>$arrcode['bcode'][$r][$c] value of the cell is $r row and $c column (0 = transparent, 1 = black)</li></ul>
     * @param $code (string) code to print
     * @param $type (string) type of barcode: <ul><li>DATAMATRIX : Datamatrix (ISO/IEC 16022)</li><li>PDF417 : PDF417 (ISO/IEC 15438:2006)</li><li>PDF417,a,e,t,s,f,o0,o1,o2,o3,o4,o5,o6 : PDF417 with parameters: a = aspect ratio (width/height); e = error correction level (0-8); t = total number of macro segments; s = macro segment index (0-99998); f = file ID; o0 = File Name (text); o1 = Segment Count (numeric); o2 = Time Stamp (numeric); o3 = Sender (text); o4 = Addressee (text); o5 = File Size (numeric); o6 = Checksum (numeric). NOTES: Parameters t, s and f are required for a Macro Control Block, all other parametrs are optional. To use a comma character ',' on text options, replace it with the character 255: "\xff".</li><li>QRCODE : QRcode Low error correction</li><li>QRCODE,L : QRcode Low error correction</li><li>QRCODE,M : QRcode Medium error correction</li><li>QRCODE,Q : QRcode Better error correction</li><li>QRCODE,H : QR-CODE Best error correction</li><li>RAW: raw mode - comma-separad list of array rows</li><li>RAW2: raw mode - array rows are surrounded by square parenthesis.</li><li>TEST : Test matrix</li></ul>
     * @param $w (int) Width of a single rectangle element in pixels.
     * @param $h (int) Height of a single rectangle element in pixels.
     * @param $color (array) RGB (0-255) foreground color for bar elements (background is transparent).
     * @return string|false path or false in case of error.
     * @protected
     */
    public function getBarcodePNG($code, $type, $w = 3, $h = 3, $color = array(0, 0, 0)) {
        if (!$this->store_path) {
            $this->setStorPath(app('config')->get("barcode.store_path"));
        }
        //set barcode code and type
        $this->setBarcode($code, $type);
        // calculate image size
        $width = ($this->barcode_array['num_cols'] * $w);
        $height = ($this->barcode_array['num_rows'] * $h);
        if (function_exists('imagecreate')) {
            // GD library
            $imagick = false;
            $png = imagecreate($width, $height);
            $bgcol = imagecolorallocate($png, 255, 255, 255);
            imagecolortransparent($png, $bgcol);
            $fgcol = imagecolorallocate($png, $color[0], $color[1], $color[2]);
        } elseif (extension_loaded('imagick')) {
            $imagick = true;
            $bgcol = new \imagickpixel('rgb(255,255,255');
            $fgcol = new \imagickpixel('rgb(' . $color[0] . ',' . $color[1] . ',' . $color[2] . ')');
            $png = new \Imagick();
            $png->newImage($width, $height, 'none', 'png');
            $bar = new \imagickdraw();
            $bar->setfillcolor($fgcol);
        } else {
            return false;
        }
        // print barcode elements
        $y = 0;
        // for each row
        for ($r = 0; $r < $this->barcode_array['num_rows']; ++$r) {
            $x = 0;
            // for each column
            for ($c = 0; $c < $this->barcode_array['num_cols']; ++$c) {
                if ($this->barcode_array['bcode'][$r][$c] == 1) {
                    // draw a single barcode cell
                    if ($imagick) {
                        $bar->rectangle($x, $y, ($x + ($w-1)), ($y + ($h-1)));
                    } else {
                        imagefilledrectangle($png, $x, $y, ($x + ($w-1)), ($y + ($h-1)), $fgcol);
                    }
                }
                $x += $w;
            }
            $y += $h;
        }
        ob_start();
        // get image out put

        if ($imagick) {
            $png->drawimage($bar);
            echo $png;
        } else {
            imagepng($png);
            imagedestroy($png);
        }
        $image = ob_get_clean();
        $image = base64_encode($image);
        //$image = 'data:image/png;base64,' . base64_encode($image);
        return $image;
    }

    /**
     * Return a .png file path which create in server
     * @param $code (string) code to print
     * @param $type (string) type of barcode: <ul><li>C39 : CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.</li><li>C39+ : CODE 39 with checksum</li><li>C39E : CODE 39 EXTENDED</li><li>C39E+ : CODE 39 EXTENDED + CHECKSUM</li><li>C93 : CODE 93 - USS-93</li><li>S25 : Standard 2 of 5</li><li>S25+ : Standard 2 of 5 + CHECKSUM</li><li>I25 : Interleaved 2 of 5</li><li>I25+ : Interleaved 2 of 5 + CHECKSUM</li><li>C128 : CODE 128</li><li>C128A : CODE 128 A</li><li>C128B : CODE 128 B</li><li>C128C : CODE 128 C</li><li>EAN2 : 2-Digits UPC-Based Extention</li><li>EAN5 : 5-Digits UPC-Based Extention</li><li>EAN8 : EAN 8</li><li>EAN13 : EAN 13</li><li>UPCA : UPC-A</li><li>UPCE : UPC-E</li><li>MSI : MSI (Variation of Plessey code)</li><li>MSI+ : MSI + CHECKSUM (modulo 11)</li><li>POSTNET : POSTNET</li><li>PLANET : PLANET</li><li>RMS4CC : RMS4CC (Royal Mail 4-state Customer Code) - CBC (Customer Bar Code)</li><li>KIX : KIX (Klant index - Customer index)</li><li>IMB: Intelligent Mail Barcode - Onecode - USPS-B-3200</li><li>CODABAR : CODABAR</li><li>CODE11 : CODE 11</li><li>PHARMA : PHARMACODE</li><li>PHARMA2T : PHARMACODE TWO-TRACKS</li></ul>
     * @param $w (int) Width of a single bar element in pixels.
     * @param $h (int) Height of a single bar element in pixels.
     * @param $color (array) RGB (0-255) foreground color for bar elements (background is transparent).
     * @return string|false url or false in case of error.
     * @protected
     */
    protected function getBarcodePNGUri($code, $type, $w = 3, $h = 3, $color = array(0, 0, 0)) {
        $path = $this->getBarcodePNGPath($code, $type, $w, $h, $color);
        // Replace backslash (Windows) with forward slashes, to make it compatible with url().
        return url(str_replace('\\', '/', $path));
    }

    /**
     * Return a .png file path which create in server
     * <li>$arrcode['code'] code to be printed on text label</li>
     * <li>$arrcode['num_rows'] required number of rows</li>
     * <li>$arrcode['num_cols'] required number of columns</li>
     * <li>$arrcode['bcode'][$r][$c] value of the cell is $r row and $c column (0 = transparent, 1 = black)</li></ul>
     * @param $code (string) code to print
     * @param $type (string) type of barcode: <ul><li>DATAMATRIX : Datamatrix (ISO/IEC 16022)</li><li>PDF417 : PDF417 (ISO/IEC 15438:2006)</li><li>PDF417,a,e,t,s,f,o0,o1,o2,o3,o4,o5,o6 : PDF417 with parameters: a = aspect ratio (width/height); e = error correction level (0-8); t = total number of macro segments; s = macro segment index (0-99998); f = file ID; o0 = File Name (text); o1 = Segment Count (numeric); o2 = Time Stamp (numeric); o3 = Sender (text); o4 = Addressee (text); o5 = File Size (numeric); o6 = Checksum (numeric). NOTES: Parameters t, s and f are required for a Macro Control Block, all other parametrs are optional. To use a comma character ',' on text options, replace it with the character 255: "\xff".</li><li>QRCODE : QRcode Low error correction</li><li>QRCODE,L : QRcode Low error correction</li><li>QRCODE,M : QRcode Medium error correction</li><li>QRCODE,Q : QRcode Better error correction</li><li>QRCODE,H : QR-CODE Best error correction</li><li>RAW: raw mode - comma-separad list of array rows</li><li>RAW2: raw mode - array rows are surrounded by square parenthesis.</li><li>TEST : Test matrix</li></ul>
     * @param $w (int) Width of a single rectangle element in pixels.
     * @param $h (int) Height of a single rectangle element in pixels.
     * @param $color (array) RGB (0-255) foreground color for bar elements (background is transparent).
     * @return string|false path of image which was created or false in case of error
     * @protected
     */
    protected function getBarcodePNGPath($code, $type, $w = 3, $h = 3, $color = array(0, 0, 0)) {
        if (!$this->store_path) {
            $this->setStorPath(app('config')->get("barcode.store_path"));
        }
        //set barcode code and type
        $this->setBarcode($code, $type);
        // calculate image size
        $width = ($this->barcode_array['num_cols'] * $w);
        $height = ($this->barcode_array['num_rows'] * $h);
        if (function_exists('imagecreate')) {
            // GD library
            $imagick = false;
            $png = imagecreate($width, $height);
            $bgcol = imagecolorallocate($png, 255, 255, 255);
            imagecolortransparent($png, $bgcol);
            $fgcol = imagecolorallocate($png, $color[0], $color[1], $color[2]);
        } elseif (extension_loaded('imagick')) {
            $imagick = true;
            $bgcol = new imagickpixel('rgb(255,255,255');
            $fgcol = new imagickpixel('rgb(' . $color[0] . ',' . $color[1] . ',' . $color[2] . ')');
            $png = new Imagick();
            $png->newImage($width, $height, 'none', 'png');
            $bar = new imagickdraw();
            $bar->setfillcolor($fgcol);
        } else {
            return false;
        }
        // print barcode elements
        $y = 0;
        // for each row
        for ($r = 0; $r < $this->barcode_array['num_rows']; ++$r) {
            $x = 0;
            // for each column
            for ($c = 0; $c < $this->barcode_array['num_cols']; ++$c) {
                if ($this->barcode_array['bcode'][$r][$c] == 1) {
                    // draw a single barcode cell
                    if ($imagick) {
                        $bar->rectangle($x, $y, ($x + $w), ($y + $h));
                    } else {
                        imagefilledrectangle($png, $x, $y, ($x + $w), ($y + $h), $fgcol);
                    }
                }
                $x += $w;
            }
            $y += $h;
        }
        $file_name= Str::slug($code.$type);
        $save_file = $this->checkfile($this->store_path . $file_name . ".png");

        if ($imagick) {
            $png->drawimage($bar);
            //echo $png;
        }
        if (ImagePng($png, $save_file)) {
            imagedestroy($png);
            return str_replace(public_path(), '', $save_file);
        } else {
            imagedestroy($png);
            return $code;
        }
    }

    /**
     * Set the barcode.
     * @param $code (string) code to print
     * @param $type (string) type of barcode: <ul><li>DATAMATRIX : Datamatrix (ISO/IEC 16022)</li><li>PDF417 : PDF417 (ISO/IEC 15438:2006)</li><li>PDF417,a,e,t,s,f,o0,o1,o2,o3,o4,o5,o6 : PDF417 with parameters: a = aspect ratio (width/height); e = error correction level (0-8); t = total number of macro segments; s = macro segment index (0-99998); f = file ID; o0 = File Name (text); o1 = Segment Count (numeric); o2 = Time Stamp (numeric); o3 = Sender (text); o4 = Addressee (text); o5 = File Size (numeric); o6 = Checksum (numeric). NOTES: Parameters t, s and f are required for a Macro Control Block, all other parametrs are optional. To use a comma character ',' on text options, replace it with the character 255: "\xff".</li><li>QRCODE : QRcode Low error correction</li><li>QRCODE,L : QRcode Low error correction</li><li>QRCODE,M : QRcode Medium error correction</li><li>QRCODE,Q : QRcode Better error correction</li><li>QRCODE,H : QR-CODE Best error correction</li><li>RAW: raw mode - comma-separad list of array rows</li><li>RAW2: raw mode - array rows are surrounded by square parenthesis.</li><li>TEST : Test matrix</li></ul>
     * @return array
     */
    protected function setBarcode($code, $type) {
        $mode = explode(',', $type);
        $qrtype = strtoupper($mode[0]);
        switch ($qrtype) {
            case 'DATAMATRIX': { // DATAMATRIX (ISO/IEC 16022)
                    $barcode = new Datamatrix($code);
                    $this->barcode_array = $barcode->getBarcodeArray();
                    $this->barcode_array['code'] = $code;
                    break;
                }
            case 'PDF417': { // PDF417 (ISO/IEC 15438:2006)
                    if (!isset($mode[1]) OR ($mode[1] === '')) {
                        $aspectratio = 2; // default aspect ratio (width / height)
                    } else {
                        $aspectratio = floatval($mode[1]);
                    }
                    if (!isset($mode[2]) OR ($mode[2] === '')) {
                        $ecl = -1; // default error correction level (auto)
                    } else {
                        $ecl = intval($mode[2]);
                    }
                    // set macro block
                    $macro = array();
                    if (isset($mode[3]) AND ($mode[3] !== '') AND isset($mode[4]) AND ($mode[4] !== '') AND isset($mode[5]) AND ($mode[5] !== '')) {
                        $macro['segment_total'] = intval($mode[3]);
                        $macro['segment_index'] = intval($mode[4]);
                        $macro['file_id'] = strtr($mode[5], "\xff", ',');
                        for ($i = 0; $i < 7; ++$i) {
                            $o = $i + 6;
                            if (isset($mode[$o]) AND ($mode[$o] !== '')) {
                                // add option
                                $macro['option_' . $i] = strtr($mode[$o], "\xff", ',');
                            }
                        }
                    }
                    $barcode = new PDF417($code, $ecl, $aspectratio, $macro);
                    $this->barcode_array = $barcode->getBarcodeArray();
                    $this->barcode_array['code'] = $code;
                    break;
                }
            case 'QRCODE': { // QR-CODE
                    if (!isset($mode[1]) OR (!in_array($mode[1], array('L', 'M', 'Q', 'H')))) {
                        $mode[1] = 'L'; // Ddefault: Low error correction
                    }
                    $barcode = new QRcode($code, strtoupper($mode[1]));
                    $this->barcode_array = $barcode->getBarcodeArray();
                    $this->barcode_array['code'] = $code;
                    break;
                }
            default: {
                    $this->barcode_array = false;
                }
        }
    }

    /**
     *
     * @param type $path
     * @return type
     */
    protected function checkfile($path) {
        if (file_exists($path)) {
            unlink($path);
        }
        return $path;
    }

    public function setStorPath($path) {
        $this->store_path = rtrim((string) $path, '/' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        return $this;
    }



	/**
	 * Handle dynamic method calls.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		return $this->$method(...$parameters);
	}

	/**
	 * Handle dynamic static method calls.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		return (new static)->$method(...$parameters);
	}
}
