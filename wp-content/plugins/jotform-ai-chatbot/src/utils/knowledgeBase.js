// Search Method Summary
//
//     Type: Keyword-based scoring (regex word-boundary match)
//     Search Fields:
//         title (double weighted)
//         data (article body; falls back to meta.summary if empty)

//     Process:
//         Normalize text (lowercase, remove accents/diacritics, trim spaces).
//         Tokenize query (remove English stopwords, min length 2).
//         For each query term:
//             Build a regex \bterm\w*\b to match whole words or suffixes (e.g., store → stores).
//             Count a hit if it matches title or body.
//             Count a titleHit if it matches title specifically.
//         Score = (hits / total query terms) + (titleHits / total query terms × 0.2)
//         Sort by score, hits, titleHits, then title alphabetically.
//     Filters: Applies selectedMaterial and materialStatusFilter.
//     Empty Query: Returns all materials sorted by title.
//     Stopwords: Common filler words (the, and, of, etc.) are ignored.

// ✅ Pros:
// Fast, predictable, plural/flection tolerant, no heavy dependencies.

// ⚠️ Cons:
// No synonyms (unless you add a map), no typo tolerance, no semantic understanding.

const STOP = new Set([
  'a', 'an', 'the', 'and', 'but', 'or', 'as', 'at', 'by', 'for', 'in', 'of', 'on', 'to',
  'with', 'up', 'down', 'is', 'it', 'he', 'she', 'they', 'we', 'you', 'that', 'this',
  'those', 'these', 'be', 'am', 'are', 'was', 'were'
]);

const escapeRegExp = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
const normalize = (s) => (s || '')
  .toLowerCase()
  .normalize('NFKD') // handle accents nicely
  .replace(/[\u0300-\u036f]/g, '')
  .replace(/\s+/g, ' ');

export const tokenizeQuery = (q) => normalize(q)
  .replace(/[!"#$%&'()*+,\-./:;<=>?@[\\\]^_`{|}~]/g, ' ')
  .split(' ')
  .filter(Boolean)
  .filter(t => !STOP.has(t))
  .filter(t => t.length >= 2);

// word-boundary matcher (plural/flection tolerant via \w*)
const makeTermRegex = (term) => new RegExp(`\\b${escapeRegExp(term)}\\w*\\b`, 'i');

// Build weighted searchable fields
// Build weighted searchable fields (TITLE + DATA)
export const buildSearchText = (m) => {
  const title = normalize(m.title);
  // Use `data` field instead of body, fall back to summary if needed
  const body = normalize(m.data || m?.meta?.summary || '');

  // Weight: title*2 + body
  return {
    title,
    combined: `${title} ${title} ${body}`
  };
};

export const scoreItem = (qTerms, searchText) => {
  if (!qTerms.length) return { hits: 0, titleHits: 0, score: 0 };

  let hits = 0;
  let titleHits = 0;
  qTerms.forEach(term => {
    const rx = makeTermRegex(term);
    const termHit = rx.test(searchText.combined);
    if (termHit) {
      hits += 1;
      if (rx.test(searchText.title)) {
        titleHits += 1;
      }
    }
  });

  // Score: coverage + small title bonus
  const coverage = hits / qTerms.length; // [0..1]
  const titleBonus = qTerms.length ? (titleHits / qTerms.length) * 0.2 : 0;
  const score = coverage + titleBonus;
  return { hits, titleHits, score };
};
