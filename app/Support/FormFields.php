<?php

namespace App\Support;

class FormFields
{
    private static ?array $config = null;

    public static function config(): array
    {
        return self::$config ??= json_decode(
            file_get_contents(storage_path('app/pdf/fields.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    public static function fields(): array
    {
        return self::config()['fields'];
    }

    /**
     * Lahat ng PDF field keys.
     */
    public static function keys(): array
    {
        $keys = [];

        foreach (self::fields() as $field) {
            $keys[$field['name']] = true;
        }

        return array_keys($keys);
    }

    /**
     * Linisin ang submitted form data.
     * Panatilihin din ang additional database fields
     * na wala sa PDF fields.json.
     */
    public static function sanitize(array $data): array
    {
        $out = [];
        $listValues = [];

        foreach (self::fields() as $field) {
            $name = $field['name'];
            $type = $field['type'];

            if ($type === 'boxes') {
                $value = self::clean($data[$name] ?? '');

                if (!empty($field['num'])) {
                    $value = preg_replace('/\D/', '', $value) ?? '';
                }

                $out[$name] = rtrim(
                    mb_substr($value, 0, (int) $field['n'])
                );

            } elseif ($type === 'text') {
                $out[$name] = trim(
                    mb_substr(self::clean($data[$name] ?? ''), 0, 200)
                );

            } elseif (array_key_exists('value', $field) && $field['value'] !== null) {
                $listValues[$name][] = (string) $field['value'];

            } else {
                $out[$name] = filter_var(
                    $data[$name] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );
            }
        }

        // Checkbox groups na may multiple choices.
        foreach ($listValues as $name => $allowed) {
            $sent = is_array($data[$name] ?? null)
                ? array_map(
                    'strval',
                    array_filter($data[$name], 'is_scalar')
                )
                : [];

            $out[$name] = array_values(
                array_intersect($allowed, $sent)
            );
        }

        /*
         * ADDITIONAL DATABASE FIELDS
         * Hindi kailangang idagdag ang mga ito sa fields.json
         * dahil hindi sila direct PDF positioning fields.
         */

        // School year: sy_from + sy_to
        $syFrom = trim((string) ($out['sy_from'] ?? ''));
        $syTo = trim((string) ($out['sy_to'] ?? ''));

        $schoolYear = trim((string) ($data['school_year'] ?? ''));

        if ($schoolYear === '' && $syFrom !== '' && $syTo !== '') {
            $schoolYear = $syFrom . '-' . $syTo;
        }

        $out['school_year'] = $schoolYear !== ''
            ? mb_substr(self::clean($schoolYear), 0, 9)
            : null;

        // Birthdate: bd_month + bd_day + bd_year
        $birthdate = trim((string) ($data['birthdate'] ?? ''));

        if ($birthdate === '') {
            $month = (int) ($out['bd_month'] ?? 0);
            $day = (int) ($out['bd_day'] ?? 0);
            $year = (int) ($out['bd_year'] ?? 0);

            if (
                $month >= 1 &&
                $month <= 12 &&
                $day >= 1 &&
                $year >= 1900 &&
                checkdate($month, $day, $year)
            ) {
                $birthdate = sprintf(
                    '%04d-%02d-%02d',
                    $year,
                    $month,
                    $day
                );
            }
        }

        $out['birthdate'] = $birthdate !== ''
            ? $birthdate
            : null;

        // Sex: based on DepEd checkboxes.
        $sex = trim((string) ($data['sex'] ?? ''));

        if ($sex === '') {
            if (!empty($out['sex_male'])) {
                $sex = 'Male';
            } elseif (!empty($out['sex_female'])) {
                $sex = 'Female';
            }
        }

        $out['sex'] = $sex !== ''
            ? mb_substr(self::clean($sex), 0, 6)
            : null;

        // Student contact information.
        $phone = $data['phone_number'] ?? '';

        $out['phone_number'] = is_scalar($phone)
            ? trim((string) $phone)
            : '';

        $email = $data['email'] ?? '';

        $out['email'] = is_scalar($email)
            ? trim((string) $email)
            : '';

        return $out;
    }

    /**
     * Linisin ang text at alisin ang control characters.
     */
    private static function clean($value): string
    {
        if (!is_scalar($value)) {
            return '';
        }

        $string = preg_replace(
            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u',
            '',
            (string) $value
        ) ?? '';

        return mb_strtoupper($string);
    }
}
