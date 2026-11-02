/**
 * BookTrack - Initial Data & Storage Manager
 */

// Initial Seed Data matching the screenshots
const INITIAL_BOOKS = [
  {
    id: 1,
    title: "Sapiens",
    author: "Yuval Noah Harari",
    genre: "Non-Fiksi",
    totalPages: 498,
    currentPage: 191,
    status: "Sedang dibaca", // 'Ingin dibaca' | 'Sedang dibaca' | 'Selesai'
    rating: 0,
    coverColor: "from-[#7b3f27] to-[#3a1d13]", // Warm sepia / brown
    coverUrl: "",
    quotes: [
      "Kita belajar biologi untuk memahami tubuh kita, kita belajar sejarah untuk membebaskan diri kita dari masa lalu."
    ],
    notes: "Buku yang sangat membuka wawasan mengenai evolusi peradaban manusia.",
    createdAt: "2024-01-30T10:00:00.000Z",
    lastReadAt: "2024-01-30T09:00:00.000Z"
  },
  {
    id: 2,
    title: "Filosofi Teras",
    author: "Henry Manampiring",
    genre: "Self-Improvement",
    totalPages: 320,
    currentPage: 0,
    status: "Ingin dibaca",
    rating: 0,
    coverColor: "from-[#1b436c] to-[#0e2136]", // Deep ocean navy
    coverUrl: "",
    quotes: [],
    notes: "Rekomendasi teman untuk memahami Stoisisme dalam kehidupan sehari-hari.",
    createdAt: "2024-01-28T10:00:00.000Z"
  },
  {
    id: 3,
    title: "Bumi Manusia",
    author: "Pramoedya A. Toer",
    genre: "Fiksi",
    totalPages: 535,
    currentPage: 535,
    status: "Selesai",
    rating: 5.0,
    coverColor: "from-[#17723e] to-[#0b331c]", // Emerald forest green
    coverUrl: "",
    quotes: [
      "Berterimakasihlah pada segala yang memberi kehidupan.",
      "Seorang terpelajar harus sudah berbuat adil sejak dalam pikiran apalagi dalam perbuatan."
    ],
    notes: "Mahakarya sastra Indonesia. Kisah Minke dan Annelies yang luar biasa.",
    createdAt: "2024-01-26T10:00:00.000Z"
  },
  {
    id: 4,
    title: "Clean Code",
    author: "Robert C. Martin",
    genre: "Teknologi",
    totalPages: 464,
    currentPage: 96,
    status: "Sedang dibaca",
    rating: 0,
    coverColor: "from-[#4b791e] to-[#22390c]", // Olive green
    coverUrl: "",
    quotes: [
      "Any fool can write code that a computer can understand. Good programmers write code that humans can understand."
    ],
    notes: "Prinsip-prinsip penulisan kode bersih yang wajib dibaca setiap software engineer.",
    createdAt: "2024-01-24T10:00:00.000Z",
    lastReadAt: "2024-01-30T12:00:00.000Z"
  },
  {
    id: 5,
    title: "Laskar Pelangi",
    author: "Andrea Hirata",
    genre: "Fiksi",
    totalPages: 529,
    currentPage: 210,
    status: "Sedang dibaca",
    rating: 0,
    coverColor: "from-[#6b165b] to-[#2f0727]", // Magenta purple
    coverUrl: "",
    quotes: [
      "Bermimpilah, karena Tuhan akan memeluk mimpi-mimpi itu."
    ],
    notes: "Kisah inspiratif anak-anak Belitong memperjuangkan pendidikan.",
    createdAt: "2024-01-22T10:00:00.000Z",
    lastReadAt: "2024-01-30T15:00:00.000Z"
  },
  {
    id: 6,
    title: "Atomic Habits",
    author: "James Clear",
    genre: "Self-Improvement",
    totalPages: 320,
    currentPage: 320,
    status: "Selesai",
    rating: 5.0,
    coverColor: "from-[#20693a] to-[#0f3a1e]", // Green / teal
    coverUrl: "",
    quotes: [
      "Kamu tidak naik ke level tujuanmu, kamu jatuh ke level sistemmu."
    ],
    notes: "Buku panduan praktis untuk membangun kebiasaan baik dan menghilangkan kebiasaan buruk.",
    createdAt: "2024-01-20T10:00:00.000Z"
  }
];

// Initial Quotes matching screenshot 2
const INITIAL_QUOTES = [
  {
    id: 1,
    bookTitle: "Atomic Habits",
    author: "James Clear",
    text: "Kamu tidak naik ke level tujuanmu, kamu jatuh ke level sistemmu.",
    genre: "Self-Improvement"
  },
  {
    id: 2,
    bookTitle: "Bumi Manusia",
    author: "Pramoedya Ananta Toer",
    text: "Seorang terpelajar harus sudah berbuat adil sejak dalam pikiran apalagi dalam perbuatan.",
    genre: "Fiksi"
  },
  {
    id: 3,
    bookTitle: "Laskar Pelangi",
    author: "Andrea Hirata",
    text: "Bermimpilah, karena Tuhan akan memeluk mimpi-mimpi itu.",
    genre: "Fiksi"
  },
  {
    id: 4,
    bookTitle: "Clean Code",
    author: "Robert C. Martin",
    text: "Even bad code can function. But if code isn't clean, it can bring a development organization to its knees.",
    genre: "Teknologi"
  }
];

// Genre gradient mapping
const GENRE_GRADIENTS = {
  "Non-Fiksi": "from-[#7b3f27] to-[#3a1d13]",
  "Self-Improvement": "from-[#1b436c] to-[#0e2136]",
  "Fiksi": "from-[#17723e] to-[#0b331c]",
  "Teknologi": "from-[#4b791e] to-[#22390c]",
  "Bisnis": "from-[#b45309] to-[#78350f]",
  "Filosofi": "from-[#4338ca] to-[#1e1b4b]",
  "Sains": "from-[#0e7490] to-[#164e63]",
  "Sejarah": "from-[#854d0e] to-[#422006]",
  "Biografi": "from-[#475569] to-[#0f172a]"
};

// Storage keys
const STORAGE_KEYS = {
  BOOKS: "booktrack_books",
  QUOTES: "booktrack_quotes",
  YEARLY_TARGET: "booktrack_yearly_target",
  DARK_MODE: "booktrack_dark_mode"
};

// Storage helper functions
const Storage = {
  getBooks() {
    try {
      if (typeof localStorage === "undefined") return INITIAL_BOOKS;
      const data = localStorage.getItem(STORAGE_KEYS.BOOKS);
      if (!data) {
        localStorage.setItem(STORAGE_KEYS.BOOKS, JSON.stringify(INITIAL_BOOKS));
        return INITIAL_BOOKS;
      }
      return JSON.parse(data);
    } catch (e) {
      console.error("Error reading books from storage:", e);
      return INITIAL_BOOKS;
    }
  },

  saveBooks(books) {
    try {
      if (typeof localStorage === "undefined") return;
      localStorage.setItem(STORAGE_KEYS.BOOKS, JSON.stringify(books));
    } catch (e) {
      console.error("Error saving books to storage:", e);
    }
  },

  getQuotes() {
    try {
      if (typeof localStorage === "undefined") return INITIAL_QUOTES;
      const data = localStorage.getItem(STORAGE_KEYS.QUOTES);
      if (!data) {
        localStorage.setItem(STORAGE_KEYS.QUOTES, JSON.stringify(INITIAL_QUOTES));
        return INITIAL_QUOTES;
      }
      return JSON.parse(data);
    } catch (e) {
      console.error("Error reading quotes from storage:", e);
      return INITIAL_QUOTES;
    }
  },

  saveQuotes(quotes) {
    try {
      if (typeof localStorage === "undefined") return;
      localStorage.setItem(STORAGE_KEYS.QUOTES, JSON.stringify(quotes));
    } catch (e) {
      console.error("Error saving quotes to storage:", e);
    }
  },

  getYearlyTarget() {
    try {
      if (typeof localStorage === "undefined") return 12;
      const target = localStorage.getItem(STORAGE_KEYS.YEARLY_TARGET);
      return target ? parseInt(target, 10) : 12;
    } catch (e) {
      return 12;
    }
  },

  setYearlyTarget(target) {
    try {
      if (typeof localStorage === "undefined") return;
      localStorage.setItem(STORAGE_KEYS.YEARLY_TARGET, target.toString());
    } catch (e) {
      console.error("Error saving yearly target:", e);
    }
  },

  getDarkMode() {
    try {
      if (typeof localStorage === "undefined") return false;
      return localStorage.getItem(STORAGE_KEYS.DARK_MODE) === "true";
    } catch (e) {
      return false;
    }
  },

  setDarkMode(isDark) {
    try {
      if (typeof localStorage === "undefined") return;
      localStorage.setItem(STORAGE_KEYS.DARK_MODE, isDark ? "true" : "false");
    } catch (e) {
      console.error("Error saving dark mode:", e);
    }
  },

  resetToDefault() {
    if (typeof localStorage === "undefined") return;
    localStorage.setItem(STORAGE_KEYS.BOOKS, JSON.stringify(INITIAL_BOOKS));
    localStorage.setItem(STORAGE_KEYS.QUOTES, JSON.stringify(INITIAL_QUOTES));
    localStorage.setItem(STORAGE_KEYS.YEARLY_TARGET, "12");
  }
};

if (typeof window !== "undefined") {
  window.INITIAL_BOOKS = INITIAL_BOOKS;
  window.INITIAL_QUOTES = INITIAL_QUOTES;
  window.GENRE_GRADIENTS = GENRE_GRADIENTS;
  window.STORAGE_KEYS = STORAGE_KEYS;
  window.Storage = Storage;
}
