/**
 * BookTrack - Main Application Logic
 * Implements:
 * 1. JavaScript Event Listeners (specifically `<select>` change events, input events, click events)
 * 2. Google Books API live autocomplete and auto-fill
 * 3. Dynamic Rendering for Beranda, Koleksi, Statistik, Catatan, Tambah Buku, and Detail Buku Modal
 * 4. Dark mode toggle & state persistence
 * 5. Hash Routing
 */

// Application State
const AppState = {
  books: [],
  quotes: [],
  yearlyTarget: 12,
  isDarkMode: false,
  activeRoute: 'beranda', // 'beranda' | 'koleksi' | 'statistik' | 'catatan' | 'tambah' | 'detail'
  collectionFilter: {
    status: 'semua', // 'semua' | 'Ingin dibaca' | 'Sedang dibaca' | 'Selesai'
    genre: 'Semua genre',
    sortBy: 'terbaru',
    searchQuery: ''
  },
  selectedBookId: null,
  activeQuoteFilter: 'Semua'
};

// DOM Elements Cache
const DOM = {};

/**
 * Initialize Application
 */
document.addEventListener("DOMContentLoaded", () => {
  // Load initial data from Storage
  AppState.books = Storage.getBooks();
  AppState.quotes = Storage.getQuotes();
  AppState.yearlyTarget = Storage.getYearlyTarget();
  AppState.isDarkMode = Storage.getDarkMode();

  // Cache DOM elements
  cacheDOMElements();

  // Setup theme
  applyDarkMode(AppState.isDarkMode);

  // Bind All Event Listeners (including `<select>` handle user events)
  initEventListeners();

  // Setup initial route via hash
  handleRoute();
  window.addEventListener("hashchange", handleRoute);

  console.log("BookTrack initialized successfully.");
});

/**
 * Cache frequently used DOM elements
 */
function cacheDOMElements() {
  DOM.app = document.getElementById("app");
  DOM.sidebar = document.getElementById("sidebar");
  DOM.navLinks = document.querySelectorAll(".nav-link");
  DOM.darkModeToggle = document.getElementById("btn-dark-mode");
  DOM.globalSearch = document.getElementById("global-search");
  DOM.globalSearchDropdown = document.getElementById("global-search-dropdown");
  DOM.btnAddBook = document.getElementById("btn-add-book-header");
  DOM.mobileMenuBtn = document.getElementById("mobile-menu-btn");

  // Views
  DOM.viewBeranda = document.getElementById("view-beranda");
  DOM.viewKoleksi = document.getElementById("view-koleksi");
  DOM.viewStatistik = document.getElementById("view-statistik");
  DOM.viewCatatan = document.getElementById("view-catatan");
  DOM.viewTambah = document.getElementById("view-tambah");

  // Form Elements (Add Book)
  DOM.formAddBook = document.getElementById("form-add-book");
  DOM.inputGoogleSearch = document.getElementById("input-google-search");
  DOM.btnTriggerGoogleSearch = document.getElementById("btn-trigger-google-search");
  DOM.googleResultsDropdown = document.getElementById("google-search-results");
  DOM.inputBookTitle = document.getElementById("book-title");
  DOM.inputBookAuthor = document.getElementById("book-author");
  DOM.selectBookGenre = document.getElementById("book-genre");
  DOM.inputBookPages = document.getElementById("book-pages");
  DOM.selectBookStatus = document.getElementById("book-status");
  DOM.extraFieldsContainer = document.getElementById("extra-form-fields");
  DOM.inputCurrentPage = document.getElementById("book-current-page");
  DOM.previewCard = document.getElementById("live-preview-card");
  DOM.previewCoverImg = document.getElementById("preview-cover-img");
  DOM.previewGenre = document.getElementById("preview-genre");
  DOM.previewTitle = document.getElementById("preview-title");
  DOM.previewAuthor = document.getElementById("preview-author");
  DOM.formRatingInput = document.getElementById("form-book-rating");
  DOM.formRatingLabel = document.getElementById("form-rating-label");
  DOM.formStarContainer = document.getElementById("form-star-container");
  DOM.btnResetFormRating = document.getElementById("btn-reset-form-rating");

  // Collection Elements
  DOM.filterTabs = document.querySelectorAll(".tab-filter");
  DOM.selectGenreFilter = document.getElementById("genre-filter");
  DOM.selectSortFilter = document.getElementById("sort-filter");
  DOM.booksGrid = document.getElementById("books-grid");

  // Modal Elements
  DOM.modalDetail = document.getElementById("book-detail-modal");
  DOM.modalDetailContent = document.getElementById("modal-detail-content");
  DOM.modalTarget = document.getElementById("target-modal");
  DOM.inputTargetVal = document.getElementById("input-target-val");
  DOM.modalQuote = document.getElementById("quote-modal");
  DOM.selectQuoteBook = document.getElementById("quote-book-select");
  DOM.inputQuoteText = document.getElementById("quote-text");
  DOM.inputQuoteAuthor = document.getElementById("quote-author");
}

/**
 * =========================================================================
 * JAVASCRIPT EVENT LISTENERS IMPLEMENTATION
 * Highlights user event handlers, especially for `<select>` elements!
 * =========================================================================
 */
function initEventListeners() {
  /* -----------------------------------------------------------------------
   * 1. SELECT USER EVENT HANDLERS (User explicit requirement)
   * ----------------------------------------------------------------------- */

  // Event handler for Genre Filter Select dropdown in Koleksi View
  if (DOM.selectGenreFilter) {
    DOM.selectGenreFilter.addEventListener("change", handleGenreFilterChange);
  }

  // Event handler for Sort Order Select dropdown in Koleksi View
  if (DOM.selectSortFilter) {
    DOM.selectSortFilter.addEventListener("change", handleSortFilterChange);
  }

  // Event handler for Genre Select in Tambah Buku Form (updates live cover preview)
  if (DOM.selectBookGenre) {
    DOM.selectBookGenre.addEventListener("change", handleFormGenreChange);
  }

  // Event handler for Status Select in Tambah Buku Form (toggles current page / rating)
  if (DOM.selectBookStatus) {
    DOM.selectBookStatus.addEventListener("change", handleFormStatusChange);
  }

  // Event handler for Book Select in Tambah Catatan Modal
  if (DOM.selectQuoteBook) {
    DOM.selectQuoteBook.addEventListener("change", handleQuoteBookSelectChange);
  }

  /* -----------------------------------------------------------------------
   * 2. INPUT & SEARCH USER EVENT HANDLERS
   * ----------------------------------------------------------------------- */

  // Global header search input
  if (DOM.globalSearch) {
    DOM.globalSearch.addEventListener("input", (e) => {
      const q = e.target.value.trim();
      AppState.collectionFilter.searchQuery = q.toLowerCase();

      // Show dropdown for worldwide search suggestion if query >= 2 characters
      if (DOM.globalSearchDropdown) {
        if (q.length >= 2) {
          const localMatches = AppState.books.filter(b => 
            b.title.toLowerCase().includes(q.toLowerCase()) || 
            b.author.toLowerCase().includes(q.toLowerCase())
          );

          DOM.globalSearchDropdown.innerHTML = `
            <div class="p-2 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-[11px] font-semibold text-slate-500 dark:text-slate-400 flex justify-between">
              <span>Hasil Koleksi Lokal (${localMatches.length})</span>
              <span class="text-blue-600 dark:text-blue-400">Tekan Enter untuk Koleksi</span>
            </div>
            ${localMatches.slice(0, 3).map(b => `
              <div onclick="window.location.hash='#/buku/${b.id}'; DOM.globalSearchDropdown.classList.add('hidden');"
                class="px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer flex items-center justify-between text-xs">
                <span class="font-medium text-slate-800 dark:text-slate-200 truncate">${escapeHTML(b.title)}</span>
                <span class="text-[10px] text-slate-400">${b.genre}</span>
              </div>
            `).join("")}
            <div onclick="searchWorldwideBook('${escapeHTML(q)}'); DOM.globalSearchDropdown.classList.add('hidden');"
              class="p-3 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 cursor-pointer text-xs font-semibold text-blue-700 dark:text-blue-300 flex items-center justify-between transition">
              <span class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Cari <strong>"${escapeHTML(q)}"</strong> di Google Books / Buku Dunia</span>
              </span>
              <span class="text-[10px] bg-blue-600 text-white px-2 py-0.5 rounded-full font-bold">&rarr;</span>
            </div>
          `;
          DOM.globalSearchDropdown.classList.remove("hidden");
        } else {
          DOM.globalSearchDropdown.classList.add("hidden");
        }
      }

      // If user starts searching, switch to Koleksi view automatically
      if (window.location.hash !== "#/koleksi" && q.length > 0) {
        window.location.hash = "#/koleksi";
      } else {
        renderKoleksiView();
      }
    });

    // Close global search dropdown on click outside
    document.addEventListener("click", (e) => {
      if (DOM.globalSearchDropdown && !DOM.globalSearch.contains(e.target) && !DOM.globalSearchDropdown.contains(e.target)) {
        DOM.globalSearchDropdown.classList.add("hidden");
      }
    });
  }

  // Google Books API Search Input with Debounce & Enter key
  if (DOM.inputGoogleSearch) {
    let debounceTimer = null;
    DOM.inputGoogleSearch.addEventListener("input", (e) => {
      clearTimeout(debounceTimer);
      const query = e.target.value.trim();
      if (!query || query.length < 2) {
        hideGoogleResults();
        return;
      }

      showGoogleLoading();
      debounceTimer = setTimeout(() => {
        triggerGoogleSearch(query);
      }, 350);
    });

    // Prevent form submission when pressing Enter in Google Books input
    DOM.inputGoogleSearch.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        clearTimeout(debounceTimer);
        const query = DOM.inputGoogleSearch.value.trim();
        if (query.length >= 2) {
          triggerGoogleSearch(query);
        }
      }
    });

    // Trigger button click
    if (DOM.btnTriggerGoogleSearch) {
      DOM.btnTriggerGoogleSearch.addEventListener("click", () => {
        const query = DOM.inputGoogleSearch.value.trim();
        if (query.length >= 2) {
          triggerGoogleSearch(query);
        } else {
          DOM.inputGoogleSearch.focus();
        }
      });
    }

    // Close Google Books dropdown when clicking outside
    document.addEventListener("click", (e) => {
      if (!DOM.inputGoogleSearch.contains(e.target) && !DOM.googleResultsDropdown.contains(e.target)) {
        hideGoogleResults();
      }
    });
  }

  // Live Preview input sync for Add Book form
  if (DOM.inputBookTitle) {
    DOM.inputBookTitle.addEventListener("input", (e) => {
      DOM.previewTitle.textContent = e.target.value.trim() || "Judul Buku";
    });
  }

  if (DOM.inputBookAuthor) {
    DOM.inputBookAuthor.addEventListener("input", (e) => {
      DOM.previewAuthor.textContent = e.target.value.trim() || "Penulis";
    });
  }

  /* -----------------------------------------------------------------------
   * 3. BUTTONS & FORMS USER EVENT HANDLERS
   * ----------------------------------------------------------------------- */

  // Dark Mode Toggle Button
  if (DOM.darkModeToggle) {
    DOM.darkModeToggle.addEventListener("click", () => {
      AppState.isDarkMode = !AppState.isDarkMode;
      Storage.setDarkMode(AppState.isDarkMode);
      applyDarkMode(AppState.isDarkMode);
    });
  }

  // Add Book Form Submit
  if (DOM.formAddBook) {
    DOM.formAddBook.addEventListener("submit", handleAddBookSubmit);
  }

  // Mobile menu toggle
  if (DOM.mobileMenuBtn) {
    DOM.mobileMenuBtn.addEventListener("click", () => {
      DOM.sidebar.classList.toggle("-translate-x-full");
    });
  }

  // Status Filter Tabs in Koleksi (Semua, Ingin dibaca, Sedang dibaca, Selesai)
  DOM.filterTabs.forEach(tab => {
    tab.addEventListener("click", (e) => {
      const status = tab.getAttribute("data-status");
      AppState.collectionFilter.status = status;
      updateFilterTabsUI(tab);
      renderKoleksiView();
    });
  });

  // Interactive Star Rating in Add Book Form
  if (DOM.formStarContainer) {
    const starBtns = DOM.formStarContainer.querySelectorAll(".form-star");
    starBtns.forEach(btn => {
      btn.addEventListener("click", () => {
        const val = parseInt(btn.getAttribute("data-val"), 10);
        setFormStarRating(val);
      });
    });
  }

  if (DOM.btnResetFormRating) {
    DOM.btnResetFormRating.addEventListener("click", () => {
      setFormStarRating(0);
    });
  }
}

/**
 * Update star rating visual state in Tambah Buku Form
 */
function setFormStarRating(rating) {
  if (DOM.formRatingInput) DOM.formRatingInput.value = rating;
  if (DOM.formRatingLabel) {
    const labels = {
      0: "0.0 (Belum dinilai)",
      1: "1.0 (Kurang)",
      2: "2.0 (Cukup)",
      3: "3.0 (Bagus)",
      4: "4.0 (Sangat Bagus)",
      5: "5.0 (Sempurna ⭐)"
    };
    DOM.formRatingLabel.textContent = labels[rating] || `${rating}.0`;
  }
  if (DOM.formStarContainer) {
    const starBtns = DOM.formStarContainer.querySelectorAll(".form-star");
    starBtns.forEach(btn => {
      const val = parseInt(btn.getAttribute("data-val"), 10);
      if (val <= rating) {
        btn.className = "form-star text-amber-400 hover:scale-110 transition cursor-pointer";
      } else {
        btn.className = "form-star text-slate-300 hover:text-amber-400 hover:scale-110 transition cursor-pointer";
      }
    });
  }
  if (DOM.btnResetFormRating) {
    DOM.btnResetFormRating.classList.toggle("hidden", rating === 0);
  }
}

/**
 * =========================================================================
 * SELECT EVENT HANDLERS (DETAILED IMPLEMENTATION)
 * =========================================================================
 */

/**
 * Handle `<select id="genre-filter">` change event
 * Filters the displayed books in Koleksi view by genre
 */
function handleGenreFilterChange(event) {
  const selectedGenre = event.target.value;
  console.log(`[Select Event] Genre filter changed to: ${selectedGenre}`);
  AppState.collectionFilter.genre = selectedGenre;
  renderKoleksiView();
}

/**
 * Handle `<select id="sort-filter">` change event
 * Sorts books by selected criteria (terbaru, terlama, judul a-z, rating, etc.)
 */
function handleSortFilterChange(event) {
  const selectedSort = event.target.value;
  console.log(`[Select Event] Sort order changed to: ${selectedSort}`);
  AppState.collectionFilter.sortBy = selectedSort;
  renderKoleksiView();
}

/**
 * Handle `<select id="book-genre">` change event in Add Book form
 * Dynamically updates live cover preview style, badge text, and color gradients
 */
function handleFormGenreChange(event) {
  const selectedGenre = event.target.value;
  console.log(`[Select Event] Form book genre selected: ${selectedGenre}`);

  // Update preview badge text
  DOM.previewGenre.textContent = selectedGenre.toUpperCase();

  // Get matching gradient
  const gradientClass = GENRE_GRADIENTS[selectedGenre] || "from-[#1b436c] to-[#0e2136]";

  // Reset existing gradients on preview card (ALWAYS keep relative overflow-hidden to prevent image leaking)
  DOM.previewCard.className = `w-48 h-72 rounded-xl shadow-xl p-5 flex flex-col justify-between text-white transition-all duration-300 bg-gradient-to-br ${gradientClass} relative overflow-hidden`;
}

/**
 * Handle `<select id="book-status">` change event in Add Book form
 * Toggles fields based on reading status (e.g., current page input or rating)
 */
function handleFormStatusChange(event) {
  const selectedStatus = event.target.value;
  console.log(`[Select Event] Form book status selected: ${selectedStatus}`);

  const pageCount = parseInt(DOM.inputBookPages.value, 10) || 0;

  if (selectedStatus === "Sedang dibaca") {
    DOM.extraFieldsContainer.innerHTML = `
      <div>
        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Halaman saat ini</label>
        <input type="number" id="book-current-page" min="0" max="${pageCount || 9999}" value="1"
          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>
    `;
  } else if (selectedStatus === "Selesai") {
    DOM.extraFieldsContainer.innerHTML = "";
    // If no rating given yet, default to 5.0
    if (DOM.formRatingInput && (DOM.formRatingInput.value === "0" || !DOM.formRatingInput.value)) {
      setFormStarRating(5);
    }
  } else {
    // "Ingin dibaca"
    DOM.extraFieldsContainer.innerHTML = "";
  }
}

/**
 * Handle `<select id="quote-book-select">` change event in Quote Modal
 * Automatically sets the author field when a book is picked from collection
 */
function handleQuoteBookSelectChange(event) {
  const bookTitle = event.target.value;
  console.log(`[Select Event] Quote modal book select changed: ${bookTitle}`);
  const book = AppState.books.find(b => b.title === bookTitle);
  if (book && DOM.inputQuoteAuthor) {
    DOM.inputQuoteAuthor.value = book.author;
  }
}

/**
 * =========================================================================
 * GOOGLE BOOKS API AUTOCOMPLETE & FILL
 * =========================================================================
 */
/**
 * =========================================================================
 * GOOGLE BOOKS API AUTOCOMPLETE & FILL (WORLDWIDE SEARCH)
 * =========================================================================
 */
/* =========================================================================
 * SPRINT 03 (WEEK 4) - TASK 03: HANDLE API STATE (LOADING STATE)
 * Menampilkan indikator loading / spinner saat request sedang berlangsung
 * ========================================================================= */
function showGoogleLoading(query) {
  if (!DOM.googleResultsDropdown) return;
  DOM.googleResultsDropdown.innerHTML = `
    <div class="p-5 text-center text-sm text-slate-500 dark:text-slate-400 flex flex-col items-center justify-center gap-2">
      <div class="flex items-center gap-2">
        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span class="font-medium text-slate-700 dark:text-slate-200">Mencari di Google Books & Katalog Dunia...</span>
      </div>
      <p class="text-xs text-slate-400">Mencari: "${escapeHTML(query || '')}"</p>
    </div>
  `;
  DOM.googleResultsDropdown.classList.remove("hidden");
}

function hideGoogleResults() {
  if (DOM.googleResultsDropdown) {
    DOM.googleResultsDropdown.classList.add("hidden");
  }
}

/* =========================================================================
 * SPRINT 03 (WEEK 4) - TASK 03: HANDLE API STATE (ERROR STATE)
 * Memberikan feedback jelas saat request API gagal atau koneksi terputus
 * ========================================================================= */
function renderGoogleErrorState(errorMessage, query) {
  if (!DOM.googleResultsDropdown) return;
  DOM.googleResultsDropdown.innerHTML = `
    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-xl m-2 text-center">
      <div class="text-rose-600 dark:text-rose-400 font-semibold text-xs flex items-center justify-center gap-1.5 mb-1">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Gagal Mengambil Data dari API</span>
      </div>
      <p class="text-[11px] text-slate-500 dark:text-slate-400">
        ${escapeHTML(errorMessage || "Koneksi terputus atau server sedang sibuk.")}
      </p>
      <button type="button" onclick="triggerGoogleSearch('${escapeHTML(query || '')}')"
        class="mt-2.5 px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-[11px] font-medium transition active:scale-95">
        Coba Lagi
      </button>
    </div>
  `;
  DOM.googleResultsDropdown.classList.remove("hidden");
}

/* =========================================================================
 * SPRINT 03 (WEEK 4) - TASK 01 & TASK 03: FETCH DATA & ORCHESTRATE STATES
 * Mengirim request pencarian ke API dan mengatur transisi loading -> success / error
 * ========================================================================= */
async function triggerGoogleSearch(query) {
  if (!query || query.trim().length < 2) {
    hideGoogleResults();
    return;
  }
  const cleanQ = query.trim();
  
  // 1. Tampilkan status LOADING (Task 03)
  showGoogleLoading(cleanQ);

  try {
    // 2. Hubungkan ke API & ambil data JSON (Task 01)
    const results = await BookAPI.searchGoogleBooks(cleanQ);

    // 3. Render data hasil ke DOM (Task 02: Success State)
    renderGoogleResults(results, cleanQ);
  } catch (error) {
    // 4. Tangani jika terjadi error (Task 03: Error State)
    console.error("[BookTrack] Error API search:", error);
    renderGoogleErrorState(error.message, cleanQ);
  }
}

/* =========================================================================
 * SPRINT 03 (WEEK 4) - TASK 02: DISPLAY API DATA (SUCCESS STATE)
 * Mengubah array data JSON menjadi elemen DOM HTML secara dinamis
 * ========================================================================= */
function renderGoogleResults(results, query) {
  if (!DOM.googleResultsDropdown) return;

  if (!results || results.length === 0) {
    DOM.googleResultsDropdown.innerHTML = `
      <div class="p-4 text-center text-sm text-slate-600 dark:text-slate-300">
        <p class="font-semibold">Buku "${escapeHTML(query || '')}" tidak ditemukan.</p>
        <p class="text-xs text-slate-400 mt-1">Coba kata kunci lain atau isi formulir di bawah secara manual.</p>
      </div>
    `;
    DOM.googleResultsDropdown.classList.remove("hidden");
    return;
  }

  // Cache in window to avoid JSON escaping issues
  window._cachedApiBooks = results;

  DOM.googleResultsDropdown.innerHTML = `
    <div class="px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 text-[11px] font-bold text-slate-500 dark:text-slate-400 flex justify-between items-center">
      <span>DITEMUKAN ${results.length} BUKU DARI KATALOG DUNIA</span>
      <span class="text-[10px] text-blue-600 dark:text-blue-400">Klik buku untuk isi formulir</span>
    </div>
    ${results.map((book, idx) => `
      <div class="google-result-item p-3 hover:bg-blue-50/80 dark:hover:bg-slate-700/60 cursor-pointer transition flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 last:border-b-0"
        data-index="${idx}">
        ${book.coverUrl 
          ? `<img src="${book.coverUrl}" class="w-10 h-14 object-cover rounded shadow-sm flex-shrink-0 bg-slate-100" alt="${escapeHTML(book.title)}" onerror="this.style.display='none'" />` 
          : `<div class="w-10 h-14 bg-gradient-to-br ${GENRE_GRADIENTS[book.genre] || 'from-slate-700 to-slate-900'} rounded flex items-center justify-center text-[9px] text-white font-bold text-center px-1 flex-shrink-0">${book.genre}</div>`
        }
        <div class="min-w-0 flex-1">
          <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">${escapeHTML(book.title)}</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 truncate">${escapeHTML(book.authors)}</p>
          <div class="flex items-center gap-2 mt-1">
            <span class="text-[10px] bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded-full font-medium">${book.genre}</span>
            <span class="text-[10px] text-slate-400">${book.pageCount} halaman</span>
            <span class="text-[9px] text-slate-400 border border-slate-200 dark:border-slate-700 px-1.5 py-0.5 rounded">${book.source || 'API'}</span>
          </div>
        </div>
      </div>
    `).join("")}
  `;

  DOM.googleResultsDropdown.classList.remove("hidden");

  // Attach click listener to each Google Books result
  DOM.googleResultsDropdown.querySelectorAll(".google-result-item").forEach(item => {
    item.addEventListener("click", () => {
      const idx = parseInt(item.getAttribute("data-index"), 10);
      const bookData = window._cachedApiBooks[idx];
      if (bookData) {
        populateFormFromGoogleBook(bookData);
      }
      hideGoogleResults();
    });
  });
}

function populateFormFromGoogleBook(book) {
  DOM.inputGoogleSearch.value = book.title;
  DOM.inputBookTitle.value = book.title;
  DOM.inputBookAuthor.value = book.authors;
  DOM.inputBookPages.value = book.pageCount;

  // Set genre select value & trigger change event
  DOM.selectBookGenre.value = book.genre;
  DOM.selectBookGenre.dispatchEvent(new Event("change"));

  // Update preview text
  DOM.previewTitle.textContent = book.title;
  DOM.previewAuthor.textContent = book.authors;
  DOM.previewGenre.textContent = book.genre.toUpperCase();

  // If cover image exists, safely bind to previewCoverImg inside live preview card
  if (book.coverUrl) {
    DOM.previewCard.setAttribute("data-cover-url", book.coverUrl);
    if (DOM.previewCoverImg) {
      DOM.previewCoverImg.src = book.coverUrl;
      DOM.previewCoverImg.classList.remove("hidden");
    }
  } else {
    DOM.previewCard.removeAttribute("data-cover-url");
    if (DOM.previewCoverImg) {
      DOM.previewCoverImg.src = "";
      DOM.previewCoverImg.classList.add("hidden");
    }
  }

  // Clean up any legacy dynamic element if present
  const oldDynamicImg = DOM.previewCard.querySelector(".live-cover-img");
  if (oldDynamicImg) oldDynamicImg.remove();

  // If book has rating, set the interactive stars
  if (book.averageRating) {
    setFormStarRating(Math.round(book.averageRating));
  }

  showNotification(`Data buku "${book.title}" berhasil dimuat dari ${book.source || 'API'}!`);
}

// Global helper to search worldwide book from any view
window.searchWorldwideBook = function(query) {
  window.location.hash = "#/tambah";
  setTimeout(() => {
    if (DOM.inputGoogleSearch) {
      DOM.inputGoogleSearch.value = query;
      triggerGoogleSearch(query);
      DOM.inputGoogleSearch.focus();
    }
  }, 120);
};

/**
 * =========================================================================
 * FORM SUBMISSION (ADD BOOK)
 * =========================================================================
 */
function handleAddBookSubmit(e) {
  e.preventDefault();

  const title = DOM.inputBookTitle.value.trim();
  const author = DOM.inputBookAuthor.value.trim();
  const genre = DOM.selectBookGenre.value;
  const totalPages = parseInt(DOM.inputBookPages.value, 10) || 100;
  const status = DOM.selectBookStatus.value;
  const coverUrl = DOM.previewCard.getAttribute("data-cover-url") || "";

  if (!title || !author) {
    alert("Harap masukkan judul buku dan nama penulis.");
    return;
  }

  let currentPage = 0;
  let rating = 0;

  if (DOM.formRatingInput) {
    rating = parseFloat(DOM.formRatingInput.value) || 0;
  }

  if (status === "Sedang dibaca") {
    const curPageInput = document.getElementById("book-current-page");
    currentPage = curPageInput ? Math.min(parseInt(curPageInput.value, 10) || 1, totalPages) : 1;
  } else if (status === "Selesai") {
    currentPage = totalPages;
    if (rating === 0) {
      rating = 5.0; // Default to 5.0 if marked as Selesai without custom rating
    }
  }

  const newBook = {
    id: Date.now(),
    title,
    author,
    genre,
    totalPages,
    currentPage,
    status,
    rating,
    coverColor: GENRE_GRADIENTS[genre] || "from-[#1b436c] to-[#0e2136]",
    coverUrl,
    quotes: [],
    notes: "",
    createdAt: new Date().toISOString()
  };

  AppState.books.unshift(newBook);
  Storage.saveBooks(AppState.books);

  showNotification(`Buku "${title}" berhasil ditambahkan ke koleksi!`);

  // Reset form
  DOM.formAddBook.reset();
  DOM.previewCard.removeAttribute("data-cover-url");
  if (DOM.previewCoverImg) {
    DOM.previewCoverImg.src = "";
    DOM.previewCoverImg.classList.add("hidden");
  }
  const oldDynamicImg = DOM.previewCard.querySelector(".live-cover-img");
  if (oldDynamicImg) oldDynamicImg.remove();

  setFormStarRating(0);
  DOM.previewTitle.textContent = "Judul Buku";
  DOM.previewAuthor.textContent = "Penulis";
  DOM.previewGenre.textContent = "FIKSI";
  DOM.selectBookGenre.value = "Fiksi";
  DOM.selectBookGenre.dispatchEvent(new Event("change"));
  DOM.selectBookStatus.value = "Ingin dibaca";
  DOM.selectBookStatus.dispatchEvent(new Event("change"));

  // Navigate to collection
  window.location.hash = "#/koleksi";
}

/**
 * =========================================================================
 * ROUTING & VIEW CONTROLLERS
 * =========================================================================
 */
function handleRoute() {
  const hash = window.location.hash || "#/";
  console.log(`[Route] Navigating to: ${hash}`);

  // Hide all views
  DOM.viewBeranda.classList.add("hidden");
  DOM.viewKoleksi.classList.add("hidden");
  DOM.viewStatistik.classList.add("hidden");
  DOM.viewCatatan.classList.add("hidden");
  DOM.viewTambah.classList.add("hidden");

  // Reset active state on navigation items
  DOM.navLinks.forEach(link => {
    link.classList.remove("bg-blue-50", "text-blue-600", "dark:bg-blue-900/30", "dark:text-blue-400", "font-medium");
    link.classList.add("text-slate-600", "dark:text-slate-400");
  });

  // Check detail route: `#/buku/:id` (matching screenshot 3)
  if (hash.startsWith("#/buku/")) {
    const bookId = parseInt(hash.replace("#/buku/", ""), 10);
    openBookDetailModal(bookId);
    // Keep Koleksi view visible under modal
    DOM.viewKoleksi.classList.remove("hidden");
    setActiveNavLink("koleksi");
    renderKoleksiView();
    return;
  }

  // Close modal if open
  closeAllModals();

  if (hash === "#/" || hash === "#/beranda") {
    AppState.activeRoute = "beranda";
    DOM.viewBeranda.classList.remove("hidden");
    setActiveNavLink("beranda");
    renderBerandaView();
  } else if (hash === "#/koleksi") {
    AppState.activeRoute = "koleksi";
    DOM.viewKoleksi.classList.remove("hidden");
    setActiveNavLink("koleksi");
    renderKoleksiView();
  } else if (hash === "#/statistik") {
    AppState.activeRoute = "statistik";
    DOM.viewStatistik.classList.remove("hidden");
    setActiveNavLink("statistik");
    renderStatistikView();
  } else if (hash === "#/catatan") {
    AppState.activeRoute = "catatan";
    DOM.viewCatatan.classList.remove("hidden");
    setActiveNavLink("catatan");
    renderCatatanView();
  } else if (hash === "#/tambah") {
    AppState.activeRoute = "tambah";
    DOM.viewTambah.classList.remove("hidden");
    renderTambahView();
  }

  // Close mobile sidebar on route change
  if (window.innerWidth < 1024) {
    DOM.sidebar.classList.add("-translate-x-full");
  }

  // Scroll to top
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function setActiveNavLink(viewName) {
  const activeLink = document.querySelector(`.nav-link[data-view="${viewName}"]`);
  if (activeLink) {
    activeLink.classList.remove("text-slate-600", "dark:text-slate-400");
    activeLink.classList.add("bg-blue-50", "text-blue-600", "dark:bg-blue-900/30", "dark:text-blue-400", "font-medium");
  }
}

/**
 * =========================================================================
 * 1. BERANDA VIEW RENDERING (Image 5)
 * =========================================================================
 */
function renderBerandaView() {
  const container = document.getElementById("beranda-content");
  if (!container) return;

  // 1. Dynamic Greeting based on time of day
  const hour = new Date().getHours();
  let timeGreeting = "sore";
  if (hour >= 4 && hour < 11) timeGreeting = "pagi";
  else if (hour >= 11 && hour < 15) timeGreeting = "siang";
  else if (hour >= 15 && hour < 18) timeGreeting = "sore";
  else timeGreeting = "malam";

  // Books currently being read ("Sedang dibaca") sorted by latest activity (Laskar Pelangi, Clean Code, Sapiens)
  const readingBooks = AppState.books
    .filter(b => b.status === "Sedang dibaca")
    .sort((a, b) => new Date(b.lastReadAt || b.createdAt || 0) - new Date(a.lastReadAt || a.createdAt || 0));
  const readingCount = readingBooks.length;

  // Finished books this year
  const finishedBooks = AppState.books.filter(b => b.status === "Selesai");
  const finishedCount = finishedBooks.length;
  const target = AppState.yearlyTarget;

  // Calculate circular progress for target
  const targetPercent = Math.min(Math.round((finishedCount / target) * 100), 100);
  const strokeDashoffset = 251.2 - (251.2 * targetPercent) / 100;

  container.innerHTML = `
    <!-- Greeting Header -->
    <div class="mb-8">
      <h1 class="text-3xl sm:text-4xl font-serif font-bold text-slate-900 dark:text-white tracking-tight">
        Selamat ${timeGreeting}, pembaca.
      </h1>
      <p class="text-slate-500 dark:text-slate-400 mt-2 text-base">
        Ada ${readingCount} buku yang menunggu dilanjutkan.
      </p>
    </div>

    <!-- Main Grid: 2 Columns Layout matching Screenshot 5 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <!-- Left Column: Lanjutkan Membaca (span 2) -->
      <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-7 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-6">Lanjutkan membaca</h2>

        ${readingBooks.length === 0 ? `
          <div class="text-center py-12 text-slate-400">
            <p>Tidak ada buku yang sedang dibaca.</p>
            <a href="#/koleksi" class="inline-block mt-3 text-sm text-blue-600 dark:text-blue-400 font-medium hover:underline">
              Pilih dari Koleksi &rarr;
            </a>
          </div>
        ` : `
          <div class="space-y-6">
            ${readingBooks.map(book => {
              const percent = Math.min(Math.round((book.currentPage / book.totalPages) * 100), 100);
              const gradientClass = book.coverColor || GENRE_GRADIENTS[book.genre] || "from-blue-700 to-blue-900";
              return `
                <div class="reading-book-card flex items-center gap-4 sm:gap-5 pb-6 border-b border-slate-100 dark:border-slate-700 last:border-b-0 last:pb-0">
                  
                  <!-- Mini Cover -->
                  <div onclick="window.location.hash='#/buku/${book.id}'"
                    class="w-14 sm:w-16 h-20 sm:h-24 rounded-lg bg-gradient-to-br ${gradientClass} flex-shrink-0 p-2 text-white flex flex-col justify-between shadow-sm cursor-pointer hover:scale-105 transition-transform overflow-hidden relative">
                    ${book.coverUrl ? `
                      <img src="${book.coverUrl}" class="absolute inset-0 w-full h-full object-cover rounded-lg" alt="" onerror="this.style.display='none'" />
                      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent pointer-events-none rounded-lg"></div>
                    ` : ''}
                    <span class="text-[8px] uppercase tracking-wider font-bold opacity-80 truncate z-10">${book.genre}</span>
                    <div class="z-10">
                      <p class="text-[10px] sm:text-xs font-bold leading-tight line-clamp-2">${escapeHTML(book.title)}</p>
                      <p class="text-[8px] opacity-75 truncate">${escapeHTML(book.author)}</p>
                    </div>
                  </div>

                  <!-- Details & Progress Bar -->
                  <div class="flex-1 min-w-0">
                    <h3 onclick="window.location.hash='#/buku/${book.id}'"
                      class="text-base sm:text-lg font-bold text-slate-900 dark:text-white truncate cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition">
                      ${escapeHTML(book.title)}
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                      ${book.currentPage} dari ${book.totalPages} halaman · ${percent}%
                    </p>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-100 dark:bg-slate-700 h-2 rounded-full mt-3 overflow-hidden">
                      <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: ${percent}%"></div>
                    </div>

                    <!-- Quick Page Increment Buttons (+10 hlm, +25 hlm) -->
                    <div class="flex items-center gap-2 mt-3">
                      <button onclick="handleQuickProgress(${book.id}, 10)"
                        class="px-3 py-1 bg-slate-50 hover:bg-blue-50 dark:bg-slate-700/60 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-full text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-blue-600 transition active:scale-95">
                        +10 hlm
                      </button>
                      <button onclick="handleQuickProgress(${book.id}, 25)"
                        class="px-3 py-1 bg-slate-50 hover:bg-blue-50 dark:bg-slate-700/60 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-full text-xs font-medium text-slate-700 dark:text-slate-300 hover:text-blue-600 transition active:scale-95">
                        +25 hlm
                      </button>
                    </div>
                  </div>

                </div>
              `;
            }).join("")}
          </div>
        `}
      </div>

      <!-- Right Column: Target buku tahun ini (span 1) matching Screenshot 5 -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-100 dark:border-slate-700/60 shadow-sm flex flex-col items-center justify-center text-center">
        
        <!-- Circular Progress Ring -->
        <div class="relative w-40 h-40 flex items-center justify-center mb-6">
          <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
            <!-- Background circle -->
            <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="8" class="text-slate-100 dark:text-slate-700" fill="transparent" />
            <!-- Progress circle -->
            <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="8" stroke-linecap="round" class="text-blue-600 transition-all duration-1000 ease-out"
              fill="transparent"
              stroke-dasharray="251.2"
              stroke-dashoffset="${strokeDashoffset}" />
          </svg>
          <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-3xl font-serif font-black text-slate-900 dark:text-white">
              ${finishedCount}/${target}
            </span>
          </div>
        </div>

        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Target buku tahun ini</p>
        <button onclick="openTargetModal()" class="mt-2 text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold focus:outline-none">
          ubah target
        </button>

        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700 w-full flex justify-between text-xs text-slate-400">
          <span>Tercapai: <strong class="text-slate-700 dark:text-slate-200 font-semibold">${targetPercent}%</strong></span>
          <span>Sisa: <strong class="text-slate-700 dark:text-slate-200 font-semibold">${Math.max(0, target - finishedCount)} buku</strong></span>
        </div>
      </div>

    </div>
  `;
}

/**
 * Handle quick progress addition (+10 hlm, +25 hlm)
 */
window.handleQuickProgress = function(bookId, increment) {
  const book = AppState.books.find(b => b.id === bookId);
  if (!book) return;

  const previousPage = book.currentPage;
  book.currentPage = Math.min(book.currentPage + increment, book.totalPages);

  if (book.currentPage >= book.totalPages) {
    book.status = "Selesai";
    book.rating = book.rating || 5.0;
    showNotification(`Selamat! Anda telah menyelesaikan buku "${book.title}"! 🎉`);
  } else {
    showNotification(`Progress "${book.title}": ${previousPage} &rarr; ${book.currentPage} hlm`);
  }

  Storage.saveBooks(AppState.books);
  renderBerandaView();
  renderKoleksiView();
};

/**
 * =========================================================================
 * 2. KOLEKSI VIEW RENDERING (Image 3)
 * =========================================================================
 */
function renderKoleksiView() {
  const grid = document.getElementById("books-grid");
  if (!grid) return;

  // Update tab counts
  updateTabCounts();

  // Filter books
  let filtered = [...AppState.books];

  // 1. Status Filter
  if (AppState.collectionFilter.status !== "semua") {
    filtered = filtered.filter(b => b.status === AppState.collectionFilter.status);
  }

  // 2. Genre Filter (via <select id="genre-filter">)
  if (AppState.collectionFilter.genre !== "Semua genre") {
    filtered = filtered.filter(b => b.genre === AppState.collectionFilter.genre);
  }

  // 3. Search Query Filter
  if (AppState.collectionFilter.searchQuery) {
    const q = AppState.collectionFilter.searchQuery;
    filtered = filtered.filter(b => 
      b.title.toLowerCase().includes(q) || 
      b.author.toLowerCase().includes(q) || 
      b.genre.toLowerCase().includes(q)
    );
  }

  // 4. Sort Order (via <select id="sort-filter">)
  const sortBy = AppState.collectionFilter.sortBy;
  if (sortBy === "terbaru") {
    filtered.sort((a, b) => new Date(b.createdAt || 0) - new Date(a.createdAt || 0));
  } else if (sortBy === "terlama") {
    filtered.sort((a, b) => new Date(a.createdAt || 0) - new Date(b.createdAt || 0));
  } else if (sortBy === "judul-az") {
    filtered.sort((a, b) => a.title.localeCompare(b.title));
  } else if (sortBy === "judul-za") {
    filtered.sort((a, b) => b.title.localeCompare(a.title));
  } else if (sortBy === "halaman") {
    filtered.sort((a, b) => b.totalPages - a.totalPages);
  } else if (sortBy === "rating") {
    filtered.sort((a, b) => (b.rating || 0) - (a.rating || 0));
  }

  // Render Grid
  if (filtered.length === 0) {
    if (AppState.collectionFilter.searchQuery) {
      const q = AppState.collectionFilter.searchQuery;
      grid.innerHTML = `
        <div class="col-span-full bg-blue-50/70 dark:bg-slate-800 border border-blue-100 dark:border-slate-700 rounded-2xl p-8 text-center my-6">
          <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300 flex items-center justify-center mx-auto mb-3 text-2xl">
            🌍
          </div>
          <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100">Buku "${escapeHTML(q)}" belum ada di koleksi Anda</h3>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
            Ingin mencari buku ini di katalog buku dunia (Google Books & Open Library) dan menambahkannya ke koleksi?
          </p>
          <button onclick="searchWorldwideBook('${escapeHTML(q)}')"
            class="mt-4 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-medium text-xs sm:text-sm rounded-xl transition shadow-md hover:shadow-lg inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>Cari "${escapeHTML(q)}" di Google Books / Buku Dunia</span>
          </button>
        </div>
      `;
      return;
    }

    grid.innerHTML = `
      <div class="col-span-full text-center py-16 text-slate-400">
        <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <p class="text-base font-medium text-slate-600 dark:text-slate-300">Tidak ada buku yang sesuai kriteria.</p>
        <p class="text-sm text-slate-400 mt-1">Coba ubah filter atau tambahkan buku baru ke koleksi.</p>
      </div>
    `;
    return;
  }

  grid.innerHTML = filtered.map(book => {
    const percent = Math.min(Math.round((book.currentPage / book.totalPages) * 100), 100);
    const gradientClass = book.coverColor || GENRE_GRADIENTS[book.genre] || "from-blue-700 to-blue-900";

    // Indicator below cover
    let bottomIndicator = "";
    if (book.status === "Sedang dibaca") {
      bottomIndicator = `
        <div class="w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full mt-2 overflow-hidden">
          <div class="bg-blue-600 h-1.5 rounded-full" style="width: ${percent}%"></div>
        </div>
      `;
    } else if (book.status === "Selesai" || (book.rating && book.rating > 0)) {
      const r = Math.round(book.rating || 5);
      bottomIndicator = `
        <div class="flex items-center text-xs mt-1.5 gap-0.5">
          ${[1, 2, 3, 4, 5].map(i => `<span class="${i <= r ? 'text-amber-400' : 'text-slate-300 dark:text-slate-600'}">★</span>`).join("")}
          <span class="text-[10px] text-slate-400 dark:text-slate-400 ml-1 font-semibold">${book.rating ? Number(book.rating).toFixed(1) : '5.0'}</span>
        </div>
      `;
    }

    return `
      <div onclick="window.location.hash='#/buku/${book.id}'"
        class="book-card group cursor-pointer flex flex-col transition-all duration-300 hover:-translate-y-1.5">
        
        <!-- Book Cover matching Screenshot 3 -->
        <div class="w-full aspect-[2/3] rounded-xl bg-gradient-to-br ${gradientClass} p-4 sm:p-5 text-white flex flex-col justify-between shadow-md group-hover:shadow-xl transition-all duration-300 relative overflow-hidden">
          
          ${book.coverUrl ? `
            <img src="${book.coverUrl}" class="absolute inset-0 w-full h-full object-cover rounded-xl opacity-90 transition-transform duration-300 group-hover:scale-105" alt="${escapeHTML(book.title)}" onerror="this.style.display='none'" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-black/25 rounded-xl pointer-events-none"></div>
          ` : ''}

          <!-- Category Pill top-left -->
          <span class="text-[10px] sm:text-[11px] font-bold tracking-widest uppercase opacity-85 z-10">
            ${book.genre}
          </span>

          <!-- Book Title & Author bottom -->
          <div class="z-10">
            <h3 class="font-serif font-bold text-base sm:text-lg leading-tight line-clamp-2 text-white">
              ${escapeHTML(book.title)}
            </h3>
            <p class="text-xs text-white/80 mt-1 truncate">
              ${escapeHTML(book.author)}
            </p>
          </div>

          <!-- Subtle overlay texture / shine -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-white/10 pointer-events-none"></div>
        </div>

        <!-- Book Meta below cover -->
        <div class="mt-3 px-0.5">
          <h4 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white truncate group-hover:text-blue-600 transition">
            ${escapeHTML(book.title)}
          </h4>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            ${book.status}
          </p>
          ${bottomIndicator}
        </div>

      </div>
    `;
  }).join("");
}

function updateTabCounts() {
  const total = AppState.books.length;
  const want = AppState.books.filter(b => b.status === "Ingin dibaca").length;
  const reading = AppState.books.filter(b => b.status === "Sedang dibaca").length;
  const finished = AppState.books.filter(b => b.status === "Selesai").length;

  const tabSemua = document.querySelector('.tab-filter[data-status="semua"]');
  const tabWant = document.querySelector('.tab-filter[data-status="Ingin dibaca"]');
  const tabReading = document.querySelector('.tab-filter[data-status="Sedang dibaca"]');
  const tabFinished = document.querySelector('.tab-filter[data-status="Selesai"]');

  if (tabSemua) tabSemua.textContent = `Semua · ${total}`;
  if (tabWant) tabWant.textContent = `Ingin dibaca · ${want}`;
  if (tabReading) tabReading.textContent = `Sedang dibaca · ${reading}`;
  if (tabFinished) tabFinished.textContent = `Selesai · ${finished}`;
}

function updateFilterTabsUI(activeTab) {
  DOM.filterTabs.forEach(tab => {
    tab.className = "tab-filter px-4 py-1.5 rounded-full text-sm font-medium transition cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700";
  });
  activeTab.className = "tab-filter px-4 py-1.5 rounded-full text-sm font-medium transition cursor-pointer bg-slate-900 text-white dark:bg-blue-600 dark:text-white";
}

/**
 * =========================================================================
 * 3. STATISTIK VIEW RENDERING (Image 4)
 * =========================================================================
 */
function renderStatistikView() {
  const container = document.getElementById("statistik-content");
  if (!container) return;

  const totalBooks = AppState.books.length;
  
  // Total pages read: sum of currentPage for reading books + totalPages for completed books
  const totalPagesRead = AppState.books.reduce((acc, b) => {
    if (b.status === "Selesai") return acc + (b.totalPages || 0);
    return acc + (b.currentPage || 0);
  }, 0);

  const completedBooks = AppState.books.filter(b => b.status === "Selesai").length;

  // Average rating of rated books
  const ratedBooks = AppState.books.filter(b => b.rating && b.rating > 0);
  const avgRating = ratedBooks.length > 0 
    ? (ratedBooks.reduce((acc, b) => acc + b.rating, 0) / ratedBooks.length).toFixed(1)
    : "5.0";

  // Per Genre stats
  const genreCounts = {};
  AppState.books.forEach(b => {
    genreCounts[b.genre] = (genreCounts[b.genre] || 0) + 1;
  });
  const preferredOrder = ["Self-Improvement", "Fiksi", "Teknologi", "Non-Fiksi", "Bisnis", "Filosofi", "Sains", "Sejarah", "Biografi"];
  const genreList = Object.entries(genreCounts).sort((a, b) => {
    if (b[1] !== a[1]) return b[1] - a[1];
    return preferredOrder.indexOf(a[0]) - preferredOrder.indexOf(b[0]);
  });

  // Per Status stats
  const statusCounts = {
    "Ingin dibaca": AppState.books.filter(b => b.status === "Ingin dibaca").length,
    "Sedang dibaca": AppState.books.filter(b => b.status === "Sedang dibaca").length,
    "Selesai": AppState.books.filter(b => b.status === "Selesai").length
  };

  container.innerHTML = `
    <!-- Title -->
    <div class="mb-8">
      <h1 class="text-3xl sm:text-4xl font-serif font-bold text-slate-900 dark:text-white tracking-tight">
        Statistik
      </h1>
    </div>

    <!-- 4 Top Metric Cards matching Screenshot 4 -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8">
      
      <!-- Card 1: Buku -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white">${totalBooks}</p>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-2">Buku</p>
      </div>

      <!-- Card 2: Halaman terbaca -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white">${totalPagesRead.toLocaleString("id-ID")}</p>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-2">Halaman terbaca</p>
      </div>

      <!-- Card 3: Selesai -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white">${completedBooks}</p>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-2">Selesai</p>
      </div>

      <!-- Card 4: Rata-rata rating -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <p class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white">${avgRating}</p>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-2">Rata-rata rating</p>
      </div>

    </div>

    <!-- 2 Bottom Comparison Cards matching Screenshot 4 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      <!-- Card: Per genre -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-7 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 dark:text-white mb-6">Per genre</h2>

        <div class="space-y-5">
          ${genreList.map(([genre, count]) => {
            const barWidth = totalBooks > 0 ? Math.round((count / totalBooks) * 100) : 0;
            return `
              <div>
                <div class="flex justify-between items-center text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                  <span>${genre}</span>
                  <span class="text-slate-500 dark:text-slate-400">${count}</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                  <div class="bg-blue-600 h-2 rounded-full transition-all duration-700" style="width: ${barWidth}%"></div>
                </div>
              </div>
            `;
          }).join("")}
        </div>
      </div>

      <!-- Card: Per status -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-7 border border-slate-100 dark:border-slate-700/60 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 dark:text-white mb-6">Per status</h2>

        <div class="space-y-5">
          <!-- Ingin dibaca (gray bar) -->
          <div>
            <div class="flex justify-between items-center text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
              <span>Ingin dibaca</span>
              <span class="text-slate-500 dark:text-slate-400">${statusCounts["Ingin dibaca"]}</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
              <div class="bg-slate-400 h-2 rounded-full transition-all duration-700"
                style="width: ${totalBooks > 0 ? (statusCounts["Ingin dibaca"] / totalBooks) * 100 : 0}%"></div>
            </div>
          </div>

          <!-- Sedang dibaca (yellow/amber bar) -->
          <div>
            <div class="flex justify-between items-center text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
              <span>Sedang dibaca</span>
              <span class="text-slate-500 dark:text-slate-400">${statusCounts["Sedang dibaca"]}</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
              <div class="bg-amber-400 h-2 rounded-full transition-all duration-700"
                style="width: ${totalBooks > 0 ? (statusCounts["Sedang dibaca"] / totalBooks) * 100 : 0}%"></div>
            </div>
          </div>

          <!-- Selesai (green bar) -->
          <div>
            <div class="flex justify-between items-center text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
              <span>Selesai</span>
              <span class="text-slate-500 dark:text-slate-400">${statusCounts["Selesai"]}</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
              <div class="bg-emerald-500 h-2 rounded-full transition-all duration-700"
                style="width: ${totalBooks > 0 ? (statusCounts["Selesai"] / totalBooks) * 100 : 0}%"></div>
            </div>
          </div>
        </div>

      </div>

    </div>
  `;
}

/**
 * =========================================================================
 * 4. KUTIPAN & CATATAN VIEW RENDERING (Image 2)
 * =========================================================================
 */
function renderCatatanView() {
  const container = document.getElementById("catatan-content");
  if (!container) return;

  container.innerHTML = `
    <!-- Header with Add Quote & Random API Quote Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-slate-900 dark:text-white tracking-tight">
          Kutipan & catatan
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Koleksi mutiara kata dan catatan penting dari bacaanmu.</p>
      </div>

      <div class="flex items-center gap-3">
        <button onclick="fetchNewAPIQuote()" id="btn-fetch-quote"
          class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition flex items-center gap-2">
          <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span>Kutipan API Acak</span>
        </button>

        <button onclick="openQuoteModal()"
          class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition flex items-center gap-2">
          <span>+ Tambah Kutipan</span>
        </button>
      </div>
    </div>

    <!-- Quote Cards List matching Screenshot 2 -->
    <div class="space-y-6">
      ${AppState.quotes.map(quote => `
        <div class="quote-card bg-white dark:bg-slate-800 p-8 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-sm relative group transition hover:shadow-md">
          <!-- Big serif quote text -->
          <blockquote class="text-xl sm:text-2xl font-serif italic font-medium text-slate-800 dark:text-slate-100 leading-relaxed">
            “${escapeHTML(quote.text)}”
          </blockquote>
          
          <!-- Source metadata -->
          <div class="mt-4 flex items-center justify-between">
            <p class="text-xs sm:text-sm text-slate-400 dark:text-slate-500 font-medium">
              ${escapeHTML(quote.bookTitle)} · ${escapeHTML(quote.author)}
            </p>

            <button onclick="deleteQuote(${quote.id})" class="opacity-0 group-hover:opacity-100 text-xs text-rose-500 hover:underline transition">
              Hapus
            </button>
          </div>
        </div>
      `).join("")}
    </div>
  `;
}

/* =========================================================================
 * SPRINT 03 (WEEK 4) - TASK 01, 02, 03: FETCH RANDOM QUOTE DARI API
 * ========================================================================= */
window.fetchNewAPIQuote = async function() {
  const btn = document.getElementById("btn-fetch-quote");
  if (btn) {
    btn.classList.add("opacity-50", "pointer-events-none");
    btn.innerHTML = `
      <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
      <span>Memuat...</span>
    `;
  }

  try {
    const quote = await BookAPI.fetchRandomQuote();
    if (quote) {
      AppState.quotes.unshift({
        id: Date.now(),
        bookTitle: "Kutipan Inspiratif",
        author: quote.author,
        text: quote.text,
        genre: "Inspirasi"
      });
      Storage.saveQuotes(AppState.quotes);
      renderCatatanView();
      showNotification("Kutipan baru berhasil diambil via API!");
    }
  } catch (err) {
    console.error("Gagal mengambil quote dari API:", err);
    showNotification("Gagal mengambil kutipan dari API, silakan coba lagi.");
  } finally {
    if (btn) {
      btn.classList.remove("opacity-50", "pointer-events-none");
      btn.innerHTML = `
        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span>Kutipan API Acak</span>
      `;
    }
  }
};

window.deleteQuote = function(quoteId) {
  if (confirm("Hapus kutipan ini?")) {
    AppState.quotes = AppState.quotes.filter(q => q.id !== quoteId);
    Storage.saveQuotes(AppState.quotes);
    renderCatatanView();
  }
};

/**
 * =========================================================================
 * 5. TAMBAH BUKU VIEW RENDERING (Image 1)
 * =========================================================================
 */
function renderTambahView() {
  const currentCover = DOM.previewCard.getAttribute("data-cover-url");
  if (!currentCover) {
    const selectedGenre = DOM.selectBookGenre ? DOM.selectBookGenre.value : "Fiksi";
    const gradientClass = GENRE_GRADIENTS[selectedGenre] || "from-[#1b436c] to-[#0e2136]";
    DOM.previewCard.className = `w-48 h-72 rounded-xl shadow-xl p-5 flex flex-col justify-between text-white transition-all duration-300 bg-gradient-to-br ${gradientClass} relative overflow-hidden`;
    DOM.previewGenre.textContent = selectedGenre.toUpperCase();
    DOM.previewTitle.textContent = "Judul Buku";
    DOM.previewAuthor.textContent = "Penulis";
    if (DOM.previewCoverImg) {
      DOM.previewCoverImg.src = "";
      DOM.previewCoverImg.classList.add("hidden");
    }
    const img = DOM.previewCard.querySelector(".live-cover-img");
    if (img) img.remove();
  }
}

/**
 * =========================================================================
 * 6. DETAIL BUKU MODAL (matching `#/buku/:id` in Screenshot 3)
 * =========================================================================
 */
function openBookDetailModal(bookId) {
  const book = AppState.books.find(b => b.id === bookId);
  if (!book) {
    window.location.hash = "#/koleksi";
    return;
  }

  AppState.selectedBookId = bookId;
  const gradientClass = book.coverColor || GENRE_GRADIENTS[book.genre] || "from-blue-700 to-blue-900";
  const percent = Math.min(Math.round((book.currentPage / book.totalPages) * 100), 100);

  DOM.modalDetailContent.innerHTML = `
    <div class="p-6 sm:p-8">
      
      <!-- Close button -->
      <button onclick="window.location.hash='#/koleksi'"
        class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <div class="flex flex-col md:flex-row gap-6 sm:gap-8">
        
        <!-- Book Cover Left -->
        <div class="w-full md:w-52 aspect-[2/3] rounded-2xl bg-gradient-to-br ${gradientClass} p-5 text-white flex flex-col justify-between shadow-xl flex-shrink-0 relative overflow-hidden">
          ${book.coverUrl ? `
            <img src="${book.coverUrl}" class="absolute inset-0 w-full h-full object-cover rounded-2xl" alt="${escapeHTML(book.title)}" onerror="this.style.display='none'" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-black/20 rounded-2xl pointer-events-none"></div>
          ` : ''}
          <span class="text-xs font-bold tracking-widest uppercase opacity-85 z-10">${book.genre}</span>
          <div class="z-10">
            <h3 class="font-serif font-bold text-xl leading-tight line-clamp-3">${escapeHTML(book.title)}</h3>
            <p class="text-xs text-white/80 mt-1 truncate">${escapeHTML(book.author)}</p>
          </div>
        </div>

        <!-- Book Info & Interaction Right -->
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-2">
            <span class="text-xs bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-semibold px-2.5 py-1 rounded-full">
              ${book.genre}
            </span>
            <span class="text-xs text-slate-400 font-medium">
              ${book.totalPages} halaman
            </span>
          </div>

          <h2 class="text-2xl sm:text-3xl font-serif font-bold text-slate-900 dark:text-white">
            ${escapeHTML(book.title)}
          </h2>
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">
            Penulis: <span class="text-slate-800 dark:text-slate-200">${escapeHTML(book.author)}</span>
          </p>

          <!-- Status & Event Handler on Select -->
          <div class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-700">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">
              Status Membaca (Select Handle Event)
            </label>
            <select id="detail-book-status"
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm font-medium focus:ring-2 focus:ring-blue-500 cursor-pointer">
              <option value="Ingin dibaca" ${book.status === "Ingin dibaca" ? "selected" : ""}>Ingin dibaca</option>
              <option value="Sedang dibaca" ${book.status === "Sedang dibaca" ? "selected" : ""}>Sedang dibaca</option>
              <option value="Selesai" ${book.status === "Selesai" ? "selected" : ""}>Selesai</option>
            </select>
          </div>

          <!-- Rating Interaktif (Bintang 1 - 5) -->
          <div class="mt-4 p-3.5 bg-slate-50 dark:bg-slate-700/40 rounded-xl border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between mb-1.5">
              <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Rating Buku:</label>
              <span class="text-xs font-bold text-amber-500">
                ${book.rating > 0 ? `${Number(book.rating).toFixed(1)} ⭐` : 'Belum dinilai'}
              </span>
            </div>
            <div class="flex items-center gap-2 text-2xl select-none">
              ${[1, 2, 3, 4, 5].map(star => `
                <button type="button" onclick="setBookRating(${book.id}, ${star})"
                  class="transition hover:scale-125 cursor-pointer ${star <= (book.rating || 0) ? 'text-amber-400' : 'text-slate-300 dark:text-slate-600 hover:text-amber-400'}"
                  title="Beri rating ${star} bintang">
                  ★
                </button>
              `).join("")}
              ${book.rating > 0 ? `
                <button type="button" onclick="setBookRating(${book.id}, 0)"
                  class="ml-3 text-[11px] text-slate-400 hover:text-rose-500 underline cursor-pointer">
                  Reset rating
                </button>
              ` : ''}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Klik bintang untuk memberi atau memperbarui rating buku.</p>
          </div>

          <!-- Progress Slider / Input -->
          <div class="mt-4">
            <div class="flex justify-between items-center text-xs font-medium text-slate-500 dark:text-slate-400 mb-2">
              <span>Halaman saat ini: <strong>${book.currentPage} / ${book.totalPages}</strong></span>
              <span>${percent}%</span>
            </div>
            <input type="range" id="detail-progress-slider" min="0" max="${book.totalPages}" value="${book.currentPage}"
              class="w-full accent-blue-600 cursor-pointer" />
            
            <div class="flex gap-2 mt-3">
              <button onclick="updateBookPage(${book.id}, ${Math.min(book.currentPage + 10, book.totalPages)})"
                class="px-3 py-1 text-xs font-medium rounded-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 transition">
                +10 hlm
              </button>
              <button onclick="updateBookPage(${book.id}, ${Math.min(book.currentPage + 25, book.totalPages)})"
                class="px-3 py-1 text-xs font-medium rounded-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 transition">
                +25 hlm
              </button>
              <button onclick="updateBookPage(${book.id}, ${book.totalPages})"
                class="px-3 py-1 text-xs font-medium rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 transition">
                Tandai Selesai
              </button>
            </div>
          </div>

          <!-- Notes -->
          <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-700">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Catatan Pribadi</label>
            <textarea id="detail-book-notes" rows="2" placeholder="Tuliskan pemikiran atau ringkasan..."
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm focus:ring-2 focus:ring-blue-500">${escapeHTML(book.notes || "")}</textarea>
            <button onclick="saveBookNotes(${book.id})"
              class="mt-2 text-xs bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-medium px-3 py-1.5 rounded-lg transition">
              Simpan Catatan
            </button>
          </div>

          <!-- Delete Action -->
          <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
            <button onclick="deleteBook(${book.id})"
              class="text-xs text-rose-600 hover:text-rose-700 font-medium flex items-center gap-1.5 transition">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              <span>Hapus Buku dari Koleksi</span>
            </button>
          </div>

        </div>

      </div>

    </div>
  `;

  // Attach event listener for the `<select id="detail-book-status">` inside modal
  const detailStatusSelect = document.getElementById("detail-book-status");
  if (detailStatusSelect) {
    detailStatusSelect.addEventListener("change", (e) => {
      const newStatus = e.target.value;
      console.log(`[Select Event in Modal] Status changed for "${book.title}": ${newStatus}`);
      book.status = newStatus;
      if (newStatus === "Selesai") {
        book.currentPage = book.totalPages;
        book.rating = book.rating || 5.0;
      } else if (newStatus === "Ingin dibaca") {
        book.currentPage = 0;
      }
      Storage.saveBooks(AppState.books);
      renderKoleksiView();
      renderBerandaView();
      renderStatistikView();
      openBookDetailModal(book.id);
      showNotification(`Status buku diperbarui menjadi "${newStatus}"`);
    });
  }

  // Attach range slider listener
  const slider = document.getElementById("detail-progress-slider");
  if (slider) {
    slider.addEventListener("input", (e) => {
      const val = parseInt(e.target.value, 10);
      book.currentPage = val;
      if (val >= book.totalPages) {
        book.status = "Selesai";
      } else if (val > 0) {
        book.status = "Sedang dibaca";
      }
      Storage.saveBooks(AppState.books);
      renderBerandaView();
      renderKoleksiView();
    });
    slider.addEventListener("change", () => {
      openBookDetailModal(book.id);
    });
  }

  DOM.modalDetail.classList.remove("hidden");
}

window.updateBookPage = function(bookId, page) {
  const book = AppState.books.find(b => b.id === bookId);
  if (!book) return;
  book.currentPage = page;
  if (book.currentPage >= book.totalPages) {
    book.status = "Selesai";
  } else if (book.currentPage > 0) {
    book.status = "Sedang dibaca";
  }
  Storage.saveBooks(AppState.books);
  renderBerandaView();
  renderKoleksiView();
  openBookDetailModal(bookId);
};

window.setBookRating = function(bookId, newRating) {
  const book = AppState.books.find(b => b.id === bookId);
  if (!book) return;
  book.rating = newRating;
  Storage.saveBooks(AppState.books);
  openBookDetailModal(book.id);
  renderKoleksiView();
  renderBerandaView();
  renderStatistikView();
  showNotification(newRating > 0 ? `Rating untuk "${book.title}" disimpan: ${newRating}.0 ⭐` : `Rating untuk "${book.title}" telah direset.`);
};

window.saveBookNotes = function(bookId) {
  const book = AppState.books.find(b => b.id === bookId);
  const notesText = document.getElementById("detail-book-notes");
  if (book && notesText) {
    book.notes = notesText.value.trim();
    Storage.saveBooks(AppState.books);
    showNotification("Catatan buku berhasil disimpan!");
  }
};

window.deleteBook = function(bookId) {
  const book = AppState.books.find(b => b.id === bookId);
  if (!book) return;

  if (confirm(`Apakah Anda yakin ingin menghapus "${book.title}" dari koleksi?`)) {
    AppState.books = AppState.books.filter(b => b.id !== bookId);
    Storage.saveBooks(AppState.books);
    showNotification(`Buku "${book.title}" telah dihapus.`);
    window.location.hash = "#/koleksi";
  }
};

/**
 * =========================================================================
 * MODALS (TARGET & QUOTE)
 * =========================================================================
 */
function closeAllModals() {
  if (DOM.modalDetail) DOM.modalDetail.classList.add("hidden");
  if (DOM.modalTarget) DOM.modalTarget.classList.add("hidden");
  if (DOM.modalQuote) DOM.modalQuote.classList.add("hidden");
}

window.openTargetModal = function() {
  DOM.inputTargetVal.value = AppState.yearlyTarget;
  DOM.modalTarget.classList.remove("hidden");
};

window.closeTargetModal = function() {
  DOM.modalTarget.classList.add("hidden");
};

window.saveYearlyTarget = function() {
  const val = parseInt(DOM.inputTargetVal.value, 10);
  if (val && val > 0) {
    AppState.yearlyTarget = val;
    Storage.setYearlyTarget(val);
    closeTargetModal();
    renderBerandaView();
    showNotification(`Target tahunan diubah menjadi ${val} buku!`);
  }
};

window.openQuoteModal = function() {
  // Populate quote book select
  DOM.selectQuoteBook.innerHTML = `
    <option value="">-- Pilih dari Koleksi Anda --</option>
    ${AppState.books.map(b => `<option value="${escapeHTML(b.title)}">${escapeHTML(b.title)} - ${escapeHTML(b.author)}</option>`).join("")}
    <option value="Lainnya">Buku Lainnya / Umum</option>
  `;
  DOM.inputQuoteText.value = "";
  DOM.inputQuoteAuthor.value = "";
  DOM.modalQuote.classList.remove("hidden");
};

window.closeQuoteModal = function() {
  DOM.modalQuote.classList.add("hidden");
};

window.saveNewQuote = function() {
  const text = DOM.inputQuoteText.value.trim();
  const author = DOM.inputQuoteAuthor.value.trim() || "Anonim";
  const bookTitle = DOM.selectQuoteBook.value || "Kutipan";

  if (!text) {
    alert("Harap masukkan teks kutipan.");
    return;
  }

  AppState.quotes.unshift({
    id: Date.now(),
    text,
    author,
    bookTitle,
    genre: "Catatan"
  });

  Storage.saveQuotes(AppState.quotes);
  closeQuoteModal();
  renderCatatanView();
  showNotification("Kutipan baru berhasil ditambahkan!");
};

/**
 * =========================================================================
 * THEME / DARK MODE
 * =========================================================================
 */
function applyDarkMode(isDark) {
  if (isDark) {
    document.documentElement.classList.add("dark");
    if (DOM.darkModeToggle) {
      DOM.darkModeToggle.innerHTML = `
        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
        </svg>
        <span>Mode terang</span>
      `;
    }
  } else {
    document.documentElement.classList.remove("dark");
    if (DOM.darkModeToggle) {
      DOM.darkModeToggle.innerHTML = `
        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        <span>Mode gelap</span>
      `;
    }
  }
}

/**
 * =========================================================================
 * UTILITY HELPERS
 * =========================================================================
 */
function showNotification(message) {
  let toast = document.getElementById("toast-notification");
  if (!toast) {
    toast = document.createElement("div");
    toast.id = "toast-notification";
    toast.className = "fixed bottom-5 right-5 z-50 bg-slate-900 text-white dark:bg-blue-600 px-5 py-3 rounded-xl shadow-2xl text-sm font-medium transition-all duration-300 transform translate-y-10 opacity-0 pointer-events-none flex items-center gap-2";
    document.body.appendChild(toast);
  }

  toast.innerHTML = `
    <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
    </svg>
    <span>${escapeHTML(message)}</span>
  `;

  toast.classList.remove("translate-y-10", "opacity-0", "pointer-events-none");
  toast.classList.add("translate-y-0", "opacity-100");

  setTimeout(() => {
    toast.classList.add("translate-y-10", "opacity-0", "pointer-events-none");
    toast.classList.remove("translate-y-0", "opacity-100");
  }, 3200);
}

function escapeHTML(str) {
  if (!str) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
