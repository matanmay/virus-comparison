<?php require_once "parts/header.php"; ?>

<main class="min-h-screen pt-16">

  <!-- ===== Hero Section ===== -->
  <section class="relative overflow-hidden py-24 sm:py-32">

    <!-- Subtle radial gradient background -->
    <div class="pointer-events-none absolute inset-0 -z-10">
      <div class="absolute left-1/2 top-0 -translate-x-1/2 w-[900px] h-[600px]
                  bg-brand-700/20 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col-reverse md:flex-row items-center gap-12 md:gap-16">

        <!-- Text -->
        <div class="flex-1 text-center md:text-left">
          <p class="text-sm font-semibold tracking-widest uppercase text-brand-400 mb-3">
            Malware Analysis Platform
          </p>
          <h1 class="text-5xl sm:text-6xl font-extrabold tracking-tight text-white leading-tight mb-6">
            Welcome to&nbsp;<span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-indigo-400">MalCom.</span>
          </h1>
          <p class="text-lg text-gray-400 leading-relaxed max-w-xl mx-auto md:mx-0">
            Select multiple malwares and have them all compared in one place —
            making it easier, clearer, and faster to identify common attributes
            and write a single, perfect generic signature while saving many
            hours of valuable work time.
          </p>
          <div class="mt-8 flex flex-wrap gap-3 justify-center md:justify-start">
            <a href="clear_session/clear_session_table.php"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-500
                      text-white font-semibold text-sm shadow-lg shadow-brand-900/50
                      transition-all duration-200 hover:-translate-y-0.5">
              Get Started <i class="fa-solid fa-arrow-right"></i>
            </a>
            <a href="#features"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-white/20
                      hover:bg-white/10 text-gray-300 hover:text-white font-semibold text-sm
                      transition-all duration-200">
              Learn More <i class="fa-solid fa-chevron-down"></i>
            </a>
          </div>
        </div>

        <!-- Hero image -->
        <div class="flex-shrink-0">
          <div class="relative">
            <div class="absolute inset-0 rounded-2xl bg-brand-500/20 blur-2xl scale-110"></div>
            <img src="../assets/img/index.jpg"
                 alt="Malware analysis illustration"
                 class="relative w-72 sm:w-80 rounded-2xl ring-1 ring-white/10 shadow-2xl">
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ===== Features Section ===== -->
  <section id="features" class="py-20 bg-gray-900/60 border-t border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <!-- Section heading -->
      <div class="text-center mb-14">
        <h2 class="text-3xl sm:text-4xl font-bold text-white">What You Can Do</h2>
        <p class="mt-3 text-gray-400 text-base">Three powerful tools, one unified platform.</p>
      </div>

      <!-- Feature cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Card 1 – Compare Malwares -->
        <article class="group relative flex flex-col rounded-2xl border border-white/10
                        bg-gray-800/60 hover:bg-gray-800/90 backdrop-blur-sm
                        p-7 shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-brand-900/40">
          <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl
                      bg-brand-600/20 ring-1 ring-brand-500/40 group-hover:ring-brand-500/70 transition-all">
            <img src="../assets/img/icons/comp.png" alt="Compare icon" class="h-7 w-7 object-contain">
          </div>
          <h3 class="text-xl font-semibold text-white mb-1">Compare Malwares</h3>
          <p class="text-sm font-medium text-brand-400 italic mb-3">Get the full picture.</p>
          <p class="text-sm text-gray-400 leading-relaxed flex-grow">
            Generate a comprehensive report that compares multiple malwares. The report will highlight
            similarities between the malwares and let you view different attributes, ensuring
            your research is as thorough as possible.
          </p>
          <a href="clear_session/clear_session_table.php"
             class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brand-400
                    hover:text-brand-300 transition-colors">
            Try it <i class="fa-solid fa-arrow-right text-xs"></i>
          </a>
        </article>

        <!-- Card 2 – Show Similarities -->
        <article class="group relative flex flex-col rounded-2xl border border-white/10
                        bg-gray-800/60 hover:bg-gray-800/90 backdrop-blur-sm
                        p-7 shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-brand-900/40">
          <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl
                      bg-indigo-600/20 ring-1 ring-indigo-500/40 group-hover:ring-indigo-500/70 transition-all">
            <img src="../assets/img/icons/sim.png" alt="Similarities icon" class="h-7 w-7 object-contain">
          </div>
          <h3 class="text-xl font-semibold text-white mb-1">Show Similarities</h3>
          <p class="text-sm font-medium text-indigo-400 italic mb-3">Focus on the mutual.</p>
          <p class="text-sm text-gray-400 leading-relaxed flex-grow">
            View only the attributes that are common among all the searched malwares in a concise
            and organized display — zero noise, pure signal.
          </p>
          <a href="clear_session/clear_session_circles.php"
             class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-indigo-400
                    hover:text-indigo-300 transition-colors">
            Try it <i class="fa-solid fa-arrow-right text-xs"></i>
          </a>
        </article>

        <!-- Card 3 – Search Verdicts -->
        <article class="group relative flex flex-col rounded-2xl border border-white/10
                        bg-gray-800/60 hover:bg-gray-800/90 backdrop-blur-sm
                        p-7 shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-brand-900/40">
          <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl
                      bg-violet-600/20 ring-1 ring-violet-500/40 group-hover:ring-violet-500/70 transition-all">
            <img src="../assets/img/icons/verd.png" alt="Verdicts icon" class="h-7 w-7 object-contain">
          </div>
          <h3 class="text-xl font-semibold text-white mb-1">Search Verdicts</h3>
          <p class="text-sm font-medium text-violet-400 italic mb-3">Need a helping hand?</p>
          <p class="text-sm text-gray-400 leading-relaxed flex-grow">
            Obtain a list of all malwares that share the same verdicts as the malware you search for.
            The verdicts filtering feature lets you decide between a larger, general list or a smaller,
            more concise one.
          </p>
          <a href="search_verdicts.php"
             class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-violet-400
                    hover:text-violet-300 transition-colors">
            Try it <i class="fa-solid fa-arrow-right text-xs"></i>
          </a>
        </article>

      </div>
    </div>
  </section>

</main>

<?php
/* Show error alert if redirected with ?error=1 (minimum 2 malwares required) */
if (isset($_GET['error']) && $_GET['error'] == 1):
?>
<div id="error-toast"
     role="alert"
     class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl bg-red-600 px-5 py-4
            text-white shadow-xl shadow-red-900/50 animate-bounce">
  <i class="fa-solid fa-circle-exclamation text-xl"></i>
  <span class="font-medium">Minimum amount of viruses to compare is 2.</span>
  <button onclick="document.getElementById('error-toast').remove()"
          class="ml-2 text-white/70 hover:text-white transition-colors"
          aria-label="Dismiss">
    <i class="fa-solid fa-xmark"></i>
  </button>
</div>
<?php endif; ?>

<?php require_once("parts/footer.php"); ?>