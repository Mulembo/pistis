<?php
// licence_key.php

class LicenseManager {

    public static function generateKey($duration_type) {
        $raw = strtoupper(bin2hex(random_bytes(6)));
        $prefix = ($duration_type === '7_days') ? 'TRY' : 'LIC';
        return $prefix . '-' . substr($raw, 0, 4) . '-' . substr($raw, 4, 4) . '-' . substr($raw, 8, 4);
    }

    public static function getDurationDays($duration_type) {
        switch ($duration_type) {
            case '7_days': return 7;
            case '1_month': return 30;
            case '2_months': return 60;
            default: return 30;
        }
    }

    public static function getPrice($duration_type) {
        switch ($duration_type) {
            case '7_days': return 0.00;
            case '1_month': return 10.00;
            case '2_months': return 15.00;
            default: return 0.00;
        }
    }

    public static function calculateExpiryDate($createdAt, $duration_type) {
        $date = new DateTime($createdAt);
        switch ($duration_type) {
            case '7_days': $date->modify('+7 days'); break;
            case '1_month': $date->modify('+1 month'); break;
            case '2_months': $date->modify('+2 months'); break;
            default: $date->modify('+1 month'); break;
        }
        return $date->format('Y-m-d H:i:s');
    }
}