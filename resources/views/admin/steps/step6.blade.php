@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto transition-all duration-300"
     x-data="{
         selectedStudentId: null,
         searchQuery: '',
         activeStep: 1,
         students: @js($step6_students ?? []),
         activeStudent: {},
         selectStudent(student) {
             this.activeStudent = JSON.parse(JSON.stringify(student));
             this.selectedStudentId = this.selectedStudentId === student.student_id ? null : student.student_id;
             this.activeStep = 1;
         },
         getDocumentPath(docName) {
             if (!this.activeStudent.documents) return null;
             let doc = this.activeStudent.documents.find(d =>
                 d.document_name && d.document_name.toLowerCase().includes(docName.toLowerCase())
             );
             return doc ? '/storage/' + doc.file_path : null;
         },
         get filteredStudents() {
             if (!this.searchQuery) return this.students;
             return this.students.filter(s =>
                 (s.first_name && s.first_name.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                 (s.last_name && s.last_name.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                 (s.editable_student_id && s.editable_student_id.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                 (s.reference_id && s.reference_id.toLowerCase().includes(this.searchQuery.toLowerCase()))
             );
         }
     }">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-[10px] font-black tracking-wider text-emerald-600 uppercase mb-1">Admissions / Student Workflow</p>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Step 6 &bull; Officially Enrolled Master List</h2>
            <p class="text-xs text-slate-500 mt-0.5">Comprehensive records and uploaded document verifications from Steps 1 to 5.</p>
        </div>
        <!-- Search Bar -->
        <div class="w-full md:w-80">
            <input type="text" x-model="searchQuery" placeholder="Search student name, ID, or ref ID..." class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 focus:border-emerald-600 shadow-2xs transition">
        </div>
    </div>

    <!-- Main Table Directory -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 bg-emerald-600 text-white text-[11px] font-bold rounded-lg shadow-sm">06</span>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Enrolled Students Directory</h4>
                    <p class="text-[11px] text-slate-400">Master record of officially enrolled students with full admission audit trails.</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[11px] font-bold" x-text="filteredStudents.length + ' students'"></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                        <th class="px-4 py-3">Student ID / Ref ID</th>
                        <th class="px-4 py-3">Full Name</th>
                        <th class="px-4 py-3">Strand</th>
                        <th class="px-4 py-3">Contact Person</th>
                        <th class="px-4 py-3">GCO Date</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    <template x-for="student in filteredStudents" :key="student.student_id">
                        <tr class="hover:bg-emerald-50/30 transition-colors duration-150">
                            <td class="px-4 py-3.5">
                                <div class="font-mono font-semibold text-slate-900" x-text="student.editable_student_id || student.student_id || '—'"></div>
                                <div class="text-[10px] text-slate-400 font-mono" x-text="student.reference_id || '—'"></div>
                            </td>
                            <td class="px-4 py-3.5 text-slate-800 font-medium">
                                <span x-text="(student.last_name || '') + ', ' + (student.first_name || '') + ' ' + (student.suffix || '')"></span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-md font-bold text-[10px]" x-text="student.preferred_strand || '—'"></span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600" x-text="(student.contact_person || '—') + ' (' + (student.contact_relationship || '—') + ')'"></td>
                            <td class="px-4 py-3.5 text-slate-600 font-mono text-[11px]" x-text="student.gco_assessment_date || '—'"></td>
                            <td class="px-4 py-3.5">
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold shadow-2xs">Enrolled</span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <button @click="selectStudent(student)"
                                        class="px-3 py-1.5 bg-[#0a1128] text-white rounded-lg font-semibold hover:bg-slate-900 shadow-2xs transition transform active:scale-95 cursor-pointer">
                                    <span x-text="selectedStudentId === student.student_id ? 'Close' : 'View Details (1-5)'"></span>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredStudents.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                                No officially enrolled students found matching your search.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Strictly Click-Based Master Step Panel -->
    <div x-show="selectedStudentId !== null"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="bg-white border border-slate-200/80 rounded-2xl shadow-xl p-6 space-y-6" style="display: none;">

        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">MASTER RECORD &bull; STEP-BY-STEP AUDIT TRAIL (STEPS 1-5)</p>
                <h3 class="text-lg font-extrabold text-slate-900" x-text="(activeStudent.first_name || '') + ' ' + (activeStudent.middle_name || '') + ' ' + (activeStudent.last_name || '') + ' ' + (activeStudent.suffix || '')"></h3>
            </div>
            <div class="text-right">
                <span class="text-xs font-mono font-bold text-slate-900 px-3 py-1 bg-slate-100 rounded-lg shadow-inner" x-text="'Ref ID: ' + (activeStudent.reference_id || '—')"></span>
            </div>
        </div>

        <!-- Step Navigation Tabs (Strictly Clickable Only) -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
            <button @click="activeStep = 1"
                    :class="activeStep === 1 ? 'bg-blue-600 text-white shadow-md border-blue-600' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200'"
                    class="p-3 rounded-xl border text-center transition font-semibold text-xs flex flex-col items-center justify-center space-y-1 cursor-pointer">
                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold" :class="activeStep === 1 ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-700'">1</span>
                <span>Step 1: Info</span>
            </button>
            <button @click="activeStep = 2"
                    :class="activeStep === 2 ? 'bg-indigo-600 text-white shadow-md border-indigo-600' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200'"
                    class="p-3 rounded-xl border text-center transition font-semibold text-xs flex flex-col items-center justify-center space-y-1 cursor-pointer">
                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold" :class="activeStep === 2 ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-700'">2</span>
                <span>Step 2: Medical</span>
            </button>
            <button @click="activeStep = 3"
                    :class="activeStep === 3 ? 'bg-amber-600 text-white shadow-md border-amber-600' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200'"
                    class="p-3 rounded-xl border text-center transition font-semibold text-xs flex flex-col items-center justify-center space-y-1 cursor-pointer">
                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold" :class="activeStep === 3 ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700'">3</span>
                <span>Step 3: Docs</span>
            </button>
            <button @click="activeStep = 4"
                    :class="activeStep === 4 ? 'bg-purple-600 text-white shadow-md border-purple-600' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200'"
                    class="p-3 rounded-xl border text-center transition font-semibold text-xs flex flex-col items-center justify-center space-y-1 cursor-pointer">
                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold" :class="activeStep === 4 ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-700'">4</span>
                <span>Step 4: Barcode</span>
            </button>
            <button @click="activeStep = 5"
                    :class="activeStep === 5 ? 'bg-emerald-600 text-white shadow-md border-emerald-600' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200'"
                    class="p-3 rounded-xl border text-center transition font-semibold text-xs flex flex-col items-center justify-center space-y-1 col-span-2 sm:col-span-1 cursor-pointer">
                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold" :class="activeStep === 5 ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700'">5</span>
                <span>Step 5: GCO</span>
            </button>
        </div>

        <!-- Step Content Area Featuring Modern Card Grids -->
        <div class="p-5 bg-slate-50/90 border border-slate-200 rounded-2xl text-xs space-y-4">

            <!-- STEP 1 CARDS -->
            <div x-show="activeStep === 1" x-transition.opacity class="space-y-3">
                <h4 class="font-bold text-blue-700 text-sm uppercase tracking-wide border-b border-slate-200 pb-2">Step 1 &bull; Personal Information &amp; Enrollment Form</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Student ID</span>
                        <span class="font-mono font-bold text-slate-900 text-sm" x-text="activeStudent.editable_student_id || activeStudent.student_id || '—'"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">LRN</span>
                        <span class="font-mono font-bold text-slate-900 text-sm" x-text="activeStudent.lrn || '—'"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Date of Birth</span>
                        <span class="font-medium text-slate-900 text-sm" x-text="activeStudent.date_of_birth || '—'"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Preferred Strand</span>
                        <span class="font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded inline-block" x-text="activeStudent.preferred_strand || '—'"></span>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Previous School</span>
                        <span class="font-medium text-slate-900" x-text="activeStudent.school || '—'"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Contact Person &amp; Relationship</span>
                        <span class="font-medium text-slate-900" x-text="(activeStudent.contact_person || '—') + ' (' + (activeStudent.contact_relationship || '—') + ')'"></span>
                    </div>
                </div>
            </div>

            <!-- STEP 2 CARDS -->
            <div x-show="activeStep === 2" x-transition.opacity class="space-y-3" style="display: none;">
                <h4 class="font-bold text-indigo-700 text-sm uppercase tracking-wide border-b border-slate-200 pb-2">Step 2 &bull; Infirmary &amp; Medical Clearance</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Medical Assessment Date</span>
                        <span class="font-mono font-bold text-slate-900 text-sm" x-text="activeStudent.medical_assessment_date || '—'"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Physical Exam Status</span>
                        <span class="font-bold text-sm" :class="activeStudent.physical_exam == 1 ? 'text-emerald-600' : 'text-slate-500'" x-text="activeStudent.physical_exam == 1 ? 'Passed / Checked' : 'Pending'"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Dental Exam Status</span>
                        <span class="font-bold text-sm" :class="activeStudent.dental_exam == 1 ? 'text-emerald-600' : 'text-slate-500'" x-text="activeStudent.dental_exam == 1 ? 'Passed / Checked' : 'Pending'"></span>
                    </div>
                </div>
            </div>

            <!-- STEP 3 CARDS (Requirements & Files) -->
            <div x-show="activeStep === 3" x-transition.opacity class="space-y-3" style="display: none;">
                <h4 class="font-bold text-amber-700 text-sm uppercase tracking-wide border-b border-slate-200 pb-2">Step 3 &bull; Admission Requirements &amp; Files</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Form 138</span>
                        <span class="font-bold text-sm" :class="activeStudent.form_138 == 1 ? 'text-emerald-600' : 'text-slate-500'" x-text="activeStudent.form_138 == 1 ? 'Submitted / Verified' : 'Pending'"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">PSA Birth Certificate</span>
                        <span class="font-bold text-sm" :class="activeStudent.psa == 1 ? 'text-emerald-600' : 'text-slate-500'" x-text="activeStudent.psa == 1 ? 'Submitted / Verified' : 'Pending'"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Good Moral Certificate</span>
                        <span class="font-bold text-sm" :class="activeStudent.good_moral == 1 ? 'text-emerald-600' : 'text-slate-500'" x-text="activeStudent.good_moral == 1 ? 'Submitted / Verified' : 'Pending'"></span>
                    </div>
                </div>
                <div class="bg-amber-50/60 p-4 rounded-xl border border-amber-200/60 space-y-2">
                    <span class="font-bold text-amber-900 text-xs block">Uploaded Requirement Files:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <template x-if="getDocumentPath('Form 138')">
                            <a :href="getDocumentPath('Form 138')" target="_blank" class="p-3 bg-white hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs transition">
                                <span>Form 138 Document</span>
                                <span class="text-[10px] underline">View</span>
                            </a>
                        </template>
                        <template x-if="getDocumentPath('PSA')">
                            <a :href="getDocumentPath('PSA')" target="_blank" class="p-3 bg-white hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs transition">
                                <span>PSA Document</span>
                                <span class="text-[10px] underline">View</span>
                            </a>
                        </template>
                        <template x-if="getDocumentPath('Good Moral')">
                            <a :href="getDocumentPath('Good Moral')" target="_blank" class="p-3 bg-white hover:bg-amber-100 text-amber-900 border border-amber-300 rounded-xl text-xs font-semibold flex items-center justify-between shadow-2xs transition">
                                <span>Good Moral Document</span>
                                <span class="text-[10px] underline">View</span>
                            </a>
                        </template>
                    </div>
                    <template x-if="!getDocumentPath('Form 138') && !getDocumentPath('PSA') && !getDocumentPath('Good Moral')">
                        <p class="text-slate-400 italic text-xs">No requirement documents uploaded for Step 3.</p>
                    </template>
                </div>
            </div>

            <!-- STEP 4 CARDS -->
            <div x-show="activeStep === 4" x-transition.opacity class="space-y-3" style="display: none;">
                <h4 class="font-bold text-purple-700 text-sm uppercase tracking-wide border-b border-slate-200 pb-2">Step 4 &bull; ID Capturing &amp; Barcode File</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Assigned Student ID</span>
                        <span class="font-mono font-bold text-slate-900 text-sm" x-text="activeStudent.editable_student_id || activeStudent.student_id || '—'"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs flex flex-col justify-between">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Uploaded Barcode Document</span>
                        <template x-if="getDocumentPath('Barcode')">
                            <a :href="getDocumentPath('Barcode')" target="_blank" class="mt-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold inline-flex items-center justify-center space-x-1.5 shadow-sm transition">
                                <span>View / Download Barcode File</span>
                            </a>
                        </template>
                        <template x-if="!getDocumentPath('Barcode')">
                            <span class="text-slate-400 italic text-xs">No barcode file uploaded in Step 4.</span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- STEP 5 CARDS -->
            <div x-show="activeStep === 5" x-transition.opacity class="space-y-3" style="display: none;">
                <h4 class="font-bold text-emerald-700 text-sm uppercase tracking-wide border-b border-slate-200 pb-2">Step 5 &bull; Guidance Counseling (GCO) Assessment</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">GCO Assessment Date</span>
                        <span class="font-mono font-bold text-slate-900 text-sm" x-text="activeStudent.gco_assessment_date || '—'"></span>
                    </div>
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs">
                        <span class="text-slate-400 block text-[10px] font-semibold uppercase mb-1">Clearance Status</span>
                        <span class="font-bold text-emerald-700 text-sm">Cleared &amp; Enrolled (Step 6)</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Panel Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <p class="text-[11px] text-slate-400 italic">Click on any step button above to inspect specific clearance details.</p>
            <button type="button" @click="selectedStudentId = null" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                Close Panel
            </button>
        </div>
    </div>
</div>
@endsection
