<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard System' ?></title>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS v4 Compiled -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="overflow-x-hidden">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden lg:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 bottom-0 w-[var(--sidebar-width)] z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <div id="sidebar-content">
            <!-- Brand -->
            <?= $this->include('layouts/brand') ?>

            <!-- Navigation -->
            <?= $this->include('layouts/nav') ?>

        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-[var(--sidebar-width)] min-h-screen flex flex-col transition-all duration-300">
        <!-- Top Navbar -->
        <?= $this->include('layouts/header') ?>

        <!-- Page Content -->
        <section class="flex-1 p-6 md:p-8 pt-4">
            <div class="max-w-7xl mx-auto">
                <!-- Main Content Area -->
                <div class="animate-content">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <?= $this->include('layouts/footer') ?>
        <script src="<?= base_url('js/main.js') ?>"></script>

        <?= $this->renderSection('js') ?>
</body>

</html>