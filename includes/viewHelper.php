<?php
/**
 * Provides an array that is indicating which section is open. The following
 * example is the array that is returned for six sections and a section
 * selection of 37:
 *   Array([0] => 1, [1] => 0, [2] => 1, [3] => 0, [4] => 0, [5] => 1)
 *
 * @param $sectionNum int Number, representing the state of all the sections.
 * @param $sectionCount int Total number of sections.
 * @return array
 */
function sections($sectionNum, $sectionCount)
{
    $section = array_fill(0, $sectionCount, false);
    for ($i = $sectionCount; $sectionNum > 0 && $i >= 0; $i--) {
        $code = 2 << ($i - 1);
        if ($sectionNum >= $code) {
            $section[$i] = true;
            $sectionNum -= $code;
        }
    }
    return $section;
}