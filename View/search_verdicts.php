<?php
require_once("parts/header.php");
require_once "../Controller/dbcontroller.php";
require_once("../Controller/verdictsController.php");
require_once("../Controller/malwaresCount.php");
?>

<main class="min-h-screen pt-24 pb-20">

  <!-- ===== Search Section (collapsible) ===== -->
  <div id="search-section" class="overflow-hidden transition-all duration-500 ease-in-out" style="max-height: 600px;">
    <section class="max-w-2xl mx-auto px-4 sm:px-6 text-center">

      <!-- Heading -->
      <div class="mb-8">
        <p class="text-sm font-semibold tracking-widest uppercase text-brand-400 mb-2">
          Verdict Search
        </p>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-3">Search by MD5</h1>
        <p class="text-gray-400 text-base">
          Enter an MD5 hash to retrieve its verdicts, then filter to find similar malwares.
        </p>
      </div>

      <!-- Search form -->
      <form method="post" action="" id="md5-form" class="flex gap-2 w-full">
        <label for="input_string" class="sr-only">MD5 hash</label>
        <input type="text" id="input_string" name="input_string" placeholder="Enter MD5 hash…" required
          autocomplete="off"
          value="<?php echo isset($_POST['input_string']) ? htmlspecialchars($_POST['input_string']) : ''; ?>" class="flex-1 rounded-xl bg-gray-800 border border-white/10 px-4 py-3 text-sm
                      text-gray-100 placeholder-gray-500 outline-none
                      focus:ring-2 focus:ring-brand-500 focus:border-brand-500
                      transition-all duration-200">
        <button type="submit" name="submitMD5" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-brand-600
                       hover:bg-brand-500 text-white text-sm font-semibold shadow-lg
                       shadow-brand-900/50 transition-all duration-200 hover:-translate-y-0.5
                       whitespace-nowrap">
          <i class="fa-solid fa-magnifying-glass"></i> Search
        </button>
      </form>

    </section>
  </div><!-- /#search-section -->

  <?php
  /* =====================================================
     Results — shown only after the form is submitted
  ====================================================== */
  if (isset($_POST['submitMD5'])):
    $md5ToSearch = htmlspecialchars(trim($_POST['input_string']));
    $jsonMD5 = getData($md5ToSearch);
    $verdictsString = ($jsonMD5 !== null) ? (string) ($jsonMD5['verdicts'] ?? '') : '';
    $verdicts = array_filter(array_map('trim', explode(',', $verdictsString)));
    ?>

    <!-- ===== Results Section ===== -->
    <section id="results-section" class="max-w-4xl mx-auto px-4 sm:px-6 pt-6">

      <!-- Compact header bar -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
          <p class="text-xs uppercase tracking-widest text-brand-400 font-semibold mb-1">Verdicts for</p>
          <code class="inline-block bg-gray-800 border border-white/10 rounded-lg
                     px-4 py-1.5 text-brand-300 text-sm font-mono tracking-wide break-all">
              <?php echo $md5ToSearch; ?>
            </code>
        </div>
        <button onclick="expandSearch()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/20
                     text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10
                     transition-all duration-200 shrink-0">
          <i class="fa-solid fa-chevron-up"></i> Edit Search
        </button>
      </div>

      <?php if ($jsonMD5 === null): ?>
        <!-- MD5 not found -->
        <div class="flex flex-col items-center gap-3 py-14 rounded-2xl border border-white/10 bg-gray-900/60 text-gray-500">
          <i class="fa-solid fa-circle-exclamation text-3xl text-yellow-500"></i>
          <p class="text-base font-medium">MD5 not found in VirusTotal or the database.</p>
          <p class="text-sm">Please verify the hash and try again.</p>
        </div>

      <?php elseif (empty($verdicts)): ?>
        <!-- No verdicts on this sample -->
        <div class="flex flex-col items-center gap-3 py-14 rounded-2xl border border-white/10 bg-gray-900/60 text-gray-500">
          <i class="fa-solid fa-circle-exclamation text-3xl text-yellow-500"></i>
          <p class="text-base font-medium">No verdicts found for this MD5.</p>
        </div>

      <?php else: ?>

        <!-- Verdict picker card -->
        <div class="rounded-2xl border border-white/10 bg-gray-900/60 p-8">
          <p class="text-center text-sm font-semibold text-gray-300 mb-6">
            Select the verdicts you would like to search in the database:
          </p>

          <!-- Verdict toggle buttons (populated by JS) -->
          <div id="buttonContainer" class="flex flex-wrap justify-center gap-3 mb-8" role="group"
            aria-label="Verdict selection">
          </div>

          <!-- Submit -->
          <div class="text-center">
            <button id="searchButton" type="button" onclick="chooseVerdicts()" class="inline-flex items-center gap-2 px-7 py-3 rounded-xl bg-brand-600
                         hover:bg-brand-500 text-white font-semibold text-sm shadow-lg
                         shadow-brand-900/50 transition-all duration-200 hover:-translate-y-0.5">
              <i class="fa-solid fa-filter"></i> Find Similar Malwares
            </button>
          </div>
        </div>

      <?php endif; ?>
    </section>

    <!-- ===== JavaScript ===== -->
    <script>
      /* ── Collapse the search form and scroll to results ── */
      (function () {
        var searchEl = document.getElementById('search-section');
        var resultsEl = document.getElementById('results-section');
        if (searchEl && resultsEl) {
          searchEl.style.maxHeight = '0px';
          searchEl.style.opacity = '0';
          searchEl.style.paddingTop = '0';
          searchEl.style.paddingBottom = '0';
          setTimeout(function () {
            resultsEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 100);
        }
      })();

      /* ── Re-expand search form ── */
      function expandSearch() {
        var searchEl = document.getElementById('search-section');
        if (searchEl) {
          searchEl.style.maxHeight = '600px';
          searchEl.style.opacity = '1';
          searchEl.style.paddingTop = '';
          searchEl.style.paddingBottom = '';
          searchEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }

      /* ── Verdict toggle buttons ── */
      function createButtons(verdicts) {
        var container = document.getElementById('buttonContainer');
        if (!container) return;

        verdicts.forEach(function (verdict, i) {
          if (!verdict) return;

          var btn = document.createElement('button');
          btn.type = 'button';
          btn.id = 'verdict-btn-' + i;
          btn.textContent = verdict;
          btn.dataset.selected = 'false';

          var base = ['px-5', 'py-2', 'rounded-lg', 'text-sm', 'font-medium', 'border', 'transition-all', 'duration-150', 'select-none', 'cursor-pointer'];
          var unselected = ['bg-gray-800', 'border-white/10', 'text-gray-300', 'hover:bg-gray-700', 'hover:text-white'];
          var selected = ['bg-brand-600', 'border-brand-500', 'text-white', 'shadow-md', 'shadow-brand-900/40', '-translate-y-0.5'];

          btn.className = [...base, ...unselected].join(' ');

          btn.addEventListener('click', function () {
            var isNowSelected = btn.dataset.selected !== 'true';
            btn.dataset.selected = isNowSelected ? 'true' : 'false';
            if (isNowSelected) {
              unselected.forEach(function (c) { btn.classList.remove(c); });
              selected.forEach(function (c) { btn.classList.add(c); });
            } else {
              selected.forEach(function (c) { btn.classList.remove(c); });
              unselected.forEach(function (c) { btn.classList.add(c); });
            }
          });

          container.appendChild(btn);
        });
      }

      /* ── Collect selected verdicts → POST to verdicts_result.php ── */
      function chooseVerdicts() {
        var buttons = document.querySelectorAll('#buttonContainer button[data-selected="true"]');
        var selectedVerdicts = Array.from(buttons).map(function (b) { return b.textContent.trim(); });

        if (selectedVerdicts.length === 0) {
          alert('Please select at least one verdict before searching.');
          return;
        }

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'verdicts_result.php';

        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'verdicts';
        input.value = JSON.stringify(selectedVerdicts);

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
      }

      /* ── Initialise buttons with PHP-supplied verdicts ── */
      createButtons(<?php echo json_encode(array_values($verdicts)); ?>);
    </script>

  <?php endif; ?>

</main>

<?php require_once("parts/footer.php"); ?>