<?php
// Prevent multiple_choose.php from outputting the footer mid-page.
// This file is responsible for the footer at the correct position.
define('MULTIPLE_CHOOSE_INCLUDED', true);
require_once "multiple_choose.php";
require_once "../Controller/equalsController.php";
?>

<?php if (isset($_POST['submitMD5']) && count($virusesJsonArray) > 1): ?>

  <section id="results-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-20">

    <!-- Compact "back to search" bar -->
    <div class="flex items-center justify-between mb-8 border-b border-white/10 pb-6">
      <div>
        <h2 class="text-2xl font-bold text-white flex items-center gap-2">
          <i class="fa-solid fa-chart-bar text-brand-400"></i>
          Similarities
        </h2>
        <p class="text-gray-400 text-sm mt-1">You chose to compare these MD5s:</p>
      </div>
      <button onclick="expandSearch()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/20
                   text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10
                   transition-all duration-200 shrink-0">
        <i class="fa-solid fa-chevron-up"></i> Edit Search
      </button>
    </div>

    <?php
    // Build general details string for each virus (md5, extension, size)
    $generalDetailsArray = array();
    foreach ($virusesJsonArray as $currentVirus) {
      $generalDetailsArray[] =
        $currentVirus->md5
        . ', File Extension: ' . $currentVirus->file_extension
        . ', Size: ' . $currentVirus->file_size;
    }

    // Get common attributes across all selected viruses
    $json_data = getCommonObject($virusesJsonArray);
    ?>

    <!-- D3 visualisation panels -->
    <div class="rounded-2xl border border-white/10 bg-gray-900/60 p-6 overflow-x-auto mb-8">
      <div id="second-svg"></div>
    </div>

    <div class="rounded-2xl border border-white/10 bg-gray-900/60 p-6 overflow-x-auto">
      <div id="res"></div>
    </div>

  </section>

  <script>
    /* ── General details rectangles ── */
    var generalDetailsArray = <?php echo json_encode($generalDetailsArray); ?>;

    var svgHeight = 200;
    if (generalDetailsArray.length === 3) svgHeight = 300;
    if (generalDetailsArray.length === 4) svgHeight = 400;

    var svg2 = d3.select('#second-svg').append('svg').attr('width', 1000).attr('height', svgHeight);
    var rectW = 600, rectH = 90, radius = 10, yPad = 10;
    var xPad = (svg2.attr('width') - rectW) / 2;

    svg2.selectAll('rect').data(generalDetailsArray).enter().append('rect')
      .attr('x', xPad)
      .attr('y', function (d, i) { return i * (rectH + yPad) + yPad; })
      .attr('width', rectW).attr('height', rectH)
      .attr('fill', '#3A61E2').attr('rx', radius).attr('ry', radius);

    svg2.selectAll('text').data(generalDetailsArray).enter().append('text')
      .attr('x', xPad + rectW / 2)
      .attr('y', function (d, i) { return i * (rectH + yPad) + yPad + rectH / 2 - 7; })
      .attr('text-anchor', 'middle').attr('fill', 'white').attr('font-size', 16)
      .selectAll('tspan')
      .data(function (d) { return d.split(','); }).enter().append('tspan')
      .text(function (d) { return d.trim(); })
      .attr('x', xPad + rectW / 2)
      .attr('dy', function (d, i) { return i ? '1.2em' : 0; });

    /* ── Similarities rectangles ── */
    var svg = d3.select('#res').append('svg').attr('width', 1400).attr('height', 1000);
    var json_data = <?php echo json_encode($json_data); ?>;
    var keys = Object.keys(json_data);
    var values = Object.values(json_data);
    var myColor = '#C3D0F2';

    var groups = svg.selectAll('.group').data(keys).enter().append('g')
      .attr('class', 'group')
      .attr('transform', function (d, i) {
        var row = Math.floor(i / 3);
        var x = 130 + (i % 3) * 360;
        var y = 80 + row * 220;
        return 'translate(' + x + ',' + y + ')';
      });

    groups.each(function (d, i) {
      var group = d3.select(this);
      var value_of_json = values[i];
      var longestLen = d.length + 5;

      if (typeof value_of_json === 'string') {
        longestLen = Math.max(longestLen, value_of_json.length);
      } else if (typeof value_of_json === 'number') {
        longestLen = Math.max(longestLen, value_of_json.toString().length);
      } else if (Array.isArray(value_of_json)) {
        value_of_json.forEach(function (v) {
          longestLen = Math.max(longestLen, (typeof v === 'string' ? v : v.toString()).length);
        });
      }

      var width = longestLen * 8;
      var height = 40 + (Array.isArray(value_of_json) ? value_of_json.length * 9 : 0);
      var isTotalVotes = (d === 'total_votes');

      group.append('rect').attr('width', width).attr('height', height)
        .attr('rx', 10).attr('ry', 10).attr('x', -width / 2).attr('fill', myColor);

      group.append('text').attr('text-anchor', 'middle').attr('dy', '-0.5em')
        .attr('font-size', 20).text(d);

      if (isTotalVotes) {
        group.append('text').attr('text-anchor', 'middle').attr('dy', '1.35em').attr('x', 0)
          .attr('font-size', 14).text('Harmless: ' + value_of_json.harmless);
        group.append('text').attr('text-anchor', 'middle').attr('dy', '2.75em').attr('x', 0)
          .attr('font-size', 14).text('Malicious: ' + value_of_json.malicious);
      } else if (typeof value_of_json === 'string' || typeof value_of_json === 'number') {
        group.append('text').attr('text-anchor', 'middle').attr('dy', '1.35em').attr('x', 0)
          .attr('font-size', 14).text(value_of_json);
      } else {
        console.log(value_of_json);
        group.selectAll('text').data(value_of_json).enter().append('text')
          .attr('text-anchor', 'middle')
          .attr('dy', function (d, i) { return (i - value_of_json.length / 3 + 2.6) + 'em'; })
          .attr('font-size', 14).text(function (d) { return d; });
      }
    });

    /* ── Collapse the search form and scroll to results ── */
    (function () {
      var searchSection = document.getElementById('search-section');
      var resultsSection = document.getElementById('results-section');

      if (searchSection && resultsSection) {
        // Collapse the search form with a smooth animation
        searchSection.style.maxHeight = '0px';
        searchSection.style.opacity = '0';
        searchSection.style.paddingTop = '0';
        searchSection.style.paddingBottom = '0';
        searchSection.style.marginBottom = '0';

        // Scroll to the results after the CSS transition completes
        setTimeout(function () {
          resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
      }
    })();

    /* ── Allow the user to expand the search form again ── */
    function expandSearch() {
      var searchSection = document.getElementById('search-section');
      if (searchSection) {
        searchSection.style.maxHeight = '600px';
        searchSection.style.opacity = '1';
        searchSection.style.paddingTop = '';
        searchSection.style.paddingBottom = '';
        searchSection.style.marginBottom = '';
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