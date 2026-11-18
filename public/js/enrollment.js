
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('enrollForm');

    if (!form) {
        console.error('Enrollment form not found.');
        return;
    }

    const chainGroups = (group) => group.dataset.chain
        ? [...document.querySelectorAll(`.boxes[data-chain="${group.dataset.chain}"]`)]
        : [group];

    /* =====================================================
       1. LETTER BOXES - ISANG CHARACTER BAWAT KAHON
    ===================================================== */

    document.querySelectorAll('.boxes').forEach(group => {
        const len = parseInt(group.dataset.len, 10);
        const numeric = group.dataset.type === 'num';
        const name = group.dataset.name;

        for (let i = 0; i < len; i++) {
            const input = document.createElement('input');

            input.type = 'text';
            input.maxLength = 1;
            input.autocomplete = 'off';

            if (name) {
                input.name = `${name}[]`;
            }

            if (numeric) {
                input.inputMode = 'numeric';
            }

            group.appendChild(input);
        }

        const focusNext = (input) => {
            if (input.nextElementSibling) {
                input.nextElementSibling.focus();
                return;
            }

            const groups = chainGroups(group);
            const next = groups[groups.indexOf(group) + 1];

            if (next && next.firstElementChild) {
                next.firstElementChild.focus();
            }
        };

        const focusPrev = (input) => {
            if (input.previousElementSibling) {
                input.previousElementSibling.focus();
                return;
            }

            const groups = chainGroups(group);
            const prev = groups[groups.indexOf(group) - 1];

            if (prev && prev.lastElementChild) {
                prev.lastElementChild.focus();
            }
        };

        group.addEventListener('input', (e) => {
            const target = e.target;

            if (target.tagName !== 'INPUT') return;

            target.value = numeric
                ? target.value.replace(/\D/g, '')
                : target.value.toUpperCase();

            if (target.value) {
                focusNext(target);
            }
        });

        group.addEventListener('keydown', (e) => {
            const target = e.target;

            if (e.key === 'Backspace' && !target.value) {
                focusPrev(target);
            }

            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                focusPrev(target);
            }

            if (e.key === 'ArrowRight') {
                e.preventDefault();
                focusNext(target);
            }
        });

        group.addEventListener('paste', (e) => {
            e.preventDefault();

            let text = (e.clipboardData || window.clipboardData)
                .getData('text');

            text = numeric
                ? text.replace(/\D/g, '')
                : text.toUpperCase();

            const inputs = [...group.querySelectorAll('input')];
            const start = inputs.indexOf(e.target);

            [...text]
                .slice(0, inputs.length - start)
                .forEach((character, index) => {
                    inputs[start + index].value = character;
                });

            const last = inputs[
                Math.min(start + text.length, inputs.length) - 1
            ];

            if (last) {
                last.focus();
            }
        });
    });


    /* =====================================================
       2. REGULAR TEXT FIELDS - AUTOMATIC CAPITAL LETTERS
    ===================================================== */

    form.querySelectorAll('input[type="text"]:not(.boxes input)')
        .forEach(input => {
            input.addEventListener('input', () => {
                const position = input.selectionStart;

                input.value = input.value.toUpperCase();

                input.setSelectionRange(position, position);
            });
        });


    /* =====================================================
       3. YES / NO CHECKBOX - ISA LANG ANG PWEDE
    ===================================================== */

    form.querySelectorAll('input[type="checkbox"][data-exclusive]')
        .forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                if (!checkbox.checked) return;

                form.querySelectorAll(
                    `input[data-exclusive="${checkbox.dataset.exclusive}"]`
                ).forEach(other => {
                    if (other !== checkbox) {
                        other.checked = false;
                    }
                });
            });
        });


    /* =====================================================
       4. SAME WITH CURRENT ADDRESS
    ===================================================== */

    const same = document.getElementById('sameAddr');

    const addressMap = {
        per_house: 'cur_house',
        per_street: 'cur_street',
        per_barangay: 'cur_barangay',
        per_city: 'cur_city',
        per_province: 'cur_province',
        per_country: 'cur_country',
        per_zip: 'cur_zip'
    };

    const syncAddress = () => {
        Object.entries(addressMap).forEach(([targetName, sourceName]) => {
            const target = form.elements[targetName];
            const source = form.elements[sourceName];

            if (!target || !source) return;

            target.value = same.checked ? source.value : '';
            target.readOnly = same.checked;
        });
    };

    if (same) {
        same.addEventListener('change', syncAddress);

        Object.values(addressMap).forEach(sourceName => {
            const source = form.elements[sourceName];

            if (source) {
                source.addEventListener('input', () => {
                    if (same.checked) {
                        syncAddress();
                    }
                });
            }
        });
    }


    /* =====================================================
       5. COLLECT ALL FORM DATA
       KASAMA ANG PHONE NUMBER AT EMAIL
    ===================================================== */

    const collect = () => {
        const data = {};

        // Letter boxes
        form.querySelectorAll('.boxes').forEach(group => {
            const name = group.dataset.name;

            data[name] = [...group.querySelectorAll('input')]
                .map(input => input.value || ' ')
                .join('')
                .trimEnd();
        });

        // Regular inputs and checkboxes
        form.querySelectorAll('input[name]').forEach(element => {
            if (element.closest('.boxes')) return;

            const name = element.name;

            if (element.type === 'checkbox') {
                if (name.endsWith('[]')) {
                    const key = name.slice(0, -2);

                    data[key] = data[key] || [];

                    if (element.checked) {
                        data[key].push(element.value);
                    }
                } else {
                    data[name] = element.checked;
                }
            } else if (element.type !== 'file') {
                data[name] = element.value.trim();
            }
        });

        return data;
    };


    /* =====================================================
       6. REQUIREMENTS UPLOAD
    ===================================================== */

    const MAX_BYTES = 5 * 1024 * 1024;

    const docInputs = [
        ...document.querySelectorAll('input[type="file"][data-doc]')
    ];

const prepared = {};

/**
 * Make compressed uploaded files accessible
 * to the Step 3 review panel in welcome.blade.php.
 */
window.getPreparedEnrollmentFiles = function () {
    return prepared;
};
    const fmtSize = (size) => {
        return size >= 1048576
            ? (size / 1048576).toFixed(1) + ' MB'
            : Math.max(1, Math.round(size / 1024)) + ' KB';
    };


    // Compress cellphone photos
    const compress = async (file) => {
        if (!/^image\/(jpeg|png)$/i.test(file.type)) {
            return file;
        }

        try {
            const bitmap = await createImageBitmap(file);

            const scale = Math.min(
                1,
                1800 / Math.max(bitmap.width, bitmap.height)
            );

            const width = Math.round(bitmap.width * scale);
            const height = Math.round(bitmap.height * scale);

            const canvas = document.createElement('canvas');

            canvas.width = width;
            canvas.height = height;

            const context = canvas.getContext('2d');

            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, width, height);

            context.drawImage(bitmap, 0, 0, width, height);

            bitmap.close();

            const blob = await new Promise(resolve => {
                canvas.toBlob(resolve, 'image/jpeg', 0.85);
            });

            if (!blob || blob.size >= file.size) {
                return file;
            }

            const filename = file.name.replace(/\.\w+$/, '') + '.jpg';

            return new File(
                [blob],
                filename,
                { type: 'image/jpeg' }
            );

        } catch (error) {
            console.error('Image compression error:', error);
            return file;
        }
    };


    const setInfo = (key, text, cls = '') => {
        const element = document.getElementById(`info_${key}`);

        if (!element) return;

        element.textContent = text;
        element.className = 'docinfo' + (cls ? ' ' + cls : '');
    };


    docInputs.forEach(input => {
        const key = input.dataset.doc;

        input.addEventListener('change', async () => {
            delete prepared[key];

            setInfo(key, '');

            const file = input.files[0];

            if (!file) return;

            setInfo(key, 'Inihahanda...');

            const output = await compress(file);

            if (output.size > MAX_BYTES) {
                setInfo(
                    key,
                    `Masyadong malaki (${fmtSize(output.size)}). Hanggang 5 MB lang.`,
                    'err'
                );

                input.value = '';
                return;
            }

            prepared[key] = output;

            setInfo(
                key,
                `✓ ${output.name} (${fmtSize(output.size)})`,
                'ok'
            );
        });
    });


    /* =====================================================
       7. SUBMIT ENROLLMENT TO DATABASE
    ===================================================== */

    const routes = window.ENROLLMENT_ROUTES || {};

    const msg = document.getElementById('saveMsg');
    const saveBtn = document.getElementById('btnSave');

    const phoneInput = document.getElementById('student_phone_number');
    const emailInput = document.getElementById('student_email');

    const setMsg = (text, isError = false) => {
        if (!msg) return;

        msg.textContent = text;
        msg.className = isError ? 'msg err' : 'msg';
    };


    /* =====================================================
       ADMISSION SCHEDULE (Step 1: date + hourly slot)
    ===================================================== */

    const schedulePanel = document.getElementById('schedulePanel');
    const formWrap = document.getElementById('formWrap');
    const apptDate = document.getElementById('apptDate');
    const apptSlots = document.getElementById('apptSlots');
    const apptMsg = document.getElementById('apptMsg');
    const apptContinue = document.getElementById('apptContinue');
    const apptSummary = document.getElementById('apptSummary');
    const apptSummaryText = document.getElementById('apptSummaryText');
    const apptChange = document.getElementById('apptChange');

    let chosenTime = '';
    let chosenLabel = '';

    const dateText = () => {
        const opt = apptDate && apptDate.selectedOptions[0];
        return opt ? opt.textContent.replace(' (Full)', '') : '';
    };

    const renderSlots = (json) => {
        apptSlots.textContent = '';
        apptMsg.textContent = '';

        if (!json.open || !json.slots.length) {
            apptMsg.textContent = json.message || 'Walang available na oras sa petsang ito.';
            return;
        }

        json.slots.forEach(slot => {
            const card = document.createElement('label');
            const input = document.createElement('input');
            const info = document.createElement('div');
            const title = document.createElement('strong');
            const note = document.createElement('small');

            const walkIn = slot.mode === 'walkin';

            input.type = 'radio';
            input.name = 'appt_slot';
            input.value = slot.time;
            input.disabled = !slot.bookable;
            input.checked = slot.bookable && chosenTime === slot.time;

            title.textContent = slot.label;

            note.textContent = slot.closed
                ? 'Closed'
                : walkIn
                    ? 'Walk-in only'
                    : (slot.past ? 'Unavailable' : 'Online booking');

            if (walkIn && !slot.closed) note.className = 'is-walkin';

            info.append(title, note);
            card.className = 'sched-slot' + (slot.bookable ? '' : ' is-off') + (input.checked ? ' on' : '');
            card.append(input, info);

            if (!slot.closed && !walkIn && !slot.past) {
                const pill = document.createElement('span');
                const full = slot.left < 1;

                pill.className = 'sched-pill' + (full ? ' is-full' : (slot.left <= 5 ? ' is-low' : ''));
                pill.textContent = full
                    ? '● Full'
                    : '● ' + slot.left + ' / ' + slot.capacity + ' Available';

                card.append(pill);
            }

            input.addEventListener('change', () => {
                chosenTime = slot.time;
                chosenLabel = slot.label;

                apptSlots.querySelectorAll('.sched-slot').forEach(el =>
                    el.classList.toggle('on', el === card)
                );

                apptContinue.disabled = false;
            });

            apptSlots.appendChild(card);
        });
    };

    const loadSlots = async (keepSelection) => {
        if (!apptDate || !apptSlots || !apptDate.value || !routes.slots) return;

        if (!keepSelection) {
            chosenTime = '';
            chosenLabel = '';
            apptContinue.disabled = true;
        }

        try {
            const res = await fetch(
                routes.slots + '?date=' + encodeURIComponent(apptDate.value),
                { headers: { 'Accept': 'application/json' } }
            );

            const json = await res.json().catch(() => ({}));

            if (!res.ok) {
                throw new Error(json.message || 'Hindi makuha ang mga oras.');
            }

            // Drop the choice if someone else just took the last seat.
            const stillOpen = json.slots && json.slots.some(s => s.bookable && s.time === chosenTime);

            if (keepSelection && chosenTime && !stillOpen) {
                chosenTime = '';
                chosenLabel = '';
                apptContinue.disabled = true;
            }

            renderSlots(json);
        } catch (error) {
            apptMsg.textContent = error.message || 'Hindi makuha ang mga oras.';
        }
    };

    // Progress dots at the top: 1 Schedule, 2 Enrollment Form, 3 Confirm.
    window.setApptStep = (n) => {
        document.querySelectorAll('#schedSteps li').forEach((li, i) => {
            li.classList.toggle('on', i + 1 === n);
            li.classList.toggle('done', i + 1 < n);
        });
    };

    const showSchedule = () => {
        if (!schedulePanel || !formWrap) return;
        window.setApptStep(1);
        formWrap.hidden = true;
        schedulePanel.hidden = false;
        window.scrollTo({ top: 0 });
    };

    if (apptDate) {
        apptDate.addEventListener('change', () => loadSlots(false));

        // Pick the first date that is not full and show its hours.
        const firstOpen = Array.from(apptDate.options).find(o => !o.disabled);
        if (firstOpen) apptDate.value = firstOpen.value;
        loadSlots(false);

        // Keep the seat counts fresh while the student is choosing.
        setInterval(() => {
            if (schedulePanel && !schedulePanel.hidden) loadSlots(true);
        }, 20000);
    }

    if (apptContinue) {
        apptContinue.addEventListener('click', () => {
            if (!chosenTime) return;

            schedulePanel.hidden = true;
            formWrap.hidden = false;
            window.setApptStep(2);
            apptSummaryText.textContent = dateText() + ', ' + chosenLabel;
            apptSummary.hidden = false;
            window.scrollTo({ top: 0 });
        });
    }

    if (apptChange) {
        apptChange.addEventListener('click', () => {
            showSchedule();
            loadSlots(true);
        });
    }

    // Called when the server says the chosen hour was taken meanwhile.
    window.refreshApptSlots = async () => {
        showSchedule();
        await loadSlots(false);

        if (apptMsg) {
            apptMsg.textContent = 'Hindi na available ang napili mong oras. Pumili ng ibang oras.';
        }
    };


    if (saveBtn) {
        saveBtn.addEventListener('click', async () => {

            // Validate phone and email
            if (!phoneInput || !emailInput) {
                setMsg('Hindi makita ang contact information fields.', true);
                return;
            }

            const phone = phoneInput.value.trim();
            const email = emailInput.value.trim();

            if (!phone) {
                setMsg('Pakilagay ang student phone number.', true);
                phoneInput.focus();
                return;
            }

            if (!/^[0-9+\-\s()]{7,20}$/.test(phone)) {
                setMsg('Hindi valid ang phone number.', true);
                phoneInput.focus();
                return;
            }

            if (!email) {
                setMsg('Pakilagay ang student email address.', true);
                emailInput.focus();
                return;
            }

            if (!emailInput.checkValidity()) {
                setMsg('Pakilagay ang valid na email address.', true);
                emailInput.focus();
                return;
            }


            // Validate other required HTML fields
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }


            // Check required documents
            const missing = routes.walkIn ? [] : docInputs
                .filter(input =>
                    input.dataset.required === '1' &&
                    !prepared[input.dataset.doc]
                )
                .map(input => input.dataset.label);

            if (missing.length) {
                setMsg(
                    'Kulang pa ang requirements: ' + missing.join(', '),
                    true
                );

                const requirementsSection = docInputs[0]?.closest('.uploads');

                if (requirementsSection) {
                    requirementsSection.scrollIntoView({
                        behavior: 'smooth'
                    });
                }

                return;
            }


            // Online students must pick an admission appointment
            if (!routes.walkIn && apptDate) {
                if (!apptDate.value || !chosenTime) {
                    setMsg('Pumili ng petsa at oras ng admission appointment.', true);

                    showSchedule();

                    return;
                }
            }

            if (!routes.store) {
                setMsg('Hindi makita ang enrollment submission route.', true);
                return;
            }


            setMsg('Nagse-save at nag-a-upload...');

            saveBtn.disabled = true;

            try {
                const csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    .content;

                const formData = new FormData();

                // Includes all DepEd fields, phone_number, and email
                formData.append(
                    'data',
                    JSON.stringify(collect())
                );

                if (!routes.walkIn && apptDate) {
                    formData.append('appointment_date', apptDate.value);
                    formData.append('appointment_time', chosenTime);
                }

                // Attach uploaded requirements
                Object.entries(prepared).forEach(([key, file]) => {
                    formData.append(
                        `docs[${key}]`,
                        file,
                        file.name
                    );
                });


                const response = await fetch(routes.store, {
                    method: 'POST',

                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },

                    body: formData
                });


                const json = await response.json().catch(() => ({}));


                if (!response.ok) {
                    if (response.status === 413) {
                        throw new Error(
                            'Masyadong malaki ang kabuuang laki ng mga file. Paliitin o bawasan ang mga ito.'
                        );
                    }

                    if (json.slot_unavailable && window.refreshApptSlots) {
                        window.refreshApptSlots();
                    }

                    let errorMessage = json.message || 'May error sa pag-save.';

                    if (json.errors) {
                        const firstError = Object.values(json.errors)[0];

                        if (firstError && firstError.length) {
                            errorMessage = firstError[0];
                        }
                    }

                    throw new Error(errorMessage);
                }


                // Successful submission
                let successMessage =
                    'Na-submit na ang form at mga requirements. Salamat! Pwede mo na ring i-print ang form.';

                setMsg(successMessage);

                if (json.tracking_number) {
                    showTrackingModal(json.tracking_number, json);

                    if (window.setApptStep) window.setApptStep(4);
                }

                // Keep disabled to prevent duplicate submission

            } catch (error) {
                console.error('Enrollment submission error:', error);

                setMsg(error.message || 'May error sa pag-submit.', true);

                saveBtn.disabled = false;
            }
        });
    }



    /* =====================================================
       REFERENCE ID POPUP (after successful save)
    ===================================================== */

    function showTrackingModal(ref, info) {
        const overlay = document.createElement('div');
        overlay.className = 'no-print';
        overlay.style.cssText =
            'position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:99999;' +
            'display:flex;align-items:center;justify-content:center;padding:16px;';

        const box = document.createElement('div');
        box.style.cssText =
            'background:#fff;max-width:440px;width:100%;border-radius:12px;' +
            'padding:28px 24px;text-align:center;font-family:Arial,sans-serif;' +
            'box-shadow:0 10px 40px rgba(0,0,0,.3);';

        const title = document.createElement('h2');
        title.textContent = 'Na-submit na ang enrollment mo!';
        title.style.cssText = 'margin:0 0 6px;font-size:20px;color:#1b5e20;';

        const note = document.createElement('p');
        note.textContent =
            'Itago ang Reference ID na ito. Gamitin ito (kasama ang email mo) para ma-track ang application mo.';
        note.style.cssText = 'margin:0 0 16px;font-size:14px;color:#555;';

        const code = document.createElement('div');
        code.textContent = ref;
        code.style.cssText =
            'font-size:26px;font-weight:700;letter-spacing:2px;padding:14px;' +
            'border:2px dashed #1b5e20;border-radius:8px;background:#f1f8e9;' +
            'user-select:all;margin-bottom:16px;word-break:break-all;';

        const copyBtn = document.createElement('button');
        copyBtn.type = 'button';
        copyBtn.textContent = 'I-copy ang Reference ID';
        copyBtn.style.cssText =
            'padding:10px 16px;margin:0 6px 8px;border:0;border-radius:6px;' +
            'background:#1b5e20;color:#fff;cursor:pointer;font-size:14px;';
        copyBtn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(ref);
                copyBtn.textContent = 'Na-copy!';
            } catch (e) {
                const range = document.createRange();
                range.selectNodeContents(code);
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
                copyBtn.textContent = 'Piliin na — pindutin ang Ctrl+C';
            }
        });

        const trackLink = document.createElement('a');
        trackLink.href = routes.track || '#';
        trackLink.target = '_blank';
        trackLink.textContent = 'Track my application';
        trackLink.style.cssText =
            'display:inline-block;padding:10px 16px;margin:0 6px 8px;border-radius:6px;' +
            'border:1px solid #1b5e20;color:#1b5e20;text-decoration:none;font-size:14px;';

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.textContent = 'Isara';
        closeBtn.style.cssText =
            'display:block;margin:8px auto 0;border:0;background:none;color:#777;' +
            'cursor:pointer;font-size:13px;text-decoration:underline;';
        closeBtn.addEventListener('click', () => overlay.remove());

        box.append(title, note, code);

        if (info && info.appointment_label) {
            const appt = document.createElement('p');
            appt.textContent = 'Appointment: ' + info.appointment_label;
            appt.style.cssText = 'margin:0 0 14px;font-size:14px;font-weight:700;color:#1d3a7a;';
            box.append(appt);
        }

        box.append(copyBtn);

        if (info && info.view_url) {
            const viewLink = document.createElement('a');
            viewLink.href = info.view_url;
            viewLink.textContent = 'Open student record';
            viewLink.style.cssText = trackLink.style.cssText;
            box.append(viewLink);
        } else {
            box.append(trackLink);
        }

        box.append(closeBtn);
        overlay.appendChild(box);
        document.body.appendChild(overlay);
    }


    /* =====================================================
       8. PRINT BUTTON
    ===================================================== */

    const printBtn = document.getElementById('btnPrint');

    if (printBtn) {
        printBtn.addEventListener('click', () => {
            window.print();
        });
    }


    /* =====================================================
       9. NEW / CLEAR FORM BUTTON
    ===================================================== */

    const clearBtn = document.getElementById('btnClear');

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {

            if (!confirm('Magsisimula ng bagong blangkong form. Tuloy?')) {
                return;
            }

            form.reset();

            // Clear letter boxes
            form.querySelectorAll('.boxes input').forEach(input => {
                input.value = '';
            });

            // Restore default country
            if (form.elements['cur_country']) {
                form.elements['cur_country'].value = 'PHILIPPINES';
            }

            // Restore address behavior
            if (same) {
                syncAddress();
            }

            // Clear uploaded files and their information
            docInputs.forEach(input => {
                input.value = '';
                setInfo(input.dataset.doc, '');
            });

            Object.keys(prepared).forEach(key => {
                delete prepared[key];
            });

            // Re-enable save button
            if (saveBtn) {
                saveBtn.disabled = false;
            }

            setMsg('');
        });
    }

});
