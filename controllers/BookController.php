<?php
/**
 * Class BookController (Controller)
 * Sesuai Modul Pertemuan 7 (Slide 5 & Slide 10)
 * Bertanggung jawab menerima request pengguna, memanggil Model,
 * mengolah data, dan mengirimkannya ke View.
 */
class BookController {
    /**
     * Action utama: Menangani request halaman BookTrack
     */
    public function index(): void {
        // 1. REQUEST HANDLING: Menerima parameter filter dari request URL (misal: ?status=Selesai)
        $statusFilter = isset($_GET['status']) ? trim($_GET['status']) : 'all';

        // 2. CALL MODEL: Memanggil Model untuk mendapatkan data
        $user       = BookModel::getUser();
        $allBooks   = BookModel::getAllBooks();
        $books      = BookModel::getFilteredBooks($statusFilter);
        $stats      = BookModel::getStatistics($allBooks, $user);

        // Metadata halaman
        $appName    = "BookTrack";
        $pageTitle  = "Arsitektur MVC (Model-View-Controller) - Sprint 06";

        // 3. SEND TO VIEW: Mengirim data ke View untuk ditampilkan kepada pengguna
        require_once __DIR__ . '/../views/book-page.php';
    }
}
