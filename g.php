<?php
// Menyertakan file program koneksi.php pada register
include 'koneksi.php'; // Include database connection

// Inisialisasi session
session_start();

$error = '';
$validate = '';

// Mengecek apakah session username tersedia atau tidak, jika tersedia maka akan diredirect ke halaman index
if (isset($_SESSION['username'])) {
    header('Location: dashboard_admin.php');
    exit();
}

// Mengecek apakah form disubmit atau tidak
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (!empty(trim($username)) && !empty(trim($password))) {
        // Query untuk mengambil data user berdasarkan username
        $query = "SELECT * FROM users WHERE username = ?";
        
        // Persiapkan statement
        if ($stmt = mysqli_prepare($conn, $query)) {
            // Binding parameter
            mysqli_stmt_bind_param($stmt, "s", $username);
            
            // Eksekusi query
            mysqli_stmt_execute($stmt);
            
            // Ambil hasilnya
            $result = mysqli_stmt_get_result($stmt);
            
            if ($row = mysqli_fetch_array($result)) {
                $stored_hash = $row['password'];
                $role = $row['role'];
                
                // Cek password menggunakan password_verify jika password di-hash dengan password_hash()
                if (hash('sha256', $password) == $stored_hash) {
                    $_SESSION['username'] = $row['username'];
                    if ($role == 'admin') {
                         $_SESSION['dashboard'] = 'dashboard_admin.php';
                        header('Location: dashboard_admin.php');
                        exit();
                    } else if ($role == 'manajer') {
                        $_SESSION['dashboard'] = 'dashboard_manajer.php';
                        header('Location: dashboard_manajer.php');
                        exit();
                    } else {
                        $_SESSION['dashboard'] = 'dashboard_kasir.php';
                        header('Location: dashboard_kasir.php');
                        exit();
                    }
                } else {
                    $error = 'Password salah';
                }
            } else {
                $error = 'Username tidak ada';
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $error = 'Data tidak boleh kosong !!';
    }
}
?>
