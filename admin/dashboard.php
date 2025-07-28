<?php
session_start();
require_once 'config.php';

// Check authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Get current page
$current_page = $_GET['page'] ?? 'home';
$content = loadContent($current_page) ?? $content_structure[$current_page] ?? [];

// Handle content updates
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_content') {
        $page = $_POST['page'];
        $data = $_POST['content'];
        
        // Convert array data properly
        if (is_array($data)) {
            $processed_data = [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $processed_data[$key] = array_filter($value, function($item) {
                        return !empty($item);
                    });
                } else {
                    $processed_data[$key] = $value;
                }
            }
            $data = $processed_data;
        }
        
        if (saveContent($page, $data)) {
            $success_message = "Content updated successfully!";
            $content = loadContent($current_page) ?? $content_structure[$current_page] ?? [];
        } else {
            $error_message = "Failed to update content.";
        }
    }
}

$all_content = getAllContent();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitSync Admin - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-gray-900">BitSync Admin</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <a href="?logout=1" class="text-red-600 hover:text-red-800 text-sm font-medium">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Messages -->
        <?php if (isset($success_message)): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Navigation Tabs -->
        <div class="border-b border-gray-200 mb-8">
            <nav class="-mb-px flex space-x-8">
                <?php
                $pages = ['home', 'about', 'services', 'contact', 'newsletter'];
                foreach ($pages as $page):
                    $is_active = $current_page === $page;
                ?>
                    <a href="?page=<?php echo $page; ?>" 
                       class="<?php echo $is_active ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                        <?php echo $page === 'newsletter' ? 'Newsletter Subscribers' : ucfirst($page); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Content Editor -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Edit <?php echo ucfirst($current_page); ?> Content</h2>
                <p class="text-sm text-gray-600 mt-1">Update the content for the <?php echo $current_page; ?> page</p>
            </div>

            <form method="POST" class="p-6">
                <input type="hidden" name="action" value="update_content">
                <input type="hidden" name="page" value="<?php echo $current_page; ?>">

                <?php if ($current_page === 'home'): ?>
                    <!-- Home Page Content -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Title</label>
                            <input type="text" name="content[hero_title]" value="<?php echo htmlspecialchars($content['hero_title'] ?? ''); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Subtitle</label>
                            <input type="text" name="content[hero_subtitle]" value="<?php echo htmlspecialchars($content['hero_subtitle'] ?? ''); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Description</label>
                            <textarea name="content[hero_description]" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($content['hero_description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="border-t pt-6">
                            <h3 class="text-md font-medium text-gray-900 mb-4">Statistics</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Clients</label>
                                    <input type="text" name="content[stats][clients]" value="<?php echo htmlspecialchars($content['stats']['clients'] ?? ''); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Countries</label>
                                    <input type="text" name="content[stats][countries]" value="<?php echo htmlspecialchars($content['stats']['countries'] ?? ''); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Projects</label>
                                    <input type="text" name="content[stats][projects]" value="<?php echo htmlspecialchars($content['stats']['projects'] ?? ''); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Support</label>
                                    <input type="text" name="content[stats][support]" value="<?php echo htmlspecialchars($content['stats']['support'] ?? ''); ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($current_page === 'about'): ?>
                    <!-- About Page Content -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mission</label>
                            <textarea name="content[mission]" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($content['mission'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vision</label>
                            <textarea name="content[vision]" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($content['vision'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="border-t pt-6">
                            <h3 class="text-md font-medium text-gray-900 mb-4">Values</h3>
                            <div class="space-y-4">
                                <?php
                                $values = $content['values'] ?? ['Innovation' => '', 'Excellence' => '', 'Integrity' => '', 'Collaboration' => ''];
                                foreach ($values as $key => $value):
                                ?>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo $key; ?></label>
                                        <input type="text" name="content[values][<?php echo $key; ?>]" value="<?php echo htmlspecialchars($value); ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                <?php elseif ($current_page === 'contact'): ?>
                    <!-- Contact Page Content -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="content[email]" value="<?php echo htmlspecialchars($content['email'] ?? ''); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="text" name="content[phone]" value="<?php echo htmlspecialchars($content['phone'] ?? ''); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="content[address]" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($content['address'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Hours</label>
                            <input type="text" name="content[hours]" value="<?php echo htmlspecialchars($content['hours'] ?? ''); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Services Page Content -->
                    <div class="space-y-8">
                        <?php
                        $services = $content['services'] ?? $content_structure['services'];
                        foreach ($services as $service_key => $service):
                        ?>
                            <div class="border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4"><?php echo ucwords(str_replace('_', ' ', $service_key)); ?></h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                        <input type="text" name="content[services][<?php echo $service_key; ?>][title]" 
                                               value="<?php echo htmlspecialchars($service['title'] ?? ''); ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea name="content[services][<?php echo $service_key; ?>][description]" rows="3" 
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($service['description'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Features (comma-separated)</label>
                                        <input type="text" name="content[services][<?php echo $service_key; ?>][features]" 
                                               value="<?php echo htmlspecialchars(is_array($service['features'] ?? '') ? implode(', ', $service['features']) : ($service['features'] ?? '')); ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="React, Vue.js, Node.js">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($current_page === 'newsletter'): ?>
                    <!-- Newsletter Subscribers -->
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Newsletter Subscribers</h3>
                            <div class="flex space-x-2">
                                <button type="button" onclick="exportSubscribers()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                                    Export CSV
                                </button>
                                <button type="button" onclick="refreshSubscribers()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                                    Refresh
                                </button>
                            </div>
                        </div>
                        
                        <div id="subscribersList" class="bg-gray-50 rounded-lg p-4">
                            <div class="text-center text-gray-500">Loading subscribers...</div>
                        </div>
                    </div>

                    <script>
                        // Load subscribers on page load
                        document.addEventListener('DOMContentLoaded', function() {
                            loadSubscribers();
                        });

                        function loadSubscribers() {
                            fetch('../newsletter-subscribers.php?action=list&admin_key=bitsync2024')
                                .then(response => response.json())
                                .then(data => {
                                    const container = document.getElementById('subscribersList');
                                    if (data.success && data.subscribers.length > 0) {
                                        container.innerHTML = `
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-100">
                                                        <tr>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subscribed</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        ${data.subscribers.map(subscriber => `
                                                            <tr>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${subscriber.email}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${subscriber.subscribed_at}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${subscriber.ip_address}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    <button onclick="deleteSubscriber('${subscriber.email}')" class="text-red-600 hover:text-red-900">Delete</button>
                                                                </td>
                                                            </tr>
                                                        `).join('')}
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-4 text-sm text-gray-600">
                                                Total subscribers: ${data.subscribers.length}
                                            </div>
                                        `;
                                    } else {
                                        container.innerHTML = '<div class="text-center text-gray-500">No subscribers found.</div>';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading subscribers:', error);
                                    document.getElementById('subscribersList').innerHTML = '<div class="text-center text-red-500">Error loading subscribers.</div>';
                                });
                        }

                        function deleteSubscriber(email) {
                            if (confirm('Are you sure you want to delete this subscriber?')) {
                                fetch('../newsletter-subscribers.php?action=delete&admin_key=bitsync2024', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({ email: email })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        loadSubscribers();
                                    } else {
                                        alert('Error deleting subscriber: ' + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Error deleting subscriber.');
                                });
                            }
                        }

                        function exportSubscribers() {
                            window.open('../newsletter-subscribers.php?action=export&admin_key=bitsync2024', '_blank');
                        }

                        function refreshSubscribers() {
                            loadSubscribers();
                        }
                    </script>
                <?php endif; ?>

                <!-- Submit Button -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                        Save Changes
                    </button>
                    <a href="../" target="_blank" class="ml-4 text-gray-600 hover:text-gray-800 text-sm">
                        View Site →
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-save functionality
        let autoSaveTimer;
        const form = document.querySelector('form');
        
        form.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Show auto-save indicator
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Auto-saving...';
                submitBtn.disabled = true;
                
                // Simulate auto-save (you can implement actual auto-save here)
                setTimeout(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }, 1000);
            }, 2000);
        });
    </script>
</body>
</html> 