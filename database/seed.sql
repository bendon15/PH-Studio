-- =====================================================================
-- PHStudio — Seed / Demo Data
-- All names, clients, and images are FICTIONAL for portfolio purposes.
-- Portfolio Demo — Not a real photography booking service.
-- =====================================================================
USE phstudio;

-- ---------------------------------------------------------------------
-- USERS
-- Password for ALL demo accounts: Demo@1234
-- Hash generated with PHP password_hash('Demo@1234', PASSWORD_BCRYPT)
-- ---------------------------------------------------------------------
INSERT INTO users (id, role, full_name, email, phone, password_hash, avatar_path, is_active) VALUES
(1, 'admin',        'Isabela Cruz',      'admin@phstudio.demo',        '+63 917 000 0001', '$2y$10$92k1o4rXe2Q1m0S0P0zGx.zM3z8u3rM8bXwYcQe0V4hFhZ1oYQeXa', 'avatars/isabela.jpg', 1),
(2, 'photographer',  'Marco Villanueva', 'marco@phstudio.demo',        '+63 917 000 0002', '$2y$10$92k1o4rXe2Q1m0S0P0zGx.zM3z8u3rM8bXwYcQe0V4hFhZ1oYQeXa', 'avatars/marco.jpg', 1),
(3, 'client',        'Juan Dela Cruz',   'juan.delacruz@example.com',  '+63 917 111 2222', '$2y$10$92k1o4rXe2Q1m0S0P0zGx.zM3z8u3rM8bXwYcQe0V4hFhZ1oYQeXa', NULL, 1),
(4, 'client',        'Maria Santos',     'maria.santos@example.com',   '+63 917 222 3333', '$2y$10$92k1o4rXe2Q1m0S0P0zGx.zM3z8u3rM8bXwYcQe0V4hFhZ1oYQeXa', NULL, 1),
(5, 'client',        'Andrea Reyes',     'andrea.reyes@example.com',   '+63 917 333 4444', '$2y$10$92k1o4rXe2Q1m0S0P0zGx.zM3z8u3rM8bXwYcQe0V4hFhZ1oYQeXa', NULL, 1),
(6, 'client',        'Paolo Ramirez',    'paolo.ramirez@example.com',  '+63 917 444 5555', '$2y$10$92k1o4rXe2Q1m0S0P0zGx.zM3z8u3rM8bXwYcQe0V4hFhZ1oYQeXa', NULL, 1);

-- NOTE: If the hash above does not verify in your PHP version, run:
--   php -r "echo password_hash('Demo@1234', PASSWORD_BCRYPT);"
-- and UPDATE users SET password_hash = '<new-hash>' for all demo rows.

-- ---------------------------------------------------------------------
-- CATEGORIES
-- ---------------------------------------------------------------------
INSERT INTO categories (id, name, slug, description, cover_image, sort_order) VALUES
(1, 'Weddings',    'weddings',    'Timeless love stories, told frame by frame.',            'categories/weddings.jpg', 1),
(2, 'Portraits',   'portraits',   'Editorial portraits that capture true character.',       'categories/portraits.jpg', 2),
(3, 'Events',      'events',      'Milestones, parties, and gatherings worth remembering.',  'categories/events.jpg', 3),
(4, 'Graduation',  'graduation',  'Celebrating the finish line of a hard-earned chapter.',   'categories/graduation.jpg', 4),
(5, 'Prenatal',    'prenatal',    'Soft, intimate photography for growing families.',        'categories/prenatal.jpg', 5),
(6, 'Corporate',   'corporate',   'Polished branding and headshots for modern businesses.',  'categories/corporate.jpg', 6);

-- ---------------------------------------------------------------------
-- PORTFOLIO PHOTOS (public gallery — using royalty-free placeholder URLs)
-- ---------------------------------------------------------------------
INSERT INTO portfolio_photos (category_id, title, image_path, is_featured, sort_order) VALUES
(1, 'Golden Hour Vows', 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1200', 1, 1),
(1, 'First Dance', 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=1200', 1, 2),
(1, 'The Bouquet', 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=1200', 0, 3),
(1, 'Ring Exchange', 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=1200', 0, 4),
(2, 'Studio Portrait I', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=1200', 1, 1),
(2, 'Natural Light Portrait', 'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?w=1200', 0, 2),
(2, 'Black and White Series', 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=1200', 0, 3),
(3, 'Birthday Celebration', 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=1200', 1, 1),
(3, 'Corporate Gala', 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1200', 0, 2),
(4, 'Cap Toss', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200', 1, 1),
(4, 'Proud Graduate', 'https://images.unsplash.com/photo-1627556592933-ffe99c1cd9eb?w=1200', 0, 2),
(5, 'Maternity Glow', 'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=1200', 1, 1),
(5, 'Soft Silhouette', 'https://images.unsplash.com/photo-1544126592-807ade215a0b?w=1200', 0, 2),
(6, 'Executive Headshot', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=1200', 1, 1),
(6, 'Team Branding Shoot', 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=1200', 0, 2);

-- ---------------------------------------------------------------------
-- PACKAGES
-- ---------------------------------------------------------------------
INSERT INTO packages (category_id, name, slug, description, price, duration_hours, deliverables, is_popular, sort_order) VALUES
(1, 'Wedding Essentials', 'wedding-essentials', 'A beautifully documented wedding day for intimate ceremonies.', 45000.00, 6,
 '["1 Photographer","6 hours coverage","200+ edited photos","Online private gallery","USB drive"]', 0, 1),
(1, 'Wedding Signature', 'wedding-signature', 'Our most-loved full-day wedding package with two shooters.', 78000.00, 10,
 '["2 Photographers","10 hours coverage","500+ edited photos","Same-day teaser reel","Premium photo album","Online private gallery"]', 1, 2),
(2, 'Portrait Session', 'portrait-session', 'A relaxed one-hour studio or outdoor portrait session.', 6500.00, 1,
 '["1 hour session","1 location","30 edited photos","Online gallery"]', 0, 1),
(2, 'Portrait Deluxe', 'portrait-deluxe', 'Extended portrait session with outfit changes and multiple looks.', 12000.00, 2,
 '["2 hour session","2 outfit changes","60 edited photos","Online gallery","5 printed photos"]', 1, 2),
(3, 'Event Coverage — Half Day', 'event-half-day', 'Coverage for birthdays, reunions, and small celebrations.', 15000.00, 4,
 '["1 Photographer","4 hours coverage","150 edited photos","Online gallery"]', 0, 1),
(3, 'Event Coverage — Full Day', 'event-full-day', 'Full-day coverage for larger events and corporate parties.', 28000.00, 8,
 '["1-2 Photographers","8 hours coverage","300 edited photos","Online gallery","Highlight reel"]', 1, 2),
(4, 'Graduation Package', 'graduation-package', 'Celebrate the achievement with a dedicated grad shoot.', 5500.00, 1,
 '["1 hour session","2 locations on campus","40 edited photos","Online gallery"]', 0, 1),
(5, 'Prenatal Glow', 'prenatal-glow', 'A gentle maternity session designed around your comfort.', 8500.00, 1.5,
 '["90 minute session","Studio or outdoor","45 edited photos","Online gallery","Partner & family shots"]', 0, 1),
(6, 'Corporate Headshots', 'corporate-headshots', 'Professional headshots for individuals or full teams.', 3500.00, 0.5,
 '["30 minute session per person","5 edited photos per person","Online gallery","Fast 48-hour turnaround"]', 0, 1),
(6, 'Corporate Branding Day', 'corporate-branding-day', 'Full-day on-location branding and culture photography.', 32000.00, 8,
 '["1-2 Photographers","8 hours coverage","Team + candid shots","200+ edited photos","Commercial usage license"]', 1, 2);

-- ---------------------------------------------------------------------
-- AVAILABILITY (Marco Villanueva — photographer id 2)
-- Working slots for the next few weeks; a few blocked days demonstrate
-- unavailability logic.
-- ---------------------------------------------------------------------
INSERT INTO availability_slots (photographer_id, slot_date, start_time, end_time, is_blocked) VALUES
(2, CURDATE() + INTERVAL 3 DAY, '09:00:00', '12:00:00', 0),
(2, CURDATE() + INTERVAL 3 DAY, '13:00:00', '17:00:00', 0),
(2, CURDATE() + INTERVAL 5 DAY, '09:00:00', '17:00:00', 0),
(2, CURDATE() + INTERVAL 7 DAY, '10:00:00', '18:00:00', 0),
(2, CURDATE() + INTERVAL 9 DAY, '09:00:00', '12:00:00', 1),
(2, CURDATE() + INTERVAL 12 DAY, '09:00:00', '17:00:00', 0),
(2, CURDATE() + INTERVAL 15 DAY, '09:00:00', '17:00:00', 0);

-- ---------------------------------------------------------------------
-- BOOKINGS (demo history across every status)
-- ---------------------------------------------------------------------
INSERT INTO bookings (id, client_id, photographer_id, package_id, category_id, session_date, start_time, end_time, location, event_details, status, admin_notes) VALUES
(1, 3, 2, 2, 1, CURDATE() + INTERVAL 20 DAY, '09:00:00', '19:00:00', 'Tagaytay Wedding Garden', 'Juan & Maria wedding — outdoor garden ceremony, 120 guests.', 'confirmed', 'Bring drone for aerial shots. Client requested candid style over posed.'),
(2, 4, 2, 4, 2, CURDATE() + INTERVAL 5 DAY, '14:00:00', '16:00:00', 'PHStudio — Studio A', 'Anniversary portrait session for Maria and family.', 'pending', NULL),
(3, 5, 2, 8, 5, CURDATE() - INTERVAL 10 DAY, '10:00:00', '11:30:00', 'Andrea''s Residence, Quezon City', 'Maternity session, 7 months along, soft natural light preferred.', 'completed', 'Client loved the window-light series. Delivered gallery on time.'),
(4, 6, 2, 6, 3, CURDATE() - INTERVAL 30 DAY, '10:00:00', '18:00:00', 'Makati Corporate Events Hall', 'Company anniversary event, ~300 attendees.', 'completed', 'Great lighting conditions. Client requested extra candid shots of leadership team.'),
(5, 3, 2, 3, 2, CURDATE() + INTERVAL 1 DAY, '09:00:00', '10:00:00', 'PHStudio — Studio B', 'Quick solo portrait refresh.', 'in_progress', 'Session currently underway.'),
(6, 4, 2, 9, 6, CURDATE() - INTERVAL 60 DAY, '09:00:00', '09:30:00', 'Ortigas Center Office', 'Corporate headshot, cancelled due to reschedule request.', 'cancelled', 'Client rescheduled internally; slot released.');

-- ---------------------------------------------------------------------
-- BOOKING REFERENCE IMAGES
-- ---------------------------------------------------------------------
INSERT INTO booking_references (booking_id, file_path) VALUES
(1, 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800'),
(1, 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=800');

-- ---------------------------------------------------------------------
-- BOOKING STATUS HISTORY
-- ---------------------------------------------------------------------
INSERT INTO booking_status_history (booking_id, old_status, new_status, changed_by) VALUES
(1, NULL, 'pending', 3),
(1, 'pending', 'confirmed', 2),
(3, 'pending', 'confirmed', 2),
(3, 'confirmed', 'completed', 2),
(4, 'confirmed', 'completed', 2),
(6, 'pending', 'cancelled', 4);

-- ---------------------------------------------------------------------
-- BOOKING MESSAGES (client <-> photographer notes thread)
-- ---------------------------------------------------------------------
INSERT INTO booking_messages (booking_id, sender_id, message) VALUES
(1, 3, 'Hi Marco! Excited for the big day. Can we add a family portrait list?'),
(1, 2, 'Of course, Juan — please email the list a week before and we will build it into the timeline.'),
(3, 5, 'Thank you so much for the beautiful photos, Marco!'),
(3, 2, 'It was a pleasure, Andrea. Wishing you and the baby all the best!');

-- ---------------------------------------------------------------------
-- INVOICES
-- ---------------------------------------------------------------------
INSERT INTO invoices (booking_id, invoice_number, amount_total, amount_paid, status, due_date, paid_at) VALUES
(1, 'INV-2026-0001', 78000.00, 30000.00, 'partial', CURDATE() + INTERVAL 18 DAY, NULL),
(2, 'INV-2026-0002', 12000.00, 0.00, 'unpaid', CURDATE() + INTERVAL 10 DAY, NULL),
(3, 'INV-2026-0003', 8500.00, 8500.00, 'paid', CURDATE() - INTERVAL 12 DAY, CURDATE() - INTERVAL 11 DAY),
(4, 'INV-2026-0004', 28000.00, 28000.00, 'paid', CURDATE() - INTERVAL 32 DAY, CURDATE() - INTERVAL 31 DAY);

-- ---------------------------------------------------------------------
-- PRIVATE GALLERIES
-- ---------------------------------------------------------------------
INSERT INTO galleries (id, booking_id, client_id, title, cover_image, photographer_notes, share_token, is_published, allow_download) VALUES
(1, 4, 6, 'Paolo Ramirez — Corporate Event Highlights', 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=1200',
 'Hi Paolo — here are the highlights from the anniversary event. Let me know your favorites for the printed recap!', 'gal_corp_9f2a1c', 1, 1),
(2, 3, 5, 'Andrea Reyes — Prenatal Session', 'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=1200',
 'Andrea, congratulations again! These turned out beautifully in the afternoon light.', 'gal_prenat_7b3d20', 1, 1),
(3, NULL, 3, 'Juan & Maria — Wedding Teasers', 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1200',
 'A sneak peek before the full wedding gallery is ready — full set coming in 3 weeks!', 'gal_wed_44ac10', 1, 0);

-- ---------------------------------------------------------------------
-- GALLERY PHOTOS
-- ---------------------------------------------------------------------
INSERT INTO gallery_photos (gallery_id, file_path, caption, sort_order) VALUES
(1, 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=1400', 'Opening remarks', 1),
(1, 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1400', 'Leadership toast', 2),
(1, 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=1400', 'Team portrait', 3),
(2, 'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=1400', 'Window light series', 1),
(2, 'https://images.unsplash.com/photo-1544126592-807ade215a0b?w=1400', 'Silhouette', 2),
(2, 'https://images.unsplash.com/photo-1519689680058-324335c77eba?w=1400', 'Hands on belly', 3),
(3, 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1400', 'Golden hour vows', 1),
(3, 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=1400', 'First dance', 2),
(3, 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=1400', 'The bouquet', 3),
(3, 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=1400', 'Ring exchange', 4);

-- ---------------------------------------------------------------------
-- GALLERY FAVORITES
-- ---------------------------------------------------------------------
INSERT INTO gallery_favorites (gallery_photo_id, client_id) VALUES
(4, 5), (5, 5), (7, 3), (9, 3);

-- ---------------------------------------------------------------------
-- TESTIMONIALS
-- ---------------------------------------------------------------------
INSERT INTO testimonials (client_name, client_photo, category_id, rating, quote, is_published) VALUES
('Juan & Maria Dela Cruz', NULL, 1, 5, 'Marco captured our wedding day exactly as we felt it — joyful, warm, and completely us. We cried looking at the gallery.', 1),
('Andrea Reyes', NULL, 5, 5, 'The most comfortable I have ever felt in front of a camera. The maternity photos are pure art.', 1),
('Paolo Ramirez', NULL, 3, 5, 'Professional, punctual, and the turnaround time was incredible. Our company event has never looked this good.', 1),
('Camille Torres', NULL, 2, 4, 'Loved my portrait session! Great direction and the editing style is so clean and timeless.', 1),
('Rafael Uy', NULL, 6, 5, 'PHStudio handled headshots for our entire 40-person team in one afternoon without a hitch.', 1);

-- ---------------------------------------------------------------------
-- FAQ
-- ---------------------------------------------------------------------
INSERT INTO faqs (question, answer, sort_order) VALUES
('How do I book a session?', 'Create a free client account, browse our packages, then submit a booking request with your preferred date and event details. You will receive a confirmation once the photographer approves your slot.', 1),
('What is your rescheduling policy?', 'You may request a reschedule up to 5 days before your session at no extra cost, subject to availability. Reach out through your booking''s message thread.', 2),
('How long until I receive my photos?', 'Standard turnaround is 2-3 weeks for full sessions and 48 hours for headshot sessions. You will be notified the moment your private gallery is published.', 3),
('Can I download my photos?', 'Yes — most galleries allow full-resolution downloads. Look for the download icon on each photo, or use "Download All" at the top of your gallery.', 4),
('Do you require a deposit?', 'A 30-50% deposit is required to confirm most bookings. The remaining balance is due on or before the session date, visible in your Invoices tab.', 5),
('Can I share my gallery with family?', 'Absolutely. Every private gallery has a secure share link you can copy from the gallery page and send to anyone you would like to view your photos.', 6);

-- ---------------------------------------------------------------------
-- CONTACT MESSAGES (sample)
-- ---------------------------------------------------------------------
INSERT INTO contact_messages (name, email, subject, message, is_read) VALUES
('Kristine Aban', 'kristine.aban@example.com', 'Question about destination weddings', 'Hi, do you travel outside Metro Manila for weddings? Looking at a Palawan ceremony next year.', 0);
