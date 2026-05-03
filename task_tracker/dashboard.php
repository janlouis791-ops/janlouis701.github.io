<?php 
include 'header.php'; 

$user_id = $_SESSION['user_id'];

// Get all tasks for user
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ?");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($tasks);
$completed = 0;
$upcoming = 0;
$overdue = 0;
$not_started = 0;
$in_progress = 0;

$today = date('Y-m-d');
$overdue_tasks = [];

foreach ($tasks as $task) {
    if ($task['status'] == 'Completed') $completed++;
    if ($task['status'] == 'Not Started') $not_started++;
    if ($task['status'] == 'In Progress') $in_progress++;
    
    if ($task['due_date'] < $today && $task['status'] != 'Completed') {
        $overdue++;
        $overdue_tasks[] = $task;
    }
    if ($task['due_date'] >= $today && $task['status'] != 'Completed') {
        $upcoming++;
    }
}

$completion_rate = $total > 0 ? round(($completed / $total) * 100) : 0;
?>

<div class="content">
    <div style="margin-bottom: 25px; padding: 20px 25px; background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 style="color: #333; font-size: 20px;">Welcome, <?= htmlspecialchars($_SESSION['fullname']) ?>! 👋</h2>
        <p style="color: #888; font-size: 13px; margin-top: 5px;">Here's an overview of your tasks.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card card-blue">
            <h3>Total Task</h3>
            <div class="count"><?= $total ?></div>
        </div>
        <div class="stat-card card-green">
            <h3>Completed</h3>
            <div class="count"><?= $completed ?></div>
        </div>
        <div class="stat-card card-orange">
            <h3>Upcoming</h3>
            <div class="count"><?= $upcoming ?></div>
        </div>
        <div class="stat-card card-red">
            <h3>Overdue</h3>
            <div class="count"><?= $overdue ?></div>
        </div>
    </div>

    <div class="dashboard-details">
        <div class="detail-card">
            <h4>Completion Rate</h4>
            <div style="font-size: 36px; color: #1a73e8; font-weight: bold; margin-bottom: 10px;"><?= $completion_rate ?>%</div>
            <div style="width: 100%; height: 10px; background: #eee; border-radius: 5px; margin-bottom: 20px;">
                <div style="width: <?= $completion_rate ?>%; height: 100%; background: #1a73e8; border-radius: 5px;"></div>
            </div>
            
            <h4 style="margin-top: 30px;">Progress Overview</h4>
            <p style="font-size: 12px; color: #888;">This gauge shows your overall completion rate based on all the tasks you have added to your account.</p>
        </div>
        <div class="detail-card">
            <h4>Tasks by Status</h4>
            <ul style="list-style: none; padding: 0; line-height: 2;">
                <li><span style="display:inline-block; width:10px; height:10px; background:#4285f4; border-radius:50%; margin-right:10px;"></span>Not Started: <?= $not_started ?></li>
                <li><span style="display:inline-block; width:10px; height:10px; background:#fa7b17; border-radius:50%; margin-right:10px;"></span>In Progress: <?= $in_progress ?></li>
                <li><span style="display:inline-block; width:10px; height:10px; background:#34a853; border-radius:50%; margin-right:10px;"></span>Completed: <?= $completed ?></li>
            </ul>

            <h4 style="margin-top: 30px;">Overdue Tasks</h4>
            <?php if (empty($overdue_tasks)): ?>
                <div class="empty-state" style="padding: 20px;">
                    <i class="fa fa-check-circle" style="font-size: 40px; color: #34a853; margin-bottom: 10px; display:block;"></i>
                    <p>No Overdue Task!</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php foreach ($overdue_tasks as $otask): ?>
                        <div style="border-left: 3px solid #ea4335; padding: 10px; background: #fff8f8; font-size: 13px;">
                            <strong><?= htmlspecialchars($otask['title']) ?></strong> (Due: <?= htmlspecialchars($otask['due_date']) ?>)
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
