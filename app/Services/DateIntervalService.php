<?php
namespace App\Services;

class DateIntervalService {
    public static function toSecond($duration) {
        $dateInterval = new \DateInterval($duration);
        $seconds = $dateInterval->s + ($dateInterval->i * 60) + ($dateInterval->h * 3600) + ($dateInterval->d * 86400) + ($dateInterval->m * 2592000);
        return $seconds;
    }
}
