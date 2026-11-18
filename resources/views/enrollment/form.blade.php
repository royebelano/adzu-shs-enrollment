
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($walkIn ?? false) ? 'Walk-in Enrollment' : 'Basic Education Enrollment Form' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/enrollment.css') }}?v={{ filemtime(public_path('css/enrollment.css')) }}">
</head>

<body>

{{-- ===================== WIZARD HEADER ===================== --}}

<header class="sched-head no-print" id="schedHead">
    <p>
        {{ $schoolName ?? 'Ateneo de Zamboanga University — Senior High School' }}
        · SY {{ $schoolYear ?? '' }}
    </p>

    <div class="wizard-header">
        <ol class="wizard-steps" id="wizardSteps">
            <li class="{{ ($walkIn ?? false) ? '' : 'active' }}" data-wizard-step="1">
                <span class="step-number">1</span>
                <em>Schedule</em>
            </li>

            <li class="{{ ($walkIn ?? false) ? 'active' : '' }}" data-wizard-step="2">
                <span class="step-number">2</span>
                <em>Enrollment Form</em>
            </li>

            <li data-wizard-step="3">
                <span class="step-number">3</span>
                <em>Review & Confirm</em>
            </li>
        </ol>
    </div>
</header>

{{-- ===================== STEP 1: SCHEDULE ===================== --}}

@unless($walkIn ?? false)
<main class="sched no-print" id="schedulePanel">

    <h2>Step 1: Choose an Appointment Schedule</h2>

    <p class="sched-sub">
        Select an available date and a one-hour appointment slot.
        Availability is updated in real time.
    </p>

    @if(count($apptDates))
        <label class="sched-label" for="apptDate">
            APPOINTMENT DATE
        </label>

        <select id="apptDate" class="sched-select">
            @foreach($apptDates as $d)
                <option value="{{ $d['value'] }}" @disabled($d['full'])>
                    {{ $d['label'] }}{{ $d['full'] ? ' (Full)' : '' }}
                </option>
            @endforeach
        </select>

        <div class="sched-label">SELECT AN HOURLY TIME SLOT</div>

        <div id="apptSlots" class="sched-slots" role="radiogroup" aria-label="Hourly appointment slots"></div>

        <div id="apptMsg" class="sched-msg" aria-live="polite"></div>

        <div class="sched-warn">
            <span aria-hidden="true">⚠</span>
            <span>
                Schedules marked <b>Walk-in only</b> are designated by the
                Admissions Office and cannot be reserved online.
            </span>
        </div>

        <button type="button" id="apptContinue" class="sched-continue" disabled>
            Continue to Student Details →
        </button>
    @else
        <div class="sched-warn">
            <span aria-hidden="true">⚠</span>
            <span>
                There are currently no available online appointment schedules.
                Please visit the Admissions Office for walk-in enrollment.
            </span>
        </div>
    @endif

</main>
@endunless

{{-- ===================== STEP 2: ENROLLMENT FORM ===================== --}}

<div id="formWrap" @unless($walkIn ?? false) hidden @endunless>

    @unless($walkIn ?? false)
        <div class="appt-summary no-print" id="apptSummary" hidden>
            <span>
                Appointment:
                <strong id="apptSummaryText"></strong>
            </span>

            <button type="button" id="apptChange">Change Schedule</button>
        </div>
    @endunless

    {{-- TOOLBAR --}}

    <div class="toolbar no-print">

        <button type="button" id="btnPrint">Print Form</button>

        <button type="button" id="btnClear">New Form</button>

        @if($walkIn ?? false)
            <a href="{{ route('admin.index') }}" class="btnlink">
                Back to Step 1
            </a>
        @else
            <a href="{{ route('enrollment.track') }}" target="_blank" class="btnlink">
                Track My Application
            </a>
        @endif

        <button type="button" id="btnSave" class="primary" hidden>
            Save Application
        </button>

        <span id="saveMsg" class="msg"></span>

    </div>

    {{-- WALK-IN NOTICE --}}

    @if($walkIn ?? false)
        <div class="staffbar no-print">
            Walk-in enrollment. This form is completed by Admissions Office staff.
            Document uploads are optional because the student will provide physical copies.
        </div>
    @endif

    <form id="enrollForm" autocomplete="off" onsubmit="return false;">

        {{-- =====================================================
             PAGE 1
        ====================================================== --}}

        <section class="sheet">

            <img class="bg" src="{{ asset('forms/page1.svg') }}" alt="">

            <div class="boxes" data-len="4" data-type="num" data-name="sy_from"
                 style="--n:4;left:18.4551%;top:14.0338%;width:9.7565%;height:2.1371%"></div>

            <div class="boxes" data-len="4" data-type="num" data-name="sy_to"
                 style="--n:4;left:30.8648%;top:14.0338%;width:9.8069%;height:2.1371%"></div>

            <div class="boxes" data-len="2" data-type="num" data-name="grade_level"
                 style="--n:2;left:30.9488%;top:18.6999%;width:4.9034%;height:2.1371%"></div>

            <input type="checkbox" class="cb" name="graded"
                   style="left:6.1293%;top:19.2817%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="non_graded"
                   style="left:6.1293%;top:21.5850%;width:1.7464%;height:1.2348%">

            <div class="boxes" data-len="12" data-type="num" data-name="lrn"
                 style="--n:12;left:55.4492%;top:15.7554%;width:28.8497%;height:2.1371%"></div>

            <input type="checkbox" class="cb" name="attended_elp"
                   style="left:55.4492%;top:21.4544%;width:1.7464%;height:1.2348%">

            <input type="text" class="tf" name="elp_specify"
                   style="left:58.6902%;top:23.3066%;width:35.1973%;height:1.6622%">

            <input type="text" class="tf" name="psa_no"
                   style="left:43.3417%;top:26.6904%;width:49.9076%;height:1.7809%">

            <div class="boxes" data-len="20" data-name="last_name"
                 style="--n:20;left:5.6423%;top:31.2021%;width:57.2124%;height:2.4933%"></div>

            <div class="boxes" data-len="20" data-name="first_name"
                 style="--n:20;left:5.5584%;top:36.0107%;width:57.1956%;height:2.4933%"></div>

            <div class="boxes" data-len="20" data-name="middle_name"
                 style="--n:20;left:5.5584%;top:40.8311%;width:57.1956%;height:2.4933%"></div>

            <div class="boxes" data-len="8" data-name="extension_name"
                 style="--n:8;left:5.5584%;top:45.6397%;width:21.9647%;height:2.5052%"></div>

            <div class="boxes" data-len="2" data-type="num" data-chain="bd" data-name="bd_month"
                 style="--n:2;left:66.5057%;top:31.1309%;width:5.4597%;height:2.4933%"></div>

            <div class="boxes" data-len="2" data-type="num" data-chain="bd" data-name="bd_day"
                 style="--n:2;left:74.6952%;top:31.1309%;width:5.4597%;height:2.4933%"></div>

            <div class="boxes" data-len="4" data-type="num" data-chain="bd" data-name="bd_year"
                 style="--n:4;left:82.8847%;top:31.1309%;width:10.9193%;height:2.4933%"></div>

            <div class="boxes" data-len="2" data-type="num" data-name="age"
                 style="--n:2;left:66.4819%;top:35.9869%;width:5.5751%;height:2.4933%"></div>

            <input type="checkbox" class="cb" name="sex_male" data-exclusive="sex"
                   style="left:75.0126%;top:36.5568%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="sex_female" data-exclusive="sex"
                   style="left:82.9891%;top:36.4737%;width:1.7464%;height:1.2348%">

            <input type="text" class="tf" name="place_of_birth"
                   style="left:66.4987%;top:40.9380%;width:27.3720%;height:2.4933%">

            <input type="text" class="tf" name="religion"
                   style="left:66.4987%;top:45.6753%;width:27.3720%;height:2.4933%">

            <input type="text" class="tf" name="mother_tongue"
                   style="left:66.4987%;top:50.3176%;width:27.3720%;height:2.4933%">

            <input type="checkbox" class="cb" name="ip_yes" data-exclusive="ip"
                   style="left:6.6163%;top:51.4099%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="ip_no" data-exclusive="ip"
                   style="left:13.1486%;top:51.4099%;width:1.7464%;height:1.2348%">

            <input type="text" class="tf" name="ip_specify"
                   style="left:33.8875%;top:50.9706%;width:30.2603%;height:1.6622%">

            <input type="checkbox" class="cb" name="fourps_yes" data-exclusive="fourps"
                   style="left:30.0084%;top:53.6420%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="fourps_no" data-exclusive="fourps"
                   style="left:36.5239%;top:53.6420%;width:1.7464%;height:1.2348%">

            <div class="boxes" data-len="21" data-type="num" data-name="fourps_id"
                 style="--n:21;left:7.5527%;top:57.5245%;width:57.3264%;height:2.4933%"></div>

            <input type="text" class="tf" name="cur_house"
                   style="left:5.3233%;top:63.6628%;width:17.5987%;height:1.8166%">

            <input type="text" class="tf" name="cur_street"
                   style="left:23.5936%;top:63.6628%;width:36.9941%;height:1.8166%">

            <input type="text" class="tf" name="cur_barangay"
                   style="left:61.2594%;top:63.6628%;width:33.2494%;height:1.8166%">

            <input type="text" class="tf" name="cur_city"
                   style="left:5.1721%;top:66.7023%;width:23.4089%;height:1.8997%">

            <input type="text" class="tf" name="cur_province"
                   style="left:29.2527%;top:66.7023%;width:23.4929%;height:1.8997%">

            <input type="text" class="tf" name="cur_country" value="PHILIPPINES"
                   style="left:53.4173%;top:66.7023%;width:25.5584%;height:1.8997%">

            <input type="text" class="tf" name="cur_zip"
                   style="left:79.6474%;top:66.7023%;width:15.0630%;height:1.8997%">

            <input type="checkbox" class="cb" id="sameAddr" name="same_address_yes" data-exclusive="same"
                   style="left:49.8573%;top:68.9819%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="same_address_no" data-exclusive="same"
                   style="left:56.3896%;top:68.9819%;width:1.7464%;height:1.2348%">

            <input type="text" class="tf" name="per_house"
                   style="left:5.3233%;top:71.8670%;width:17.5987%;height:1.7928%">

            <input type="text" class="tf" name="per_street"
                   style="left:23.5936%;top:71.8670%;width:36.9941%;height:1.7928%">

            <input type="text" class="tf" name="per_barangay"
                   style="left:61.2594%;top:71.8670%;width:33.2494%;height:1.7928%">

            <input type="text" class="tf" name="per_city"
                   style="left:5.1721%;top:74.8471%;width:23.4089%;height:1.9234%">

            <input type="text" class="tf" name="per_province"
                   style="left:29.2527%;top:74.8471%;width:23.4929%;height:1.9234%">

            <input type="text" class="tf" name="per_country"
                   style="left:53.4173%;top:74.8471%;width:25.5584%;height:1.9234%">

            <input type="text" class="tf" name="per_zip"
                   style="left:79.6474%;top:74.8471%;width:15.0630%;height:1.9234%">

            <input type="text" class="tf" name="father_last"
                   style="left:5.5082%;top:83.4907%;width:21.8143%;height:2.1134%">

            <input type="text" class="tf" name="father_first"
                   style="left:27.9943%;top:83.4907%;width:21.7421%;height:2.1134%">

            <input type="text" class="tf" name="father_middle"
                   style="left:50.4081%;top:83.4907%;width:21.7421%;height:2.1134%">

            <input type="text" class="tf" name="father_contact"
                   style="left:72.8219%;top:83.4907%;width:22.0989%;height:2.1134%">

            <input type="text" class="tf" name="mother_last"
                   style="left:5.5082%;top:88.4061%;width:21.8143%;height:2.1134%">

            <input type="text" class="tf" name="mother_first"
                   style="left:27.9943%;top:88.4061%;width:21.7421%;height:2.1134%">

            <input type="text" class="tf" name="mother_middle"
                   style="left:50.4081%;top:88.4061%;width:21.7421%;height:2.1134%">

            <input type="text" class="tf" name="mother_contact"
                   style="left:72.8219%;top:88.4061%;width:22.0989%;height:2.1134%">

            <input type="text" class="tf" name="guardian_last"
                   style="left:5.5082%;top:93.4164%;width:21.8143%;height:2.1134%">

            <input type="text" class="tf" name="guardian_first"
                   style="left:27.9943%;top:93.4164%;width:21.7421%;height:2.1134%">

            <input type="text" class="tf" name="guardian_middle"
                   style="left:50.4081%;top:93.4164%;width:21.7421%;height:2.1134%">

            <input type="text" class="tf" name="guardian_contact"
                   style="left:72.8219%;top:93.4164%;width:22.0989%;height:2.1134%">

        </section>

        {{-- =====================================================
             PAGE 2
        ====================================================== --}}

        <section class="sheet last">

            <img class="bg" src="{{ asset('forms/page2.svg') }}" alt="">

            <input type="checkbox" class="cb" name="sned_yes" data-exclusive="sned"
                   style="left:58.4551%;top:6.6845%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="sned_no" data-exclusive="sned"
                   style="left:64.9706%;top:6.6845%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Attention Deficit Hyperactivity Disorder"
                   style="left:8.6146%;top:12.0036%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Autism Spectrum Disorder"
                   style="left:8.6146%;top:13.8083%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Cerebral Palsy"
                   style="left:8.6482%;top:15.6961%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Emotional-Behavior Disorder"
                   style="left:8.6482%;top:17.5007%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Hearing Impairment"
                   style="left:8.6146%;top:19.3292%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Intellectual Disability"
                   style="left:36.0202%;top:11.9917%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Learning Disability"
                   style="left:36.0705%;top:13.8795%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Multiple Disabilities"
                   style="left:36.0705%;top:15.6842%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Orthopedic/Physical Handicap"
                   style="left:36.0705%;top:17.4533%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Speech/Language Disorder"
                   style="left:36.0705%;top:19.2223%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Special Health Problem/Chronic Disease"
                   style="left:64.4500%;top:11.9917%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx[]" value="Visual Impairment"
                   style="left:64.6180%;top:15.7435%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx_cancer" data-exclusive="chronic"
                   style="left:67.8757%;top:13.7370%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx_noncancer" data-exclusive="chronic"
                   style="left:76.2049%;top:13.6776%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx_blind" data-exclusive="visual"
                   style="left:68.0269%;top:17.4889%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="dx_lowvision" data-exclusive="visual"
                   style="left:76.2049%;top:17.5482%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="manifest[]" value="Difficulty in Applying Knowledge"
                   style="left:8.6146%;top:23.4847%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="manifest[]" value="Difficulty in Communicating"
                   style="left:8.6146%;top:25.2894%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="manifest[]" value="Difficulty in Displaying Interpersonal Behavior (Emotional and Behavioral)"
                   style="left:8.6482%;top:27.1772%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="manifest[]" value="Difficulty in Hearing"
                   style="left:8.5642%;top:29.7655%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="manifest[]" value="Difficulty in Mobility (Walking, Climbing and Grasping)"
                   style="left:50.9992%;top:23.4372%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="manifest[]" value="Difficulty in Performing Adaptive Skills (Self-Care)"
                   style="left:50.9992%;top:25.2419%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="manifest[]" value="Difficulty in Remembering, Concentrating, Paying Attention and Understanding"
                   style="left:51.0327%;top:27.1297%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="manifest[]" value="Difficulty in Seeing"
                   style="left:50.9992%;top:29.6824%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="pwd_yes" data-exclusive="pwd"
                   style="left:34.1562%;top:32.3894%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="pwd_no" data-exclusive="pwd"
                   style="left:40.6885%;top:32.3894%;width:1.7464%;height:1.2348%">

            <input type="text" class="tf" name="last_grade"
                   style="left:7.9765%;top:38.8008%;width:40.4702%;height:1.9590%">

            <input type="text" class="tf" name="last_sy"
                   style="left:49.4542%;top:38.8008%;width:43.6608%;height:1.9590%">

            <input type="text" class="tf" name="last_school"
                   style="left:7.9765%;top:42.1846%;width:40.4702%;height:1.8997%">

            <div class="boxes" data-len="6" data-type="num" data-name="school_id"
                 style="--n:6;left:57.2396%;top:41.3773%;width:14.3631%;height:2.1371%"></div>

            <input type="checkbox" class="cb" name="sem_1" data-exclusive="sem"
                   style="left:17.5315%;top:48.5723%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="sem_2" data-exclusive="sem"
                   style="left:24.1982%;top:48.5723%;width:1.7464%;height:1.2348%">

            <input type="text" class="tf" name="shs_track"
                   style="left:14.7775%;top:51.1487%;width:78.4215%;height:2.0184%">

            <input type="text" class="tf" name="shs_strand"
                   style="left:15.4492%;top:54.4731%;width:77.7498%;height:2.0184%">

            <input type="checkbox" class="cb" name="modality[]" value="Blended (Combination)"
                   style="left:13.3837%;top:64.4702%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="modality[]" value="Homeschooling"
                   style="left:35.7011%;top:64.3514%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="modality[]" value="Modular (Print)"
                   style="left:53.0143%;top:64.4346%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="modality[]" value="Radio-Based Television"
                   style="left:71.0999%;top:64.3277%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="modality[]" value="Educational Television"
                   style="left:13.3837%;top:66.3580%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="modality[]" value="Modular (Digital)"
                   style="left:35.7347%;top:66.3105%;width:1.7464%;height:1.2348%">

            <input type="checkbox" class="cb" name="modality[]" value="Online"
                   style="left:53.0143%;top:66.3224%;width:1.7464%;height:1.2348%">

            <input type="text" class="tf center" name="parent_signature_name"
                   style="left:10.6297%;top:83.2651%;width:43.8623%;height:1.8403%">

            <input type="text" class="tf center" name="sign_date"
                   style="left:65.9950%;top:83.2651%;width:21.2427%;height:1.8403%">

        </section>

        {{-- STUDENT CONTACT INFORMATION --}}

        <section class="uploads no-print">

            <h2>Student Contact Information</h2>

            <p class="uploads-note">
                Please provide an active phone number and email address
                so the Admissions Office can contact you regarding your enrollment application.
            </p>

            <div class="upload-grid">

                <div class="upload-item">
                    <label for="student_phone_number">
                        Student Phone Number <b class="req">*</b>
                    </label>

                    <input type="tel"
                           id="student_phone_number"
                           name="phone_number"
                           placeholder="e.g. 09171234567"
                           maxlength="20"
                           pattern="[0-9+\-\s()]{7,20}"
                           required>
                </div>

                <div class="upload-item">
                    <label for="student_email">
                        Student Email Address <b class="req">*</b>
                    </label>

                    <input type="email"
                           id="student_email"
                           name="email"
                           placeholder="e.g. student@gmail.com"
                           maxlength="255"
                           required>
                </div>

            </div>
        </section>

        {{-- REQUIREMENTS --}}

        <section class="uploads no-print">

            <h2>Enrollment Requirements</h2>

            <p class="uploads-note">
                Upload a clear photo or scanned copy of each required document
                (JPG, PNG, or PDF). Documents marked with <b class="req">*</b> are required.
            </p>

            <div class="upload-grid">

                @foreach (\App\Support\Requirements::ALL as $key => $req)

                    @php
                        $isPsa = str_contains(strtolower($key), 'psa')
                            || str_contains(strtolower($req['label']), 'psa');
                    @endphp

                    <div class="upload-item">

                        <label for="doc_{{ $key }}">
                            {{ $req['label'] }}

                            @if ($req['required'])
                                <b class="req">*</b>
                            @endif
                        </label>

                        <input type="file"
                               id="doc_{{ $key }}"
                               data-doc="{{ $key }}"
                               data-label="{{ $req['label'] }}"
                               data-required="{{ $req['required'] ? 1 : 0 }}"
                               accept="{{ $isPsa
                                   ? 'image/jpeg,image/png,application/pdf'
                                   : ($req['images_only']
                                       ? 'image/jpeg,image/png'
                                       : 'image/jpeg,image/png,application/pdf') }}">

                        @if ($isPsa)
                            <div class="docnote">
                                <strong>
                                    A clear copy of the PSA Birth Certificate must be uploaded
                                    in JPG, PNG, or PDF format.
                                </strong>
                            </div>
                        @elseif ($req['images_only'])
                            <div class="docnote">JPG or PNG format only.</div>
                        @else
                            <div class="docnote">Accepted formats: JPG, PNG, or PDF.</div>
                        @endif

                        <div class="docinfo" id="info_{{ $key }}"></div>

                    </div>

                @endforeach

            </div>
        </section>

    </form>

    {{-- STEP 2 ACTIONS --}}

    <div class="wizard-actions no-print">

        <button type="button" class="wizard-btn secondary" id="backToSchedule">
            ← Back to Schedule
        </button>

        <button type="button" class="wizard-btn primary" id="reviewContinue">
            Review & Continue →
        </button>

    </div>

</div>

{{-- =====================================================
     STEP 3: REVIEW & CONFIRM
====================================================== --}}

<section id="confirmPanel" class="review-panel" hidden>

    <h2>Review Your Enrollment Application</h2>

    <p class="review-description">
        Please review your completed DepEd Enrollment Form and all uploaded
        requirements before submitting your application.
    </p>

    <div class="review-section">
        <h3>Completed DepEd Enrollment Form</h3>

        <div class="review-preview-actions no-print">
            <button type="button"
                    class="wizard-btn primary"
                    id="viewPrintForm">
                View / Print Form
            </button>
        </div>

        <div id="enrollmentPrintPreview" class="enrollment-print-preview"></div>
    </div>

    <div class="review-section">
        <h3>Uploaded Documents</h3>

        <p class="review-description">
            Review the documents you have uploaded. Click a filename
            to open and view the corresponding file.
        </p>

        <div id="studentUploadList" class="student-upload-list"></div>
    </div>

    <div class="review-notice">
        <strong>Important:</strong>
        Please make sure that all information is accurate and all required
        documents are complete before clicking Confirm & Submit.
    </div>

    <div id="reviewError" class="review-error" role="alert"></div>

    <div class="wizard-actions no-print">

        <button type="button"
                class="wizard-btn secondary"
                id="editInformation">
            ← Edit Information
        </button>

        <button type="button"
                class="wizard-btn primary"
                id="confirmSubmit">
            Confirm & Submit
        </button>

    </div>

</section>

{{-- ROUTE CONFIGURATION --}}

<script>
    window.ENROLLMENT_ROUTES = {
        store: @json(route(($walkIn ?? false) ? 'admin.walkin.store' : 'enrollment.store')),
        track: @json(route('enrollment.track')),
        slots: @json(route('enrollment.slots')),
        walkIn: @json((bool) ($walkIn ?? false))
    };
</script>

<script src="{{ asset('js/enrollment.js') }}"></script>

{{-- =====================================================
     WIZARD JAVASCRIPT
====================================================== --}}

<script>
(function () {
    'use strict';

    const formWrap = document.getElementById('formWrap');
    const schedulePanel = document.getElementById('schedulePanel');
    const confirmPanel = document.getElementById('confirmPanel');
    const enrollForm = document.getElementById('enrollForm');

    const reviewContinue = document.getElementById('reviewContinue');
    const editInformation = document.getElementById('editInformation');
    const confirmSubmit = document.getElementById('confirmSubmit');
    const backToSchedule = document.getElementById('backToSchedule');

    const apptContinue = document.getElementById('apptContinue');
    const apptChange = document.getElementById('apptChange');

    const originalSaveButton = document.getElementById('btnSave');

    const viewPrintForm = document.getElementById('viewPrintForm');
    const printPreview = document.getElementById('enrollmentPrintPreview');
    const studentUploadList = document.getElementById('studentUploadList');

    const walkIn = Boolean(window.ENROLLMENT_ROUTES?.walkIn);

    let currentStep = walkIn ? 2 : 1;
    let finalSubmission = false;
    let uploadObjectUrls = [];

    const stepItems = document.querySelectorAll('[data-wizard-step]');

    /*
    |--------------------------------------------------------------------------
    | WIZARD NAVIGATION
    |--------------------------------------------------------------------------
    */

    function updateSteps(step) {
        currentStep = step;

        stepItems.forEach(function (item) {
            const itemStep = Number(item.dataset.wizardStep);

            item.classList.remove('active', 'done');

            if (itemStep < step) {
                item.classList.add('done');
            } else if (itemStep === step) {
                item.classList.add('active');
            }
        });
    }

    function showStep(step) {
        updateSteps(step);

        if (step === 1) {
            if (schedulePanel) schedulePanel.hidden = false;
            if (formWrap) formWrap.hidden = true;
            if (confirmPanel) confirmPanel.hidden = true;
        }

        if (step === 2) {
            if (schedulePanel) schedulePanel.hidden = true;
            if (formWrap) formWrap.hidden = false;
            if (confirmPanel) confirmPanel.hidden = true;
        }

        if (step === 3) {
            if (schedulePanel) schedulePanel.hidden = true;
            if (formWrap) formWrap.hidden = true;
            if (confirmPanel) confirmPanel.hidden = false;
        }

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    /*
    |--------------------------------------------------------------------------
    | COPY LIVE VALUES INTO THE PRINT PREVIEW
    |--------------------------------------------------------------------------
    */

    function copyLiveFormValues(originalSheet, clonedSheet) {
        const originalFields = originalSheet.querySelectorAll(
            'input, textarea, select'
        );

        const clonedFields = clonedSheet.querySelectorAll(
            'input, textarea, select'
        );

        originalFields.forEach(function (field, index) {
            const clonedField = clonedFields[index];

            if (!clonedField) return;

            if (field.type === 'checkbox' || field.type === 'radio') {
                clonedField.checked = field.checked;

                if (field.checked) {
                    clonedField.setAttribute('checked', 'checked');
                } else {
                    clonedField.removeAttribute('checked');
                }

                return;
            }

            if (field.tagName === 'SELECT') {
                clonedField.value = field.value;

                Array.from(clonedField.options).forEach(function (option) {
                    option.selected = option.value === field.value;
                });

                return;
            }

            clonedField.value = field.value;
            clonedField.setAttribute('value', field.value);
        });
    }

    function createPrintPreview() {
        if (!printPreview || !enrollForm) return;

        printPreview.innerHTML = '';

        const originalSheets = enrollForm.querySelectorAll('.sheet');

        if (!originalSheets.length) {
            printPreview.textContent = 'Enrollment form preview is unavailable.';
            return;
        }

        originalSheets.forEach(function (originalSheet) {
            const clonedSheet = originalSheet.cloneNode(true);

            copyLiveFormValues(originalSheet, clonedSheet);

            clonedSheet.removeAttribute('id');

            clonedSheet.querySelectorAll('[id]').forEach(function (element) {
                element.removeAttribute('id');
            });

            clonedSheet.querySelectorAll('input, textarea, select').forEach(
                function (field) {
                    field.disabled = true;
                    field.removeAttribute('name');
                }
            );

            printPreview.appendChild(clonedSheet);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | STUDENT UPLOADS
    |--------------------------------------------------------------------------
    */

    function revokeUploadUrls() {
        uploadObjectUrls.forEach(function (url) {
            URL.revokeObjectURL(url);
        });

        uploadObjectUrls = [];
    }

    function getPreparedFiles() {
        if (typeof window.getPreparedEnrollmentFiles === 'function') {
            return window.getPreparedEnrollmentFiles() || {};
        }

        return {};
    }

    function buildStudentUploads() {
        if (!studentUploadList || !enrollForm) return;

        revokeUploadUrls();
        studentUploadList.innerHTML = '';

        const fileInputs = enrollForm.querySelectorAll(
            'input[type="file"][data-doc]'
        );

        const preparedFiles = getPreparedFiles();

        if (!fileInputs.length) {
            const empty = document.createElement('p');
            empty.className = 'student-upload-empty';
            empty.textContent = 'No upload fields were found.';
            studentUploadList.appendChild(empty);
            return;
        }

        let uploadedCount = 0;

        fileInputs.forEach(function (input) {
            const key = input.dataset.doc;
            const label = input.dataset.label || key;

            const file =
                preparedFiles[key] ||
                (input.files && input.files.length ? input.files[0] : null);

            const row = document.createElement('div');
            row.className = 'student-upload-row';

            const info = document.createElement('div');
            info.className = 'student-upload-info';

            const title = document.createElement('strong');
            title.className = 'student-upload-name';
            title.textContent = label;

            const fileName = document.createElement('span');
            fileName.className = 'student-upload-filename';

            info.appendChild(title);
            row.appendChild(info);

            if (file) {
                uploadedCount++;

                fileName.textContent = file.name;
                info.appendChild(fileName);

                const link = document.createElement('a');
                link.className = 'student-upload-link';
                link.textContent = 'View File';

                const objectUrl = URL.createObjectURL(file);
                uploadObjectUrls.push(objectUrl);

                link.href = objectUrl;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';

                row.appendChild(link);
            } else {
                fileName.textContent = 'No file selected';
                info.appendChild(fileName);

                row.classList.add('is-empty');

                const status = document.createElement('span');
                status.className = 'student-upload-status';
                status.textContent =
                    input.dataset.required === '1' ? 'Required' : 'Optional';

                row.appendChild(status);
            }

            studentUploadList.appendChild(row);
        });

        if (uploadedCount === 0) {
            const note = document.createElement('p');
            note.className = 'student-upload-empty';
            note.textContent = 'No documents have been uploaded yet.';
            studentUploadList.prepend(note);
        }
    }

    function buildReview() {
        createPrintPreview();
        buildStudentUploads();
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW / PRINT
    |--------------------------------------------------------------------------
    */

    if (viewPrintForm) {
        viewPrintForm.addEventListener('click', function () {
            createPrintPreview();

            if (printPreview) {
                printPreview.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            window.print();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    function validateContactInformation() {
        const phone = document.getElementById('student_phone_number');
        const email = document.getElementById('student_email');

        if (!phone || !email) return true;

        if (!phone.value.trim()) {
            alert('Please enter your phone number.');
            phone.focus();
            return false;
        }

        if (!phone.checkValidity()) {
            alert('Please enter a valid phone number.');
            phone.focus();
            return false;
        }

        if (!email.value.trim()) {
            alert('Please enter your email address.');
            email.focus();
            return false;
        }

        if (!email.checkValidity()) {
            alert('Please enter a valid email address.');
            email.focus();
            return false;
        }

        return true;
    }

    function validateRequiredDocuments() {
        if (walkIn) return true;

        const requiredFiles = enrollForm.querySelectorAll(
            'input[type="file"][data-required="1"]'
        );

        for (const input of requiredFiles) {
            if (!input.files || !input.files.length) {
                const label = input.dataset.label || 'required document';

                alert('Please upload the required document: ' + label);

                input.focus();
                return false;
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | APPOINTMENT NAVIGATION
    |--------------------------------------------------------------------------
    */

    if (apptContinue) {
        apptContinue.addEventListener('click', function () {
            setTimeout(function () {
                if (formWrap && !formWrap.hidden) {
                    showStep(2);
                }
            }, 100);
        });
    }

    if (apptChange) {
        apptChange.addEventListener('click', function () {
            showStep(1);
        });
    }

    if (backToSchedule) {
        backToSchedule.addEventListener('click', function () {
            if (walkIn) return;

            showStep(1);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | REVIEW & CONTINUE
    |--------------------------------------------------------------------------
    */

    if (reviewContinue) {
        reviewContinue.addEventListener('click', function () {
            if (!validateContactInformation()) return;

            if (!validateRequiredDocuments()) return;

            buildReview();
            showStep(3);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT INFORMATION
    |--------------------------------------------------------------------------
    */

    if (editInformation) {
        editInformation.addEventListener('click', function () {
            showStep(2);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE BUTTON INTERCEPTION
    |--------------------------------------------------------------------------
    */

    if (originalSaveButton) {
        originalSaveButton.addEventListener('click', function (event) {

            if (finalSubmission) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            if (!validateContactInformation()) {
                showStep(2);
                return;
            }

            if (!validateRequiredDocuments()) {
                showStep(2);
                return;
            }

            buildReview();
            showStep(3);

        }, true);
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRM & SUBMIT
    |--------------------------------------------------------------------------
    */

    if (confirmSubmit) {
        confirmSubmit.addEventListener('click', function () {

            if (!validateContactInformation()) {
                showStep(2);
                return;
            }

            if (!validateRequiredDocuments()) {
                showStep(2);
                return;
            }

            if (!originalSaveButton) {
                alert('The Save button could not be found. Please check enrollment.js.');
                return;
            }

            finalSubmission = true;

            confirmSubmit.disabled = true;
            confirmSubmit.textContent = 'Submitting...';

            originalSaveButton.click();

        });
    }

    /*
    |--------------------------------------------------------------------------
    | INITIAL STEP
    |--------------------------------------------------------------------------
    */

    if (walkIn) {
        showStep(2);
    } else {
        showStep(1);
    }

})();
</script>

</body>
</html>
