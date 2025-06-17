// assets/script.js

// DOM Elements
const themeToggle      = document.getElementById('themeToggle');
const body             = document.body;
const taskModal        = document.getElementById('taskModal');
const addTaskBtn       = document.getElementById('addTaskBtn');
const closeModal       = document.getElementById('closeModal');
const taskForm         = document.getElementById('taskForm');
const tasksContainer   = document.getElementById('tasksContainer');
const taskSearch       = document.getElementById('taskSearch');
const statusFilter     = document.getElementById('statusFilter');
const priorityFilter   = document.getElementById('priorityFilter');

// Live Task Elements
const startTimer          = document.getElementById('startTimer');
const pauseTimer          = document.getElementById('pauseTimer');
const resetTimer          = document.getElementById('resetTimer');
const hoursDisplay        = document.getElementById('hours');
const minutesDisplay      = document.getElementById('minutes');
const secondsDisplay      = document.getElementById('seconds');
const currentTaskDisplay  = document.getElementById('currentTask');
const taskProgress        = document.getElementById('taskProgress');
const progressText        = document.getElementById('progressText');

// State
let tasks = [];
let currentFilter = {
  search: '',
  status: 'all',
  priority: 'all'
};

// Live Task State
let timerState = {
  isRunning: false,
  startTime: null,
  elapsedTime: 0,
  currentTask: null,
  timerInterval: null
};

// Theme Toggle
function initTheme() {
  if (localStorage.getItem('theme') === 'dark') {
    body.classList.add('dark-mode');
    themeToggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
  } else {
    body.classList.remove('dark-mode');
    themeToggle.innerHTML = '<i class="fa-solid fa-moon"></i>';
  }
}

themeToggle.onclick = function() {
  body.classList.toggle('dark-mode');
  if (body.classList.contains('dark-mode')) {
    localStorage.setItem('theme', 'dark');
    themeToggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
  } else {
    localStorage.setItem('theme', 'light');
    themeToggle.innerHTML = '<i class="fa-solid fa-moon"></i>';
  }
};

// Modal Functions
function showModal() {
  taskModal.classList.remove('hidden');
  taskModal.classList.add('active');
  document.getElementById('taskTitle').focus();
}

function hideModal() {
  taskModal.classList.remove('active');
  setTimeout(() => {
    taskModal.classList.add('hidden');
    taskForm.reset();
    bindFormSubmit(); // re-bind for new adds
  }, 300);
}

// Fetch Tasks from PHP
async function fetchTasks() {
  try {
    const res = await fetch('backend/get_tasks.php');
    tasks = await res.json();
    renderTasks();
    updateStats();
  } catch (err) {
    console.error('Failed to load tasks', err);
  }
}

// Task Management
function createTaskElement(task) {
  const taskElement = document.createElement('div');
  taskElement.className = 'task-card';
  taskElement.dataset.id = task.id;
  
  const priorityClass = `priority-${task.priority}`;
  const darkClass = body.classList.contains('dark-mode') ? 'dark' : '';
  
  taskElement.innerHTML = `
    <div class="task-info">
      <h3 class="task-title">${task.title}</h3>
      <div class="task-description">${task.description}</div>
      <div class="task-meta">
        <span><i class="fa-regular fa-calendar"></i> ${formatDate(task.dueDate)}</span>
        <span class="task-priority ${priorityClass} ${darkClass}">${task.priority}</span>
        <span class="task-status">${task.status}</span>
      </div>
    </div>
    <div class="task-actions">
      <button class="task-action-btn start-task" title="Start Task">
        <i class="fa-solid fa-play"></i>
      </button>
      <button class="task-action-btn edit-task" title="Edit Task">
        <i class="fa-solid fa-pen"></i>
      </button>
      <button class="task-action-btn delete-task" title="Delete Task">
        <i class="fa-solid fa-trash"></i>
      </button>
    </div>
  `;

  taskElement.querySelector('.start-task').onclick = () => startLiveTask(task.id);
  taskElement.querySelector('.edit-task').onclick  = () => beginEditTask(task.id);
  taskElement.querySelector('.delete-task').onclick= () => removeTask(task.id);

  return taskElement;
}

function renderTasks() {
  tasksContainer.innerHTML = '';
  const filtered = tasks.filter(task => {
    const matchSearch   = task.title.toLowerCase().includes(currentFilter.search.toLowerCase())
                       || task.description.toLowerCase().includes(currentFilter.search.toLowerCase());
    const matchStatus   = currentFilter.status === 'all' || task.status === currentFilter.status;
    const matchPriority = currentFilter.priority === 'all' || task.priority === currentFilter.priority;
    return matchSearch && matchStatus && matchPriority;
  });
  filtered.forEach(task => tasksContainer.appendChild(createTaskElement(task)));
}

// Stats and Charts
function updateStats() {
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('todayTasks').textContent     = tasks.filter(t => t.dueDate === today).length;
  document.getElementById('completedTasks').textContent = tasks.filter(t => t.status === 'completed').length;
  document.getElementById('pendingTasks').textContent   = tasks.filter(t => t.status !== 'completed').length;
  updateCharts();
}

function updateCharts() {
  // Completion Chart
  const compCtx = document.getElementById('completionChart').getContext('2d');
  new Chart(compCtx, {
    type: 'doughnut',
    data: {
      labels: ['Pending','In Progress','Completed'],
      datasets: [{
        data: [
          tasks.filter(t => t.status==='pending').length,
          tasks.filter(t => t.status==='in-progress').length,
          tasks.filter(t => t.status==='completed').length
        ]
      }]
    },
    options: { responsive:true, plugins:{legend:{position:'bottom'}} }
  });
  // Priority Chart
  const priCtx = document.getElementById('priorityChart').getContext('2d');
  new Chart(priCtx, {
    type:'bar',
    data:{
      labels:['High','Medium','Low'],
      datasets:[{
        data:[
          tasks.filter(t=>t.priority==='high').length,
          tasks.filter(t=>t.priority==='medium').length,
          tasks.filter(t=>t.priority==='low').length
        ]
      }]
    },
    options:{responsive:true, scales:{y:{beginAtZero:true, ticks:{stepSize:1}}}, plugins:{legend:{display:false}}}
  });
}

// Utility
function formatDate(ds) {
  return new Date(ds).toLocaleDateString(undefined, { year:'numeric', month:'short', day:'numeric' });
}

// Create or Update Task via PHP
function bindFormSubmit() {
  taskForm.onsubmit = async e => {
  e.preventDefault();

  // build a FormData with exactly the keys your PHP expects
  const data = new FormData();
  data.append('title',       document.getElementById('taskTitle').value);
  data.append('description', document.getElementById('taskDescription').value);
  data.append('due_date',    document.getElementById('taskDueDate').value);  // <-- match $_POST['due_date']
  data.append('priority',    document.getElementById('taskPriority').value);
  data.append('status',      document.getElementById('taskStatus').value);

  try {
    const res = await fetch('backend/add_task.php', {
      method: 'POST',
      body: data
    });
    const json = await res.json();

    if (json.success) {
      hideModal();
      fetchTasks();
    } else {
      alert(json.message || 'Failed to save task');
    }
  } catch (err) {
    console.error(err);
    alert('Unexpected error, check console');
  }
};

}

// Delete Task
async function removeTask(id) {
  if (!confirm('Are you sure?')) return;
  try {
    const res = await fetch('backend/delete_task.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`id=${encodeURIComponent(id)}`
    });
    const json = await res.json();
    if (json.success) fetchTasks();
    else alert('Delete failed');
  } catch (err) {
    console.error(err);
  }
}

// Edit Task
function beginEditTask(id) {
  const t = tasks.find(x => x.id === id);
  if (!t) return;
  document.getElementById('taskTitle').value       = t.title;
  document.getElementById('taskDescription').value = t.description;
  document.getElementById('taskDueDate').value     = t.dueDate;
  document.getElementById('taskPriority').value    = t.priority;
  document.getElementById('taskStatus').value      = t.status;
  taskForm.dataset.editId = id;
  showModal();
}

// Live Task
function startLiveTask(id) {
  const task = tasks.find(t=>t.id===id);
  if (!task) return;
  timerState.currentTask = task;
  timerState.isRunning = true;
  timerState.startTime = Date.now() - timerState.elapsedTime*1000;
  updateCurrentTaskDisplay();
  startTimer.click();
}

function updateCurrentTaskDisplay() {
  if (!timerState.currentTask) {
    currentTaskDisplay.innerHTML = '<p class="no-task-message">No task in progress</p>';
    return;
  }
  currentTaskDisplay.innerHTML = `
    <h3>${timerState.currentTask.title}</h3>
    <p>${timerState.currentTask.description||'No description'}</p>
    <div class="task-meta">
      <span class="task-priority priority-${timerState.currentTask.priority}">${timerState.currentTask.priority}</span>
      <span class="task-status">${timerState.currentTask.status}</span>
    </div>
  `;
}

function updateTimerDisplay() {
  const hrs = Math.floor(timerState.elapsedTime/3600);
  const mins= Math.floor((timerState.elapsedTime%3600)/60);
  const secs= timerState.elapsedTime%60;
  hoursDisplay.textContent   = String(hrs).padStart(2,'0');
  minutesDisplay.textContent = String(mins).padStart(2,'0');
  secondsDisplay.textContent = String(secs).padStart(2,'0');
}

function updateProgress() {
  if (!timerState.currentTask) return;
  const pct = Math.min(100, Math.floor((timerState.elapsedTime/3600)*100));
  taskProgress.style.width = pct+'%';
  progressText.textContent = pct+'%';
}

startTimer.onclick = () => {
  if (!timerState.isRunning) {
    timerState.isRunning = true;
    timerState.startTime = Date.now() - timerState.elapsedTime*1000;
    timerState.timerInterval = setInterval(() => {
      timerState.elapsedTime = Math.floor((Date.now() - timerState.startTime)/1000);
      updateTimerDisplay();
      updateProgress();
    }, 1000);
  }
};

pauseTimer.onclick = () => {
  if (timerState.isRunning) {
    timerState.isRunning = false;
    clearInterval(timerState.timerInterval);
  }
};

resetTimer.onclick = () => {
  timerState.isRunning = false;
  clearInterval(timerState.timerInterval);
  timerState.elapsedTime = 0;
  timerState.currentTask = null;
  updateTimerDisplay();
  updateCurrentTaskDisplay();
  taskProgress.style.width = '0%';
  progressText.textContent = '0%';
};

// Filters & Search
taskSearch.oninput     = e => { currentFilter.search = e.target.value; renderTasks(); };
statusFilter.onchange  = e => { currentFilter.status = e.target.value; renderTasks(); };
priorityFilter.onchange= e => { currentFilter.priority = e.target.value; renderTasks(); };

// Event Listeners
addTaskBtn.onclick = showModal;
closeModal.onclick = hideModal;
taskModal.onclick  = e => { if (e.target===taskModal) hideModal(); };

// Initialize
initTheme();
bindFormSubmit();
fetchTasks();
