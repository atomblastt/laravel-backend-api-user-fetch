<?php

namespace App\Helpers;

class GeneralHelper 
{
    /**
     * Get Fullname
     *
     * @param string $firstName String
     * @param string $titleName String
     * @param string $lastName String
     *
     * @return string
     */
    public static function getFullname(?string $titleName = '', ?string $firstName = '', ?string $lastName = ''): string
    {
        $titleName = trim($titleName ?? '');
        $firstName = trim($firstName ?? '');
        $lastName = trim($lastName ?? '');

        return trim(trim($titleName . ' ' . $firstName) . ' ' . $lastName);
    }
}