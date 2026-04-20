<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | System Production</title>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS v4 Compiled -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--slate-50);
        }

        h1, h2, h3, .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        .bg-gradient-mesh {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, hsla(210, 100%, 93%, 1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(215, 100%, 95%, 1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(220, 100%, 93%, 1) 0, transparent 50%);
        }

        .input-group:focus-within .input-icon {
            color: var(--primary);
        }

        .btn-primary {
            background-color: var(--slate-900);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--slate-800);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body class="bg-gradient-mesh min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md animate-fade-in">
        <!-- Logo Section -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm mb-4">
                <i data-lucide="shield-check" class="w-10 h-10 text-slate-900"></i>
            </div>
            <h1 class="text-3xl font-bold text-slate-900 font-outfit">Bienvenido</h1>
            <p class="text-slate-500 mt-2">Accede a tu cuenta para continuar</p>
        </div>

        <!-- Login Card -->
        <div class="glass-card rounded-3xl p-8 md:p-10">
            <form action="<?= base_url('/') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <!-- Email Input -->
                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium text-slate-700 ml-1">Correo electrónico</label>
                    <div class="input-group relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="input-icon w-5 h-5 text-slate-400 transition-colors"></i>
                        </div>
                        <input type="email" name="email" id="email" 
                            class="block w-full pl-11 pr-4 py-3 bg-white/50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900/5 focus:border-slate-900 transition-all" 
                            placeholder="tu@ejemplo.com" required>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between ml-1">
                        <label for="password" class="text-sm font-medium text-slate-700">Contraseña</label>
                        <a href="#" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="input-group relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="input-icon w-5 h-5 text-slate-400 transition-colors"></i>
                        </div>
                        <input type="password" name="password" id="password" 
                            class="block w-full pl-11 pr-12 py-3 bg-white/50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900/5 focus:border-slate-900 transition-all" 
                            placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <i data-lucide="eye" id="eye-icon" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" 
                        class="h-4 w-4 text-slate-900 focus:ring-slate-900 border-slate-300 rounded cursor-pointer">
                    <label for="remember-me" class="ml-2 block text-sm text-slate-600 cursor-pointer select-none">
                        Recordarme en este equipo
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary w-full py-3 px-4 rounded-xl font-semibold flex items-center justify-center gap-2">
                    <span>Iniciar Sesión</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Error/Success Messages (Optional) -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mt-6 p-4 bg-red-50 border border-red-100 rounded-xl flex items-center gap-3 animate-fade-in">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                    <p class="text-sm text-red-600"><?= session()->getFlashdata('error') ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer Info -->
        <p class="text-center mt-8 text-sm text-slate-500">
            &copy; <?= date('Y') ?> System Production. Todos los derechos reservados.
        </p>
    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>

</html>
