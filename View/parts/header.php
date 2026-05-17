<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description"
    content="MalCom – Compare multiple malwares, show similarities, and search verdicts in one place.">
  <title>MalCom – Malware Comparison Tool</title>

  <!-- Favicon -->
  <link rel="shortcut icon" type="image/x-icon" href="../assets/img/icons/hacker.ico">

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      /* Safelist: classes built dynamically in JS (e.g. verdict toggle buttons)
         are invisible to the CDN JIT scanner — listing them here guarantees
         they are always included in the generated stylesheet. */
      safelist: [
        /* verdict button — unselected state */
        'px-5', 'py-2', 'rounded-lg', 'text-sm', 'font-medium',
        'border', 'transition-all', 'duration-150', 'select-none', 'cursor-pointer',
        'bg-gray-800', 'border-white/10', 'text-gray-300',
        'hover:bg-gray-700', 'hover:text-white',
        /* verdict button — selected state */
        'bg-brand-600', 'border-brand-500', 'text-white',
        'shadow-md', 'shadow-brand-900/40', '-translate-y-0.5',
      ],
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          },
          colors: {
            brand: {
              50: '#f0f4ff',
              100: '#dde6ff',
              200: '#c3d0ff',
              300: '#9db3ff',
              400: '#708bff',
              500: '#4a63f5',
              600: '#3347eb',
              700: '#2834d6',
              800: '#252cad',
              900: '#242b88',
            },
          },
        },
      },
    }
  </script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer">

  <!-- D3.js -->
  <script src="https://d3js.org/d3.v7.min.js"></script>

  <!-- jQuery (kept for existing page logic) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>

<body class="bg-gray-950 text-gray-100 antialiased">

  <!-- ===== Navbar ===== -->
  <header>
    <nav class="fixed top-0 inset-x-0 z-50 bg-gray-900/80 backdrop-blur-md border-b border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

          <!-- Brand -->
          <a href="index.php"
            class="flex items-center gap-2 text-white font-bold text-xl tracking-tight hover:text-brand-400 transition-colors">
            <i class="fa-solid fa-shield-virus text-brand-400"></i>
            MalCom
          </a>

          <!-- Desktop nav links -->
          <div class="hidden md:flex items-center gap-1">
            <a href="clear_session/clear_session_table.php"
              class="px-4 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200">
              <i class="fa-solid fa-code-compare mr-1.5"></i>Compare Malwares
            </a>
            <a href="clear_session/clear_session_circles.php"
              class="px-4 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200">
              <i class="fa-solid fa-circle-nodes mr-1.5"></i>Show Similarities
            </a>
            <a href="search_verdicts.php"
              class="px-4 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200">
              <i class="fa-solid fa-magnifying-glass mr-1.5"></i>Search Verdicts
            </a>
          </div>

          <!-- Mobile hamburger -->
          <button id="mobile-menu-btn" type="button" aria-label="Toggle navigation" aria-expanded="false"
            aria-controls="mobile-menu"
            class="md:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
            <i class="fa-solid fa-bars text-lg"></i>
          </button>

        </div>
      </div>

      <!-- Mobile menu -->
      <div id="mobile-menu" class="hidden md:hidden border-t border-white/10 bg-gray-900">
        <div class="px-4 py-3 space-y-1">
          <a href="clear_session/clear_session_table.php"
            class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all">
            <i class="fa-solid fa-code-compare w-4 text-center"></i>Compare Malwares
          </a>
          <a href="clear_session/clear_session_circles.php"
            class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all">
            <i class="fa-solid fa-circle-nodes w-4 text-center"></i>Show Similarities
          </a>
          <a href="search_verdicts.php"
            class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all">
            <i class="fa-solid fa-magnifying-glass w-4 text-center"></i>Search Verdicts
          </a>
        </div>
      </div>
    </nav>
  </header>

  <script>
    // Mobile menu toggle
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    btn.addEventListener('click', () => {
      const open = !menu.classList.contains('hidden');
      menu.classList.toggle('hidden', open);
      btn.setAttribute('aria-expanded', String(!open));
    });
  </script>