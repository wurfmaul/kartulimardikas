<?php
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