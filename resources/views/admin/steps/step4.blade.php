@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto transition-all duration-300"
     x-data="{
         selectedStudentId: null,
         searchQuery: '',
         students: @js($step4_students ?? []),
         activeStudent: {},

         get filteredStudents() {
             const query = this.searchQuery.trim().toLowerCase();

             if (!query) return this.students;

             return this.students.filter(student => {
                 return [
                     student.student_id,
                     student.editable_student_id,
                     student.reference_id,
                     student.first_name,
                     student.last_name,
                     student.contact_person,
                     student.contact_relationship
                 ].some(value => String(value ?? '').toLowerCase().includes(query));
             });
         },

         selectStudent(student) {
             this.activeStudent = JSON.parse(JSON.stringify(student));
             this.activeStudent.student_id = student.editable_student_id || student.student_id;
             this.activeStudent.original_lookup_id = student.reference_id || student.student_id;
             this.activeStudent.locked_ref_id = student.reference_id || ('adzu-' + new Date().getFullYear() + '-' + String(student.student_id).padStart(7, '0'));

             this.selectedStudentId = this.selectedStudentId === student.student_id
                 ? null
                 : student.student_id;
         },

         getDocument(name) {
             if (!this.activeStudent.documents) return null;

             return this.activeStudent.documents.find(d =>
                 d.document_name &&
                 d.document_name.toLowerCase().includes(name.toLowerCase())
             );
         },

         saveStudent() {
             if (!this.activeStudent.original_lookup_id) return;

             if (!confirm('Are you sure you want to save these changes?')) {
                 return;
             }

             let formData = new FormData();
             formData.append('_method', 'PUT');
             formData.append('student_id', this.activeStudent.student_id || '');
             formData.append('contact_person', this.activeStudent.contact_person || '');
             formData.append('contact_address', this.activeStudent.contact_address || '');
             formData.append('contact_relationship', this.activeStudent.contact_relationship || '');

             let fileInput = document.getElementById('barcodeInput');

             if (fileInput && fileInput.files[0]) {
                 formData.append('barcode_file', fileInput.files[0]);
             }

             let csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]');
             let tokenValue = csrfToken ? csrfToken.getAttribute('content') : '{{ csrf_token() }}';

             fetch('/admin/students/' + this.activeStudent.original_lookup_id, {
                 method: 'POST',
                 headers: {
                     'X-CSRF-TOKEN': tokenValue,
                     'Accept': 'application/json'
                 },
                 body: formData
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     alert(data.message || 'Student information has been updated successfully!');
                     location.reload();
                 } else {
                     alert('Error: ' + (data.message || 'Failed to save the data.'));
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 alert('A connection or server error occurred.');
             });
         },

         advanceToNextStep() {
             if (!this.activeStudent.original_lookup_id) return;

             if (!confirm('Are you sure you want to advance this student to Step 5?')) {
                 return;
             }

             let csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]');
             let tokenValue = csrfToken ? csrfToken.getAttribute('content') : '{{ csrf_token() }}';

             fetch('/admin/students/' + this.activeStudent.original_lookup_id + '/advance-step5', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': tokenValue,
                     'Accept': 'application/json'
                 }
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     alert(data.message || 'Student has been successfully advanced to Step 5!');
                     location.reload();
                 } else {
                     alert('Error: ' + (data.message || 'Failed to advance the student to the next step.'));
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 alert('A connection or server error occurred.');
             });
         }
     }">

    <!-- Header -->
    <div>
        <p class="text-[10px] font-black tracking-wider text-blue-600 uppercase mb-1">
            Admissions / Student Workflow
        </p>
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">
            Step 4 &bull; ID Capturing
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Manage student ID capturing, contact persons, and barcode files.
        </p>
    </div>

    <!-- Search Header -->
    <div class="flex items-center justify-end pt-2">
        <div class="w-72">
            <input
                type="text"
                x-model="searchQuery"
                placeholder="Search student or ID"
                class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 shadow-2xs">
        </div>
    </div>

    <!-- Main Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 bg-[#0a1128] text-white text-[11px] font-bold rounded-lg shadow-sm">
                    04
                </span>

                <div>
                    <h4 class="text-sm font-bold text-slate-900">ID Capturing</h4>
                    <p class="text-[11px] text-slate-400">Current records shown by default.</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <span
                    class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[11px] font-bold"
                    x-text="filteredStudents.length + ' shown'">
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                        <th class="px-4 py-3">Student ID</th>
                        <th class="px-4 py-3">Student</th>
                        <th class="px-4 py-3">Contact Person</th>
                        <th class="px-4 py-3">Relationship</th>
                        <th class="px-4 py-3 text-center">Barcode File</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    <template x-for="student in filteredStudents" :key="student.student_id">
                        <tr class="hover:bg-blue-50/40 transition-colors duration-150">

                            <td
                                class="px-4 py-3.5 font-mono font-semibold text-slate-900"
                                x-text="student.editable_student_id || student.student_id || '—'">
                            </td>

                            <td
                                class="px-4 py-3.5 text-slate-800 font-medium"
                                x-text="(student.last_name || '') + ', ' + (student.first_name || '')">
                            </td>

                            <td
                                class="px-4 py-3.5 text-slate-600"
                                x-text="student.contact_person || '—'">
                            </td>

                            <td
                                class="px-4 py-3.5 text-slate-600"
                                x-text="student.contact_relationship || '—'">
                            </td>

                            <td class="px-4 py-3.5 text-center">
                                <span
                                    class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                                    :class="student.documents && student.documents.some(d => d.document_name && d.document_name.toLowerCase().includes('barcode'))
                                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                        : 'bg-amber-50 text-amber-700 border border-amber-200'"
                                    x-text="student.documents && student.documents.some(d => d.document_name && d.document_name.toLowerCase().includes('barcode'))
                                        ? 'Uploaded'
                                        : 'Pending'">
                                </span>
                            </td>

                            <td class="px-4 py-3.5">
                                <span
                                    class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-[10px] font-bold shadow-2xs"
                                    x-text="student.status || 'In Progress'">
                                </span>
                            </td>

                            <td class="px-4 py-3.5 text-right">
                                <button
                                    type="button"
                                    @click="selectStudent(student)"
                                    class="px-3 py-1 bg-white border border-slate-200 rounded-lg font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition">
                                    <span x-text="selectedStudentId === student.student_id ? 'Close' : 'View / Edit'"></span>
                                </button>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredStudents.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                                No records are currently available for Step 4.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div
            class="px-6 py-3 bg-slate-50/50 border-t border-slate-100 text-[11px] text-slate-500"
            x-text="filteredStudents.length + ' shown'">
        </div>
    </div>

    <!-- View / Edit Panel -->
    <div
        x-show="selectedStudentId !== null"
        x-transition.opacity.duration.300ms
        class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6 space-y-6"
        style="display: none;">

        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    Step 4 Record
                </p>

                <h3
                    class="text-lg font-extrabold text-slate-900"
                    x-text="(activeStudent.first_name || '') + ' ' + (activeStudent.last_name || '')">
                </h3>
            </div>

            <span
                class="text-xs font-mono font-semibold text-slate-400"
                x-text="'Ref ID: ' + (activeStudent.locked_ref_id || '')">
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <!-- Editable Student ID -->
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">
                    Student ID &bull; Editable
                </label>

                <input
                    type="text"
                    x-model="activeStudent.student_id"
                    placeholder="Enter student ID"
                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 font-mono">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">
                    Family Name &bull; From Student Record
                </label>

                <input
                    type="text"
                    :value="activeStudent.last_name || ''"
                    readonly
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">
                    Given Name &bull; From Student Record
                </label>

                <input
                    type="text"
                    :value="activeStudent.first_name || ''"
                    readonly
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">
                    Contact Person
                </label>

                <input
                    type="text"
                    x-model="activeStudent.contact_person"
                    placeholder="Enter contact person"
                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">
                    Contact Address
                </label>

                <input
                    type="text"
                    x-model="activeStudent.contact_address"
                    placeholder="Enter contact address"
                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">
                    Relationship to Student
                </label>

                <input
                    type="text"
                    x-model="activeStudent.contact_relationship"
                    placeholder="Mother, Father, Guardian..."
                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>

            <div class="md:col-span-3">
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">
                    Strand &bull; From Student Record
                </label>

                <input
                    type="text"
                    :value="activeStudent.preferred_strand || ''"
                    readonly
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed font-semibold">
            </div>
        </div>

        <!-- Uploaded Barcode Preview & Replace Input -->
        <div class="pt-4 border-t border-slate-100 space-y-3">
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                Barcode / ID Document
            </label>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Current Barcode -->
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-2xs space-y-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800">Current Barcode File</span>

                            <span
                                class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold"
                                x-text="getDocument('Barcode') ? 'Uploaded' : 'No File'">
                            </span>
                        </div>

                        <template x-if="getDocument('Barcode')">
                            <div class="h-36 bg-slate-100 border border-slate-200 rounded-xl overflow-hidden flex items-center justify-center">
                                <img
                                    :src="'/storage/' + getDocument('Barcode').file_path"
                                    class="w-full h-full object-cover"
                                    alt="Barcode">
                            </div>
                        </template>

                        <template x-if="!getDocument('Barcode')">
                            <div class="h-36 bg-slate-50 border border-dashed border-slate-200 rounded-xl flex items-center justify-center text-slate-400 text-xs italic">
                                No barcode file has been uploaded.
                            </div>
                        </template>
                    </div>

                    <template x-if="getDocument('Barcode')">
                        <a
                            :href="'/storage/' + getDocument('Barcode').file_path"
                            target="_blank"
                            class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold text-center transition block shadow-xs">
                            View / Download File &rarr;
                        </a>
                    </template>
                </div>

                <!-- Replace Barcode -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 shadow-2xs space-y-3 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block mb-2">
                            Upload / Replace Barcode File
                        </span>

                        <input
                            type="file"
                            id="barcodeInput"
                            class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                        <p class="text-[11px] text-slate-400 mt-2">
                            Select a new file to replace the existing barcode before clicking Save Changes.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <p class="text-[11px] text-slate-400 italic">
                Click Save Changes to update the student details or replace the barcode file.
            </p>

            <div class="flex items-center space-x-3">
                <button
                    type="button"
                    @click="saveStudent()"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md transition transform active:scale-95">
                    Save Changes
                </button>

                <button
                    type="button"
                    @click="advanceToNextStep()"
                    class="px-5 py-2.5 bg-[#0a1128] hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-md transition transform active:scale-95">
                    Advance to Step 5 &rarr;
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

