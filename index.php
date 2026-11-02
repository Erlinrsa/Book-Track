<?php
/**
 * =========================================================================
 * SPRINT 06 (WEEK 7) - FROM OOP TO MVC ARCHITECTURE
 * Tema Project : BookTrack - Pelacak Buku & Program Prioritas
 * Sesuai Modul Pertemuan 7: Model - View - Controller (MVC)
 * =========================================================================
 * 
 * ALUR APLIKASI (MVC FLOW):
 * 1. User mengirim HTTP Request ke index.php
 * 2. index.php (Front Controller) memanggil BookController
 * 3. BookController menerima request, memanggil BookModel & User
 * 4. Model mengolah data, filter, dan kalkulasi statistik
 * 5. Controller mengirimkan data hasil olahan ke View (views/book-page.php)
 * 6. View me-render tampilan HTML dan mengirimkannya kembali ke Browser User
 */

// 1. Load Models
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Book.php';
require_once __DIR__ . '/models/BookModel.php';

// 2. Load Controller
require_once __DIR__ . '/controllers/BookController.php';

// 3. Dispatch Request ke Controller
$controller = new BookController();
$controller->index();
