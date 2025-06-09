@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <main class="container mx-auto min-h-screen p-4 md:p-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <!-- Header Section -->
            <div class="flex flex-col items-start justify-between border-b border-gray-200 p-4 md:flex-row md:items-center">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">Pengaturan Profile</h1>
                    <p class="mt-1 text-sm text-gray-500">Kelola informasi akun dan keamanan Anda</p>
                </div>
            </div>

            <!-- Form Section -->
            <div class="p-6">
                <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Name -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-2 block text-sm font-medium text-gray-700" for="name">Nama</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                                    id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                                    placeholder="Your name" required data-original="{{ $user->name }}">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-2 block text-sm font-medium text-gray-700" for="email">Email</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                                    id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                                    placeholder="Your email" required data-original="{{ $user->email }}">
                            </div>
                        </div>

                        <!-- Current Password -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-2 block text-sm font-medium text-gray-700" for="current_password">Password saat ini</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                                    id="current_password" name="current_password" type="password"
                                    placeholder="Required for any changes">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="togglePassword('current_password')">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Hanya diperlukan saat mengubah email atau password</p>
                        </div>

                        <!-- Password Section -->
                        <div class="col-span-2">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <h3 class="mb-3 text-sm font-medium text-gray-700">Ubah Password (Opsional)</h3>

                                <!-- New Password -->
                                <div class="mb-3">
                                    <label class="mb-2 block text-sm font-medium text-gray-700" for="new_password">Password Baru</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                        <input
                                            class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                                            id="new_password" name="new_password" type="password" minlength="8"
                                            placeholder="Minimal 8 karakter">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="togglePassword('new_password')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="password-strength" class="mt-1 h-1 w-full rounded-full bg-gray-200">
                                        <div id="password-strength-bar" class="h-1 rounded-full transition-all duration-300"></div>
                                    </div>
                                    <p id="password-strength-text" class="mt-1 text-xs"></p>
                                </div>

                                <!-- Confirm New Password -->
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-700" for="new_password_confirmation">Konfirmasi Password Baru</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                        <input
                                            class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                                            id="new_password_confirmation" name="new_password_confirmation" type="password"
                                            placeholder="Konfirmasi password baru Anda">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <button type="button" class="text-gray-400 hover:text-gray-500" onclick="togglePassword('new_password_confirmation')">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p id="password-match" class="mt-1 text-xs"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mt-4 rounded-lg bg-red-50 p-4 text-red-700" role="alert">
                            <div class="flex items-center">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-sm font-medium">Terdapat {{ $errors->count() }} kesalahan</h3>
                            </div>
                            <div class="mt-2 ml-7 text-sm">
                                <ul class="list-disc space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Session Messages -->
                    @if (session('success'))
                        <div class="mt-4 flex items-center rounded-lg bg-green-50 p-4 text-green-800" role="alert">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mt-4 flex items-center rounded-lg bg-red-50 p-4 text-red-800" role="alert">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="mt-6 flex flex-col-reverse justify-between gap-4 sm:flex-row">
                        <button type="button" onclick="window.location.href='{{ route('logout') }}'" 
                            class="flex items-center justify-center rounded-lg border border-red-600 bg-white px-5 py-2.5 text-center text-sm font-medium text-red-600 transition-colors hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-200"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                        
                        <button
                            id="updateButton"
                            class="flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-center text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-50"
                            type="submit" disabled>
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Update Profile
                        </button>
                    </div>
                </form>

                <!-- Logout Form -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </main>

    <script>
        // Toggle password visibility
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        // Check if form has changes
        function checkFormChanges() {
            const form = document.getElementById('profileForm');
            const updateButton = document.getElementById('updateButton');
            let hasChanges = false;

            // Check name and email fields
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            
            if (nameInput.value !== nameInput.dataset.original || 
                emailInput.value !== emailInput.dataset.original) {
                hasChanges = true;
            }

            // Check password fields
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;

            if (newPassword || confirmPassword) {
                hasChanges = true;
            }

            // Enable/disable button based on changes
            updateButton.disabled = !hasChanges;
        }

        // Password strength indicator
        function checkPasswordStrength(password) {
            let strength = 0;
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');

            if (password.length >= 8) strength += 1;
            if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1;
            if (password.match(/([0-9])/)) strength += 1;
            if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1;

            switch(strength) {
                case 0:
                    strengthBar.style.width = '0%';
                    strengthBar.className = 'h-1 rounded-full bg-red-500 transition-all duration-300';
                    strengthText.textContent = '';
                    break;
                case 1:
                    strengthBar.style.width = '25%';
                    strengthBar.className = 'h-1 rounded-full bg-red-500 transition-all duration-300';
                    strengthText.textContent = 'Weak';
                    strengthText.className = 'text-xs text-red-500';
                    break;
                case 2:
                    strengthBar.style.width = '50%';
                    strengthBar.className = 'h-1 rounded-full bg-yellow-500 transition-all duration-300';
                    strengthText.textContent = 'Moderate';
                    strengthText.className = 'text-xs text-yellow-500';
                    break;
                case 3:
                    strengthBar.style.width = '75%';
                    strengthBar.className = 'h-1 rounded-full bg-blue-500 transition-all duration-300';
                    strengthText.textContent = 'Strong';
                    strengthText.className = 'text-xs text-blue-500';
                    break;
                case 4:
                    strengthBar.style.width = '100%';
                    strengthBar.className = 'h-1 rounded-full bg-green-500 transition-all duration-300';
                    strengthText.textContent = 'Very Strong';
                    strengthText.className = 'text-xs text-green-500';
                    break;
            }
        }

        // Password match checker
        function checkPasswordMatch() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;
            const matchText = document.getElementById('password-match');

            if (!password || !confirmPassword) {
                matchText.textContent = '';
                return;
            }

            if (password === confirmPassword) {
                matchText.textContent = 'Passwords cocok';
                matchText.className = 'text-xs text-green-500';
            } else {
                matchText.textContent = 'Passwords tidak cocok';
                matchText.className = 'text-xs text-red-500';
            }
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Form change detection
            const inputs = document.querySelectorAll('#profileForm input');
            inputs.forEach(input => {
                input.addEventListener('input', checkFormChanges);
            });

            // Password strength check
            document.getElementById('new_password').addEventListener('input', function(e) {
                checkPasswordStrength(e.target.value);
                checkPasswordMatch();
            });

            // Password match check
            document.getElementById('new_password_confirmation').addEventListener('input', checkPasswordMatch);
        });
    </script>
@endsection