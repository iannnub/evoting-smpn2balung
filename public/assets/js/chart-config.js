// Chart.js helper and live polling

let voteChartInstance = null;
let participationChartInstance = null;

function initVoteChart(canvasId, labels, data, colors = null) {
  const ctx = document.getElementById(canvasId);
  if (!ctx || typeof Chart === 'undefined') return null;

  const defaultColors = [
    'rgba(236, 72, 153, 0.85)',
    'rgba(168, 85, 247, 0.85)',
    'rgba(20, 184, 166, 0.85)',
    'rgba(245, 158, 11, 0.85)',
    'rgba(59, 130, 246, 0.85)',
    'rgba(239, 68, 68, 0.85)',
  ];

  if (voteChartInstance) {
    voteChartInstance.destroy();
  }

  voteChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Jumlah Suara',
        data: data,
        backgroundColor: colors || defaultColors.slice(0, data.length),
        borderColor: '#ffffff',
        borderWidth: 1.5,
        borderRadius: 8,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => ` ${ctx.parsed.y} Suara`
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { precision: 0, font: { family: 'Inter' } },
          grid: { color: '#f3f4f6' }
        },
        x: {
          ticks: { font: { family: 'Inter', weight: '500' } },
          grid: { display: false }
        }
      }
    }
  });

  return voteChartInstance;
}

function initParticipationPieChart(canvasId, voted, remaining) {
  const ctx = document.getElementById(canvasId);
  if (!ctx || typeof Chart === 'undefined') return null;

  if (participationChartInstance) {
    participationChartInstance.destroy();
  }

  participationChartInstance = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Sudah Memilih', 'Belum Memilih'],
      datasets: [{
        data: [voted, remaining],
        backgroundColor: ['#10b981', '#e5e7eb'],
        borderWidth: 0,
        hoverOffset: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { font: { family: 'Inter' }, padding: 16 }
        }
      },
      cutout: '72%'
    }
  });

  return participationChartInstance;
}

// Live polling for public results page (10s interval)
function startLiveResultsPolling(apiUrl, intervalMs = 10000) {
  let interval = setInterval(async () => {
    try {
      const res = await fetch(apiUrl);
      if (!res.ok) return;
      const json = await res.json();
      if (!json.success || !json.data) return;

      const { results, statistics } = json.data;

      // Update Chart
      if (voteChartInstance && results) {
        voteChartInstance.data.labels = results.map(r => r.candidate_name);
        voteChartInstance.data.datasets[0].data = results.map(r => r.total_votes);
        voteChartInstance.update();
      }

      // Update Statistics cards if elements exist
      const statTotalVoted = document.getElementById('stat-total-voted');
      const statParticipation = document.getElementById('stat-participation');
      const statTotalStudents = document.getElementById('stat-total-students');

      if (statTotalVoted && statistics) statTotalVoted.textContent = statistics.total_voted;
      if (statTotalStudents && statistics) statTotalStudents.textContent = statistics.total_students;
      if (statParticipation && statistics) statParticipation.textContent = `${statistics.participation_rate}%`;

      // Update table rankings
      results.forEach((r, idx) => {
        const rowVotes = document.getElementById(`rank-votes-${r.candidate_id}`);
        const rowPct = document.getElementById(`rank-pct-${r.candidate_id}`);
        const rowBar = document.getElementById(`rank-bar-${r.candidate_id}`);

        if (rowVotes) rowVotes.textContent = `${r.total_votes} suara`;
        if (rowPct) rowPct.textContent = `${r.vote_percentage}%`;
        if (rowBar) rowBar.style.width = `${r.vote_percentage}%`;
      });

      const updatedTime = document.getElementById('live-updated-time');
      if (updatedTime) {
        const now = new Date();
        updatedTime.textContent = `Pembaruan: ${now.toLocaleTimeString('id-ID')}`;
      }
    } catch (e) {
      console.warn('Polling error:', e);
    }
  }, intervalMs);

  // Stop polling on page unload
  window.addEventListener('beforeunload', () => clearInterval(interval));
}
