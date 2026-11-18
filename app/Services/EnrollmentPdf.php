<?php

namespace App\Services;

use App\Support\FormFields;

class EnrollmentPdf
{
    /**
     * Generate completed enrollment PDF using the original DepEd template.
     *
     * Accepts either an array or JSON string from the database.
     */
    public function render(array|string $data): string
    {
        /*
        |--------------------------------------------------------------------------
        | 1. CONVERT DATA TO ARRAY
        |--------------------------------------------------------------------------
        */

        if (is_string($data)) {
            $decoded = json_decode($data, true);

            $data = is_array($decoded) ? $decoded : [];
        }

        /*
        |--------------------------------------------------------------------------
        | 2. LOAD PDF CONFIGURATION
        |--------------------------------------------------------------------------
        */

        $cfg = FormFields::config();

        $W  = (float) $cfg['page_width'];
        $H  = (float) $cfg['page_height'];
        $DY = (float) $cfg['dy'];

        /*
        |--------------------------------------------------------------------------
        | 3. INITIALIZE PDF
        |--------------------------------------------------------------------------
        */

        $pdf = new PdfCanvas('P', 'pt', [$W, $H]);

        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetTitle('Basic Education Enrollment Form');
        $pdf->SetCreator('Enrollment System');

        /*
        |--------------------------------------------------------------------------
        | 4. LOAD ORIGINAL DEPED PDF TEMPLATE
        |--------------------------------------------------------------------------
        */

        $templatePath = storage_path('app/pdf/form-template.pdf');

        if (!file_exists($templatePath)) {
            throw new \RuntimeException(
                'DepEd PDF template not found: ' . $templatePath
            );
        }

        $pages = $pdf->setSourceFile($templatePath);

        /*
        |--------------------------------------------------------------------------
        | 5. OVERLAY STUDENT ANSWERS ON ORIGINAL PDF
        |--------------------------------------------------------------------------
        */

        for ($page = 1; $page <= $pages; $page++) {

            $pdf->AddPage('P', [$W, $H]);

            $template = $pdf->importPage($page);

            $pdf->useTemplate($template, 0, 0, $W, $H);

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            foreach ($cfg['fields'] as $f) {

                if ((int) $f['page'] !== $page) {
                    continue;
                }

                $fieldName = $f['name'];

                $value = $data[$fieldName] ?? null;

                switch ($f['type']) {

                    case 'boxes':
                        $this->boxes($pdf, $f, $value, $DY);
                        break;

                    case 'text':
                        $this->text($pdf, $f, $value, $DY);
                        break;

                    case 'check':
                        $this->check($pdf, $f, $value, $DY);
                        break;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. RETURN GENERATED PDF
        |--------------------------------------------------------------------------
        */

        return $pdf->Output('S');
    }

    /**
     * Render text into individual DepEd boxes.
     */
    private function boxes(
        PdfCanvas $pdf,
        array $f,
        $value,
        float $dy
    ): void {

        if (is_array($value)) {
            $value = implode('', $value);
        }

        $s = trim((string) ($value ?? ''), "\r\n\t");

        if ($s === '') {
            return;
        }

        $fs = 11;

        $pdf->SetFont('Helvetica', 'B', $fs);

        $cw = ($f['x1'] - $f['x0']) / $f['n'];

        $base = $f['y0']
            + $dy
            + ($f['y1'] - $f['y0']) / 2
            + 0.35 * $fs;

        foreach (
            array_slice(mb_str_split($s), 0, (int) $f['n'])
            as $i => $ch
        ) {

            if (trim($ch) === '') {
                continue;
            }

            $ch = $this->enc($ch);

            $x = $f['x0']
                + $cw * $i
                + $cw / 2
                - $pdf->GetStringWidth($ch) / 2;

            $pdf->Text($x, $base, $ch);
        }
    }

    /**
     * Render normal text fields.
     */
    private function text(
        PdfCanvas $pdf,
        array $f,
        $value,
        float $dy
    ): void {

        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        $s = $this->enc(
            trim((string) ($value ?? ''))
        );

        if ($s === '') {
            return;
        }

        $fs = 9.0;

        $wmax = $f['x1'] - $f['x0'] - 4;

        $pdf->SetFont('Helvetica', 'B', $fs);

        // Reduce font size if the text is too wide.
        while (
            $fs > 6 &&
            $pdf->GetStringWidth($s) > $wmax
        ) {
            $fs -= 0.5;

            $pdf->SetFont('Helvetica', 'B', $fs);
        }

        // Trim text only if it still does not fit.
        while (
            strlen($s) > 1 &&
            $pdf->GetStringWidth($s) > $wmax
        ) {
            $s = substr($s, 0, -1);
        }

        $w = $pdf->GetStringWidth($s);

        $x = ($f['align'] ?? 'left') === 'center'
            ? $f['x0'] + ($f['x1'] - $f['x0'] - $w) / 2
            : $f['x0'] + 2;

        $base = $f['y0']
            + $dy
            + ($f['y1'] - $f['y0']) / 2
            + 0.35 * $fs;

        $pdf->Text($x, $base, $s);
    }

    /**
     * Render checkbox marks.
     */
    private function check(
        PdfCanvas $pdf,
        array $f,
        $value,
        float $dy
    ): void {

        if ($f['value'] !== null) {

            $submittedValues = is_array($value)
                ? $value
                : [$value];

            $on = in_array(
                $f['value'],
                $submittedValues,
                true
            );

        } else {

            $on = filter_var(
                $value,
                FILTER_VALIDATE_BOOLEAN
            );
        }

        if (!$on) {
            return;
        }

        $x = $f['x'];
        $y = $f['y'] + $dy;

        $pdf->SetLineWidth(1.5);

        $pdf->rawOut('1 J 1 j');

        $pdf->Line(
            $x + 1.8,
            $y + 5.6,
            $x + 4.4,
            $y + 8.4
        );

        $pdf->Line(
            $x + 4.4,
            $y + 8.4,
            $x + 9.0,
            $y + 1.9
        );
    }

    /**
     * Convert UTF-8 text to Windows-1252 for Helvetica core font.
     */
    private function enc(string $s): string
    {
        return mb_convert_encoding(
            $s,
            'Windows-1252',
            'UTF-8'
        );
    }
}
