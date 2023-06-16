<?php

namespace Milon\Barcode\GS1_128;

use Ayeo\Barcode\Data\BinaryMap;

/**
 * @class
 * @description Generate GS1_128 format
 * @link https://www.gs1-128.info/
 * */
class GS1128
{
    /**
     * @var SectionSlicer
     */
    private $slicer;

    private $currentSubset;

    /**
     * @var Subsets
     */
    private $subsets;

    private $binaryCodeOffsets = [];


    public function __construct()
    {
        $this->slicer = new SectionSlicer();
        $this->subsets = new Subsets();
        $this->currentSubset = $this->subsets->getSubsetSefault();
    }

    public function generate($barcodeString)
    {
        $this->binaryCodeOffsets = [];
        $this->binaryCodeOffsets[] = 105; //start
        $this->binaryCodeOffsets[] = 102; //fcn1

        $sections = $this->slicer->getSections($barcodeString);
        $totalSectionsNumber = count($sections);
        $i = 1;

        /* @var $section Section */
        foreach ($sections as $section) {
            $this->doShit($this->getPairs((string) $section), $barcodeString);

            if ($i++ < $totalSectionsNumber && $section->hasFixedLength() === false) {
                $this->binaryCodeOffsets[] = 102; //fcn1
            }
        }

        $this->binaryCodeOffsets[] = $this->generateChecksum($this->binaryCodeOffsets);
        $this->binaryCodeOffsets[] = 106; // STOP
        $this->binaryCodeOffsets[] = 107; // TERMINATE

        return array_map(function($i) { return (int) $i; }, $this->binaryCodeOffsets);
    }

    /**
     * @param $letter
     * @param $pair
     * @return bool
     */
    private function setProperSubset($letter, $pair)
    {
        if (array_search((string) $pair, $this->getSubsetMap($letter), true)) {
            $this->currentSubset = $letter;
            $this->binaryCodeOffsets[] = $this->subsets->getAllSubset()[$letter];
            return true;
        }

        return false;
    }

    /**
     * @param $pair
     * @param $fullCode
     */
    private function checkSubsetMap($pair, $fullCode)
    {
        if (array_search((string) $pair, $this->getCurrentSubset(), true)) {
            return;
        }

        foreach (array_keys($this->subsets->getAllSubset()) as $letter) {
            if ($this->setProperSubset($letter, $pair)) {
                return;
            }
        }
    }

    /**
     * @param $array
     * @return string (binary)
     */
    private function generateChecksum($array)
    {
        $total = 0;
        foreach ($array as $i => $value) {
            $multiplier = $i === 0 ? 1 : $i;
            $total += $value * $multiplier;
        }

        return $total % 103;
    }

    /**
     * @param $array
     * @param $fullCode
     */
    private function doShit($array, $fullCode)
    {
        foreach ($array as $pair) {
            $this->checkSubsetMap($pair, $fullCode);
            $key = array_search($pair, $this->getCurrentSubset(), true);
            $key === false ? $this->doShit(str_split($pair), $fullCode) : $this->binaryCodeOffsets[] = $key;
        }
    }

    /**
     * @param $code
     * @return array
     */
    private function getPairs($code)
    {
        return str_split($code, 2);
    }

    /**
     * @return array
     */
    private function getCurrentSubset()
    {
        return $this->getSubsetMap($this->currentSubset);
    }

    private function getSubsetMap($letter)
    {
        return $this->subsets->get($letter);
    }
}
