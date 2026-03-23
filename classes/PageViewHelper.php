<?php

class PageViewHelper
{
    public static function value(?string $value, string $fallback = ''): string
    {
        return htmlspecialchars($value ?? $fallback);
    }

    public static function activityTitle(Activity $activity): string
    {
        $opmerking = trim($activity->getOpmerkingen());

        return htmlspecialchars($opmerking !== '' ? $opmerking : 'Activiteit');
    }
}
