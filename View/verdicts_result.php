<?php
require_once "parts/header.php";
require_once '../Controller/dbcontroller.php';
require_once '../Controller/verdictsController.php';
require_once '../Controller/malwaresCount.php';
?>

<main class="min-h-screen pt-24 pb-20">
  <div class="max-w-4xl mx-auto px-4 sm:px-6">

  <?php if (isset($_POST['verdicts'])): ?>
    <?php
    $verdicts         = json_decode($_POST['verdicts']);
    $combinedVerdicts = implode(', ', $verdicts);
    $malwaresWithSameVerdicts = getSameVerdicts($combinedVerdicts);
    $similarCount     = count($malwaresWithSameVerdicts);
    $totalCount       = getCountOfAllMalwares();
    ?>

    <!-- Heading -->
    <div class="mb-8 text-center">
      <p class="text-sm font-semibold tracking-widest uppercase text-brand-400 mb-2">Results</p>
      <h1 class="text-4xl font-extrabold text-white mb-4">Verdict Search Results</h1>
      <a href="search_verdicts.php"
         class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-white/20
                text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200">
        <i class="fa-solid fa-rotate-left"></i> New Search
      </a>
    </div>

    <!-- Verdict badges -->
    <div class="text-center mb-8">
      <p class="text-gray-400 text-sm mb-3">Displaying malwares that got the verdicts:</p>
      <div class="inline-flex flex-wrap justify-center gap-2">
        <?php foreach ($verdicts as $v): ?>
          <span class="px-4 py-1.5 rounded-full bg-brand-700/30 border border-brand-500/40
                       text-brand-300 text-sm font-mono">
            <?php echo htmlspecialchars($v); ?>
          </span>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Stats + bar chart -->
    <div class="rounded-2xl border border-white/10 bg-gray-900/60 p-6 mb-8">
      <p class="text-gray-200 font-semibold mb-4">
        <span class="text-brand-400 font-bold text-xl"><?php echo $similarCount; ?></span>
        malwares found out of
        <span class="text-brand-400 font-bold text-xl"><?php echo $totalCount; ?></span>
        in the database:
      </p>
      <div id="horizontal_bar_plot" class="overflow-x-auto"></div>
    </div>

    <script>
      (function () {
        var sim   = <?php echo $similarCount; ?>;
        var tot   = <?php echo $totalCount; ?>;
        var W = 500, H = 40;
        var frac  = tot > 0 ? sim / tot : 0;
        var matchPx = frac * W;
        var svg = d3.select('#horizontal_bar_plot').append('svg').attr('width', W).attr('height', H);
        svg.append('rect').attr('width', matchPx).attr('height', H).attr('fill', '#3A61E2');
        svg.append('rect').attr('x', matchPx).attr('width', W - matchPx).attr('height', H).attr('fill', '#C3D0F2');
        svg.append('text').attr('x', matchPx / 2).attr('y', H / 2)
          .attr('text-anchor', 'middle').attr('dominant-baseline', 'middle')
          .attr('font-size', '18px').attr('fill', 'white')
          .text((frac * 100).toFixed(1) + '%');
        svg.append('text').attr('x', matchPx + (W - matchPx) / 2).attr('y', H / 2)
          .attr('text-anchor', 'middle').attr('dominant-baseline', 'middle')
          .attr('font-size', '18px').attr('fill', '#374151')
          .text(((1 - frac) * 100).toFixed(1) + '%');
      })();
    </script>

    <!-- Results table -->
    <div class="rounded-2xl border border-white/10 bg-gray-900/60 overflow-hidden">
      <?php if (empty($malwaresWithSameVerdicts)): ?>
        <div class="flex flex-col items-center gap-3 py-14 text-gray-500">
          <i class="fa-solid fa-circle-exclamation text-3xl text-yellow-500"></i>
          <p>No malwares found with those verdicts.</p>
        </div>
      <?php else: ?>
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-800/80 border-b border-white/10">
            <tr>
              <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-400 w-16">#</th>
              <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-400">MD5</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <?php for ($i = 0; $i < count($malwaresWithSameVerdicts); $i++): ?>
              <tr class="hover:bg-white/5 transition-colors">
                <td class="px-5 py-3 text-gray-500"><?php echo $i + 1; ?></td>
                <td class="px-5 py-3 font-mono text-gray-200"><?php echo htmlspecialchars($malwaresWithSameVerdicts[$i]); ?></td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

  <?php else: ?>
    <div class="flex flex-col items-center gap-4 pt-20 text-gray-500 text-center">
      <i class="fa-solid fa-triangle-exclamation text-4xl text-yellow-500"></i>
      <p class="text-lg">No verdicts selected.</p>
      <a href="search_verdicts.php"
         class="mt-2 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600
                hover:bg-brand-500 text-white text-sm font-semibold transition-all duration-200">
        <i class="fa-solid fa-arrow-left"></i> Back to Search
      </a>
    </div>
  <?php endif; ?>

  </div>
</main>

<?php require_once("parts/footer.php"); ?>