// Voting page multi-step logic and autocomplete

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('vote-form');
  if (!form) return;

  const searchInput = document.getElementById('student-search');
  const searchResults = document.getElementById('student-results');
  const selectedStudentCard = document.getElementById('selected-student-card');
  const selectedStudentName = document.getElementById('selected-student-name');
  const selectedStudentClass = document.getElementById('selected-student-class');
  const selectedStudentNis = document.getElementById('selected-student-nis');
  const studentIdInput = document.getElementById('selected-student-id');
  const changeStudentBtn = document.getElementById('btn-change-student');
  const nisnVerificationBox = document.getElementById('nisn-verification-box');
  const nisnInput = document.getElementById('nisn-last4');

  const btnStep1Next = document.getElementById('btn-step1-next');
  const btnStep2Back = document.getElementById('btn-step2-back');
  const btnStep2Next = document.getElementById('btn-step2-next');

  const step1 = document.getElementById('step-1');
  const step2 = document.getElementById('step-2');
  const step3Modal = document.getElementById('confirm-modal');
  const step4Success = document.getElementById('step-4-success');

  const stepIndicator1 = document.getElementById('indicator-step-1');
  const stepIndicator2 = document.getElementById('indicator-step-2');
  const stepIndicator3 = document.getElementById('indicator-step-3');

  const modalCandidateName = document.getElementById('modal-candidate-name');
  const modalCandidatePhoto = document.getElementById('modal-candidate-photo');
  const modalStudentName = document.getElementById('modal-student-name');
  const btnModalCancel = document.getElementById('btn-modal-cancel');
  const btnModalSubmit = document.getElementById('btn-modal-submit');

  const rawBase = (form.getAttribute('data-base-url') || '').trim();
  const baseUrl = (rawBase === '/' || rawBase === '') ? '' : rawBase.replace(/\/+$/, '');
  const apiUrl = (endpoint) => {
    const clean = '/' + endpoint.replace(/^\/+/, '');
    return baseUrl ? `${baseUrl}${clean}` : clean;
  };

  const requireNisn = form.getAttribute('data-require-nisn') === '1';

  let selectedStudent = null;
  let selectedCandidate = null;
  let searchTimeout = null;

  // Autocomplete student search (min 3 chars, debounce 300ms)
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const q = e.target.value.trim();
      clearTimeout(searchTimeout);

      if (q.length < 3) {
        searchResults.classList.add('hidden');
        searchResults.innerHTML = '';
        return;
      }

      searchTimeout = setTimeout(async () => {
        try {
          searchResults.classList.remove('hidden');
          searchResults.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">Mencari siswa...</div>';

          const res = await fetch(apiUrl(`/api/students/search?q=${encodeURIComponent(q)}`), {
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
            },
          });

          if (!res.ok) {
            try {
              const errJson = await res.json();
              searchResults.innerHTML = `<div class="p-4 text-center text-gray-500 text-sm">${escapeHtml(errJson.message || 'Siswa tidak ditemukan dalam DPT.')}</div>`;
            } catch (_) {
              searchResults.innerHTML = '<div class="p-4 text-center text-rose-500 text-sm">Gagal memuat data pemilih. Silakan coba lagi.</div>';
            }
            return;
          }

          const json = await res.json();

          if (!json.success || !json.data || json.data.length === 0) {
            searchResults.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">Siswa tidak ditemukan dalam DPT. Pastikan ejaan nama atau NISN benar.</div>';
            return;
          }

          let html = '';
          json.data.forEach((s) => {
            const isCandidate = s.is_candidate === 1 || s.is_candidate === true;
            const alreadyVoted = s.has_voted === 1 || s.has_voted === true;

            const isTeacher = (s.class || '').toUpperCase() === 'GURU';
            const classLabel = isTeacher ? 'Dewan Guru' : `Kelas ${escapeHtml(s.class)}`;

            if (isCandidate) {
              html += `
                <div class="p-3 border-b border-gray-100 bg-amber-50/70 text-amber-900 cursor-not-allowed flex items-center justify-between">
                  <div>
                    <div class="font-medium">${escapeHtml(s.full_name)}</div>
                    <div class="text-xs text-amber-700">${classLabel} ${s.nis ? '• NISN: ' + escapeHtml(s.nis) : ''}</div>
                  </div>
                  <span class="text-xs font-semibold px-2.5 py-1 bg-amber-200 text-amber-900 rounded-full">Kandidat (Tidak Memilih)</span>
                </div>
              `;
            } else if (alreadyVoted) {
              html += `
                <div class="p-3 border-b border-gray-100 bg-gray-50 text-gray-400 cursor-not-allowed flex items-center justify-between">
                  <div>
                    <div class="font-medium line-through">${escapeHtml(s.full_name)}</div>
                    <div class="text-xs text-gray-400">${classLabel} ${s.nis ? '• NISN: ' + escapeHtml(s.nis) : ''}</div>
                  </div>
                  <span class="text-xs font-semibold px-2 py-1 bg-amber-100 text-amber-800 rounded-full">Sudah Memilih</span>
                </div>
              `;
            } else {
              html += `
                <div class="p-3 border-b border-gray-100 hover:bg-pink-50 cursor-pointer flex items-center justify-between transition-colors student-item"
                     data-id="${s.id}" data-name="${escapeHtml(s.full_name)}" data-class="${escapeHtml(s.class)}" data-nis="${escapeHtml(s.nis || '')}">
                  <div>
                    <div class="font-medium text-gray-800">${escapeHtml(s.full_name)}</div>
                    <div class="text-xs text-gray-500">${classLabel} ${s.nis ? '• NISN: ' + escapeHtml(s.nis) : ''}</div>
                  </div>
                  <span class="text-xs font-semibold px-2.5 py-1 ${isTeacher ? 'bg-purple-100 text-purple-700' : 'bg-pink-100 text-pink-700'} rounded-full">${isTeacher ? 'Dewan Guru' : 'Pilih'}</span>
                </div>
              `;
            }
          });

          searchResults.innerHTML = html;

          // Attach click listeners to selectable students
          searchResults.querySelectorAll('.student-item').forEach((item) => {
            item.addEventListener('click', () => {
              const id = item.getAttribute('data-id');
              const name = item.getAttribute('data-name');
              const cls = item.getAttribute('data-class');
              const nis = item.getAttribute('data-nis');

              selectStudent({ id, full_name: name, class: cls, nis });
            });
          });
        } catch (err) {
          searchResults.innerHTML = '<div class="p-4 text-center text-rose-500 text-sm">Gagal memuat data pemilih. Coba lagi.</div>';
        }
      }, 300);
    });
  }

  function selectStudent(student) {
    selectedStudent = student;
    studentIdInput.value = student.id;

    const isTeacher = (student.class || '').toUpperCase() === 'GURU';
    selectedStudentName.textContent = student.full_name;
    selectedStudentClass.textContent = isTeacher ? 'Dewan Guru' : `Kelas ${student.class}`;
    selectedStudentNis.textContent = isTeacher ? 'Kategori: Dewan Guru' : (student.nis ? `NISN: ${student.nis}` : 'NISN: -');

    searchInput.classList.add('hidden');
    searchResults.classList.add('hidden');
    selectedStudentCard.classList.remove('hidden');

    if (requireNisn && nisnVerificationBox) {
      if (isTeacher || !student.nis) {
        nisnVerificationBox.classList.add('hidden');
      } else {
        nisnVerificationBox.classList.remove('hidden');
      }
    }

    btnStep1Next.disabled = false;
    btnStep1Next.classList.remove('opacity-50', 'cursor-not-allowed');
  }

  if (changeStudentBtn) {
    changeStudentBtn.addEventListener('click', () => {
      selectedStudent = null;
      studentIdInput.value = '';
      searchInput.value = '';
      searchInput.classList.remove('hidden');
      selectedStudentCard.classList.add('hidden');
      if (nisnVerificationBox) nisnVerificationBox.classList.add('hidden');
      btnStep1Next.disabled = true;
      btnStep1Next.classList.add('opacity-50', 'cursor-not-allowed');
      searchInput.focus();
    });
  }

  // Next to Step 2
  if (btnStep1Next) {
    btnStep1Next.addEventListener('click', async () => {
      if (!selectedStudent) {
        showToast('Pilih nama siswa terlebih dahulu', 'error');
        return;
      }

      // If NISN verification required, check last 4 digits
      if (requireNisn && nisnInput) {
        const val = nisnInput.value.trim();
        const fullNis = (selectedStudent.nis || '').trim();
        if (fullNis.length >= 4) {
          const expectedLast4 = fullNis.slice(-4);
          if (val !== expectedLast4) {
            showToast('4 digit terakhir NISN tidak cocok dengan data Anda.', 'error');
            nisnInput.focus();
            return;
          }
        }
      }

      // Check live vote status on server
      try {
        btnStep1Next.disabled = true;
        btnStep1Next.textContent = 'Memeriksa...';
        const res = await fetch(apiUrl(`/api/votes/check/${selectedStudent.id}`), {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const check = await res.json();

        if (check.data && check.data.has_voted) {
          showToast(`Siswa ${selectedStudent.full_name} sudah memilih sebelumnya.`, 'error');
          changeStudentBtn.click();
          return;
        }
      } catch (err) {
        // Continue if network glitch
      } finally {
        btnStep1Next.disabled = false;
        btnStep1Next.textContent = 'Lanjut Pilih Kandidat →';
      }

      // Transition to Step 2
      step1.classList.add('hidden');
      step2.classList.remove('hidden');
      window.scrollTo({ top: 0, behavior: 'smooth' });

      // Update indicators
      stepIndicator1.classList.remove('bg-pink-600', 'text-white');
      stepIndicator1.classList.add('bg-emerald-500', 'text-white');
      stepIndicator1.innerHTML = '✓';
      stepIndicator2.classList.add('bg-pink-600', 'text-white');
    });
  }

  // Back to Step 1
  if (btnStep2Back) {
    btnStep2Back.addEventListener('click', () => {
      step2.classList.add('hidden');
      step1.classList.remove('hidden');
      window.scrollTo({ top: 0, behavior: 'smooth' });

      stepIndicator1.classList.add('bg-pink-600', 'text-white');
      stepIndicator1.classList.remove('bg-emerald-500');
      stepIndicator1.innerHTML = '1';
      stepIndicator2.classList.remove('bg-pink-600', 'text-white');
    });
  }

  // Candidate selection listener
  document.querySelectorAll('input[name="candidate_id"]').forEach((radio) => {
    radio.addEventListener('change', (e) => {
      document.querySelectorAll('.candidate-card-label').forEach((lbl) => {
        lbl.classList.remove('border-pink-500', 'bg-pink-50/50', 'ring-4', 'ring-pink-100');
        lbl.classList.add('border-gray-200');
      });

      const card = e.target.closest('.candidate-select-card');
      const label = card ? card.querySelector('.candidate-card-label') : null;
      if (label) {
        label.classList.remove('border-gray-200');
        label.classList.add('border-pink-500', 'bg-pink-50/50', 'ring-4', 'ring-pink-100');
      }

      selectedCandidate = {
        id: e.target.value,
        name: card.getAttribute('data-name'),
        photo: card.getAttribute('data-photo')
      };
      btnStep2Next.disabled = false;
      btnStep2Next.classList.remove('opacity-50', 'cursor-not-allowed');
    });
  });

  // Step 2 Next -> Open Confirmation Modal (Step 3)
  if (btnStep2Next) {
    btnStep2Next.addEventListener('click', () => {
      if (!selectedCandidate) {
        showToast('Silakan pilih salah satu kandidat terlebih dahulu.', 'error');
        return;
      }

      modalCandidateName.textContent = selectedCandidate.name;
      modalCandidatePhoto.src = selectedCandidate.photo;
      modalStudentName.textContent = selectedStudent.full_name;

      step3Modal.classList.remove('hidden');
      stepIndicator2.classList.remove('bg-pink-600');
      stepIndicator2.classList.add('bg-emerald-500');
      stepIndicator2.innerHTML = '✓';
      stepIndicator3.classList.add('bg-pink-600', 'text-white');
    });
  }

  // Cancel Modal
  if (btnModalCancel) {
    btnModalCancel.addEventListener('click', () => {
      step3Modal.classList.add('hidden');
      stepIndicator2.classList.add('bg-pink-600');
      stepIndicator2.classList.remove('bg-emerald-500');
      stepIndicator2.innerHTML = '2';
      stepIndicator3.classList.remove('bg-pink-600', 'text-white');
    });
  }

  // Submit Vote via AJAX
  if (btnModalSubmit) {
    btnModalSubmit.addEventListener('click', async () => {
      btnModalSubmit.disabled = true;
      btnModalSubmit.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg> Menyimpan suara...
      `;

      try {
        const payload = {
          student_id: selectedStudent.id,
          candidate_id: selectedCandidate.id,
          nisn_last4: nisnInput ? nisnInput.value.trim() : '',
        };

        const res = await fetch(apiUrl('/api/votes'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
          showToast(data.message || 'Gagal menyimpan suara', 'error');
          btnModalSubmit.disabled = false;
          btnModalSubmit.textContent = 'Ya, Simpan Pilihan Saya';
          step3Modal.classList.add('hidden');
          return;
        }

        // Success: hide modal & step 2, show Step 4 Success!
        step3Modal.classList.add('hidden');
        step2.classList.add('hidden');
        step4Success.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });

        document.getElementById('success-student-name').textContent = selectedStudent.full_name;

        stepIndicator3.classList.remove('bg-pink-600');
        stepIndicator3.classList.add('bg-emerald-500');
        stepIndicator3.innerHTML = '✓';

        // Play confetti or cheerful effect if desired
      } catch (err) {
        showToast('Koneksi terputus. Silakan coba lagi.', 'error');
        btnModalSubmit.disabled = false;
        btnModalSubmit.textContent = 'Ya, Simpan Pilihan Saya';
      }
    });
  }

  function escapeHtml(str) {
    return String(str || '').replace(/[&<>"']/g, (m) => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    })[m]);
  }
});
