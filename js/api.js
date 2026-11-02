/**
 * BookTrack - API Integration Module
 * Accurately searches Indonesian & International books worldwide.
 * Uses smart title search, keyword relevance filtering, Open Library API,
 * Google Books API, and an offline database of popular Indonesian bestsellers.
 */

// Popular Indonesian & International Bestsellers for instant accurate matching
const CURATED_BOOK_DATABASE = [
  {
    title: "Air dan Api (Tanah, Air, Api, dan Angin)",
    authors: "Hatta Azad Khan",
    genre: "Fiksi",
    pageCount: 168,
    coverUrl: "",
    description: "Kisah drama sastra yang mengeksplorasi elemen alam dan kehidupan manusia."
  },
  {
    title: "Laut Bercerita",
    authors: "Leila S. Chudori",
    genre: "Fiksi",
    pageCount: 379,
    coverUrl: "https://covers.openlibrary.org/b/id/10543666-M.jpg",
    description: "Kisah keluarga dan persahabatan para aktivis mahasiswa di era 1998."
  },
  {
    title: "Cantik Itu Luka",
    authors: "Eka Kurniawan",
    genre: "Fiksi",
    pageCount: 508,
    coverUrl: "https://covers.openlibrary.org/b/id/8315183-M.jpg",
    description: "Mahakarya realisme magis Indonesia yang telah diterjemahkan ke lebih dari 30 bahasa."
  },
  {
    title: "Hujan",
    authors: "Tere Liye",
    genre: "Fiksi",
    pageCount: 318,
    coverUrl: "",
    description: "Novel fiksi ilmiah tentang persahabatan, cinta, dan melupakan di masa depan bumi."
  },
  {
    title: "Pulang",
    authors: "Tere Liye",
    genre: "Fiksi",
    pageCount: 400,
    coverUrl: "",
    description: "Kisah tentang perjalanan hidup seorang anak bernama Bujang di dunia shadow economy."
  },
  {
    title: "Bumi",
    authors: "Tere Liye",
    genre: "Fiksi",
    pageCount: 440,
    coverUrl: "",
    description: "Awal petualangan tiga remaja di dunia paralel Klan Bulan, Matahari, dan Bintang."
  },
  {
    title: "Janji",
    authors: "Tere Liye",
    genre: "Fiksi",
    pageCount: 488,
    coverUrl: "",
    description: "Kisah tentang perjalanan tiga santri mencari Bahar untuk menunaikan janji masa lalu."
  },
  {
    title: "Perahu Kertas",
    authors: "Dee Lestari",
    genre: "Fiksi",
    pageCount: 444,
    coverUrl: "",
    description: "Kisah Kugy dan Keenan tentang mimpi, idealisme, dan perjalanan cinta sejati."
  },
  {
    title: "Supernova: Ksatria, Puteri, dan Bintang Jatuh",
    authors: "Dee Lestari",
    genre: "Fiksi",
    pageCount: 325,
    coverUrl: "",
    description: "Novel debut legendaris Dee Lestari yang memadukan sains, spiritualitas, dan romansa."
  },
  {
    title: "Negeri 5 Menara",
    authors: "A. Fuadi",
    genre: "Fiksi",
    pageCount: 423,
    coverUrl: "",
    description: "Kisah inspiratif anak-anak rantau di Pondok Madani dengan semboyan Man Jadda Wajada."
  },
  {
    title: "Dilan 1990",
    authors: "Pidi Baiq",
    genre: "Fiksi",
    pageCount: 332,
    coverUrl: "",
    description: "Kisah cinta manis dan jenaka antara Dilan dan Milea di Bandung era 1990-an."
  },
  {
    title: "Filosofi Teras",
    authors: "Henry Manampiring",
    genre: "Self-Improvement",
    pageCount: 320,
    coverUrl: "",
    description: "Penerapan filsafat Stoisisme kuno untuk mental tangguh di era modern."
  },
  {
    title: "Sebuah Seni untuk Bersikap Bodo Amat",
    authors: "Mark Manson",
    genre: "Self-Improvement",
    pageCount: 256,
    coverUrl: "https://covers.openlibrary.org/b/id/8231856-M.jpg",
    description: "Pendekatan realistis untuk meraih kehidupan yang lebih tenang dan bermakna."
  },
  {
    title: "Atomic Habits",
    authors: "James Clear",
    genre: "Self-Improvement",
    pageCount: 320,
    coverUrl: "https://covers.openlibrary.org/b/id/12843460-M.jpg",
    description: "Perubahan kecil yang memberikan hasil luar biasa dalam membangun kebiasaan baik."
  },
  {
    title: "Sapiens: Riwayat Singkat Umat Manusia",
    authors: "Yuval Noah Harari",
    genre: "Non-Fiksi",
    pageCount: 498,
    coverUrl: "https://covers.openlibrary.org/b/id/8231990-M.jpg",
    description: "Menelusuri sejarah evolusi manusia dari zaman batu hingga abad ke-21."
  },
  {
    title: "Gadis Kretek",
    authors: "Ratih Kumala",
    genre: "Fiksi",
    pageCount: 274,
    coverUrl: "",
    description: "Kisah keluarga dan sejarah perkembangan industri kretek di Indonesia."
  },
  {
    title: "Orang-Orang Biasa",
    authors: "Andrea Hirata",
    genre: "Fiksi",
    pageCount: 262,
    coverUrl: "",
    description: "Kisah sekelompok sahabat biasa di Belitong yang merencanakan kejahatan unik demi pendidikan."
  },
  {
    title: "Tenggelamnya Kapal Van der Wijck",
    authors: "Buya Hamka",
    genre: "Fiksi",
    pageCount: 224,
    coverUrl: "",
    description: "Kisah cinta tragis antara Zainuddin dan Hayati terhalang adat Minangkabau."
  },
  {
    title: "Api Sejarah",
    authors: "Ahmad Mansur Suryanegara",
    genre: "Sejarah",
    pageCount: 584,
    coverUrl: "",
    description: "Karya sejarah tentang peran ulama dan santri dalam menegakkan NKRI."
  },
  {
    title: "Clean Code",
    authors: "Robert C. Martin",
    genre: "Teknologi",
    pageCount: 464,
    coverUrl: "https://covers.openlibrary.org/b/id/11181285-M.jpg",
    description: "Panduan praktis menulis kode yang bersih, rapi, dan mudah dirawat."
  }
];

const BookAPI = {
  // Category mapping helper to map categories & title to app genres
  mapCategoryToGenre(categories, title = "") {
    const catStr = (Array.isArray(categories) ? categories.join(" ") : String(categories || "")).toLowerCase();
    const titleLower = String(title || "").toLowerCase();
    const combined = `${catStr} ${titleLower}`;

    if (combined.includes("fiction") || combined.includes("novel") || combined.includes("literature") || combined.includes("fiksi") || combined.includes("cerita") || combined.includes("drama") || combined.includes("fantasy") || combined.includes("romance") || combined.includes("manga") || combined.includes("komik") || combined.includes("puisi") || combined.includes("sastra") || combined.includes("potter") || combined.includes("pelangi") || combined.includes("dilan") || combined.includes("perahu kertas") || combined.includes("hujan") || combined.includes("bumi manusia") || combined.includes("cantik itu luka")) {
      return "Fiksi";
    }
    if (combined.includes("self-help") || combined.includes("personal development") || combined.includes("psychology") || combined.includes("motivation") || combined.includes("mind") || combined.includes("habit") || combined.includes("sukses") || combined.includes("pengembangan") || combined.includes("inspirasi") || combined.includes("filosofi teras") || combined.includes("mindset") || combined.includes("bodo amat")) {
      return "Self-Improvement";
    }
    if (combined.includes("computer") || combined.includes("technology") || combined.includes("programming") || combined.includes("software") || combined.includes("engineering") || combined.includes("coding") || combined.includes("data science") || combined.includes("internet") || /\bai\b/i.test(combined) || combined.includes("artificial intelligence") || combined.includes("teknologi") || combined.includes("clean code") || combined.includes("python") || combined.includes("javascript")) {
      return "Teknologi";
    }
    if (combined.includes("business") || combined.includes("economics") || combined.includes("management") || combined.includes("finance") || combined.includes("marketing") || combined.includes("bisnis") || combined.includes("ekonomi") || combined.includes("invest") || combined.includes("rich dad") || combined.includes("uang")) {
      return "Bisnis";
    }
    if (combined.includes("philosophy") || combined.includes("filsafat") || combined.includes("ethics") || combined.includes("stoisisme") || combined.includes("logic") || combined.includes("filosofi")) {
      return "Filosofi";
    }
    if (combined.includes("science") || combined.includes("physics") || combined.includes("biology") || combined.includes("chemistry") || combined.includes("nature") || combined.includes("sains") || combined.includes("astronomy")) {
      return "Sains";
    }
    if (combined.includes("history") || combined.includes("sejarah") || combined.includes("ancient") || combined.includes("war") || combined.includes("perang") || combined.includes("kemerdekaan") || combined.includes("sapiens")) {
      return "Sejarah";
    }
    if (combined.includes("biography") || combined.includes("memoir") || combined.includes("autobiography") || combined.includes("biografi") || combined.includes("tokoh") || combined.includes("kisah hidup")) {
      return "Biografi";
    }
    return "Non-Fiksi";
  },

  /**
   * Main Search Function
   * Searches books strictly matching the query (Indonesian & Worldwide)
   * 1. Check curated Indonesian & International titles
   * 2. Query Open Library with title search
   * 3. Filter and score results to prevent irrelevant results (e.g. prevent English "Air" matching "Air dan Api")
   * @param {string} query
   * @returns {Promise<Array>}
   */
  async searchGoogleBooks(query) {
    if (!query || query.trim().length < 2) {
      return [];
    }

    // FIX: variabel ini dipakai di bawah tapi sebelumnya belum dibuat
    const trimmedQuery = query.trim();
    const queryLower = trimmedQuery.toLowerCase();

    // FIX: baris "const url = ..." dan "await fetch(url)" di sini dihapus
    // (sisa contoh tutorial, sudah ada fetch di STEP 2 di bawah)
    console.log(`[BookAPI] Searching for book: "${trimmedQuery}"`);

    const results = [];
    const seenTitles = new Set();

    // Helper to add book without duplicates
    const addResult = (b) => {
      const key = (b.title + " " + b.authors).toLowerCase().replace(/[^a-z0-9]/g, "");
      if (!seenTitles.has(key)) {
        seenTitles.add(key);
        results.push(b);
      }
    };

    // 1. STEP 1: Search in Curated Indonesian & Global Database
    const queryWords = queryLower.split(/\s+/).filter(w => w.length > 1 && !["dan", "di", "ke", "dari", "yang", "the", "and", "of", "in"].includes(w));
    
    CURATED_BOOK_DATABASE.forEach(b => {
      const titleLower = b.title.toLowerCase();
      // Exact match or contains all significant words
      const isExact = titleLower.includes(queryLower);
      const isWordsMatch = queryWords.length > 0 && queryWords.every(w => titleLower.includes(w));
      const isAnyMatch = queryWords.length > 1 && queryWords.filter(w => titleLower.includes(w)).length >= 2;

      if (isExact || isWordsMatch || isAnyMatch) {
        addResult({
          id: `curated-${b.title.replace(/\s+/g, "-")}`,
          source: "Katalog Terverifikasi",
          title: b.title,
          authors: b.authors,
          pageCount: b.pageCount,
          genre: b.genre,
          coverUrl: b.coverUrl,
          description: b.description
        });
      }
    });

    // 2. STEP 2: Query Open Library API with title search
    try {
      const olTitleUrl = `https://openlibrary.org/search.json?title=${encodeURIComponent(trimmedQuery)}&limit=12`;
      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), 5000);

      const response = await fetch(olTitleUrl, { signal: controller.signal });
      clearTimeout(timeoutId);

      if (!response.ok) {
        throw new Error("Gagal mengambil data dari server Open Library");
      }

      const data = await response.json();
      if (data.docs && data.docs.length > 0) {
        // FIX: sebelumnya "return data.docs; {" -> harus forEach
        data.docs.forEach(doc => {
          const docTitle = doc.title || "";
          const docTitleLower = docTitle.toLowerCase();

          // RELEVANCE CHECK:
          // Ensure title actually relates to the user query!
          // If user searched "air dan api", do NOT accept titles that have no relation to "api" or "air"!
          let isRelevant = false;
          if (queryWords.length === 1) {
            isRelevant = docTitleLower.includes(queryWords[0]);
          } else if (queryWords.length > 1) {
            const matchesCount = queryWords.filter(w => docTitleLower.includes(w)).length;
            // Must match at least 1 word if 2 words, or at least 2 words if >= 3 words
            isRelevant = matchesCount >= Math.min(2, queryWords.length) || docTitleLower.includes(queryLower);
          }

          if (isRelevant) {
            let coverUrl = "";
            if (doc.cover_i) {
              coverUrl = `https://covers.openlibrary.org/b/id/${doc.cover_i}-M.jpg`;
            }

            const genre = this.mapCategoryToGenre(doc.subject || [], docTitle);
            const authors = doc.author_name && doc.author_name.length ? doc.author_name.slice(0, 2).join(", ") : "Penulis Tidak Diketahui";
            const pageCount = doc.number_of_pages_median || doc.number_of_pages || 250;

            addResult({
              id: doc.key || `ol-${Date.now()}-${Math.random()}`,
              source: "Perpustakaan Dunia",
              title: docTitle,
              authors: authors,
              pageCount: pageCount,
              genre: genre,
              coverUrl: coverUrl,
              description: `Buku terbitan tahun ${doc.first_publish_year || "tidak diketahui"}.`
            });
          }
        }); // FIX: penutup forEach harus "});" bukan "};"
      }
      // FIX: kurung kurawal "}" berlebih di sini dihapus
    } catch (err) {
      console.warn("[BookAPI] Open Library title search error:", err.message);
    }

    // 3. STEP 3: If still fewer than 3 results, query Open Library general search with strict filtering
    if (results.length < 3) {
      try {
        const olGeneralUrl = `https://openlibrary.org/search.json?q=${encodeURIComponent(trimmedQuery)}&limit=10`;
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 4000);

        const response = await fetch(olGeneralUrl, { signal: controller.signal });
        clearTimeout(timeoutId);

        if (response.ok) {
          const data = await response.json();
          if (data.docs && data.docs.length > 0) {
            data.docs.forEach(doc => {
              const docTitle = doc.title || "";
              const docTitleLower = docTitle.toLowerCase();

              // Strict filtering: only add if title actually contains user query words
              const matchesCount = queryWords.filter(w => docTitleLower.includes(w)).length;
              if (matchesCount >= Math.min(1, queryWords.length)) {
                let coverUrl = "";
                if (doc.cover_i) {
                  coverUrl = `https://covers.openlibrary.org/b/id/${doc.cover_i}-M.jpg`;
                }
                const genre = this.mapCategoryToGenre(doc.subject || [], docTitle);
                const authors = doc.author_name && doc.author_name.length ? doc.author_name.slice(0, 2).join(", ") : "Penulis Tidak Diketahui";
                const pageCount = doc.number_of_pages_median || doc.number_of_pages || 250;

                addResult({
                  id: doc.key || `ol-gen-${Date.now()}-${Math.random()}`,
                  source: "Perpustakaan Dunia",
                  title: docTitle,
                  authors: authors,
                  pageCount: pageCount,
                  genre: genre,
                  coverUrl: coverUrl,
                  description: `Buku dari katalog dunia.`
                });
              }
            });
          }
        }
      } catch (e) {
        // Ignore
      }
    }

    // 4. STEP 4: Also search local saved collection
    const localBooks = Storage.getBooks();
    localBooks.forEach(b => {
      const titleLower = b.title.toLowerCase();
      const authorLower = b.author.toLowerCase();
      if (titleLower.includes(queryLower) || authorLower.includes(queryLower)) {
        addResult({
          id: `local-${b.id}`,
          source: "Koleksi Anda",
          title: b.title,
          authors: b.author,
          pageCount: b.totalPages,
          genre: b.genre,
          coverUrl: b.coverUrl,
          description: b.notes || ""
        });
      }
    });

    console.log(`[BookAPI] Returning ${results.length} relevant books for "${trimmedQuery}"`);
    return results;
  },

  /**
   * Fetch an inspirational book or reading quote from API
   */
  async fetchRandomQuote() {
    const backupQuotes = [
      { text: "Buku adalah jendela dunia.", author: "Pepatah" },
      { text: "Seseorang yang tidak membaca buku bagus tidak punya keunggulan dibanding yang tidak bisa membaca.", author: "Mark Twain" },
      { text: "Kamu tidak naik ke level tujuanmu, kamu jatuh ke level sistemmu.", author: "Atomic Habits · James Clear" },
      { text: "Membaca semua buku bagus seperti percakapan dengan orang-orang terhebat di abad-abad lampau.", author: "René Descartes" },
      { text: "Bermimpilah, karena Tuhan akan memeluk mimpi-mimpi itu.", author: "Laskar Pelangi · Andrea Hirata" },
      { text: "Seorang terpelajar harus sudah berbuat adil sejak dalam pikiran apalagi dalam perbuatan.", author: "Bumi Manusia · Pramoedya Ananta Toer" },
      { text: "Kualitas hidup kita ditentukan oleh kualitas pertanyaan yang kita ajukan dan buku yang kita baca.", author: "Tony Robbins" },
      { text: "Buku adalah sahabat paling setia dan penasihat paling bijak.", author: "Charles W. Eliot" }
    ];

    try {
      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), 4000);
      const res = await fetch("https://dummyjson.com/quotes/random", { signal: controller.signal });
      clearTimeout(timeoutId);

      if (res.ok) {
        const data = await res.json();
        return {
          text: data.quote,
          author: data.author
        };
      }
    } catch (e) {
      // Fallback
    }

    return backupQuotes[Math.floor(Math.random() * backupQuotes.length)];
  }
};

if (typeof window !== "undefined") {
  window.BookAPI = BookAPI;
  window.CURATED_BOOK_DATABASE = CURATED_BOOK_DATABASE;
}