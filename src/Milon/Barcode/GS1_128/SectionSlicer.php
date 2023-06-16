<?php


namespace Milon\Barcode\GS1_128;


class SectionSlicer
{
    public function getSections($data)
    {
        $pattern = '#\((\d+)\)((?:[^\(])+)#';
        preg_match_all($pattern, $data, $matches);

        $result = [];
        for ($x = 0; $x < count($matches[0]); $x++) {
            $result[] = $matches[1][$x];
            $result[] = $matches[2][$x];
        }

        $expected = [];
        foreach (array_chunk($result, 2) as $sectionData) {
            $expected[] = $this->build($sectionData[0], $sectionData[1]);
        }

        return $expected;
    }

    public function build($identifier, $value)
    {
        if (array_key_exists($identifier, AIData::$default) === false) {
            throw new \LogicException(sprintf('Unknown application identifier %s', $identifier));
        }

        [$minLength, $maxLength, $description] = AIData::$default[$identifier];

        if (strlen($value) < $minLength || strlen($value) > $maxLength) {
            throw new \LogicException($description);
        }

        $fixedLength = $minLength === $maxLength;

        return new Section($identifier, $value, $fixedLength);
    }
}
