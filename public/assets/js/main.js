// Global UI scripts

document.addEventListener('DOMContentLoaded', () => {
  // Mobile nav toggle
  const mobileBtn = document.getElementById('btn-mobile-menu');
  const mobileMenu = document.getElementById('mobile-menu');
  if (mobileBtn && mobileMenu) {
    mobileBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  }

  // Admin sidebar toggle on mobile
  const sidebarBtn = document.getElementById('btn-sidebar-toggle');
  const sidebar = document.getElementById('admin-sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  if (sidebarBtn && sidebar) {
    const toggle = () => {
      sidebar.classList.toggle('-translate-x-full');
      if (overlay) overlay.classList.toggle('hidden');
    };
    sidebarBtn.addEventListener('click', toggle);
    if (overlay) overlay.addEventListener('click', toggle);
  }

  // Auto-hide alert toasts after 5 seconds
  document.querySelectorAll('.alert-dismissible').forEach((el) => {
    setTimeout(() => {
      el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      el.style.opacity = '0';
      el.style.transform = 'translateY(-10px)';
      setTimeout(() => el.remove(), 400);
    }, 5000);
  });
});

// Toast notification helper
function showToast(message, type = 'info') {
  const container = document.getElementById('toast-container') || (() => {
    const div = document.createElement('div');
    div.id = 'toast-container';
    div.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full';
    document.body.appendChild(div);
    return div;
  })();

  const toast = document.createElement('div');
  const bg = type === 'success' ? 'bg-emerald-600 text-white' : (type === 'error' ? 'bg-rose-600 text-white' : 'bg-gray-800 text-white');
  toast.className = `p-4 rounded-xl shadow-lg flex items-center justify-between text-sm transition-all duration-300 transform translate-y-2 opacity-0 ${bg}`;
  toast.innerHTML = `<span>${message}</span><button class="ml-3 font-bold">&times;</button>`;
  
  toast.querySelector('button').addEventListener('click', () => toast.remove());
  container.appendChild(toast);

  requestAnimationFrame(() => {
    toast.classList.remove('translate-y-2', 'opacity-0');
  });

  setTimeout(() => {
    toast.classList.add('opacity-0', '-translate-y-2');
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}
