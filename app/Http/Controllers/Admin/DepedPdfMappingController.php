<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepedForm;
use Illuminate\Http\Request;

class DepedPdfMappingController extends Controller
{
    public function edit(DepedForm $form)
    {
        abort_unless($form->file_path, 404);

        $schema = $form->form_fields ?? [];
        $fields = [];

        foreach ($schema as $section) {
            foreach (($section['fields'] ?? []) as $field) {
                $type = $field['type'] ?? 'text';

                /*
                 * Every select option and checkbox option
                 * receives an individual PDF mapping.
                 */
                if (in_array($type, ['checkbox', 'select'], true)) {
                    foreach (($field['options'] ?? []) as $value => $label) {
                        $fields[] = [
                            'name' => $field['name'],
                            'label' => ($field['label'] ?? $field['name'])
                                . ' — '
                                . $label,
                            'type' => 'checkbox_option',
                            'option_value' => (string) $value,
                        ];
                    }

                    continue;
                }

                $fields[] = [
                    'name' => $field['name'],
                    'label' => $field['label'] ?? $field['name'],
                    'type' => $type,
                    'options' => [],
                ];
            }
        }

        return view('admin.pdf-mapping', [
            'form' => $form,
            'fields' => $fields,
            'mappings' => $schema['pdf_mappings'] ?? [],
            'pdfUrl' => asset('storage/' . $form->file_path),
        ]);
    }

    public function save(Request $request, DepedForm $form)
    {
        $data = $request->validate([
            'mappings' => ['sometimes', 'array'],

            'mappings.*.key' => [
                'required',
                'string',
                'max:190',
            ],

            'mappings.*.field' => [
                'required',
                'string',
                'max:100',
            ],

            'mappings.*.label' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mappings.*.page' => [
                'required',
                'integer',
                'min:1',
            ],

            'mappings.*.x' => [
                'required',
                'numeric',
                'between:0,1',
            ],

            'mappings.*.y' => [
                'required',
                'numeric',
                'between:0,1',
            ],

            'mappings.*.w' => [
                'nullable',
                'numeric',
                'between:0.01,1',
            ],

            'mappings.*.font_size' => [
                'nullable',
                'numeric',
                'between:4,24',
            ],

            'mappings.*.kind' => [
                'required',
                'in:text,checkbox',
            ],

            'mappings.*.option_value' => [
                'nullable',
                'string',
                'max:255',
            ],

            'mappings.*.align' => [
                'nullable',
                'in:L,C,R',
            ],

            'mappings.*.line_height' => [
                'nullable',
                'numeric',
                'between:2,12',
            ],
        ]);

        $schema = $form->form_fields ?? [];

        // Keep all existing form sections and update only PDF mappings.
        $schema['pdf_mappings'] = array_values(
            $data['mappings'] ?? []
        );

        $form->form_fields = $schema;
        $form->save();

        return back()->with(
            'success',
            'PDF field mappings and checkbox positions saved successfully.'
        );
    }
}
