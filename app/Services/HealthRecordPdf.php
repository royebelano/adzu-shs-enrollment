<?php

namespace App\Services;

use App\Models\Enrollment;
use Illuminate\Support\Carbon;
use setasign\Fpdi\Fpdi;

/**
 * Fills the SHS Infirmary "Student's Health & Oral Examination Record"
 * with the student's basic details. Only the header fields are filled;
 * the medical history, physical exam and dental exam are left blank for
 * the school doctor / dentist to complete by hand after printing.
 */
class HealthRecordPdf
{
    /** Template inside storage/ (saved with a classic xref table so FPDI can read it). */
    public const TEMPLATE = 'app/pdf/health-record-template.pdf';

    private const PAGE_W = 612.0;
    private const PAGE_H = 792.0;

    /**
     * Position of each value on page 1 (PDF points, origin top-left).
     * x = left edge of the text, y = text baseline, w = space available.
     */
    private const FIELDS = [
        'name'      => ['label' => 'Name of Student',               'x' => 28.0,  'y' => 152.0, 'w' => 282.0],
        'birthdate' => ['label' => 'Date of Birth',                 'x' => 320.0, 'y' => 152.0, 'w' => 134.0],
        'birthplace'=> ['label' => 'Place of Birth',                'x' => 464.0, 'y' => 152.0, 'w' => 126.0],
        'address'   => ['label' => 'Address in ZC',                 'x' => 28.0,  'y' => 180.0, 'w' => 282.0],
        'religion'  => ['label' => 'Religion',                      'x' => 320.0, 'y' => 180.0, 'w' => 134.0],
        'gender'    => ['label' => 'Gender',                        'x' => 464.0, 'y' => 180.0, 'w' => 64.0],
        'age'       => ['label' => 'Age',                           'x' => 537.0, 'y' => 180.0, 'w' => 54.0],
        'father'    => ['label' => 'Name of Father',                'x' => 92.0,  'y' => 194.2, 'w' => 309.0],
        'mother'    => ['label' => 'Name of Mother',                'x' => 92.0,  'y' => 210.2, 'w' => 309.0],
        'emergency' => ['label' => 'Emergency primary contact',     'x' => 28.0,  'y' => 246.5, 'w' => 229.0],
        'mobile'    => ['label' => "Student's mobile number",       'x' => 267.3, 'y' => 246.5, 'w' => 134.0],
    ];

    /** Labels for the on-screen preview. */
    public static function labels(): array
    {
        return array_map(fn ($f) => $f['label'], self::FIELDS);
    }

    /* ------------------------------------------------------------------
     | Values
     * ------------------------------------------------------------------ */

    /** The text that will be printed in each field. */
    public function values(Enrollment $e): array
    {
        $birth = $this->birthdate($e);

        $age = $birth
            ? (string) max(0, $birth->age)
            : $this->get($e, 'age');

        $address = collect([
            $this->get($e, 'cur_house'),
            $this->get($e, 'cur_street'),
            $this->get($e, 'cur_barangay'),
            $this->get($e, 'cur_city'),
            $this->get($e, 'cur_province'),
        ])->filter(fn ($v) => $v !== '')->implode(', ');

        return [
            'name' => $this->person(
                $e->last_name, $e->first_name, $e->middle_name, $e->extension_name
            ),
            'birthdate'  => $birth ? $birth->format('F d, Y') : '',
            'birthplace' => $this->get($e, 'place_of_birth'),
            'address'    => $address,
            'religion'   => $this->get($e, 'religion'),
            'gender'     => $this->get($e, 'sex') ?: $this->sexFromCheckboxes($e),
            'age'        => $age,
            'father'     => $this->parent($e, 'father'),
            'mother'     => $this->parent($e, 'mother'),
            'emergency'  => $this->emergencyContact($e),
            'mobile'     => $this->get($e, 'phone_number'),
        ];
    }

    private function get(Enrollment $e, string $key): string
    {
        $value = $e->getAttribute($key);

        if (blank($value)) {
            $data = $e->data;

            if (is_string($data)) {
                $data = json_decode($data, true);
            }

            $value = is_array($data) ? ($data[$key] ?? null) : null;
        }

        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function person(?string $last, ?string $first, ?string $middle, ?string $ext = null): string
    {
        $given = trim(implode(' ', array_filter(
            [$first, $middle, $ext],
            fn ($v) => filled($v)
        )));

        $last = trim((string) $last);

        return ($last !== '' && $given !== '')
            ? $last . ', ' . $given
            : trim($last . $given);
    }

    private function parent(Enrollment $e, string $who): string
    {
        return $this->person(
            $this->get($e, $who . '_last'),
            $this->get($e, $who . '_first'),
            $this->get($e, $who . '_middle')
        );
    }

    /** Guardian first, then mother, then father. */
    private function emergencyContact(Enrollment $e): string
    {
        foreach (['guardian', 'mother', 'father'] as $who) {
            $name = $this->parent($e, $who);

            if ($name === '') {
                continue;
            }

            $contact = $this->get($e, $who . '_contact');

            return $contact !== '' ? $name . ' (' . $contact . ')' : $name;
        }

        return '';
    }

    private function sexFromCheckboxes(Enrollment $e): string
    {
        if ($this->get($e, 'sex_male') !== '') {
            return 'Male';
        }

        return $this->get($e, 'sex_female') !== '' ? 'Female' : '';
    }

    private function birthdate(Enrollment $e): ?Carbon
    {
        try {
            if ($e->birthdate) {
                return Carbon::parse($e->birthdate);
            }

            $raw = $this->get($e, 'birthdate');

            if ($raw !== '') {
                return Carbon::parse($raw);
            }

            $y = (int) $this->get($e, 'bd_year');
            $m = (int) $this->get($e, 'bd_month');
            $d = (int) $this->get($e, 'bd_day');

            if ($y > 0 && checkdate($m, $d, $y)) {
                return Carbon::create($y, $m, $d);
            }
        } catch (\Throwable $ex) {
            // Unreadable date: leave the field blank for the doctor.
        }

        return null;
    }

    /* ------------------------------------------------------------------
     | PDF
     * ------------------------------------------------------------------ */

    public function render(Enrollment $enrollment): string
    {
        $template = storage_path(self::TEMPLATE);

        if (!is_file($template)) {
            throw new \RuntimeException(
                'Health record template not found: ' . $template
            );
        }

        $values = $this->values($enrollment);

        $pdf = new Fpdi('P', 'pt', [self::PAGE_W, self::PAGE_H]);

        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetTitle('Student Health and Oral Examination Record');
        $pdf->SetCreator('Enrollment System');

        $pages = $pdf->setSourceFile($template);

        for ($page = 1; $page <= $pages; $page++) {
            $pdf->AddPage('P', [self::PAGE_W, self::PAGE_H]);

            $pdf->useTemplate(
                $pdf->importPage($page),
                0, 0, self::PAGE_W, self::PAGE_H
            );

            // Page 1 holds the header fields. Page 2 (physical and dental
            // exam) is intentionally left blank for the doctor.
            if ($page === 1) {
                $this->fill($pdf, $values);
            }
        }

        return $pdf->Output('S');
    }

    private function fill(Fpdi $pdf, array $values): void
    {
        $pdf->SetTextColor(0, 0, 0);

        foreach (self::FIELDS as $key => $spot) {
            $text = $this->encode($values[$key] ?? '');

            if ($text === '') {
                continue;
            }

            // Shrink long values so they stay inside their box.
            $size = 10.0;

            $pdf->SetFont('Helvetica', '', $size);

            while ($size > 6.0 && $pdf->GetStringWidth($text) > $spot['w']) {
                $size -= 0.5;
                $pdf->SetFont('Helvetica', '', $size);
            }

            // Still too long at the smallest size: cut it with an ellipsis.
            while (strlen($text) > 4 && $pdf->GetStringWidth($text) > $spot['w']) {
                $text = rtrim(substr($text, 0, -4)) . '...';
            }

            $pdf->Text($spot['x'], $spot['y'], $text);
        }
    }

    /** FPDF core fonts use Windows-1252, so convert from UTF-8. */
    private function encode(string $text): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');

        if ($text === '') {
            return '';
        }

        $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $text);

        return $converted === false ? '' : $converted;
    }
}
