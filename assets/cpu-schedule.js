// Theme toggle & init
const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
  themeToggle.onclick = () => {
    document.body.classList.toggle('dark-mode');
    if (document.body.classList.contains('dark-mode')) {
      localStorage.setItem('theme', 'dark');
      themeToggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
    } else {
      localStorage.setItem('theme', 'light');
      themeToggle.innerHTML = '<i class="fa-solid fa-moon"></i>';
    }
  };
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
    themeToggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
  }
}

// Quantum show/hide
const algorithmSelect = document.getElementById('algorithmSelect');
const quantumGroup    = document.getElementById('quantumGroup');
if (algorithmSelect && quantumGroup) {
  algorithmSelect.onchange = () => {
    quantumGroup.style.display = algorithmSelect.value === 'rr' ? '' : 'none';
  };
}
