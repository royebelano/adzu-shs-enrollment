<?php

namespace App\Services;

use App\Models\DepedForm;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use RuntimeException;

class DepedWordTemplateService
{
    public function generate(DepedForm $form, string $outputPath): string
    {
        $phpWord = new PhpWord();

        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10);

        $section = $phpWord->addSection([
            'marginTop' => 700,
            'marginBottom' => 700,
            'marginLeft' => 850,
            'marginRight' => 850,
        ]);

        // Document heading
        $section->addText(
            'STUDENT ENROLLMENT FORM',
            ['bold' => true, 'size' => 16],
            ['alignment' => Jc::CENTER]
        );

        $section->addText(
            $form->title,
            ['bold' => true, 'size' => 12],
            ['alignment' => Jc::CENTER]
        );

        $section->addTextBreak();

        $section->addText(
            'INSTRUCTIONS: Fill in the blank fields. Do not delete field identifiers or labels.',
            ['italic' => true, 'size' => 9]
        );

        // IMPORTANT: form_fields is already an array of sections.
        $sections = $form->form_fields ?? [];

        foreach ($sections as $group) {

            $section->addTextBreak();

            $section->addText(
                $group['title'] ?? 'Enrollment Information',
                ['bold' => true, 'size' => 12]
            );

            $table = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '999999',
                'cellMargin' => 100,
            ]);

            foreach (($group['fields'] ?? []) as $field) {

                $name = $field['name'] ?? null;
                $label = $field['label'] ?? null;
                $type = $field['type'] ?? 'text';

                if (!$name || !$label) {
                    continue;
                }

                $table->addRow();

                $table->addCell(4000)->addText(
                    $label,
                    ['bold' => true]
                );

                $answerCell = $table->addCell(5500);

                // Checkbox fields
                if ($type === 'checkbox') {

                    foreach (($field['options'] ?? []) as $value => $optionLabel) {

                        $answerCell->addText(
                            '[ ] ' . $optionLabel .
                            ' [[CHECK:' . $name . ':' . $value . ']]'
                        );
                    }

                    continue;
                }

                // Select / radio fields
                if (in_array($type, ['select', 'radio'], true)) {

                    foreach (($field['options'] ?? []) as $value => $optionLabel) {

                        $answerCell->addText(
                            '[ ] ' . $optionLabel .
                            ' [[OPTION:' . $name . ':' . $value . ']]'
                        );
                    }

                    continue;
                }

                // Text, date, number and other fields
                $answerCell->addText(
                    '[[FIELD:' . $name . ']]'
                );
            }
        }

        $section->addTextBreak();

        $section->addText(
            'END OF STUDENT FORM',
            ['bold' => true, 'size' => 9],
            ['alignment' => Jc::CENTER]
        );

        $directory = dirname($outputPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($outputPath);

        if (!file_exists($outputPath)) {
            throw new RuntimeException('Failed to generate Word template.');
        }

        return $outputPath;
    }
}
