/**
 * PHStudio — Private Gallery Interactions
 * Favoriting, share-link copy, and lightbox for gallery photos.
 */
document.addEventListener('DOMContentLoaded', () => {

  // Favorite heart toggle
  document.querySelectorAll('.favorite-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.stopPropagation();
      const photoId = btn.dataset.photoId;
      btn.disabled = true;
      try {
        const res = await fetch(`${window.APP_BASE_URL}api/toggle-favorite.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `photo_id=${encodeURIComponent(photoId)}&csrf_token=${encodeURIComponent(window.CSRF_TOKEN || '')}`
        });
        const data = await res.json();
        if (data.success) {
          btn.classList.toggle('favorited', data.favorited);
          btn.textContent = data.favorited ? '♥' : '♡';
        }
      } catch (err) {
        console.error('Favorite toggle failed', err);
      } finally {
        btn.disabled = false;
      }
    });
  });

  // Copy share link
  const shareBtn = document.getElementById('copyShareLink');
  if (shareBtn) {
    shareBtn.addEventListener('click', async () => {
      const link = shareBtn.dataset.link;
      try {
        await navigator.clipboard.writeText(link);
        const original = shareBtn.textContent;
        shareBtn.textContent = 'Link Copied!';
        setTimeout(() => { shareBtn.textContent = original; }, 2000);
      } catch (err) {
        prompt('Copy this link:', link);
      }
    });
  }

  // Download-all (simply triggers sequential downloads of visible photos)
  const downloadAllBtn = document.getElementById('downloadAllBtn');
  if (downloadAllBtn) {
    downloadAllBtn.addEventListener('click', () => {
      document.querySelectorAll('.gallery-photo img').forEach((img, i) => {
        setTimeout(() => {
          const a = document.createElement('a');
          a.href = img.src;
          a.download = `phstudio-photo-${i + 1}.jpg`;
          a.target = '_blank';
          a.click();
        }, i * 400);
      });
    });
  }
});
