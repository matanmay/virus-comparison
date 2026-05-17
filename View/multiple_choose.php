<?php require_once "parts/header.php"; ?>
<?php
require_once '../Controller/dbcontroller.php';
require_once '../Controller/showCompareController.php';
ob_start();
// allMD5ValuesToCompare => array of all md5s the user wants to compare
if (!isset($_SESSION['allMD5ValuesToCompare']))
  $_SESSION['allMD5ValuesToCompare'] = array();
?>

<main class="min-h-screen pt-24 pb-20">

  <!-- Collapsible search section — collapsed via JS when results are shown -->
  <div id="search-section" class="overflow-hidden transition-all duration-500 ease-in-out" style="max-height: 600px;">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 text-center">

      <!-- Page heading -->
      <div class="mb-8">
        <p class="text-sm font-semibold tracking-widest uppercase text-brand-400 mb-2">Malware Comparison</p>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-3">Compare Malwares</h1>
        <p class="text-gray-400 text-base">Enter the MD5 hashes of the viruses you want to compare.</p>
      </div>

      <!-- New comparison button -->
      <form method="post" action="" class="mb-6">
        <button type="submit" name="submitNew" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-white/20
                     text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10
                     transition-all duration-200">
          <i class="fa-solid fa-rotate-left"></i> New Comparison
        </button>
      </form>

      <!-- MD5 input form -->
      <form method="post" action="" id="md5-compare-form">
        <div id="fields" class="flex flex-col gap-2 mb-4">
          <!-- Initial input row -->
          <div class="flex gap-2 items-center">
            <button type="button" onclick="removeField(this)" class="shrink-0 w-9 h-9 flex items-center justify-center rounded-lg
                         border border-red-500/40 bg-red-500/10 text-red-400
                         hover:bg-red-500/20 hover:text-red-300 transition-all duration-150
                         text-sm font-bold" title="Remove field">
              <i class="fa-solid fa-xmark"></i>
            </button>
            <input type="text" name="field[]" placeholder="Enter MD5 hash…" required autocomplete="off" class="flex-1 rounded-xl bg-gray-800 border border-white/10 px-4 py-3 text-sm
                        text-gray-100 placeholder-gray-500 outline-none
                        focus:ring-2 focus:ring-brand-500 focus:border-brand-500
                        transition-all duration-200">
          </div>
        </div>

        <!-- Action buttons -->
        <div class="flex justify-center gap-3 mt-4">
          <button type="button" onclick="addField()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-white/20
                       text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10
                       transition-all duration-200">
            <i class="fa-solid fa-plus"></i> Add Field
          </button>
          <button type="submit" name="submitMD5" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-brand-600
                       hover:bg-brand-500 text-white text-sm font-semibold shadow-lg
                       shadow-brand-900/50 transition-all duration-200 hover:-translate-y-0.5">
            <i class="fa-solid fa-magnifying-glass"></i> Search
          </button>
        </div>
      </form>
    </div><!-- /.inner -->
  </div><!-- /#search-section -->

  <?php
  // Once the user submits the MD5 form
  if (isset($_POST['submitMD5'])) {
    $invalidMd5s = array();
    $virusesJsonArray = array();
    foreach ($_POST as $userInput => $md5List) {
      if (is_array($md5List)) {
        foreach ($md5List as $currentMD5) {
          if (!in_array($currentMD5, $_SESSION['allMD5ValuesToCompare'])) {
            array_push($_SESSION['allMD5ValuesToCompare'], $currentMD5);
          }
        }
      } else {
        if (!in_array($currentMD5, $_SESSION['allMD5ValuesToCompare'])) {
          array_push($_SESSION['allMD5ValuesToCompare'], $md5List);
        }
      }
    }

    // Check for invalid MD5s
    $invalidMd5s = array();
    foreach ($_SESSION['allMD5ValuesToCompare'] as $md5) {
      $MD5Json = getData($md5);
      if ($MD5Json === null) {
        array_push($invalidMd5s, $md5);
        $key = array_search($md5, $_SESSION['allMD5ValuesToCompare']);
        if ($key !== false)
          unset($_SESSION['allMD5ValuesToCompare'][$key]);
      }
    }

    // Cap at 4 viruses
    if (count($_SESSION['allMD5ValuesToCompare']) > 4) {
      $_SESSION['allMD5ValuesToCompare'] = array_slice($_SESSION['allMD5ValuesToCompare'], 0, 4);
      echo "<script>alert('Maximum amount of viruses to compare is 4');</script>";
    }

    // Require at least 2 viruses
    if (count($_SESSION['allMD5ValuesToCompare']) < 2) {
      session_unset();
      $_SESSION['allMD5ValuesToCompare'] = array(); // reinitialize so the page stays usable
      $current_url = $_SERVER['REQUEST_URI'];
      if (strpos($current_url, "table.php") !== false) {
        // JS redirect — header() cannot be used after HTML output has started
        echo "<script>window.location.replace('table.php?error=1');</script>";
      } else {
        echo "<script>window.location.replace('show_similarities.php?error=1');</script>";
      }
      // Stop processing — session is cleared, remaining foreach would crash
      $virusesJsonArray = array();
    } else {
      // Build the viruses JSON array
      foreach ($_SESSION['allMD5ValuesToCompare'] as $md5) {
        $MD5Json = getData($md5);
        array_push($virusesJsonArray, $MD5Json);
      }

      // Alert for invalid MD5s
      if (!empty($invalidMd5s)) {
        echo '<script>window.onload = function() { virusNotValid("' . implode(",", $invalidMd5s) . '"); };</script>';
        unset($invalidMd5s);
      }
    }
  }

  // New comparison — clear session and reinitialize
  if (isset($_POST['submitNew'])) {
    session_unset();
    $_SESSION['allMD5ValuesToCompare'] = array();
  }

  // Error from redirect — minimum 2 viruses
  if (isset($_GET['error']) && $_GET['error'] == 1) {
    echo "<script>alert('Minimum amount of viruses to compare is 2');</script>";
    $currentURL = $_SERVER['REQUEST_URI'];
    $redirectToURL = str_replace("?error=1", "", $currentURL);
    echo "<script>history.replaceState(null, '', '$redirectToURL');</script>";
  }
  ?>



<script>
  /** Add a new MD5 input row */
  function addField() {
    var fieldsDiv = document.getElementById('fields');
    var row = document.createElement('div');
    row.className = 'flex gap-2 items-center';
    row.innerHTML =
      '<button type="button" onclick="removeField(this)" title="Remove field"' +
      ' class="shrink-0 w-9 h-9 flex items-center justify-center rounded-lg' +
      ' border border-red-500/40 bg-red-500/10 text-red-400' +
      ' hover:bg-red-500/20 hover:text-red-300 transition-all duration-150 text-sm font-bold">' +
      '<i class="fa-solid fa-xmark"></i></button>' +
      '<input type="text" name="field[]" placeholder="Enter MD5 hash…" required autocomplete="off"' +
      ' class="flex-1 rounded-xl bg-gray-800 border border-white/10 px-4 py-3 text-sm' +
      ' text-gray-100 placeholder-gray-500 outline-none' +
      ' focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all duration-200">';
    fieldsDiv.appendChild(row);
  }

  /** Remove an MD5 input row */
  function removeField(button) {
    button.parentNode.remove();
  }

  /** Alert listing invalid MD5s */
  function virusNotValid(invalidNums) {
    var list = invalidNums.split(',');
    var message = list.map(function (s) { return s.trim(); }).join(' & ');
    alert('The following MD5s do not exist: ' + message);
  }

  /** Delete a column from the comparison table (used by table.php / show_similarities.php) */
  function deleteColumn(button) {
    var colIndex = button.parentNode.cellIndex;
    var table = document.getElementById('multipleTable');
    var rows = table.rows;
    var md5 = rows[1].cells[colIndex].textContent.trim();
    for (var i = 0; i < rows.length; i++) {
      rows[i].deleteCell(colIndex);
    }
    removeMD5FromSession(md5);
  }

  /** AJAX call to remove md5 from the PHP session */
  function removeMD5FromSession(md5) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'remove_md5.php?md5=' + encodeURIComponent(md5), true);
    xhr.send();
  }
</script>

<?php
// Only output the footer when this file is the top-level page.
// When included by table.php or show_similarities.php they define
// MULTIPLE_CHOOSE_INCLUDED so the footer isn't rendered mid-page.
if (!defined('MULTIPLE_CHOOSE_INCLUDED')) {
  echo "</main>";
  ob_end_flush();
  require_once("parts/footer.php");
}
?>