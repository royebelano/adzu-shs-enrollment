<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdZU SHS e-Admissions &bull; Student Tracking Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800 min-h-screen py-12 px-4">

    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Header -->
        <div class="text-center space-y-1">
            <p class="text-[10px] font-bold tracking-wider text-blue-600 uppercase">AdZU SHS e-Admissions Portal</p>
            <h1 class="text-2xl font-extrabold text-slate-900">Student Application Tracking</h1>
            <p class="text-xs text-slate-500">Enter your Official Tracking Reference ID to check your current admission step.</p>
        </div>

        <!-- Search Form Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6">
            <form action="{{ route('tracking.search') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="text" name="reference_id" value="{{ old('reference_id', $student->reference_id ?? '') }}" placeholder="e.g. adzu-2026-0000133" required class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                <button type="submit" class="px-6 py-2.5 bg-[#0a1128] hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-md transition transform active:scale-95">
                    Track Progress &rarr;
                </button>
            </form>

            @if ($errors->any())
                <div class="mt-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl">
                    {{ $errors->first() }}
                </div>
            @endif
        </div>

        <!-- Result Display Card -->
        @isset($student)
            @php
                // Alamin ang current step batay sa status o progress ng estudyante
                $status = strtolower($student->status ?? '');
                $currentStep = 1;
                if (str_contains($status, 'step 2') || $student->medical_assessment_date) $currentStep = 2;
                if (str_contains($status, 'step 3') || $student->school) $currentStep = 3;
                if (str_contains($status, 'step 4') || $student->contact_person) $currentStep = 4;
                if (str_contains($status, 'step 5') || str_contains($status, 'enrolled')) $currentStep = 5;
            @endphp

            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-8 space-y-8">
                <!-- Student Summary Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 pb-4 gap-2">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Applicant Name</p>
                        <h3 class="text-lg font-extrabold text-slate-900">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name ?? '' }}</h3>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Reference ID</p>
                        <span class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">{{ $student->reference_id }}</span>
                    </div>
                </div>

                <!-- Progress Steps Visualizer -->
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Admission Workflow Progress</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-2">
                        <!-- Step 1 -->
                        <div class="p-3 rounded-xl border text-center space-y-1 {{ $currentStep >= 1 ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                            <span class="text-[10px] font-black uppercase block">Step 01</span>
                            <span class="text-xs font-bold block">Enrollment Form</span>
                        </div>
                        <!-- Step 2 -->
                        <div class="p-3 rounded-xl border text-center space-y-1 {{ $currentStep >= 2 ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                            <span class="text-[10px] font-black uppercase block">Step 02</span>
                            <span class="text-xs font-bold block">Medical Exam</span>
                        </div>
                        <!-- Step 3 -->
                        <div class="p-3 rounded-xl border text-center space-y-1 {{ $currentStep >= 3 ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                            <span class="text-[10px] font-black uppercase block">Step 03</span>
                            <span class="text-xs font-bold block">Admission Processing</span>
                        </div>
                        <!-- Step 4 -->
                        <div class="p-3 rounded-xl border text-center space-y-1 {{ $currentStep >= 4 ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                            <span class="text-[10px] font-black uppercase block">Step 04</span>
                            <span class="text-xs font-bold block">ID Capturing</span>
                        </div>
                        <!-- Step 5 -->
                        <div class="p-3 rounded-xl border text-center space-y-1 {{ $currentStep >= 5 ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                            <span class="text-[10px] font-black uppercase block">Step 05</span>
                            <span class="text-xs font-bold block">Official Enrollment</span>
                        </div>
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="bg-slate-50 rounded-xl p-4 grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Preferred Strand</span>
                        <span class="font-bold text-slate-700">{{ $student->preferred_strand ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Student ID</span>
                        <span class="font-mono font-bold text-slate-700">{{ $student->editable_student_id ?? $student->student_id }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Current Status</span>
                        <span class="font-bold text-amber-600">{{ $student->status ?? 'In Progress' }}</span>
                    </div>
                </div>
            </div>
        @endisset
    </div>

</body>
</html>
