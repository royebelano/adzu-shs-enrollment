
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PDF Field Mapping</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #f1f5f9;
    color: #0f172a;
    font-family: Arial, sans-serif;
}

header {
    background: #0f172a;
    color: white;
    padding: 16px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

main {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 390px;
    gap: 16px;
    padding: 16px;
    max-width: 1600px;
    margin: auto;
}

.panel {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px;
    min-width: 0;
}

.toolbar {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

button,
select,
input {
    font: inherit;
    padding: 9px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: white;
}

button {
    cursor: pointer;
    background: #0f172a;
    color: white;
    border: 0;
}

button.secondary {
    background: #e2e8f0;
    color: #0f172a;
}

#pdf-wrap {
    overflow: auto;
    background: #64748b;
    padding: 16px;
    border-radius: 8px;
    min-height: 70vh;
    text-align: center;
}

.page {
    position: relative;
    margin: 0 auto 18px;
    display: inline-block;
    background: white;
    line-height: 0;
    box-shadow: 0 2px 10px #0003;
}

.page canvas {
    display: block;
    max-width: 100%;
    height: auto;
}

.marker {
    position: absolute;
    width: 12px;
    height: 12px;
    border: 2px solid #dc2626;
    border-radius: 50%;
    background: #fff8;
    transform: translate(-50%, -50%);
    pointer-events: none;
}

.field-list {
    max-height: 28vh;
    overflow: auto;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-top: 10px;
}

.field-row {
    padding: 9px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    gap: 8px;
    align-items: center;
}

.field-row:last-child {
    border: 0;
}

.field-row small {
    color: #64748b;
    display: block;
}

.field-row button {
    margin-left: auto;
    padding: 6px 9px;
    font-size: 12px;
    flex-shrink: 0;
}

.status {
    padding: 10px;
    background: #eff6ff;
    border-radius: 8px;
    margin: 10px 0;
    line-height: 1.45;
}

.controls {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin: 12px 0;
}

.controls label {
    display: block;
    font-size: 12px;
    color: #475569;
}

.controls input,
.controls select {
    width: 100%;
    margin-top: 5px;
}

a {
    color: inherit;
}

.hint {
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
}

@media(max-width: 900px) {
    main {
        grid-template-columns: 1fr;
    }

    .field-list {
        max-height: 240px;
    }
}
</style>
</head>

<body>

<header>
    <div>
        <strong>PDF Field Mapping</strong>

        <div style="opacity:.75;margin-top:4px">
            {{ $form->title }} · {{ $form->version_tag }}
        </div>
    </div>

    <a href="{{ route('admin.enrollment-forms') }}" style="color:white">
        Back to Forms
    </a>
</header>

<main>

    <!-- PDF PREVIEW -->
    <section class="panel">

        <div class="toolbar">

            <button type="button" class="secondary" id="prev">
                Previous page
            </button>

            <span id="page-label">Page 1</span>

            <button type="button" class="secondary" id="next">
                Next page
            </button>

            <label>
                Zoom

                <select id="zoom">
                    <option value="1">100%</option>
                    <option value="1.25" selected>125%</option>
                    <option value="1.5">150%</option>
                    <option value="1.75">175%</option>
                </select>
            </label>

        </div>

        <div class="status">

            <b>Text fields:</b>
            Piliin ang field at i-click ang gitna ng unang character box.
            Automatic na susundan ng system ang kasunod na boxes gamit ang Cell Width at Gap.

            <br><br>

            <b>Checkbox fields:</b>
            Piliin ang specific option, halimbawa Sex — Male,
            pagkatapos i-click ang gitna ng checkbox sa PDF.

        </div>

        <div id="pdf-wrap">
            Loading PDF preview...
        </div>

    </section>

    <!-- MAPPING CONTROLS -->
    <aside class="panel">

        <h3 style="margin-top:0">
            Selected Field
        </h3>

        <div id="selected-label" class="status">
            No field selected.
        </div>

        <div class="controls">

            <label>
                Font Size (pt)
                <input id="font-size" type="number"
                    min="4" max="24" step="0.5" value="8">
            </label>

            <label>
                Cell Width (mm)
                <input id="cell-width" type="number"
                    min="1" max="20" step="0.1" value="5">
            </label>

            <label>
                Cell Gap (mm)
                <input id="cell-gap" type="number"
                    min="0" max="10" step="0.1" value="0">
            </label>

            <label>
                Cell Height (mm)
                <input id="cell-height" type="number"
                    min="2" max="15" step="0.1" value="5">
            </label>

            <label>
                Maximum Characters
                <input id="max-chars" type="number"
                    min="1" max="100" value="30">
            </label>

            <label>
                Text Alignment

                <select id="text-align">
                    <option value="C">Center</option>
                    <option value="L">Left</option>
                    <option value="R">Right</option>
                </select>
            </label>

            <label>
                Checkbox Mark

                <select id="check-mark">
                    <option value="X">X mark</option>
                    <option value="/">Slash</option>
                </select>
            </label>

            <label>
                Checkbox Mark Size (mm)
                <input id="box-size" type="number"
                    min="2" max="12" step="0.1" value="4">
            </label>

        </div>

        <p class="hint">
            Ang X/Y ay posisyon ng gitna ng unang character cell
            o checkbox. Ang cell width, gap, height at font size
            ay naka-save sa bawat text mapping.
        </p>

        <hr style="border:0;border-top:1px solid #e2e8f0">

        <h3>Fields to Map</h3>

        <input
            id="search"
            placeholder="Search fields..."
            style="width:100%"
        >

        <div class="field-list" id="field-list"></div>

        <h3>Saved Mappings</h3>

        <div class="field-list" id="mapping-list"></div>

        <form
            method="POST"
            action="{{ route('admin.enrollment-forms.mapping.save', $form) }}"
            id="mapping-form"
        >

            @csrf

            <div id="hidden-mappings"></div>

            <button type="submit" style="width:100%;margin-top:12px">
                Save All Mappings
            </button>

        </form>

        @if(session('success'))
            <div class="status" style="background:#dcfce7;color:#166534">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="status" style="background:#fee2e2;color:#991b1b">
                {{ $errors->first() }}
            </div>
        @endif

        <p class="hint">
            I-map ang bawat checkbox option nang magkakahiwalay.
            Pagkatapos mag-save, mag-submit ng test enrollment
            at i-check ang generated PDF.
        </p>

    </aside>

</main>

<script>

pdfjsLib.GlobalWorkerOptions.workerSrc =
    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

const pdfUrl = @json($pdfUrl);
const fields = @json($fields);

let pdfDoc = null;
let pageNo = 1;
let selected = null;
let scale = 1.25;

let mappings = @json($mappings);

const wrap = document.getElementById('pdf-wrap');
const fieldList = document.getElementById('field-list');
const mappingList = document.getElementById('mapping-list');
const hidden = document.getElementById('hidden-mappings');

function keyFor(field) {
    return field.type === 'checkbox_option'
        ? field.name + '::' + field.option_value
        : field.name;
}

function val(id) {
    return document.getElementById(id).value;
}

function setVal(id, value) {
    if (value !== undefined && value !== null) {
        document.getElementById(id).value = value;
    }
}

function selectField(field) {

    selected = field;

    document.getElementById('selected-label').textContent =
        field.label + ' (' + keyFor(field) + ')';

    const existing = mappings.find(
        mapping => mapping.key === keyFor(field)
    );

    if (existing) {

        setVal('font-size', existing.font_size || 8);
        setVal('cell-width', existing.cell_width || 5);
        setVal('cell-gap', existing.cell_gap || 0);
        setVal('cell-height', existing.cell_height || 5);
        setVal('max-chars', existing.max_chars || 30);
        setVal('text-align', existing.align || 'C');
        setVal('check-mark', existing.mark || 'X');
        setVal('box-size', existing.box_size || 4);

        pageNo = Number(existing.page) || 1;

        if (pdfDoc) {
            renderPage();
        }

    } else {

        setVal(
            'font-size',
            field.type === 'checkbox_option' ? 9 : 8
        );

    }

    drawFields();
}

function drawFields() {

    const search = document
        .getElementById('search')
        .value
        .toLowerCase();

    fieldList.innerHTML = '';

    fields
        .filter(field =>
            (field.label + ' ' + field.name)
                .toLowerCase()
                .includes(search)
        )
        .forEach(field => {

            const row = document.createElement('div');
            row.className = 'field-row';

            const span = document.createElement('span');

            const strong = document.createElement('strong');
            strong.textContent = field.label;

            const small = document.createElement('small');
            small.textContent = keyFor(field);

            span.append(strong, small);

            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = 'Select';
            button.className = 'secondary';

            button.onclick = () => selectField(field);

            row.append(span, button);

            fieldList.append(row);

        });
}

function drawMappings() {

    mappingList.innerHTML = '';
    hidden.innerHTML = '';

    mappings.forEach((mapping, index) => {

        const row = document.createElement('div');
        row.className = 'field-row';

        const label = document.createElement('span');

        label.textContent =
            mapping.label +
            ' · page ' + mapping.page +
            ' · ' +
            (mapping.kind === 'checkbox'
                ? 'checkbox'
                : (mapping.font_size || 8) + 'pt');

        const remove = document.createElement('button');

        remove.type = 'button';
        remove.className = 'secondary';
        remove.textContent = 'Remove';

        remove.onclick = () => {

            mappings.splice(index, 1);

            drawMappings();

            if (pdfDoc) {
                renderPage();
            }

        };

        row.append(label, remove);
        mappingList.append(row);

        const values = {

            key: mapping.key,
            field: mapping.field,
            label: mapping.label,
            page: mapping.page,
            x: mapping.x,
            y: mapping.y,

            kind: mapping.kind,
            option_value: mapping.option_value || '',

            font_size: mapping.font_size || 8,
            cell_width: mapping.cell_width || 5,
            cell_gap: mapping.cell_gap || 0,
            cell_height: mapping.cell_height || 5,
            max_chars: mapping.max_chars || 30,

            align: mapping.align || 'C',

            box_size: mapping.box_size || 4,
            mark: mapping.mark || 'X'

        };

        Object.entries(values).forEach(([key, value]) => {

            const input = document.createElement('input');

            input.type = 'hidden';
            input.name = `mappings[${index}][${key}]`;
            input.value = value;

            hidden.append(input);

        });

    });

}

async function renderPage() {

    if (!pdfDoc) return;

    const page = await pdfDoc.getPage(pageNo);

    const viewport = page.getViewport({
        scale: scale
    });

    wrap.innerHTML = '';

    const holder = document.createElement('div');

    holder.className = 'page';
    holder.style.width = viewport.width + 'px';
    holder.style.height = viewport.height + 'px';

    const canvas = document.createElement('canvas');

    canvas.width = Math.ceil(viewport.width);
    canvas.height = Math.ceil(viewport.height);

    holder.append(canvas);
    wrap.append(holder);

    await page.render({
        canvasContext: canvas.getContext('2d'),
        viewport: viewport
    }).promise;

    mappings
        .filter(mapping => Number(mapping.page) === pageNo)
        .forEach(mapping => {

            const marker = document.createElement('div');

            marker.className = 'marker';

            marker.style.left = (Number(mapping.x) * 100) + '%';
            marker.style.top = (Number(mapping.y) * 100) + '%';

            holder.append(marker);

        });

    holder.addEventListener('click', event => {

        if (!selected) {
            alert('Select a field first.');
            return;
        }

        const rect = holder.getBoundingClientRect();

        const x = (event.clientX - rect.left) / rect.width;
        const y = (event.clientY - rect.top) / rect.height;

        const isCheckbox =
            selected.type === 'checkbox_option';

        const key = keyFor(selected);

        mappings = mappings.filter(
            mapping => mapping.key !== key
        );

        mappings.push({

            key: key,
            field: selected.name,
            label: selected.label,

            page: pageNo,

            x: Math.max(0, Math.min(1, x)),
            y: Math.max(0, Math.min(1, y)),

            kind: isCheckbox ? 'checkbox' : 'text',

            option_value: selected.option_value || '',

            font_size: Number(val('font-size')) || 8,

            cell_width: Number(val('cell-width')) || 5,
            cell_gap: Number(val('cell-gap')) || 0,
            cell_height: Number(val('cell-height')) || 5,

            max_chars: Number(val('max-chars')) || 30,

            align: val('text-align') || 'C',

            box_size: Number(val('box-size')) || 4,
            mark: val('check-mark') || 'X'

        });

        drawMappings();
        renderPage();

    });

    document.getElementById('page-label').textContent =
        `Page ${pageNo} of ${pdfDoc.numPages}`;

}

document.getElementById('prev').onclick = () => {

    if (pdfDoc && pageNo > 1) {
        pageNo--;
        renderPage();
    }

};

document.getElementById('next').onclick = () => {

    if (pdfDoc && pageNo < pdfDoc.numPages) {
        pageNo++;
        renderPage();
    }

};

document.getElementById('zoom').onchange = event => {

    scale = Number(event.target.value);

    if (pdfDoc) {
        renderPage();
    }

};

document.getElementById('search').oninput = drawFields;

document.getElementById('mapping-form').onsubmit = () => {
    drawMappings();
    return true;
};

drawFields();
drawMappings();

pdfjsLib.getDocument(pdfUrl).promise
    .then(document => {

        pdfDoc = document;

        renderPage();

    })
    .catch(error => {

        wrap.textContent =
            'Unable to preview PDF. Check public/storage symlink and PDF accessibility.';

        console.error(error);

    });

</script>

</body>
</html>
