@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto transition-all duration-300"
     x-data="{
         selectedStudentId: null,
         students: @js($step1_students ?? []),
         activeStudent: {},
         selectStudent(student) {
             this.activeStudent = JSON.parse(JSON.stringify(student));
             // Kinukuha ang editable_student_id o fallback sa student_id para ma-edit sa input box
             this.activeStudent.student_id = student.editable_student_id || student.student_id;
             // Permanent lookup key para sa backend query
             this.activeStudent.original_lookup_id = student.reference_id || student.student_id;
             // Locked reference id para sa UI display
             this.activeStudent.locked_ref_id = student.reference_id || ('adzu-' + new Date().getFullYear() + '-' + String(student.student_id).padStart(7, '0'));
             this.selectedStudentId = this.selectedStudentId === student.student_id ? null : student.student_id;
         },
         getDocument(keyword) {
             if (!this.activeStudent.documents) return null;
             return this.activeStudent.documents.find(d =>
                 d.document_name.replace(/\s+/g, ' ').trim().toLowerCase().includes(keyword)
             );
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
                 body: JSON.stringify(this.activeStudent)
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     alert(data.message || 'Matagumpay na na-update ang impormasyon ng estudyante!');
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
         advanceStudent() {
             if (!this.activeStudent.original_lookup_id) return;

             if (!confirm('Sigurado ka bang nais mong i-advance ang estudyanteng ito sa Step 2?')) {
                 return;
             }

             let csrfToken = document.querySelector('meta[name=&quot;csrf-token&quot;]');
             let tokenValue = csrfToken ? csrfToken.getAttribute('content') : '{{ csrf_token() }}';

             fetch('/admin/students/' + this.activeStudent.original_lookup_id + '/advance', {
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
                     alert(data.message || 'Matagumpay na na-advance ang estudyante sa Step 2!');
                     this.students = this.students.filter(s => s.student_id !== this.selectedStudentId);
                     this.selectedStudentId = null;
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

    <!-- Top Bar -->
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500 font-medium">{{ now()->format('l, F j, Y') }}</p>
        <div class="flex items-center space-x-2 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-full text-emerald-700 text-xs font-semibold shadow-xs">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span>Admissions Admin &bull; Real-time DB</span>
        </div>
    </div>

    <!-- Header -->
    <div>
        <p class="text-[10px] font-bold tracking-wider text-blue-600 uppercase mb-1">Admissions / Student Workflow</p>
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Step 1 &bull; Appointment Booked</h2>
        <p class="text-xs text-slate-500 mt-0.5">Manage students who reached Step 1. Records remain available after they advance.</p>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 bg-[#0a1128] text-white text-[11px] font-bold rounded-lg shadow-sm">01</span>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Enrollment form</h4>
                    <p class="text-[11px] text-slate-400">Enrollment details &bull; with documents</p>
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
                        <th class="px-6 py-3">Reference ID</th>
                        <th class="px-6 py-3">Student</th>
                        <th class="px-6 py-3">Mobile</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Strand</th>
                        <th class="px-6 py-3">Date Submitted</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    <template x-for="student in students" :key="student.student_id">
                        <tr class="hover:bg-blue-50/40 transition-colors duration-150">
                            <!-- Naka-lock na Reference ID sa Table -->
                            <td class="px-6 py-3.5 font-mono font-semibold text-slate-900" x-text="student.reference_id ? student.reference_id : ('adzu-' + new Date().getFullYear() + '-' + String(student.student_id).padStart(7, '0'))"></td>
                            <td class="px-6 py-3.5 font-semibold text-slate-900" x-text="(student.last_name || '') + ', ' + (student.first_name || '') + (student.suffix ? ' ' + student.suffix : '')"></td>
                            <td class="px-6 py-3.5 text-slate-600" x-text="student.contact_number || 'N/A'"></td>
                            <td class="px-6 py-3.5 text-slate-600" x-text="student.email || 'N/A'"></td>
                            <td class="px-6 py-3.5 text-slate-600" x-text="student.preferred_strand || 'STEM'"></td>
                            <td class="px-6 py-3.5 text-slate-600" x-text="student.created_at ? student.created_at.substring(0, 10) : 'N/A'"></td>
                            <td class="px-6 py-3.5">
                                <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-[10px] font-bold shadow-2xs" x-text="student.status || 'In progress'"></span>
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <button @click="selectStudent(student)"
                                        class="px-3 py-1 bg-white border border-slate-200 rounded-lg font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition">
                                    <span x-text="selectedStudentId === student.student_id ? 'Close' : 'View / edit'"></span>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="students.length === 0">
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-xs">
                                Walang kasalukuyang tala o records sa database.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Step 1 Record Form Section -->
    <div x-show="selectedStudentId !== null" x-transition.opacity.duration.300ms class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6 space-y-6" style="display: none;">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Step 1 Record</p>
                <h3 class="text-base font-bold text-slate-900">Selected Student Details</h3>
            </div>
            <!-- Naka-lock at Permanent Reference ID sa Header ng Panel -->
            <span class="text-xs font-mono font-semibold text-slate-400" x-text="'Ref ID: ' + (activeStudent.locked_ref_id || '')"></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- EDITABLE STUDENT ID FIELD (Nakalagay na dito) -->
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Student ID &bull; Editable</label>
                <input type="text" x-model="activeStudent.student_id" placeholder="Enter student ID" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20 font-mono">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Family name</label>
                <input type="text" x-model="activeStudent.last_name" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Given name</label>
                <input type="text" x-model="activeStudent.first_name" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Middle name</label>
                <input type="text" x-model="activeStudent.middle_name" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Suffix (e.g. Jr., III)</label>
                <input type="text" x-model="activeStudent.suffix" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20" placeholder="e.g. Jr.">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Gender</label>
                <input type="text" x-model="activeStudent.gender" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Student mobile number</label>
                <input type="text" x-model="activeStudent.contact_number" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Student email</label>
                <input type="text" x-model="activeStudent.email" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">LRN</label>
                <input type="text" x-model="activeStudent.lrn" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Date of birth</label>
                <input type="text" x-model="activeStudent.date_of_birth" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Preferred Strand</label>
                <input type="text" x-model="activeStudent.preferred_strand" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600/20">
            </div>
        </div>

        <!-- Student-uploaded requirements preview -->
        <div class="pt-2">
            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">Student-uploaded requirements</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Form 138 -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-800">Form 138</p>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md" :class="getDocument('form 138') ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'" x-text="getDocument('form 138') ? 'Uploaded' : 'No file'"></span>
                    </div>
                    <template x-if="getDocument('form 138')">
                        <div class="space-y-2">
                            <template x-if="getDocument('form 138').file_path && getDocument('form 138').file_path.match(/\.(jpg|jpeg|png)$/i)">
                                <div class="relative h-28 bg-slate-200 rounded-lg overflow-hidden border border-slate-300">
                                    <img :src="'/storage/' + getDocument('form 138').file_path" class="w-full h-full object-cover">
                                </div>
                            </template>
                            <div class="flex items-center justify-end space-x-2 pt-1">
                                <a :href="'/storage/' + getDocument('form 138').file_path" target="_blank" class="w-full text-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition">
                                    View / Download File &rarr;
                                </a>
                            </div>
                        </div>
                    </template>
                    <template x-if="!getDocument('form 138')">
                        <p class="text-[11px] text-slate-400 italic">Walang na-upload na dokumentong ito.</p>
                    </template>
                </div>

                <!-- Good Moral -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-800">Good Moral</p>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md" :class="getDocument('good moral') ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'" x-text="getDocument('good moral') ? 'Uploaded' : 'No file'"></span>
                    </div>
                    <template x-if="getDocument('good moral')">
                        <div class="space-y-2">
                            <template x-if="getDocument('good moral').file_path && getDocument('good moral').file_path.match(/\.(jpg|jpeg|png)$/i)">
                                <div class="relative h-28 bg-slate-200 rounded-lg overflow-hidden border border-slate-300">
                                    <img :src="'/storage/' + getDocument('good moral').file_path" class="w-full h-full object-cover">
                                </div>
                            </template>
                            <div class="flex items-center justify-end space-x-2 pt-1">
                                <a :href="'/storage/' + getDocument('good moral').file_path" target="_blank" class="w-full text-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition">
                                    View / Download File &rarr;
                                </a>
                            </div>
                        </div>
                    </template>
                    <template x-if="!getDocument('good moral')">
                        <p class="text-[11px] text-slate-400 italic">Walang na-upload na dokumentong ito.</p>
                    </template>
                </div>

                <!-- PSA Birth Certificate -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-800">PSA Birth Certificate</p>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md" :class="getDocument('psa') ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'" x-text="getDocument('psa') ? 'Uploaded' : 'No file'"></span>
                    </div>
                    <template x-if="getDocument('psa')">
                        <div class="space-y-2">
                            <template x-if="getDocument('psa').file_path && getDocument('psa').file_path.match(/\.(jpg|jpeg|png)$/i)">
                                <div class="relative h-28 bg-slate-200 rounded-lg overflow-hidden border border-slate-300">
                                    <img :src="'/storage/' + getDocument('psa').file_path" class="w-full h-full object-cover">
                                </div>
                            </template>
                            <div class="flex items-center justify-end space-x-2 pt-1">
                                <a :href="'/storage/' + getDocument('psa').file_path" target="_blank" class="w-full text-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition">
                                    View / Download File &rarr;
                                </a>
                            </div>
                        </div>
                    </template>
                    <template x-if="!getDocument('psa')">
                        <p class="text-[11px] text-slate-400 italic">Walang na-upload na dokumentong ito.</p>
                    </template>
                </div>

            </div>
        </div>

        <!-- Footer Action -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <button type="button" @click="saveStudent()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-md transition transform active:scale-95">
                Save Changes
            </button>
            <button type="button" @click="advanceStudent()" class="px-5 py-2.5 bg-[#0a1128] hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-md transition transform active:scale-95">
                Advance to Step 2 &rarr;
            </button>
        </div>
    </div>

</div>
@endsection
