/**
 * PHStudio — Booking Form Logic
 * Handles multi-step UI and live slot-availability checks against
 * /api/check-availability.php
 */
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('bookingForm');
  if (!form) return;

  const steps = Array.from(form.querySelectorAll('.booking-step'));
  const stepDots = Array.from(document.querySelectorAll('.step-dot'));
  let currentStep = 0;

  function showStep(index) {
    steps.forEach((s, i) => s.classList.toggle('active', i === index));
    stepDots.forEach((d, i) => {
      d.classList.toggle('active', i === index);
      d.classList.toggle('done', i < index);
    });
    currentStep = index;
    window.scrollTo({ top: form.offsetTop - 120, behavior: 'smooth' });
  }

  form.querySelectorAll('[data-next]').forEach(btn => {
    btn.addEventListener('click', () => {
      const required = steps[currentStep].querySelectorAll('[required]');
      let valid = true;
      required.forEach(input => {
        if (!input.value) { valid = false; input.classList.add('invalid'); }
        else { input.classList.remove('invalid'); }
      });
      if (!valid) return;
      if (currentStep < steps.length - 1) showStep(currentStep + 1);
    });
  });

  form.querySelectorAll('[data-prev]').forEach(btn => {
    btn.addEventListener('click', () => {
      if (currentStep > 0) showStep(currentStep - 1);
    });
  });

  // Package selection cards
  document.querySelectorAll('.package-select-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.package-select-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      document.getElementById('package_id').value = card.dataset.packageId;
    });
  });

  // Live availability check
  const dateInput = document.getElementById('session_date');
  const timeSelect = document.getElementById('start_time');
  const availabilityMsg = document.getElementById('availabilityMsg');

  async function refreshSlots() {
    if (!dateInput.value) return;
    availabilityMsg.textContent = 'Checking availability...';
    timeSelect.innerHTML = '<option value="">Loading...</option>';
    try {
      const res = await fetch(`${window.APP_BASE_URL}api/check-availability.php?date=${encodeURIComponent(dateInput.value)}`);
      const data = await res.json();
      timeSelect.innerHTML = '';
      if (data.slots && data.slots.length) {
        data.slots.forEach(slot => {
          const opt = document.createElement('option');
          opt.value = slot.start_time;
          opt.textContent = `${slot.start_label} - ${slot.end_label}`;
          timeSelect.appendChild(opt);
        });
        availabilityMsg.textContent = `${data.slots.length} time slot(s) available on this date.`;
        availabilityMsg.className = 'form-hint success';
      } else {
        timeSelect.innerHTML = '<option value="">No slots available</option>';
        availabilityMsg.textContent = 'No availability on this date. Please choose another.';
        availabilityMsg.className = 'form-hint error';
      }
    } catch (err) {
      availabilityMsg.textContent = 'Could not check availability. You can still submit and we will confirm manually.';
    }
  }

  if (dateInput) {
    dateInput.addEventListener('change', refreshSlots);
  }

  showStep(0);
});
