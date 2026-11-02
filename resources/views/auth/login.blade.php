<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Surat Dinas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Floating label styles */
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .input-label {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            background: linear-gradient(to right, #ffffff, #ffffff);
            padding: 0 8px;
            color: #6B7280;
            transition: all 0.3s ease;
            pointer-events: none;
            font-weight: 500;
            z-index: 10;
        }
        
        .input-field:focus + .input-label,
        .input-field:not(:placeholder-shown) + .input-label {
            top: -12px;
            left: 12px;
            font-size: 12px;
            color: #1E40AF;
            font-weight: 600;
        }
        
        .input-field {
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.15);
        }
        
        /* Blue gradient background */
        .blue-gradient-bg {
            background: linear-gradient(135deg, #1E3A8A 0%, #1E40AF 50%, #3B82F6 100%);
        }
        
        /* Glass card effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        }
        
        /* Button styles */
        .btn-primary {
            background: linear-gradient(135deg, #1E40AF, #1E3A8A);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #1E3A8A, #1E2A5A);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.3);
        }
        
        /* Mobile optimizations */
        @media (max-width: 640px) {
            .container-mobile {
                padding: 1rem;
                margin: 0.5rem;
            }
            
            .form-mobile {
                padding: 1.5rem;
            }
            
            .title-mobile {
                font-size: 1.5rem;
                line-height: 2rem;
            }
        }
        
        /* Loading spinner */
        .spinner {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 2px solid white;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Focus visible for accessibility */
        .input-field:focus-visible,
        .btn-primary:focus-visible {
            outline: 2px solid #1E40AF;
            outline-offset: 2px;
        }
    </style>
</head>
<body class="min-h-screen blue-gradient-bg flex items-center justify-center p-2 sm:p-4">
    <!-- Background decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white opacity-10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-white opacity-10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md container-mobile sm:max-w-sm md:max-w-md lg:max-w-lg">
        <form method="POST" action="{{ route('login.process') }}" class="glass-card rounded-2xl p-6 sm:p-8 w-full form-mobile">
            @csrf
            
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-blue-800 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2 title-mobile">
                    Sistem Surat Dinas
                </h2>
                <p class="text-gray-600 text-sm sm:text-base">Silakan masuk ke akun Anda</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <!-- Email Input -->
            <div class="input-group">
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    required 
                    placeholder=" " 
                    autocomplete="email"
                    class="input-field w-full px-4 py-3 sm:py-4 border-2 border-gray-200 rounded-xl text-gray-700 leading-tight focus:outline-none focus:border-blue-800 focus:ring-0 transition-all duration-300"
                >
                <label for="email" class="input-label">Email Address</label>
            </div>

            <!-- Password Input -->
            <div class="input-group">
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required 
                    placeholder=" "
                    autocomplete="current-password"
                    class="input-field w-full px-4 py-3 sm:py-4 border-2 border-gray-200 rounded-xl text-gray-700 leading-tight focus:outline-none focus:border-blue-800 focus:ring-0 transition-all duration-300"
                >
                <label for="password" class="input-label">Password</label>
            </div>

            <!-- Login Button -->
            <button 
                id="loginBtn" 
                type="submit" 
                class="btn-primary w-full text-white font-semibold py-3 sm:py-4 px-6 rounded-xl focus:outline-none transition-all duration-300 mb-6"
            >
                <span id="loginText">Masuk ke Sistem</span>
                <span id="loadingSpinner" class="hidden ml-2 inline-block spinner"></span>
            </button>

            <!-- Footer Links -->
            <div class="text-center space-y-4">
                <a 
                    href="{{ route('reset.password.form') }}" 
                    class="inline-block text-sm text-blue-800 hover:text-blue-900 hover:underline transition-colors duration-300"
                >
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Lupa Password?
                </a>
                
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-500">
                        © 2024 Sistem Surat Dinas. Semua hak dilindungi.
                    </p>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Loading state management
        document.addEventListener('DOMContentLoaded', function() {
            const loginBtn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const form = loginBtn.closest('form');
            
            // Handle form submission
            form.addEventListener('submit', function(e) {
                // Disable button to prevent double submission
                loginBtn.disabled = true;
                loginBtn.classList.add('opacity-75', 'cursor-not-allowed');
                
                // Update text and show spinner
                loginText.textContent = 'Memproses...';
                loadingSpinner.classList.remove('hidden');
                
                // Re-enable after timeout as fallback
                setTimeout(function() {
                    if (loginBtn.disabled) {
                        loginBtn.disabled = false;
                        loginBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        loginText.textContent = 'Masuk ke Sistem';
                        loadingSpinner.classList.add('hidden');
                    }
                }, 10000); // 10 seconds timeout
            });
            
            // Auto-focus on first input if no errors
            const errorMessages = document.querySelector('.error-message');
            if (!errorMessages) {
                document.getElementById('email').focus();
            }
            
            // Input validation feedback
            const inputs = document.querySelectorAll('.input-field');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.validity.valid) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-green-500');
                    } else {
                        this.classList.remove('border-green-500');
                        this.classList.add('border-red-500');
                    }
                });
                
                input.addEventListener('input', function() {
                    this.classList.remove('border-red-500', 'border-green-500');
                });
            });
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && document.activeElement.tagName !== 'BUTTON') {
                const inputs = Array.from(document.querySelectorAll('.input-field'));
                const currentIndex = inputs.indexOf(document.activeElement);
                
                if (currentIndex !== -1 && currentIndex < inputs.length - 1) {
                    e.preventDefault();
                    inputs[currentIndex + 1].focus();
                }
            }
        });
    </script>
</body>
</html>