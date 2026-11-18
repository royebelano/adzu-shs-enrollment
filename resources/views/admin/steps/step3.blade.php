@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto transition-all duration-300"
     x-data="{
         selectedStudentId: null,
         searchQuery: '',
         students: @js($step3_students ?? []),
         activeStudent: {},
         selectStudent(student) {
             this.activeStudent = JSON.parse(JSON.stringify(student));
             this.activeStudent.student_id = student.editable_student_id || student.student_id;
             this.activeStudent.original_lookup_id = student.reference_id || student.student_id;
             this.activeStudent.locked_ref_id = student.reference_id || ('adzu-' + new Date().getFullYear() + '-' + String(student.student_id).padStart(7, '0'));
             this.selectedStudentId = this.selectedStudentId === student.student_id ? null : student.student_id;
         },
         getDocument(type) {
             if (!this.activeStudent.documents) return null;
             return this.activeStudent.documents.find(d => d.document_type === type || (d.document_name && d.document_name.toLowerCase().includes(type.toLowerCase())));
         },
         saveStudent() {
             if (!this.activeStudent.original_lookup_id) return;

             if (!confirm('Sigurado ka bang nais mong i-save ang mga pagbabagong ito?')) {
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
                     student_id: this.activeStudent.student_id,
                     last_name: this.activeStudent.last_name,
                     first_name: this.activeStudent.first_name,
                     middle_name: this.activeStudent.middle_name,
                     suffix: this.activeStudent.suffix,
                     date_of_birth: this.activeStudent.date_of_birth,
                     lrn: this.activeStudent.lrn,
                     school: this.activeStudent.school,
                     preferred_strand: this.activeStudent.preferred_strand,
                     form_138: this.activeStudent.form_138 ? 1 : 0,
                     psa: this.activeStudent.psa ? 1 : 0,
                     good_moral: this.activeStudent.good_moral ? 1 : 0
                 })
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     alert(data.message || 'Matagumpay na na-update!');
                     location.reload();
                 } else {
                     alert('Error: ' + (data.message || 'Hindi na-save ang data.'));
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 alert('Nagkaroon ng problema sa koneksyon o server.');
             });
         },
         advanceToNextStep() {
             if (!this.activeStudent.original_lookup_id) return;

             if (!confirm('Sigurado ka bang nais mong i-advance ang estudyanteng ito sa Step 4?')) {
                 return;
             }

             let csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]');
             let tokenValue = csrfToken ? csrfToken.getAttribute('content') : '{{ csrf_token() }}';

             fetch('/admin/students/' + this.activeStudent.original_lookup_id + '/advance-step4', {
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
                     alert(data.message || 'Matagumpay na na-advance ang estudyante!');
                     location.reload();
                 } else {
                     alert('Error: ' + (data.message || 'Hindi na-proseso ang pag-advance.'));
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 alert('Nagkaroon ng problema sa koneksyon o server.');
             });
         }
     }">

    <!-- Breadcrumbs & Header -->
    <div>
        <p class="text-[10px] font-black tracking-wider text-blue-600 uppercase mb-1">Admissions / Student Workflow</p>
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Step 3 &bull; Admission Processing</h2>
        <p class="text-xs text-slate-500 mt-0.5">Manage students who reached Step 3. Records remain available after they advance.</p>
    </div>

    <!-- Search Header -->
    <div class="flex items-center justify-end pt-2">
        <div class="w-72">
            <input type="text" x-model="searchQuery" placeholder="Search student or ID" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 shadow-2xs">
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 bg-[#0a1128] text-white text-[11px] font-bold rounded-lg shadow-sm">03</span>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Admission</h4>
                    <p class="text-[11px] text-slate-400">Current records shown by default.</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[11px] font-bold" x-text="students.length + ' shown'"></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                        <th class="px-4 py-3">ID Number</th>
                        <th class="px-4 py-3">Last Name</th>
                        <th class="px-4 py-3">Given Name</th>
                        <th class="px-4 py-3">Middle Name</th>
                        <th class="px-4 py-3">School</th>
                        <th class="px-4 py-3 text-center">Form 138</th>
                        <th class="px-4 py-3 text-center">PSA</th>
                        <th class="px-4 py-3 text-center">Good Moral</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    <template x-for="student in students" :key="student.student_id">
                        <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                            <td class="px-4 py-3.5 font-mono font-semibold text-slate-900" x-text="student.editable_student_id || student.student_id || '—'"></td>
                            <td class="px-4 py-3.5 text-slate-800 font-medium" x-text="student.last_name || '—'"></td>
                            <td class="px-4 py-3.5 text-slate-800 font-medium" x-text="student.first_name || '—'"></td>
                            <td class="px-4 py-3.5 text-slate-600" x-text="student.middle_name || '—'"></td>
                            <td class="px-4 py-3.5 text-slate-500" x-text="student.school || '—'"></td>

                            <!-- Form 138 Badge -->
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                                      :class="student.form_138 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'"
                                      x-text="student.form_138 ? 'Done' : 'Pending'"></span>
                            </td>

                            <!-- PSA Badge -->
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                                      :class="student.psa ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'"
                                      x-text="student.psa ? 'Done' : 'Pending'"></span>
                            </td>

                            <!-- Good Moral Badge -->
                            <td class="px-4 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                                      :class="student.good_moral ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'"
                                      x-text="student.good_moral ? 'Done' : 'Pending'"></span>
                            </td>

                            <td class="px-4 py-3.5">
                                <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-[10px] font-bold shadow-2xs" x-text="student.status || 'Step 3'"></span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <button @click="selectStudent(student)"
                                        class="px-3 py-1 bg-white border border-slate-200 rounded-lg font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition">
                                    <span x-text="selectedStudentId === student.student_id ? 'Close' : 'View / edit'"></span>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="students.length === 0">
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-400 text-xs">
                                Walang kasalukuyang tala o records sa database para sa Step 3.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 bg-slate-50/50 border-t border-slate-100 text-[11px] text-slate-500" x-text="students.length + ' shown'"></div>
    </div>

    <!-- View / Edit Panel -->
    <div x-show="selectedStudentId !== null" x-transition.opacity.duration.300ms class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6 space-y-6" style="display: none;">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">STEP 3 RECORD</p>
                <h3 class="text-lg font-extrabold text-slate-900" x-text="(activeStudent.first_name || '') + ' ' + (activeStudent.last_name || '')"></h3>
            </div>
            <!-- Permanent Reference ID sa Header ng Panel -->
            <span class="text-xs font-mono font-semibold text-slate-400" x-text="'Ref ID: ' + (activeStudent.locked_ref_id || '')"></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Last name</label>
                <input type="text" x-model="activeStudent.last_name" placeholder="Enter last name" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Given name</label>
                <input type="text" x-model="activeStudent.first_name" placeholder="Enter given name" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Middle name</label>
                <input type="text" x-model="activeStudent.middle_name" placeholder="Enter middle name" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Suffix</label>
                <input type="text" x-model="activeStudent.suffix" placeholder="Jr., Sr." class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Date of Birth</label>
                <input type="date" x-model="activeStudent.date_of_birth" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">LRN</label>
                <input type="text" x-model="activeStudent.lrn" placeholder="12-digit LRN" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 font-mono">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Preferred Strand</label>
                <input type="text" x-model="activeStudent.preferred_strand" placeholder="Strand" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 font-semibold">
            </div>

            <div class="md:col-span-4">
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">School</label>
                <input type="text" x-model="activeStudent.school" placeholder="Enter school name" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
        </div>

        <!-- Document Checkboxes -->
        <div class="flex items-center space-x-4 pt-2">
            <label class="flex items-center space-x-2 px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 bg-white cursor-pointer hover:bg-slate-50 select-none">
                <input type="checkbox" x-model="activeStudent.form_138" :true-value="1" :false-value="0" class="rounded text-blue-600 focus:ring-blue-500">
                <span>Form 138</span>
            </label>
            <label class="flex items-center space-x-2 px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 bg-white cursor-pointer hover:bg-slate-50 select-none">
                <input type="checkbox" x-model="activeStudent.psa" :true-value="1" :false-value="0" class="rounded text-blue-600 focus:ring-blue-500">
                <span>PSA</span>
            </label>
            <label class="flex items-center space-x-2 px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 bg-white cursor-pointer hover:bg-slate-50 select-none">
                <input type="checkbox" x-model="activeStudent.good_moral" :true-value="1" :false-value="0" class="rounded text-blue-600 focus:ring-blue-500">
                <span>Good moral</span>
            </label>
        </div>

        <!-- STUDENT-UPLOADED REQUIREMENTS PREVIEW -->
        <div class="pt-4 border-t border-slate-100 space-y-3">
            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">STUDENT-UPLOADED REQUIREMENTS</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Form 138 Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-2xs space-y-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800">Form 138</span>
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold" x-text="getDocument('Form 138') ? 'Uploaded' : 'No File'"></span>
                        </div>
                        <template x-if="getDocument('Form 138')">
                            <div class="h-32 bg-slate-100 border border-slate-200 rounded-xl overflow-hidden flex items-center justify-center">
                                <img :src="'/storage/' + getDocument('Form 138').file_path" class="w-full h-full object-cover" alt="Form 138">
                            </div>
                        </template>
                        <template x-if="!getDocument('Form 138')">
                            <div class="h-32 bg-slate-50 border border-dashed border-slate-200 rounded-xl flex items-center justify-center text-slate-400 text-xs italic">
                                Walang na-upload na file
                            </div>
                        </template>
                    </div>
                    <template x-if="getDocument('Form 138')">
                        <a :href="'/storage/' + getDocument('Form 138').file_path" target="_blank" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold text-center transition block shadow-xs">
                            View / Download File &rarr;
                        </a>
                    </template>
                </div>

                <!-- Good Moral Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-2xs space-y-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800">Good Moral</span>
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold" x-text="getDocument('Good Moral') ? 'Uploaded' : 'No File'"></span>
                        </div>
                        <template x-if="getDocument('Good Moral')">
                            <div class="h-32 bg-slate-100 border border-slate-200 rounded-xl overflow-hidden flex items-center justify-center">
                                <img :src="'/storage/' + getDocument('Good Moral').file_path" class="w-full h-full object-cover" alt="Good Moral">
                            </div>
                        </template>
                        <template x-if="!getDocument('Good Moral')">
                            <div class="h-32 bg-slate-50 border border-dashed border-slate-200 rounded-xl flex items-center justify-center text-slate-400 text-xs italic">
                                Walang na-upload na file
                            </div>
                        </template>
                    </div>
                    <template x-if="getDocument('Good Moral')">
                        <a :href="'/storage/' + getDocument('Good Moral').file_path" target="_blank" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold text-center transition block shadow-xs">
                            View / Download File &rarr;
                        </a>
                    </template>
                </div>

                <!-- PSA Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-2xs space-y-3 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800">PSA Birth Certificate</span>
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold" x-text="getDocument('PSA') ? 'Uploaded' : 'No File'"></span>
                        </div>
                        <template x-if="getDocument('PSA')">
                            <div class="h-32 bg-slate-100 border border-slate-200 rounded-xl overflow-hidden flex items-center justify-center">
                                <img :src="'/storage/' + getDocument('PSA').file_path" class="w-full h-full object-cover" alt="PSA">
                            </div>
                        </template>
                        <template x-if="!getDocument('PSA')">
                            <div class="h-32 bg-slate-50 border border-dashed border-slate-200 rounded-xl flex items-center justify-center text-slate-400 text-xs italic">
                                Walang na-upload na file
                            </div>
                        </template>
                    </div>
                    <template x-if="getDocument('PSA')">
                        <a :href="'/storage/' + getDocument('PSA').file_path" target="_blank" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold text-center transition block shadow-xs">
                            View / Download File &rarr;
                        </a>
                    </template>
                </div>

            </div>
        </div>

        <!-- Footer Action -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <p class="text-[11px] text-slate-400 italic">I-click ang save para ma-update ang database status.</p>
            <div class="flex items-center space-x-3">
                <button type="button" @click="saveStudent()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md transition transform active:scale-95">
                    Save Changes
                </button>
                <button type="button" @click="advanceToNextStep()" class="px-5 py-2.5 bg-[#0a1128] hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-md transition transform active:scale-95">
                    Advance to Step 4 &rarr;
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
