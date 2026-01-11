<?php
session_start(); // Memulai session

// Menghapus semua session
session_unset(); // Menghapus variabel session
session_destroy(); // Menghancurkan session

echo "Session telah dihapus. <a href='set_session.php'>Set Ulang Session</a>";
?>