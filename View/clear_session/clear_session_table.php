<?php
// Server-side redirect — no HTML output allowed before header()
session_start();
session_unset();
header('Location: ../table.php');
exit;
?>