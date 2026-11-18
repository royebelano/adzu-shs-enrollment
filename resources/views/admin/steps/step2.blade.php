
@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto transition-all duration-300"
     x-data="{
         selectedStudentId: null,
         searchQuery: '',
         students: @js($step2_students ?? []),
         activeStudent: {},

         selectStudent(student) {
             this.activeStudent = JSON.parse(JSON.stringify(student));

             // Use the editable student ID if it has been updated in Step 4; otherwise, use the original student ID.
             this.activeStudent.student_id = student.editable_student_id || student.student_id;

             // Store the lookup key used for the backend query.
             this.activeStudent.original_lookup_id = student.reference_id || student.student_id;

             // Store the permanent reference ID displayed in the panel header.
             this.activeStudent.locked_ref_id = student.reference_id || ('adzu-' + new Date().getFullYear() + '-' + String(student.student_id).padStart(7, '0'));

             this.selectedStudentId = this.selectedStudentId === student.student_id ? null : student.student_id;
         },

         saveStudent() {
             if (!this.activeStudent.original_lookup_id) return;

             if (!confirm('Are you sure you want to save these medical record changes?')) {
                 return;
             }

             let csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]');
             let tokenValue = csrfToken ? csrfToken.getAttribute('content') : '{{ csrf_token() }}';

             fetch('/admin/students/' + this.activeStudent.original_lookup_id, {
                 method: 'PUT',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': tokenValue,
                     'Accept': 'application/json'
                 },
                 body: JSON.stringify({
                     medical_assessment_date: this.activeStudent.medical_assessment_date,
                     physical_exam: this.activeStudent.physical_exam ? 1 : 0,
                     dental_exam: this.activeStudent.dental_exam ? 1 : 0,
                     medical_certificate: this.activeStudent.medical_certificate ? 1 : 0
                 })
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     alert(data.message || 'Medical records updated successfully!');
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

             if (!confirm('Are you sure you want to advance this student to Step 3?')) {
                 return;
             }

             let csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]');
             let tokenValue = csrfToken ? csrfToken.getAttribute('content') : '{{ csrf_token() }}';

             fetch('/admin/students/' + this.activeStudent.original_lookup_id + '/advance-step3', {
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
                     alert(data.message || 'Student successfully advanced to Step 3!');
                     location.reload();
                 } else {
                     alert('Error: ' + (data.message || 'Failed to advance the student.'));
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 alert('A connection or server error occurred.');
             });
         }
     }">

    <!-- Header and Title -->
    <div>
        <p class="text-[10px] font-black tracking-wider text-blue-600 uppercase mb-1">
            Admissions / Student Workflow
        </p>
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">
            Step 2 &bull; Infirmary Medical Assessment
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Manage medical examinations and physical clearances for students in Step 2.
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

    <!-- Step 2 Student Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 bg-[#0a1128] text-white text-[11px] font-bold rounded-lg shadow-sm">
                    02
                </span>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Infirmary</h4>
                    <p class="text-[11px] text-slate-400">
                        Current records are displayed by default; previously advanced records remain available.
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <span
                    class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[11px] font-bold"
                    x-text="students.length + ' shown'">
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                        <th class="px-4 py-3">ID Number</th>
                        <th class="px-4 py-3">Family Name</th>
                        <th class="px-4 py-3">Given Name</th>
                        <th class="px-4 py-3">Suffix</th>
                        <th class="px-4 py-3">Strand</th>
                        <th class="px-4 py-3">Assessment Date</th>
                        <th class="px-4 py-3 text-center">Physical / Dental</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    <template x-for="student in students" :key="student.student_id">
                        <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                            <!-- Use the editable student ID if available; otherwise, use the original student ID. -->
                            <td class="px-4 py-3.5 font-mono font-semibold text-slate-900"
                                x-text="student.editable_student_id || student.student_id || '—'">
                            </td>

                            <td class="px-4 py-3.5 text-slate-800 font-medium"
                                x-text="student.last_name || '—'">
                            </td>

                            <td class="px-4 py-3.5 text-slate-800 font-medium"
                                x-text="student.first_name || '—'">
                            </td>

                            <td class="px-4 py-3.5 text-slate-600"
                                x-text="student.suffix || '—'">
                            </td>

                            <td class="px-4 py-3.5">
                                <span
                                    class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-md font-bold text-[10px]"
                                    x-text="student.preferred_strand || '—'">
                                </span>
                            </td>

                            <td class="px-4 py-3.5 text-slate-500"
                                x-text="student.medical_assessment_date || '—'">
                            </td>

                            <td class="px-4 py-3.5 text-center font-semibold text-slate-600"
                                x-text="(student.physical_exam ? 'Passed' : 'Pending') + ' / ' + (student.dental_exam ? 'Passed' : 'Pending')">
                            </td>

                            <td class="px-4 py-3.5">
                                <span
                                    class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-[10px] font-bold shadow-2xs"
                                    x-text="student.status || 'Step 2'">
                                </span>
                            </td>

                            <td class="px-4 py-3.5 text-right">
                                <button
                                    @click="selectStudent(student)"
                                    class="px-3 py-1 bg-white border border-slate-200 rounded-lg font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition">
                                    <span x-text="selectedStudentId === student.student_id ? 'Close' : 'View / Edit'"></span>
                                </button>
                            </td>
                        </tr>
                    </template>

                    <template x-if="students.length === 0">
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400 text-xs">
                                No student records are currently available in the database for Step 2.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div
            class="px-6 py-3 bg-slate-50/50 border-t border-slate-100 text-[11px] text-slate-500"
            x-text="students.length + ' shown'">
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
                    STEP 2 MEDICAL RECORD (PERSONAL INFORMATION LOCKED)
                </p>
                <h3
                    class="text-lg font-extrabold text-slate-900"
                    x-text="(activeStudent.first_name || '') + ' ' + (activeStudent.last_name || '')">
                </h3>
            </div>

            <!-- Permanent reference ID displayed in the panel header -->
            <span
                class="text-xs font-mono font-semibold text-slate-400"
                x-text="'Reference ID: ' + (activeStudent.locked_ref_id || '')">
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Family Name</label>
                <input type="text" :value="activeStudent.last_name || ''" readonly
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Given Name</label>
                <input type="text" :value="activeStudent.first_name || ''" readonly
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Middle Name</label>
                <input type="text" :value="activeStudent.middle_name || ''" readonly
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Suffix</label>
                <input type="text" :value="activeStudent.suffix || ''" readonly
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Date of Birth</label>
                <input type="text" :value="activeStudent.date_of_birth || ''" readonly
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">LRN</label>
                <input type="text" :value="activeStudent.lrn || ''" readonly
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed font-mono">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Preferred Strand</label>
                <input type="text" :value="activeStudent.preferred_strand || ''" readonly
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-500 focus:outline-none cursor-not-allowed font-semibold">
            </div>

            <div class="md:col-span-4">
                <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Assessment Date (Editable)</label>
                <input type="date" x-model="activeStudent.medical_assessment_date"
                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
        </div>

        <!-- Editable Medical Checkboxes -->
        <div class="flex items-center space-x-4 pt-2">
            <label class="flex items-center space-x-2 px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 bg-white cursor-pointer hover:bg-slate-50 select-none">
                <input type="checkbox" x-model="activeStudent.physical_exam" :true-value="1" :false-value="0"
                    class="rounded text-blue-600 focus:ring-blue-500">
                <span>Physical Examination</span>
            </label>

            <label class="flex items-center space-x-2 px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 bg-white cursor-pointer hover:bg-slate-50 select-none">
                <input type="checkbox" x-model="activeStudent.dental_exam" :true-value="1" :false-value="0"
                    class="rounded text-blue-600 focus:ring-blue-500">
                <span>Dental Examination</span>
            </label>

            <label class="flex items-center space-x-2 px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 bg-white cursor-pointer hover:bg-slate-50 select-none">
                <input type="checkbox" x-model="activeStudent.medical_certificate" :true-value="1" :false-value="0"
                    class="rounded text-blue-600 focus:ring-blue-500">
                <span>Medical Certificate</span>
            </label>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <p class="text-[11px] text-slate-400 italic">
                Click Save Changes to update the medical information.
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
                    Advance to Step 3 &rarr;
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
