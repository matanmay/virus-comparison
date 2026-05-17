<?php require_once "parts/header.php"; ?>
<?php require_once '../Controller/dbcontroller.php'; ?>
<?php
$md5 = htmlspecialchars(trim($_GET['md5_to_search'] ?? ''));
?>

<main class="min-h-screen pt-24 pb-20">
  <div class="max-w-4xl mx-auto px-4 sm:px-6">

    <!-- Heading -->
    <div class="mb-8">
      <p class="text-sm font-semibold tracking-widest uppercase text-brand-400 mb-2">Virus Details</p>
      <h1 class="text-3xl font-extrabold text-white mb-1">
        MD5: <span class="font-mono text-brand-300"><?php echo $md5; ?></span>
      </h1>
      <a href="choose_virus.php"
         class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-brand-400 underline underline-offset-2 transition-colors">
        <i class="fa-solid fa-arrow-left text-xs"></i> Search a different virus
      </a>
    </div>

    <!-- Details table card -->
    <div class="rounded-2xl border border-white/10 bg-gray-900/60 overflow-hidden">
      <?php
      error_reporting(E_ERROR);
      $data = getData($md5);
      if ($data):
      ?>
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-800/80 border-b border-white/10">
            <tr>
              <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-400 w-1/3">Key</th>
              <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Value</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <?php foreach ($data as $key => $value): ?>
              <?php if ($value !== 'null' && $key !== '_id' && $value !== ''): ?>
                <tr class="hover:bg-white/5 transition-colors">
                  <td class="px-5 py-3 font-medium text-gray-300 capitalize"><?php echo htmlspecialchars($key); ?></td>
                  <td class="px-5 py-3 text-gray-400 font-mono break-all"><?php echo htmlspecialchars($value); ?></td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="flex flex-col items-center gap-3 py-16 text-gray-500">
          <i class="fa-solid fa-circle-exclamation text-3xl text-yellow-500"></i>
          <p>No data found for this MD5.</p>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

<?php require_once("parts/footer.php"); ?>
