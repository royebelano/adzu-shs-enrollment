@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12" x-data="{ openAddUserModal: false, openEditUserModal: false, editUserId: '', editName: '', editEmail: '', editRole: '' }">

    <!-- Top Header Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Manage Accounts</h2>
            <p class="text-xs text-slate-500 mt-0.5">View, create, and manage administrative and staff accounts for the e-Admissions portal.</p>
        </div>
        <button @click="openAddUserModal = true" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition flex items-center justify-center space-x-2 shrink-0 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Add New Account</span>
        </button>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center space-x-2">
        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Accounts Table List -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-6">Name</th>
                        <th class="py-3 px-6">Email Address</th>
                        <th class="py-3 px-6">Role</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @forelse($users ?? [] as $user)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-6 font-bold text-slate-900 flex items-center space-x-3">
                            <div class="w-7 h-7 bg-amber-400 text-slate-950 font-bold rounded-lg flex items-center justify-center text-[11px]">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                            <span>{{ $user->name }}</span>
                        </td>
                        <td class="py-3.5 px-6 font-mono text-slate-600">{{ $user->email }}</td>
                        <td class="py-3.5 px-6">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-semibold rounded-full text-[10px]">
                                {{ $user->role ?? $user->department ?? 'Admission' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-6 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button @click="
                                    editUserId = '{{ $user->id }}';
                                    editName = '{{ $user->name }}';
                                    editEmail = '{{ $user->email }}';
                                    editRole = '{{ $user->role ?? $user->department ?? 'Admission' }}';
                                    openEditUserModal = true;
                                " class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition cursor-pointer">Edit</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-slate-400 text-xs">
                            Wala pang nakarehistrong account o hindi pa naipapasa ang variable mula sa controller.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Adding Account -->
    <div x-show="openAddUserModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;">
        <div @click.away="openAddUserModal = false" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 space-y-5 border border-slate-200">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3.5">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Create New Admin Account</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Fill in details and assign a role for portal access.</p>
                </div>
                <button @click="openAddUserModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('admin.accounts.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/25">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/25">
                </div>

                <!-- Role Selection -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Assigned Role <span class="text-rose-500">*</span></label>
                    <select name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/25 cursor-pointer">
                        <option value="">Piliin ang Role...</option>
                        <option value="Super Admin">Super Admin</option>
                        <option value="Admission">Admission</option>
                        <option value="Infirmary">Infirmary</option>
                        <option value="ID Capturing">ID Capturing</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Password <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/25">
                </div>

                <div class="flex justify-end space-x-2.5 pt-4 border-t border-slate-100">
                    <button type="button" @click="openAddUserModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition cursor-pointer shadow-sm">Save Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for Editing Account -->
    <div x-show="openEditUserModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center z-50 p-4" style="display: none;">
        <div @click.away="openEditUserModal = false" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 space-y-5 border border-slate-200">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3.5">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Edit Admin Account</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Update user details and access role.</p>
                </div>
                <button @click="openEditUserModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <form :action="'/admin/accounts/' + editUserId" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="editName" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/25">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" x-model="editEmail" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/25">
                </div>

                <!-- Role Selection for Edit -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">Assigned Role <span class="text-rose-500">*</span></label>
                    <select name="role" x-model="editRole" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/25 cursor-pointer">
                        <option value="">Piliin ang Role...</option>
                        <option value="Super Admin">Super Admin</option>
                        <option value="Admission">Admission</option>
                        <option value="Infirmary">Infirmary</option>
                        <option value="ID Capturing">ID Capturing</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">New Password <span class="text-slate-400 font-normal">(I-leave blank kung hindi babaguhin)</span></label>
                    <input type="password" name="password" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/25">
                </div>

                <div class="flex justify-end space-x-2.5 pt-4 border-t border-slate-100">
                    <button type="button" @click="openEditUserModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition cursor-pointer shadow-sm">Update Account</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
