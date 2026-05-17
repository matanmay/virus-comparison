<?php
session_start();
session_unset();
header('Location: ../show_similarities.php');
exit;