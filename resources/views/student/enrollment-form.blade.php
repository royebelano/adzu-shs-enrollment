
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Enrollment Form</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 30px 15px;
    background: #f1f5f9;
    color: #1e293b;
    font-family: Arial, sans-serif;
}

.container {
    max-width: 1050px;
    margin: auto;
}

.header,
.section {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 20px;
    border: 1px solid #e2e8f0;
}

.header {
    padding: 28px;
}

.header h1 {
    margin: 0 0 8px;
    font-size: 25px;
    color: #0f172a;
}

.header p {
    color: #64748b;
    margin: 0;
    line-height: 1.6;
}

.section h2 {
    font-size: 18px;
    margin: 0 0 22px;
    padding-bottom: 13px;
    border-bottom: 1px solid #e2e8f0;
    color: #0f172a;
}

.fields {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.field {
    min-width: 0;
}

.field.full {
    grid-column: 1 / -1;
}

label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 8px;
    color: #334155;
}

.required {
    color: #dc2626;
}

input,
select,
textarea {
    width: 100%;
    padding: 12px 13px;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    font-size: 14px;
    background: #fff;
    color: #0f172a;
    outline: none;
}

input:focus,
select:focus,
textarea:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
}

textarea {
    min-height: 90px;
    resize: vertical;
}

.checkbox-group {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.checkbox-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    cursor: pointer;
    font-weight: 400;
    margin: 0;
}

.checkbox-option input {
    width: 17px;
    height: 17px;
    margin: 0;
}

.error {
    color: #dc2626;
    font-size: 12px;
    margin-top: 6px;
}

.alert {
    padding: 14px 16px;
    border-radius: 8px;
    margin-bottom: 18px;
    font-size: 14px;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
}

.actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 20px;
}

button {
    border: none;
    padding: 13px 25px;
    border-radius: 7px;
    background: #1d4ed8;
    color: white;
    font-weight: 700;
    cursor: pointer;
    font-size: 14px;
}

button:hover {
    background: #1e40af;
}

.note {
    font-size: 12px;
    color: #64748b;
    margin-top: 12px;
}

@media(max-width: 650px) {

    .fields,
    .checkbox-group {
        grid-template-columns: 1fr;
    }

    .section {
        padding: 18px;
    }

    .header {
        padding: 20px;
    }

}

</style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">

        <h1>Student Enrollment Form</h1>

        @if($activeForm)

            <p>
                {{ $activeForm->title }}

                @if($activeForm->version_tag)
                    — {{ $activeForm->version_tag }}
                @endif
            </p>

        @else

            <p>Online enrollment application</p>

        @endif

        <p class="note">
            Please provide accurate information.
            Fields marked with
            <span class="required">*</span>
            are required.
        </p>

    </div>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

            @if(session('generated_pdf_url'))

                <br><br>

                <a
                    href="{{ session('generated_pdf_url') }}"
                    target="_blank"
                    style="font-weight:bold;color:#166534"
                >
                    Download Completed Enrollment PDF
                </a>

            @endif

        </div>

    @endif

    <!-- VALIDATION ERRORS -->
    @if($errors->any())

        <div class="alert alert-error">

            <strong>Please check the following:</strong>

            <ul style="margin-bottom:0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    @if(!$activeForm)

        <div class="section">

            <h2>No Active Enrollment Form</h2>

            <p>
                The enrollment form is currently unavailable.
                Please contact the SHS Admissions Office.
            </p>

        </div>

    @else

        <form
            method="POST"
            action="{{ route('student.enrollment.submit') }}"
        >

            @csrf

            @forelse($sections as $section)

                <div class="section">

                    <h2>
                        {{ $section['title'] ?? 'Enrollment Information' }}
                    </h2>

                    <div class="fields">

                        @foreach(($section['fields'] ?? []) as $field)

                            @php

                                $name = $field['name'] ?? '';

                                $label = $field['label'] ?? $name;

                                $type = $field['type'] ?? 'text';

                                $options = $field['options'] ?? [];

                                $value = old(
                                    'answers.' . $name,
                                    $type === 'checkbox' ? [] : ''
                                );

                                $isRequired = in_array(
                                    $name,
                                    ['last_name', 'first_name'],
                                    true
                                );

                            @endphp

                            <div class="field {{ in_array($type, ['checkbox', 'textarea'], true) ? 'full' : '' }}">

                                <label>

                                    {{ $label }}

                                    @if($isRequired)
                                        <span class="required">*</span>
                                    @endif

                                </label>

                                {{-- SELECT --}}
                                @if($type === 'select')

                                    <select
                                        name="answers[{{ $name }}]"
                                        {{ $isRequired ? 'required' : '' }}
                                    >

                                        <option value="">
                                            Select {{ $label }}
                                        </option>

                                        @foreach($options as $optionValue => $optionLabel)

                                            <option
                                                value="{{ $optionValue }}"
                                                {{ (string) $value === (string) $optionValue ? 'selected' : '' }}
                                            >
                                                {{ $optionLabel }}
                                            </option>

                                        @endforeach

                                    </select>

                                {{-- CHECKBOX --}}
                                @elseif($type === 'checkbox')

                                    <div class="checkbox-group">

                                        @foreach($options as $optionValue => $optionLabel)

                                            <label class="checkbox-option">

                                                <input
                                                    type="checkbox"
                                                    name="answers[{{ $name }}][]"
                                                    value="{{ $optionValue }}"

                                                    {{ in_array(
                                                        (string) $optionValue,
                                                        array_map('strval', (array) $value),
                                                        true
                                                    ) ? 'checked' : '' }}
                                                >

                                                <span>
                                                    {{ $optionLabel }}
                                                </span>

                                            </label>

                                        @endforeach

                                    </div>

                                {{-- TEXTAREA --}}
                                @elseif($type === 'textarea')

                                    <textarea
                                        name="answers[{{ $name }}]"
                                        placeholder="Enter {{ strtolower($label) }}"
                                    >{{ $value }}</textarea>

                                {{-- TEXT / DATE / NUMBER / EMAIL / TEL --}}
                                @else

                                    @php

                                        $inputType = in_array(
                                            $type,
                                            ['text', 'number', 'date', 'email', 'tel'],
                                            true
                                        ) ? $type : 'text';

                                    @endphp

                                    <input
                                        type="{{ $inputType }}"
                                        name="answers[{{ $name }}]"
                                        value="{{ $value }}"
                                        placeholder="Enter {{ strtolower($label) }}"
                                        {{ $isRequired ? 'required' : '' }}
                                    >

                                @endif

                                @error('answers.' . $name)

                                    <div class="error">
                                        {{ $message }}
                                    </div>

                                @enderror

                            </div>

                        @endforeach

                    </div>

                </div>

            @empty

                <div class="section">

                    <p>
                        No enrollment fields have been configured yet.
                    </p>

                </div>

            @endforelse

            <!-- SUBMIT -->
            <div class="section">

                <h2>Review and Submit</h2>

                <p class="note">
                    Please review all information before submitting.
                    Your enrollment information will be recorded
                    by the SHS Admissions Office.
                </p>

                <div class="actions">

                    <button type="submit">
                        Submit Enrollment
                    </button>

                </div>

            </div>

        </form>

    @endif

</div>

</body>
</html>
