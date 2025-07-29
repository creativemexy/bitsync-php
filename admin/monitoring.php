<?php
/**
 * BitSync Admin - Monitoring Dashboard
 * Real-time system health, analytics, and performance monitoring
 */

session_start();

// Check authentication
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

require_once '../includes/Monitoring.php';
require_once '../includes/ContentManager.php';

$monitoring = new Monitoring();
$contentManager = new ContentManager();

// Get monitoring data
$systemHealth = $monitoring->getSystemHealth();
$analytics24h = $monitoring->getAnalytics('24h');
$analytics7d = $monitoring->getAnalytics('7d');

// Get period from query parameter
$period = $_GET['period'] ?? '24h';
$analytics = $monitoring->getAnalytics($period);

$pageTitle = "System Monitoring - BitSync Admin";
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-gray-900">BitSync Admin</h1>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="index.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="pages.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Content</a>
                            <a href="subscribers.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Subscribers</a>
                            <a href="contacts.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Contacts</a>
                            <a href="monitoring.php" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-md text-sm font-medium">Monitoring</a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <a href="logout.php" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="py-10">
            <header>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">System Monitoring</h1>
                    <p class="mt-2 text-gray-600">Real-time system health, analytics, and performance metrics</p>
                </div>
            </header>
            <main>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    
                    <!-- Period Selector -->
                    <div class="mb-6">
                        <div class="flex space-x-2">
                            <a href="?period=1h" class="px-4 py-2 text-sm font-medium rounded-md <?php echo $period === '1h' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'; ?>">Last Hour</a>
                            <a href="?period=24h" class="px-4 py-2 text-sm font-medium rounded-md <?php echo $period === '24h' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'; ?>">Last 24 Hours</a>
                            <a href="?period=7d" class="px-4 py-2 text-sm font-medium rounded-md <?php echo $period === '7d' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'; ?>">Last 7 Days</a>
                            <a href="?period=30d" class="px-4 py-2 text-sm font-medium rounded-md <?php echo $period === '30d' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'; ?>">Last 30 Days</a>
                        </div>
                    </div>

                    <!-- System Health Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Database Health -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-database text-2xl <?php echo $systemHealth['database']['connected'] ? 'text-green-500' : 'text-red-500'; ?>"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Database</dt>
                                            <dd class="text-lg font-medium text-gray-900">
                                                <?php echo $systemHealth['database']['status']; ?>
                                            </dd>
                                            <?php if ($systemHealth['database']['connected']): ?>
                                            <dd class="text-sm text-gray-500">
                                                <?php echo $systemHealth['database']['response_time']; ?>ms
                                            </dd>
                                            <?php endif; ?>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Disk Space -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-hdd text-2xl text-blue-500"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Disk Space</dt>
                                            <dd class="text-lg font-medium text-gray-900">
                                                <?php echo $systemHealth['disk_space']['percentage']; ?>%
                                            </dd>
                                            <dd class="text-sm text-gray-500">
                                                <?php echo $systemHealth['disk_space']['used']; ?> / <?php echo $systemHealth['disk_space']['total']; ?>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Memory Usage -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-memory text-2xl text-purple-500"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Memory</dt>
                                            <dd class="text-lg font-medium text-gray-900">
                                                <?php echo $systemHealth['memory_usage']['current']; ?>
                                            </dd>
                                            <dd class="text-sm text-gray-500">
                                                Peak: <?php echo $systemHealth['memory_usage']['peak']; ?>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Users -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-users text-2xl text-green-500"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                                            <dd class="text-lg font-medium text-gray-900">
                                                <?php echo $systemHealth['active_users']; ?>
                                            </dd>
                                            <dd class="text-sm text-gray-500">Last 15 minutes</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analytics Overview -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Page Views Chart -->
                        <div class="bg-white shadow rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Page Views</h3>
                            <canvas id="pageViewsChart" width="400" height="200"></canvas>
                        </div>

                        <!-- Performance Metrics -->
                        <div class="bg-white shadow rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Metrics</h3>
                            <div class="space-y-4">
                                <?php foreach ($analytics['performance'] as $metric): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700"><?php echo ucfirst(str_replace('_', ' ', $metric['metric_name'])); ?></span>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900"><?php echo round($metric['avg_value'], 2); ?>ms</div>
                                        <div class="text-xs text-gray-500">Max: <?php echo round($metric['max_value'], 2); ?>ms</div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-eye text-2xl text-blue-500"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Page Views</dt>
                                            <dd class="text-lg font-medium text-gray-900"><?php echo number_format($analytics['page_views']); ?></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-user text-2xl text-green-500"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Unique Visitors</dt>
                                            <dd class="text-lg font-medium text-gray-900"><?php echo number_format($analytics['unique_visitors']); ?></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-envelope text-2xl text-purple-500"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Form Submissions</dt>
                                            <dd class="text-lg font-medium text-gray-900"><?php echo number_format($analytics['form_submissions']); ?></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Errors</dt>
                                            <dd class="text-lg font-medium text-gray-900"><?php echo number_format($analytics['errors']); ?></dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Popular Pages -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Popular Pages</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php 
                                        $totalViews = array_sum(array_column($analytics['popular_pages'], 'views'));
                                        foreach ($analytics['popular_pages'] as $page): 
                                            $percentage = $totalViews > 0 ? ($page['views'] / $totalViews) * 100 : 0;
                                        ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($page['page']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo number_format($page['views']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php echo round($percentage, 1); ?>%
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <script>
        // Page Views Chart
        const ctx = document.getElementById('pageViewsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                datasets: [{
                    label: 'Page Views',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    fill: false,
                    borderColor: 'rgb(59, 130, 246)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Auto-refresh every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html> 