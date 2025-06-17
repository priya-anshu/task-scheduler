<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Task Scheduler Dashboard</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <header>
    <h1><i class="fa-solid fa-calendar-check"></i> Task Scheduler</h1>
    <div class="header-actions">
      <button id="addTaskBtn" class="add-task-btn"><i class="fa-solid fa-plus"></i> Add Task</button>
      <a href="login.php" class="auth-btn">Login</a>
      <a href="register.php" class="auth-btn">Register</a>
      <a href="cpu-schedule.php" class="auth-btn">CPU Scheduling</a>
      <button id="themeToggle" title="Toggle Light/Dark Mode">
        <i class="fa-solid fa-moon"></i>
      </button>
    </div>
  </header>

  <main>
    <section id="dashboard" class="dashboard-section">
      <div class="dashboard-top">
        <div class="dashboard-greeting">
          <h2>Hey, Buddy! 👋</h2>
          <p class="quote" id="motivationQuote">
            "Success is the sum of small efforts, repeated day in and day out." <br>— Robert Collier
          </p>
        </div>
        <div class="dashboard-stats">
          <div class="stat-card">
            <span class="stat-num" id="todayTasks">0</span>
            <span class="stat-label">Today</span>
          </div>
          <div class="stat-card">
            <span class="stat-num" id="completedTasks">0</span>
            <span class="stat-label">Completed</span>
          </div>
          <div class="stat-card">
            <span class="stat-num" id="pendingTasks">0</span>
            <span class="stat-label">Pending</span>
          </div>
        </div>
      </div>
      <div class="dashboard-chart">
        <canvas id="taskPieChart"></canvas>
      </div>
    </section>

    

    <section id="tasks" class="tasks-section">
      <div class="tasks-header">
        <h2>All Tasks</h2>
        <div class="tasks-filters">
          <input type="text" id="taskSearch" placeholder="Search tasks..." class="search-input">
          <select id="statusFilter" class="filter-select">
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="in-progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
          <select id="priorityFilter" class="filter-select">
            <option value="all">All Priorities</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
      </div>
      <div class="tasks-container" id="tasksContainer">
        <!-- Tasks will be dynamically added here -->
      </div>
    </section>

    <section id="stats" class="stats-section">
      <h2>Task Statistics</h2>
      <div class="stats-grid">
        <div class="stats-card">
          <canvas id="completionChart"></canvas>
        </div>
        <div class="stats-card">
          <canvas id="priorityChart"></canvas>
        </div>
      </div>
    </section>
  </main>

  <!-- Task Modal -->
  <div id="taskModal" class="modal hidden">
    <div class="modal-content">
      <span id="closeModal" class="close">&times;</span>
      <h2>Add New Task</h2>
      <form id="taskForm">
        <div class="form-group">
          <label for="taskTitle">Title</label>
          <input type="text" id="taskTitle" required placeholder="Enter task title">
        </div>
        <div class="form-group">
          <label for="taskDescription">Description</label>
          <textarea id="taskDescription" placeholder="Enter task description"></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="taskDueDate">Due Date</label>
            <input type="date" id="taskDueDate" required>
          </div>
          <div class="form-group">
            <label for="taskPriority">Priority</label>
            <select id="taskPriority" required>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="taskStatus">Status</label>
          <select id="taskStatus" required>
            <option value="pending">Pending</option>
            <option value="in-progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="form-actions">
          <button type="button" class="cancel-btn">Cancel</button>
          <button type="submit" class="submit-btn">Save Task</button>
        </div>
      </form>
    </div>
  </div>

  <section id="liveTask" class="live-task-section">
    <div class="live-task-container">
      <div class="live-task-header">
        <h2><i class="fa-solid fa-clock"></i> Live Task</h2>
        <div class="timer-display">
          <span id="hours">00</span>:<span id="minutes">00</span>:<span id="seconds">00</span>
        </div>
      </div>
      <div class="live-task-content">
        <div class="current-task" id="currentTask">
          <p class="no-task-message">No task in progress</p>
        </div>
        <div class="timer-controls">
          <button id="startTimer" class="timer-btn" title="Start Timer">
            <i class="fa-solid fa-play"></i>
          </button>
          <button id="pauseTimer" class="timer-btn" title="Pause Timer">
            <i class="fa-solid fa-pause"></i>
          </button>
          <button id="resetTimer" class="timer-btn" title="Reset Timer">
            <i class="fa-solid fa-rotate"></i>
          </button>
        </div>
        <div class="progress-container">
          <div class="progress-bar">
            <div class="progress" id="taskProgress"></div>
          </div>
          <span class="progress-text" id="progressText">0%</span>
        </div>
      </div>
    </div>
  </section>

  <script src="assets/script.js"></script>
</body>
</html>
