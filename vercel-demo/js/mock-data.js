/**
 * PHStudio — Vercel Static Demo — Seed Data
 * Mirrors database/seed.sql so the live demo behaves like the real app.
 * All names, clients, and images are FICTIONAL for portfolio purposes.
 * Portfolio Demo — Not a real photography booking service.
 */
const PHSTUDIO_SEED = {
  users: [
    { id: 1, role: 'admin', full_name: 'Isabela Cruz', email: 'admin@phstudio.demo', phone: '+63 917 000 0001', password: 'Demo@1234' },
    { id: 2, role: 'photographer', full_name: 'Marco Villanueva', email: 'marco@phstudio.demo', phone: '+63 917 000 0002', password: 'Demo@1234' },
    { id: 3, role: 'client', full_name: 'Juan Dela Cruz', email: 'juan.delacruz@example.com', phone: '+63 917 111 2222', password: 'Demo@1234' },
    { id: 4, role: 'client', full_name: 'Maria Santos', email: 'maria.santos@example.com', phone: '+63 917 222 3333', password: 'Demo@1234' },
    { id: 5, role: 'client', full_name: 'Andrea Reyes', email: 'andrea.reyes@example.com', phone: '+63 917 333 4444', password: 'Demo@1234' },
    { id: 6, role: 'client', full_name: 'Paolo Ramirez', email: 'paolo.ramirez@example.com', phone: '+63 917 444 5555', password: 'Demo@1234' },
  ],

  categories: [
    { id: 1, name: 'Weddings', slug: 'weddings', cover: 'https://images.unsplash.com/photo-1519741497674-611481863552?w=900' },
    { id: 2, name: 'Portraits', slug: 'portraits', cover: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=900' },
    { id: 3, name: 'Events', slug: 'events', cover: 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=900' },
    { id: 4, name: 'Graduation', slug: 'graduation', cover: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=900' },
    { id: 5, name: 'Prenatal', slug: 'prenatal', cover: 'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=900' },
    { id: 6, name: 'Corporate', slug: 'corporate', cover: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=900' },
  ],

  portfolio: [
    { id: 1, category: 'weddings', title: 'Golden Hour Vows', img: 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1200', featured: true },
    { id: 2, category: 'weddings', title: 'First Dance', img: 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=1200', featured: true },
    { id: 3, category: 'weddings', title: 'The Bouquet', img: 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=1200', featured: false },
    { id: 4, category: 'weddings', title: 'Ring Exchange', img: 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=1200', featured: false },
    { id: 5, category: 'portraits', title: 'Studio Portrait I', img: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=1200', featured: true },
    { id: 6, category: 'portraits', title: 'Natural Light Portrait', img: 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?w=1200', featured: false },
    { id: 7, category: 'portraits', title: 'Black and White Series', img: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=1200', featured: false },
    { id: 8, category: 'events', title: 'Birthday Celebration', img: 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=1200', featured: true },
    { id: 9, category: 'events', title: 'Corporate Gala', img: 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1200', featured: false },
    { id: 10, category: 'graduation', title: 'Cap Toss', img: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200', featured: true },
    { id: 11, category: 'graduation', title: 'Proud Graduate', img: 'https://images.unsplash.com/photo-1627556592933-ffe99c1cd9eb?w=1200', featured: false },
    { id: 12, category: 'prenatal', title: 'Maternity Glow', img: 'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=1200', featured: true },
    { id: 13, category: 'prenatal', title: 'Soft Silhouette', img: 'https://images.unsplash.com/photo-1544126592-807ade215a0b?w=1200', featured: false },
    { id: 14, category: 'corporate', title: 'Executive Headshot', img: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=1200', featured: true },
    { id: 15, category: 'corporate', title: 'Team Branding Shoot', img: 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=1200', featured: false },
  ],

  packages: [
    { id: 1, category: 'weddings', name: 'Wedding Essentials', price: 45000, duration: 6, popular: false, items: ['1 Photographer','6 hours coverage','200+ edited photos','Online private gallery','USB drive'] },
    { id: 2, category: 'weddings', name: 'Wedding Signature', price: 78000, duration: 10, popular: true, items: ['2 Photographers','10 hours coverage','500+ edited photos','Same-day teaser reel','Premium photo album'] },
    { id: 3, category: 'portraits', name: 'Portrait Session', price: 6500, duration: 1, popular: false, items: ['1 hour session','1 location','30 edited photos','Online gallery'] },
    { id: 4, category: 'portraits', name: 'Portrait Deluxe', price: 12000, duration: 2, popular: true, items: ['2 hour session','2 outfit changes','60 edited photos','5 printed photos'] },
    { id: 5, category: 'events', name: 'Event Coverage — Half Day', price: 15000, duration: 4, popular: false, items: ['1 Photographer','4 hours coverage','150 edited photos','Online gallery'] },
    { id: 6, category: 'events', name: 'Event Coverage — Full Day', price: 28000, duration: 8, popular: true, items: ['1-2 Photographers','8 hours coverage','300 edited photos','Highlight reel'] },
    { id: 7, category: 'graduation', name: 'Graduation Package', price: 5500, duration: 1, popular: false, items: ['1 hour session','2 campus locations','40 edited photos'] },
    { id: 8, category: 'prenatal', name: 'Prenatal Glow', price: 8500, duration: 1.5, popular: false, items: ['90 minute session','45 edited photos','Partner & family shots'] },
    { id: 9, category: 'corporate', name: 'Corporate Headshots', price: 3500, duration: 0.5, popular: false, items: ['30 min per person','5 edited photos each','48-hour turnaround'] },
    { id: 10, category: 'corporate', name: 'Corporate Branding Day', price: 32000, duration: 8, popular: true, items: ['1-2 Photographers','8 hours coverage','200+ edited photos','Commercial license'] },
  ],

  testimonials: [
    { id: 1, name: 'Juan & Maria Dela Cruz', category: 'weddings', rating: 5, quote: 'Marco captured our wedding day exactly as we felt it — joyful, warm, and completely us. We cried looking at the gallery.' },
    { id: 2, name: 'Andrea Reyes', category: 'prenatal', rating: 5, quote: 'The most comfortable I have ever felt in front of a camera. The maternity photos are pure art.' },
    { id: 3, name: 'Paolo Ramirez', category: 'events', rating: 5, quote: 'Professional, punctual, and the turnaround time was incredible. Our company event has never looked this good.' },
    { id: 4, name: 'Camille Torres', category: 'portraits', rating: 4, quote: 'Loved my portrait session! Great direction and the editing style is so clean and timeless.' },
    { id: 5, name: 'Rafael Uy', category: 'corporate', rating: 5, quote: 'PHStudio handled headshots for our entire 40-person team in one afternoon without a hitch.' },
  ],

  faqs: [
    { q: 'How do I book a session?', a: 'Create a free client account, browse our packages, then submit a booking request with your preferred date and event details. You will receive a confirmation once approved.' },
    { q: 'What is your rescheduling policy?', a: 'You may request a reschedule up to 5 days before your session at no extra cost, subject to availability.' },
    { q: 'How long until I receive my photos?', a: 'Standard turnaround is 2-3 weeks for full sessions and 48 hours for headshot sessions.' },
    { q: 'Can I download my photos?', a: 'Yes — most galleries allow full-resolution downloads directly from your private gallery page.' },
    { q: 'Do you require a deposit?', a: 'A 30-50% deposit is required to confirm most bookings. The balance is due on or before the session date.' },
    { q: 'Can I share my gallery with family?', a: 'Absolutely. Every private gallery has a secure share link you can send to anyone you would like to view your photos.' },
  ],

  // Availability windows for the next few weeks (photographer id 2)
  availability: [
    { date_offset: 3, start: '09:00', end: '17:00' },
    { date_offset: 5, start: '09:00', end: '17:00' },
    { date_offset: 7, start: '10:00', end: '18:00' },
    { date_offset: 12, start: '09:00', end: '17:00' },
    { date_offset: 15, start: '09:00', end: '17:00' },
    { date_offset: 20, start: '09:00', end: '19:00' },
  ],

  bookings: [
    { id: 1, client_id: 3, package_id: 2, date_offset: 20, start: '09:00', location: 'Tagaytay Wedding Garden', details: "Juan & Maria wedding — outdoor garden ceremony, 120 guests.", status: 'confirmed', notes: 'Bring drone for aerial shots. Client requested candid style over posed.' },
    { id: 2, client_id: 4, package_id: 4, date_offset: 5, start: '14:00', location: 'PHStudio — Studio A', details: 'Anniversary portrait session for Maria and family.', status: 'pending', notes: '' },
    { id: 3, client_id: 5, package_id: 8, date_offset: -10, start: '10:00', location: "Andrea's Residence, Quezon City", details: 'Maternity session, 7 months along, soft natural light preferred.', status: 'completed', notes: 'Client loved the window-light series. Delivered gallery on time.' },
    { id: 4, client_id: 6, package_id: 6, date_offset: -30, start: '10:00', location: 'Makati Corporate Events Hall', details: 'Company anniversary event, ~300 attendees.', status: 'completed', notes: 'Great lighting conditions.' },
    { id: 5, client_id: 3, package_id: 3, date_offset: 1, start: '09:00', location: 'PHStudio — Studio B', details: 'Quick solo portrait refresh.', status: 'in_progress', notes: 'Session currently underway.' },
  ],

  invoices: [
    { id: 1, booking_id: 1, invoice_number: 'INV-2026-0001', total: 78000, paid: 30000, status: 'partial' },
    { id: 2, booking_id: 2, invoice_number: 'INV-2026-0002', total: 12000, paid: 0, status: 'unpaid' },
    { id: 3, booking_id: 3, invoice_number: 'INV-2026-0003', total: 8500, paid: 8500, status: 'paid' },
    { id: 4, booking_id: 4, invoice_number: 'INV-2026-0004', total: 28000, paid: 28000, status: 'paid' },
  ],

  galleries: [
    {
      id: 1, client_id: 6, title: 'Paolo Ramirez — Corporate Event Highlights',
      cover: 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=1200',
      notes: 'Hi Paolo — here are the highlights from the anniversary event. Let me know your favorites for the printed recap!',
      allow_download: true, share_token: 'gal_corp_9f2a1c',
      photos: [
        { id: 1, img: 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=1400', caption: 'Opening remarks' },
        { id: 2, img: 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1400', caption: 'Leadership toast' },
        { id: 3, img: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=1400', caption: 'Team portrait' },
      ],
    },
    {
      id: 2, client_id: 5, title: 'Andrea Reyes — Prenatal Session',
      cover: 'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=1200',
      notes: 'Andrea, congratulations again! These turned out beautifully in the afternoon light.',
      allow_download: true, share_token: 'gal_prenat_7b3d20',
      photos: [
        { id: 4, img: 'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=1400', caption: 'Window light series' },
        { id: 5, img: 'https://images.unsplash.com/photo-1544126592-807ade215a0b?w=1400', caption: 'Silhouette' },
        { id: 6, img: 'https://images.unsplash.com/photo-1519689680058-324335c77eba?w=1400', caption: 'Hands on belly' },
      ],
    },
    {
      id: 3, client_id: 3, title: 'Juan & Maria — Wedding Teasers',
      cover: 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1200',
      notes: 'A sneak peek before the full wedding gallery is ready — full set coming in 3 weeks!',
      allow_download: false, share_token: 'gal_wed_44ac10',
      photos: [
        { id: 7, img: 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1400', caption: 'Golden hour vows' },
        { id: 8, img: 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=1400', caption: 'First dance' },
        { id: 9, img: 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=1400', caption: 'The bouquet' },
        { id: 10, img: 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=1400', caption: 'Ring exchange' },
      ],
    },
  ],

  favorites: [ { photo_id: 4, client_id: 5 }, { photo_id: 7, client_id: 3 } ],
};
