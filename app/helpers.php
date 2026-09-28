<?php

/**
 * Bilingual content helper.
 * Returns the Arabic version of a field when locale is 'ar' and it exists,
 * otherwise falls back to the English (default) field.
 *
 * Usage in Blade:  {{ t($project, 'title') }}
 */
if (!function_exists('t')) {
    function t($item, string $field): string
    {
        if (app()->getLocale() === 'ar') {
            $arField = $field . '_ar';
            $arValue = is_array($item) ? ($item[$arField] ?? '') : ($item->$arField ?? '');
            if (!empty(trim((string) $arValue))) {
                return (string) $arValue;
            }
        }
        $value = is_array($item) ? ($item[$field] ?? '') : ($item->$field ?? '');
        return (string) $value;
    }
}

/**
 * Bilingual settings helper.
 * Usage in Blade:  {{ ts($settings, 'hero_name') }}
 */
if (!function_exists('ts')) {
    function ts(array $settings, string $key): string
    {
        if (app()->getLocale() === 'ar') {
            $arKey   = $key . '_ar';
            $arValue = $settings[$arKey] ?? '';
            if (!empty(trim((string) $arValue))) {
                return $arValue;
            }
        }
        return $settings[$key] ?? '';
    }
}

/**
 * Resolve a project cover for the browser.
 * Supports local public paths, normal image URLs, and Google Drive share links.
 */
if (!function_exists('project_image_url')) {
    function project_image_url(?string $image): string
    {
        $image = trim((string) $image);
        if ($image === '') {
            return '';
        }

        if (preg_match('~drive\.google\.com/(?:file/d/|open\?id=)([^/?&]+)~i', $image, $match)) {
            return 'https://drive.google.com/uc?export=view&id=' . rawurlencode($match[1]);
        }

        if (preg_match('~^https?://~i', $image)) {
            return $image;
        }

        return asset(ltrim($image, '/'));
    }
}
