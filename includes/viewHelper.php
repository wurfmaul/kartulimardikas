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

/**
 * Computes the right translation for a specific age. E.g. "minutes ago",
 * "days ago", ...
 *
 * @param int $age Number of minutes.
 * @return string The right time frame.
 */
function getTimeFrame($age)
{
    global $l10n;
    if ($age <= 1) return $l10n['one_minute_ago'];
    elseif ($age < 60) return sprintf($l10n['minutes_ago'], $age);
    elseif (intval($age / 60) == 1) return $l10n['one_hour_ago'];
    elseif ($age < 60 * 24) return sprintf($l10n['hours_ago'], $age / 60);
    elseif (intval($age / 60 / 24) == 1) return $l10n['one_day_ago'];
    else return sprintf($l10n['days_ago'], $age / 60 / 24);
}

/**
 * Checks a description for its length and trims it if necessary.
 *
 * @param string $text
 * @return string The skipped description.
 */
function skipDescription($text)
{
    if (sizeof($text) > MAX_DESCRIPTION_LENGTH) {
        return substr($text, 0, MAX_DESCRIPTION_LENGTH) . "...";
    }
    return $text;
}