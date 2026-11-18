<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;

/** FPDI na may kaunting dagdag para sa pag-drawing ng check mark. */
class PdfCanvas extends Fpdi
{
    public function rawOut(string $s): void
    {
        $this->_out($s);
    }
}
