// Admin Panel Interactions

document.addEventListener('DOMContentLoaded', () => {
  // Candidate photo instant preview
  const photoInput = document.getElementById('candidate-photo-input');
  const photoPreview = document.getElementById('candidate-photo-preview');
  if (photoInput && photoPreview) {
    photoInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        if (file.size > 2 * 1024 * 1024) {
          alert('Ukuran file melebihi 2MB. Silakan pilih foto dengan ukuran lebih kecil.');
          photoInput.value = '';
          return;
        }
        const reader = new FileReader();
        reader.onload = (ev) => {
          photoPreview.src = ev.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // DPT Select All Checkbox
  const selectAllCheckbox = document.getElementById('select-all-students');
  const studentCheckboxes = document.querySelectorAll('.student-checkbox');
  const bulkActionContainer = document.getElementById('bulk-action-container');
  const selectedCountSpan = document.getElementById('selected-count');

  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', (e) => {
      const checked = e.target.checked;
      studentCheckboxes.forEach((cb) => {
        cb.checked = checked;
      });
      updateBulkUi();
    });

    studentCheckboxes.forEach((cb) => {
      cb.addEventListener('change', () => {
        updateBulkUi();
      });
    });
  }

  function updateBulkUi() {
    const checked = Array.from(studentCheckboxes).filter(cb => cb.checked);
    if (selectedCountSpan) selectedCountSpan.textContent = checked.length;
    if (bulkActionContainer) {
      if (checked.length > 0) {
        bulkActionContainer.classList.remove('hidden');
      } else {
        bulkActionContainer.classList.add('hidden');
      }
    }
  }

  // Period Reset Confirmation Type-to-Confirm: "RESET"
  const resetInput = document.getElementById('reset-confirmation-input');
  const resetBtn = document.getElementById('btn-confirm-reset');
  if (resetInput && resetBtn) {
    resetInput.addEventListener('input', (e) => {
      const val = e.target.value.trim();
      if (val === 'RESET') {
        resetBtn.disabled = false;
        resetBtn.classList.remove('opacity-50', 'cursor-not-allowed');
      } else {
        resetBtn.disabled = true;
        resetBtn.classList.add('opacity-50', 'cursor-not-allowed');
      }
    });
  }

  // Generic confirmation for forms
  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (e) => {
      const msg = form.getAttribute('data-confirm') || 'Apakah Anda yakin?';
      if (!confirm(msg)) {
        e.preventDefault();
      }
    });
  });
});
