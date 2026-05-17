<?php
// Prevent multiple_choose.php from outputting the footer mid-page.
// table.php is responsible for the footer at the correct position.
define('MULTIPLE_CHOOSE_INCLUDED', true);
require_once "multiple_choose.php";
?>

<?php if (isset($_POST['submitMD5']) && !empty($virusesJsonArray)): ?>

  <!-- Results anchor — JS scrolls here after search -->
  <section id="results-section" class="max-w-7xl mx-auto px-4 sm:px-6 pb-20 pt-4">

    <!-- Compact "back to search" bar -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-white flex items-center gap-2">
        <i class="fa-solid fa-table text-brand-400"></i>
        Comparison Results
        <span class="text-sm font-normal text-gray-400 ml-1">
          (<?php echo count($virusesJsonArray); ?> malwares)
        </span>
      </h2>
      <button onclick="expandSearch()"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/20
                     text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10
                     transition-all duration-200">
        <i class="fa-solid fa-chevron-up"></i> Edit Search
      </button>
    </div>

    <?php printCompareTable($virusesJsonArray); ?>

  </section>

  <script>
    // Collapse the search form and scroll to results as soon as the DOM is ready
    (function () {
      var searchSection = document.getElementById('search-section');
      var resultsSection = document.getElementById('results-section');

      if (searchSection && resultsSection) {
        // Collapse the search form with a smooth animation
        searchSection.style.maxHeight = '0px';
        searchSection.style.opacity  = '0';
        searchSection.style.paddingTop    = '0';
        searchSection.style.paddingBottom = '0';
        searchSection.style.marginBottom  = '0';

        // Scroll to the results after the CSS transition completes
        setTimeout(function () {
          resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
      }
    })();

    // Allow the user to expand the search form again (Edit Search button)
    function expandSearch() {
      var searchSection = document.getElementById('search-section');
      if (searchSection) {
        searchSection.style.maxHeight = '600px';
        searchSection.style.opacity  = '1';
        searchSection.style.paddingTop    = '';
        searchSection.style.paddingBottom = '';
        searchSection.style.marginBottom  = '';
        searchSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }
  </script>

<?php endif; ?>

</main>

<?php
ob_end_flush();
require_once("parts/footer.php");
?>