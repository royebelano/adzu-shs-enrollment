
document.addEventListener('DOMContentLoaded', () => {
    const routes = window.ENROLLMENT_ROUTES || {};

    // Huwag baguhin ang existing walk-in workflow.
    if (routes.walkIn) return;

    const form = document.getElementById('enrollForm');
    const schedulePanel = document.getElementById('schedulePanel');
    const formWrap = document.getElementById('formWrap');
    const confirmPanel = document.getElementById('confirmPanel');

    const saveBtn = document.getElementById('btnSave');
    const confirmSubmit = document.getElementById('confirmSubmit');

    if (!form || !formWrap || !confirmPanel || !saveBtn) {
        console.error('Enrollment wizard elements not found.');
        return;
    }

    let allowFinalSubmit = false;

    const stepItems = [
        ...document.querySelectorAll('#schedSteps li')
    ];

    const setStep = (number) => {
        stepItems.forEach((item, index) => {
            item.classList.toggle('on', index + 1 === number);
            item.classList.toggle('done', index + 1 < number);
        });
    };

    const scrollTop = () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    };

    const showStep1 = () => {
        confirmPanel.hidden = true;
        formWrap.hidden = true;

        if (schedulePanel) {
            schedulePanel.hidden = false;
        }

        setStep(1);
        scrollTop();
    };

    const showStep2 = () => {
        if (schedulePanel) {
            schedulePanel.hidden = true;
        }

        confirmPanel.hidden = true;
        formWrap.hidden = false;

        setStep(2);
        scrollTop();
    };

    const showStep3 = () => {
        // Validate required HTML fields first.
        if (!form.reportValidity()) {
            return;
        }

        buildReview();

        if (schedulePanel) {
            schedulePanel.hidden = true;
        }

        formWrap.hidden = true;
        confirmPanel.hidden = false;

        setStep(3);
        scrollTop();
    };

    // Get ordinary text field values.
    const getValue = (name) => {
        const field = form.querySelector(
            `[name="${name}"]`
        );

        return field ? field.value.trim() : '';
    };

    // Get values from DepEd character boxes.
    const getBoxValue = (name) => {
        const group = form.querySelector(
            `.boxes[data-name="${name}"]`
        );

        if (!group) return '';

        return [...group.querySelectorAll('input')]
            .map(input => input.value || '')
            .join('')
            .trim();
    };

    const addReviewItem = (container, label, value) => {
        const item = document.createElement('div');
        item.className = 'review-item';

        const heading = document.createElement('span');
        heading.className = 'review-label';
        heading.textContent = label;

        const content = document.createElement('strong');
        content.className = 'review-value';
        content.textContent = value || 'Not provided';

        item.append(heading, content);
        container.appendChild(item);
    };

    const addReviewSection = (container, heading, fields) => {
        const section = document.createElement('div');
        section.className = 'review-section';

        const title = document.createElement('h3');
        title.textContent = heading;

        const grid = document.createElement('div');
        grid.className = 'review-grid';

        fields.forEach(([label, value]) => {
            addReviewItem(grid, label, value);
        });

        section.append(title, grid);
        container.appendChild(section);
    };

    const getCheckedValues = (name) => {
        return [
            ...form.querySelectorAll(
                `input[name="${name}"]:checked`
            )
        ].map(input => input.value || 'Yes');
    };

    const buildReview = () => {
        const appointment = document.getElementById(
            'reviewAppointment'
        );

        const student = document.getElementById(
            'reviewStudent'
        );

        const contact = document.getElementById(
            'reviewContact'
        );

        const documents = document.getElementById(
            'reviewDocuments'
        );

        if (!student || !contact || !documents || !appointment) {
            console.error('Review elements not found.');
            return;
        }

        student.replaceChildren();
        contact.replaceChildren();
        documents.replaceChildren();

        // Appointment
        const appointmentText = document
            .getElementById('apptSummaryText')
            ?.textContent.trim();

        appointment.textContent =
            appointmentText || 'No appointment selected';

        // Student information
        addReviewSection(student, 'Personal Information', [
            ['Last Name', getBoxValue('last_name')],
            ['First Name', getBoxValue('first_name')],
            ['Middle Name', getBoxValue('middle_name')],
            ['Extension Name', getBoxValue('extension_name')],
            ['LRN', getBoxValue('lrn')],
            ['Date of Birth', [
                getBoxValue('bd_month'),
                getBoxValue('bd_day'),
                getBoxValue('bd_year')
            ].filter(Boolean).join('/')],
            ['Age', getBoxValue('age')],
            ['Grade Level', getBoxValue('grade_level')],
            ['Sex', getCheckedValues('sex_male').length
                ? 'Male'
                : getCheckedValues('sex_female').length
                    ? 'Female'
                    : 'Not selected'],
            ['Place of Birth', getValue('place_of_birth')],
            ['Religion', getValue('religion')],
            ['Mother Tongue', getValue('mother_tongue')],
            ['PSA Number', getValue('psa_no')]
        ]);

        addReviewSection(student, 'Current Address', [
            ['House Number', getValue('cur_house')],
            ['Street', getValue('cur_street')],
            ['Barangay', getValue('cur_barangay')],
            ['City', getValue('cur_city')],
            ['Province', getValue('cur_province')],
            ['Country', getValue('cur_country')],
            ['ZIP Code', getValue('cur_zip')]
        ]);

        addReviewSection(student, 'Permanent Address', [
            ['House Number', getValue('per_house')],
            ['Street', getValue('per_street')],
            ['Barangay', getValue('per_barangay')],
            ['City', getValue('per_city')],
            ['Province', getValue('per_province')],
            ['Country', getValue('per_country')],
            ['ZIP Code', getValue('per_zip')]
        ]);

        addReviewSection(student, 'Parent / Guardian', [
            ['Father', [
                getValue('father_first'),
                getValue('father_middle'),
                getValue('father_last')
            ].filter(Boolean).join(' ')],
            ['Father Contact', getValue('father_contact')],
            ['Mother', [
                getValue('mother_first'),
                getValue('mother_middle'),
                getValue('mother_last')
            ].filter(Boolean).join(' ')],
            ['Mother Contact', getValue('mother_contact')],
            ['Guardian', [
                getValue('guardian_first'),
                getValue('guardian_middle'),
                getValue('guardian_last')
            ].filter(Boolean).join(' ')],
            ['Guardian Contact', getValue('guardian_contact')]
        ]);

        addReviewSection(student, 'Previous School Information', [
            ['Last Grade', getValue('last_grade')],
            ['Last School Year', getValue('last_sy')],
            ['Last School', getValue('last_school')],
            ['School ID', getBoxValue('school_id')],
            ['SHS Track', getValue('shs_track')],
            ['SHS Strand', getValue('shs_strand')]
        ]);

        // Contact information
        addReviewSection(contact, 'Student Contact Details', [
            ['Phone Number', getValue('phone_number')],
            ['Email Address', getValue('email')]
        ]);

        // Uploaded requirements
        const fileInputs = [
            ...form.querySelectorAll(
                'input[type="file"][data-doc]'
            )
        ];

        if (!fileInputs.length) {
            documents.textContent = 'No document fields found.';
        }

        fileInputs.forEach(input => {
            const item = document.createElement('div');
            item.className = 'review-document';

            const label = document.createElement('strong');
            label.textContent =
                input.dataset.label || input.dataset.doc;

            const filename = document.createElement('span');

            const file = input.files?.[0];

            if (file) {
                filename.textContent = file.name;
                item.classList.add('has-file');
            } else {
                filename.textContent =
                    input.dataset.required === '1'
                        ? 'Required — No file selected'
                        : 'Optional — No file selected';

                item.classList.add(
                    input.dataset.required === '1'
                        ? 'missing-file'
                        : 'optional-file'
                );
            }

            item.append(label, filename);
            documents.appendChild(item);
        });
    };

    /*
    |--------------------------------------------------------------------------
    | FIRST SAVE CLICK = REVIEW SCREEN
    |--------------------------------------------------------------------------
    */

    saveBtn.addEventListener('click', (event) => {
        if (allowFinalSubmit) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        showStep3();
    }, true);

    /*
    |--------------------------------------------------------------------------
    | STEP 2 NAVIGATION
    |--------------------------------------------------------------------------
    */

    document.getElementById('reviewContinue')
        ?.addEventListener('click', showStep3);

    document.getElementById('backToSchedule')
        ?.addEventListener('click', () => {
            document.getElementById('apptChange')?.click();
            showStep1();
        });

    /*
    |--------------------------------------------------------------------------
    | STEP 3 NAVIGATION
    |--------------------------------------------------------------------------
    */

    const backToForm = () => {
        showStep2();
    };

    document.getElementById('backToForm')
        ?.addEventListener('click', backToForm);

    document.getElementById('confirmBack')
        ?.addEventListener('click', backToForm);

    /*
    |--------------------------------------------------------------------------
    | FINAL SUBMISSION
    |--------------------------------------------------------------------------
    */

    confirmSubmit?.addEventListener('click', () => {
        if (confirmSubmit.disabled) return;

        allowFinalSubmit = true;

        confirmSubmit.disabled = true;
        confirmSubmit.textContent = 'Submitting...';

        confirmPanel.hidden = true;
        formWrap.hidden = false;

        setStep(2);

        // Trigger the original enrollment.js save handler.
        saveBtn.click();

        // Restore wizard state after the synchronous click event.
        allowFinalSubmit = false;

        // The original handler disables btnSave while submitting.
        // If it fails, its catch block re-enables btnSave.
        // Restore the confirmation button if the user returns.
        window.setTimeout(() => {
            if (!saveBtn.disabled) {
                confirmSubmit.disabled = false;
                confirmSubmit.textContent =
                    'Confirm & Submit Application →';
            }
        }, 500);
    });

    /*
    |--------------------------------------------------------------------------
    | SCHEDULE PROGRESS
    |--------------------------------------------------------------------------
    */

    document.getElementById('apptContinue')
        ?.addEventListener('click', () => {
            window.setTimeout(() => {
                if (!formWrap.hidden) {
                    setStep(2);
                }
            }, 0);
        });

    document.getElementById('apptChange')
        ?.addEventListener('click', () => {
            window.setTimeout(() => {
                setStep(1);
            }, 0);
        });

    // Initial progress state.
    setStep(1);
});
