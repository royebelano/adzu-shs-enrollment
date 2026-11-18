<?php

namespace App\Support;

/**
 * Ang mga requirements na i-a-upload ng estudyante.
 * Para gawing hindi kailangan ang isang requirement, palitan ang 'required' => false.
 */
class Requirements
{
    public const MAX_KB = 5120; // 5 MB bawat file

    public const ALL = [
        'report_card' => [
            'label' => 'Original Report Card',
            'required' => true,
            'images_only' => false,
        ],
        'good_moral' => [
            'label' => 'Original Good Moral Certificate',
            'required' => true,
            'images_only' => false,
        ],
        'esc_certificate' => [
            'label' => 'Original ESC Certificate (if grantee)',
            'required' => false,
            'images_only' => false,
        ],
        'medical_certificate' => [
            'label' => 'Medical certificate (if with medical condition)',
            'required' => false,
            'images_only' => false,
        ],
        'psa_birth_certificate' => [
            'label' => 'Clear copy of PSA birth certificate',
            'required' => true,
            'images_only' => false,
        ],
        'id_picture' => [
            'label' => '1" x 1" ID picture (any background)',
            'required' => true,
            'images_only' => true,
        ],
        'immunization_book' => [
            'label' => 'Photocopy of baby immunization book',
            'required' => true,
            'images_only' => false,
        ],
        'covid_vaccination_card' => [
            'label' => 'Photocopy of Covid vaccination card',
            'required' => true,
            'images_only' => false,
        ],
    ];

    /** Validation rules para sa docs.* */
    public static function rules(): array
    {
        $rules = [];
        foreach (self::ALL as $key => $r) {
            $rules["docs.$key"] = [
                $r['required'] ? 'required' : 'nullable',
                'file',
                $r['images_only'] ? 'mimes:jpg,jpeg,png' : 'mimes:jpg,jpeg,png,pdf',
                'max:' . self::MAX_KB,
            ];
        }

        return $rules;
    }

    public static function attributes(): array
    {
        $attrs = [];
        foreach (self::ALL as $key => $r) {
            $attrs["docs.$key"] = $r['label'];
        }

        return $attrs;
    }

    public static function messages(): array
    {
        return [
            'docs.*.required' => 'Kailangang i-upload ang :attribute.',
            'docs.*.file'     => 'Hindi tamang file ang :attribute.',
            'docs.*.uploaded' => 'Hindi na-upload ang :attribute. Maaaring masyadong malaki ang file.',
            'docs.*.max'      => 'Masyadong malaki ang :attribute (hanggang 5 MB lang).',
            'docs.*.mimes'    => 'Ang :attribute ay dapat JPG, PNG, o PDF.',
            'docs.id_picture.mimes' => 'Ang ID picture ay dapat JPG o PNG.',
        ];
    }
}
