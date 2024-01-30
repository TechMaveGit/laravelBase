<?php

namespace App\Traits;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Storage;

trait UtilityTrait
{
    /**
     * Format a date according to a specified format.
     *
     * @param string $date
     * @param string $format
     * @return string
     */
    public function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        return (new DateTime($date))->format($format);
    }

    /**
     * Convert a date to a different timezone.
     *
     * @param string $date
     * @param string $timezone
     * @return string
     */
    public function convertTimezone($date, $timezone = 'UTC')
    {
        $date = new DateTime($date, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($timezone));
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Check if a given file is an image.
     *
     * @param string $filePath
     * @return bool
     */
    public function isImage($filePath)
    {
        return in_array(Storage::mimeType($filePath), ['image/jpeg', 'image/png', 'image/gif']);
    }

    // Add more utility functions as needed
}
