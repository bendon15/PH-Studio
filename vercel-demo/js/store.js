/**
 * PHStudio — Vercel Static Demo — Data Store
 * A tiny localStorage-backed "database" that simulates the real
 * PHP + MySQL backend (see /backend in the GitHub repo) so this demo
 * can run entirely client-side on Vercel.
 * Portfolio Demo — Not a real photography booking service.
 */
const PHStore = (() => {
  const KEY = 'phstudio_demo_v1';

  function today(offsetDays = 0) {
    const d = new Date();
    d.setDate(d.getDate() + offsetDays);
    return d.toISOString().slice(0, 10);
  }

  function seedDatabase() {
    const bookings = PHSTUDIO_SEED.bookings.map(b => ({
      id: b.id,
      client_id: b.client_id,
      photographer_id: 2,
      package_id: b.package_id,
      session_date: today(b.date_offset),
      start_time: b.start,
      location: b.location,
      details: b.details,
      status: b.status,
      notes: b.notes,
      references: [],
      messages: [],
      created_at: new Date().toISOString(),
    }));

    const availability = PHSTUDIO_SEED.availability.map((a, i) => ({
      id: i + 1,
      photographer_id: 2,
      date: today(a.date_offset),
      start: a.start,
      end: a.end,
      blocked: false,
    }));

    const invoices = PHSTUDIO_SEED.invoices.map(inv => ({ ...inv }));
    const galleries = JSON.parse(JSON.stringify(PHSTUDIO_SEED.galleries));
    const favorites = JSON.parse(JSON.stringify(PHSTUDIO_SEED.favorites));
    const testimonials = JSON.parse(JSON.stringify(PHSTUDIO_SEED.testimonials));

    return {
      users: JSON.parse(JSON.stringify(PHSTUDIO_SEED.users)),
      bookings,
      availability,
      invoices,
      galleries,
      favorites,
      testimonials,
      contactMessages: [],
      nextIds: { booking: 6, invoice: 5, gallery: 4, photo: 11, message: 1, user: 7 },
      currentUserId: null,
    };
  }

  function load() {
    const raw = localStorage.getItem(KEY);
    if (!raw) {
      const fresh = seedDatabase();
      localStorage.setItem(KEY, JSON.stringify(fresh));
      return fresh;
    }
    try {
      return JSON.parse(raw);
    } catch (e) {
      const fresh = seedDatabase();
      localStorage.setItem(KEY, JSON.stringify(fresh));
      return fresh;
    }
  }

  function save(db) {
    localStorage.setItem(KEY, JSON.stringify(db));
  }

  function reset() {
    localStorage.removeItem(KEY);
    return load();
  }

  return { load, save, reset, today };
})();
