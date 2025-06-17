<?php
session_start();
require_once 'backend/config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
$userId = $_SESSION['user_id'];

// ——— Fetch tasks from DB ———
function fetchProcesses($conn, $userId) {
  $stmt = $conn->prepare("
    SELECT title, description, priority 
    FROM tasks 
    WHERE user_id = ? 
    ORDER BY created_at ASC
  ");
  $stmt->bind_param('i', $userId);
  $stmt->execute();
  $res = $stmt->get_result();
  $procs = [];
  while ($r = $res->fetch_assoc()) {
    $burst = strlen(trim($r['description'])) ?: 5;
    $prio  = $r['priority'] === 'high'   ? 1
           : ($r['priority'] === 'medium' ? 2 : 3);
    $procs[] = [
      'title'    => $r['title'],
      'burst'    => $burst,
      'priority' => $prio,
    ];
  }
  return $procs;
}

// ——— Scheduling Algorithms ———
function fcfs(array $ps) {
  $time = 0;
  foreach ($ps as &$p) {
    $p['waiting']    = $time;
    $p['turnaround'] = $time + $p['burst'];
    $time += $p['burst'];
  }
  return $ps;
}

function priorityScheduling(array $ps) {
  usort($ps, fn($a, $b) => $a['priority'] <=> $b['priority']);
  $time = 0;
  foreach ($ps as &$p) {
    $p['waiting']    = $time;
    $p['turnaround'] = $time + $p['burst'];
    $time += $p['burst'];
  }
  return $ps;
}

function roundRobin(array $ps, int $quantum) {
  foreach ($ps as &$p) {
    $p['remaining']  = $p['burst'];
    $p['waiting']    = 0;
    $p['turnaround'] = 0;
  }
  $n = count($ps);
  $time = 0;
  $finished = 0;

  while ($finished < $n) {
    foreach ($ps as &$p) {
      if ($p['remaining'] > 0) {
        if ($p['remaining'] > $quantum) {
          $time += $quantum;
          $p['remaining'] -= $quantum;
        } else {
          $time += $p['remaining'];
          $p['remaining'] = 0;
          $p['waiting']    = $time - $p['burst'];
          $p['turnaround'] = $time;
          $finished++;
        }
      }
    }
  }

  return array_map(fn($p) => [
    'title'      => $p['title'],
    'burst'      => $p['burst'],
    'priority'   => $p['priority'],
    'waiting'    => $p['waiting'],
    'turnaround' => $p['turnaround']
  ], $ps);
}

// ——— Handle form submission ———
$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $algo    = $_POST['algorithm'] ?? 'fcfs';
  $quantum = max(1, intval($_POST['quantum'] ?? 2));

  $processes = fetchProcesses($conn, $userId);
  switch ($algo) {
    case 'priority':
      $results = priorityScheduling($processes);
      break;
    case 'rr':
      $results = roundRobin($processes, $quantum);
      break;
    default:
      $results = fcfs($processes);
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CPU Scheduling Simulator - Task Scheduler</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <header>
    <h1><i class="fa-solid fa-microchip"></i> CPU Scheduling Simulator</h1>
    <div class="header-actions">
      <a href="index.php" class="auth-btn">Back to Dashboard</a>
      <button id="themeToggle" title="Toggle Light/Dark Mode">
        <i class="fa-solid fa-moon"></i>
      </button>
    </div>
  </header>
  <main>
    <section class="dashboard-section">
      <h2>Simulate Classic CPU Scheduling Algorithms</h2>
      <p>Use your current tasks as processes. Select an algorithm and see the waiting and turnaround times for each task.</p>

      <form method="POST"
            action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
            class="form-row"
            style="margin-bottom:2rem;">
        <div class="form-group">
          <label for="algorithmSelect">Select Algorithm</label>
          <select id="algorithmSelect" name="algorithm" required>
            <option value="fcfs"     <?= ($_POST['algorithm'] ?? '')==='fcfs'    ? 'selected':'' ?>>First Come First Serve (FCFS)</option>
            <option value="priority" <?= ($_POST['algorithm'] ?? '')==='priority'? 'selected':'' ?>>Priority Scheduling</option>
            <option value="rr"       <?= ($_POST['algorithm'] ?? '')==='rr'      ? 'selected':'' ?>>Round Robin</option>
          </select>
        </div>
        <div class="form-group" id="quantumGroup" style="display:<?= (($_POST['algorithm'] ?? '')==='rr' ? '' : 'none') ?>;">
          <label for="quantumInput">Time Quantum (for Round Robin)</label>
          <input type="number"
                 id="quantumInput"
                 name="quantum"
                 min="1"
                 value="<?= htmlspecialchars($_POST['quantum'] ?? 2) ?>"
                 style="width:100px;">
        </div>
        <div class="form-group" style="align-self:flex-end;">
          <button type="submit"
                  id="runSimulation"
                  class="add-task-btn"
                  style="margin-top:1.5rem;">
            Run Simulation
          </button>
        </div>
      </form>

      <div id="resultsSection">
        <h3>Results</h3>
        <div class="tasks-container">
          <table id="resultsTable" class="stats-card" style="width:100%;margin-top:1rem;">
            <thead>
              <tr>
                <th>Task Title</th>
                <th>Burst Time</th>
                <th>Priority</th>
                <th>Waiting Time</th>
                <th>Turnaround Time</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($results)): ?>
                <tr><td colspan="5" style="text-align:center;">Run a simulation to see results.</td></tr>
              <?php else: ?>
                <?php foreach ($results as $p): ?>
                  <tr>
                    <td><?= htmlspecialchars($p['title'])      ?></td>
                    <td><?= htmlspecialchars($p['burst'])      ?></td>
                    <td><?= htmlspecialchars($p['priority'])   ?></td>
                    <td><?= htmlspecialchars($p['waiting'])    ?></td>
                    <td><?= htmlspecialchars($p['turnaround']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <script src="assets/cpu-schedule.js"></script>
</body>
</html>
