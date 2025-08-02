<?php
/**
 * BitSync Email Client - Gmail Style Interface
 * Modern email interface for user dashboard
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/EmailClient.php';

$db = Database::getInstance();
$userManager = new User($db);
$emailClient = new EmailClient($db);

// Load user data
$userManager->loadById($_SESSION['user_id']);
$userData = $userManager->getUserData();

// Get email stats
$emailStats = $emailClient->getEmailStats($_SESSION['user_id']);

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'send_email':
                $toEmail = trim($_POST['to_email'] ?? '');
                $subject = trim($_POST['subject'] ?? '');
                $message = trim($_POST['message'] ?? '');
                
                if (empty($toEmail) || empty($subject) || empty($message)) {
                    throw new Exception('To email, subject, and message are required');
                }
                
                $result = $emailClient->sendEmail($_SESSION['user_id'], $toEmail, $subject, $message);
                
                if ($result['success']) {
                    $message = 'Email sent successfully';
                } else {
                    $error = $result['message'];
                }
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get emails for display
$inboxEmails = $emailClient->getReceivedEmails($userData['email'], 20);
$sentEmails = $emailClient->getSentEmails($_SESSION['user_id'], 20);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email - BitSync Workspace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .email-list-item:hover {
            background-color: #f8f9fa;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .email-list-item.selected {
            background-color: #e3f2fd;
            border-left: 3px solid #1976d2;
        }
        .compose-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .compose-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-gray-50 h-screen overflow-hidden">
    <!-- Top Navigation Bar -->
    <div class="bg-white shadow-sm border-b border-gray-200 h-16 flex items-center justify-between px-6">
        <div class="flex items-center space-x-4">
            <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-800 p-2">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <div class="flex items-center space-x-3">
                <i class="fas fa-envelope text-blue-600 text-xl"></i>
                <h1 class="text-xl font-semibold text-gray-900">BitSync Mail</h1>
            </div>
        </div>
        
        <div class="flex items-center space-x-4">
            <!-- Search Bar -->
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search mail" 
                       class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <!-- User Menu -->
            <div class="flex items-center space-x-3">
                <a href="dashboard.php" class="text-gray-600 hover:text-gray-800 p-2">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center">
                    <span class="text-white text-sm font-medium">
                        <?php echo strtoupper(substr($userData['first_name'] ?? $userData['username'], 0, 1)); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <?php if ($message): ?>
        <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="flex h-full">
        <!-- Sidebar -->
        <div id="sidebar" class="w-64 bg-white border-r border-gray-200 flex flex-col">
            <!-- Compose Button -->
            <div class="p-4">
                <button onclick="showCompose()" class="compose-btn w-full text-white px-6 py-3 rounded-lg font-medium flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Compose
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-4">
                <div class="space-y-1">
                    <button onclick="loadInbox()" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-inbox mr-3 text-gray-600"></i>
                            <span class="font-medium">Inbox</span>
                        </div>
                        <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1">
                            <?php echo $emailStats['unread']; ?>
                        </span>
                    </button>
                    
                    <button onclick="loadSent()" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 flex items-center">
                        <i class="fas fa-paper-plane mr-3 text-gray-600"></i>
                        <span class="font-medium">Sent</span>
                    </button>
                    
                    <button onclick="loadStarred()" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 flex items-center">
                        <i class="fas fa-star mr-3 text-gray-600"></i>
                        <span class="font-medium">Starred</span>
                    </button>
                    
                    <button onclick="loadDrafts()" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 flex items-center">
                        <i class="fas fa-file-alt mr-3 text-gray-600"></i>
                        <span class="font-medium">Drafts</span>
                    </button>
                    
                    <button onclick="loadTrash()" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 flex items-center">
                        <i class="fas fa-trash mr-3 text-gray-600"></i>
                        <span class="font-medium">Trash</span>
                    </button>
                </div>
                
                <!-- Labels -->
                <div class="mt-8">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">Labels</h3>
                    <div class="space-y-1">
                        <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 flex items-center">
                            <i class="fas fa-tag mr-3 text-red-500"></i>
                            <span class="text-sm">Important</span>
                        </button>
                        <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 flex items-center">
                            <i class="fas fa-tag mr-3 text-blue-500"></i>
                            <span class="text-sm">Work</span>
                        </button>
                        <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 flex items-center">
                            <i class="fas fa-tag mr-3 text-green-500"></i>
                            <span class="text-sm">Personal</span>
                        </button>
                    </div>
                </div>
            </nav>
            
            <!-- Storage Info -->
            <div class="p-4 border-t border-gray-200">
                <div class="text-xs text-gray-500 mb-2">Storage</div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 25%"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">2.5 GB of 10 GB used</div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- Email List -->
            <div id="emailList" class="flex-1 bg-white">
                <div class="border-b border-gray-200 px-6 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <input type="checkbox" class="rounded border-gray-300">
                            <button onclick="refreshEmails()" class="text-gray-600 hover:text-gray-800 p-1">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button onclick="loadInbox()" class="text-gray-600 hover:text-gray-800 p-1">
                                <i class="fas fa-inbox"></i>
                            </button>
                            <button onclick="loadSent()" class="text-gray-600 hover:text-gray-800 p-1">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500" id="emailCount">0 emails</span>
                        </div>
                    </div>
                </div>
                
                <div id="emailListContent" class="divide-y divide-gray-200">
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-spinner fa-spin text-2xl mb-4"></i>
                        <p>Loading emails...</p>
                    </div>
                </div>
            </div>

            <!-- Email View -->
            <div id="emailView" class="hidden flex-1 bg-white border-l border-gray-200">
                <div class="h-full flex flex-col">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900" id="emailSubject">Email</h3>
                            <div class="flex items-center space-x-2">
                                <button onclick="replyEmail()" class="text-gray-600 hover:text-gray-800 p-2">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <button onclick="forwardEmail()" class="text-gray-600 hover:text-gray-800 p-2">
                                    <i class="fas fa-share"></i>
                                </button>
                                <button onclick="deleteEmail()" class="text-gray-600 hover:text-red-600 p-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button onclick="closeEmailView()" class="text-gray-600 hover:text-gray-800 p-2">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="emailViewContent" class="flex-1 p-6 overflow-y-auto">
                        <!-- Email content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compose Email Modal -->
    <div id="composeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">New Message</h3>
                    <button onclick="closeCompose()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="composeForm" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="send_email">
                    
                    <div class="flex items-center border-b border-gray-300 py-2">
                        <label class="w-16 text-sm font-medium text-gray-700">To:</label>
                        <input type="email" id="to_email" name="to_email" required 
                               class="flex-1 border-none focus:ring-0 focus:outline-none"
                               placeholder="recipients">
                    </div>
                    
                    <div class="flex items-center border-b border-gray-300 py-2">
                        <label class="w-16 text-sm font-medium text-gray-700">Subject:</label>
                        <input type="text" id="subject" name="subject" required 
                               class="flex-1 border-none focus:ring-0 focus:outline-none"
                               placeholder="Subject">
                    </div>
                    
                    <div class="flex items-start border-b border-gray-300 py-2">
                        <label class="w-16 text-sm font-medium text-gray-700 mt-2">Message:</label>
                        <textarea id="message" name="message" rows="12" required 
                                  class="flex-1 border-none focus:ring-0 focus:outline-none resize-none"
                                  placeholder="Write your message here..."></textarea>
                    </div>
                    
                    <div class="flex justify-between items-center pt-4">
                        <div class="flex items-center space-x-2">
                            <button type="button" class="text-gray-600 hover:text-gray-800 p-2">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button type="button" class="text-gray-600 hover:text-gray-800 p-2">
                                <i class="fas fa-image"></i>
                            </button>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <button type="button" onclick="saveDraft()" 
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Save Draft
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                                <i class="fas fa-paper-plane mr-2"></i>Send
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentView = 'inbox';
        let currentEmails = [];
        let selectedEmailId = null;
        
        // Load emails on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadInbox();
        });
        
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        }
        
        // Show compose modal
        function showCompose() {
            document.getElementById('composeModal').classList.remove('hidden');
            document.getElementById('to_email').focus();
        }
        
        // Close compose modal
        function closeCompose() {
            document.getElementById('composeModal').classList.add('hidden');
            document.getElementById('composeForm').reset();
        }
        
        // Load inbox
        function loadInbox() {
            currentView = 'inbox';
            fetch('../email-api.php?action=inbox')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentEmails = data.data;
                        displayEmails(data.data);
                    }
                });
        }
        
        // Load sent emails
        function loadSent() {
            currentView = 'sent';
            fetch('../email-api.php?action=sent')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentEmails = data.data;
                        displayEmails(data.data);
                    }
                });
        }
        
        // Load starred emails
        function loadStarred() {
            currentView = 'starred';
            const starredEmails = currentEmails.filter(email => email.is_starred);
            displayEmails(starredEmails);
        }
        
        // Load drafts
        function loadDrafts() {
            currentView = 'drafts';
            displayEmails([]); // No drafts implemented yet
        }
        
        // Load trash
        function loadTrash() {
            currentView = 'trash';
            const trashEmails = currentEmails.filter(email => email.is_deleted);
            displayEmails(trashEmails);
        }
        
        // Display emails
        function displayEmails(emails) {
            const container = document.getElementById('emailListContent');
            document.getElementById('emailCount').textContent = `${emails.length} emails`;
            
            if (emails.length === 0) {
                container.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-4"></i>
                        <p class="text-lg font-medium">No emails found</p>
                        <p class="text-sm">Your ${currentView} is empty</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            emails.forEach(email => {
                const isUnread = !email.is_read;
                const timeAgo = getTimeAgo(email.created_at);
                const isSelected = selectedEmailId === email.id;
                
                html += `
                    <div class="email-list-item p-4 cursor-pointer ${isUnread ? 'bg-blue-50' : ''} ${isSelected ? 'selected' : ''}" 
                         onclick="selectEmail('${email.id}')">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" class="rounded border-gray-300" onclick="event.stopPropagation()">
                                <button onclick="toggleStar('${email.id}', event)" class="text-gray-400 hover:text-yellow-400">
                                    <i class="fas fa-star ${email.is_starred ? 'text-yellow-400' : ''}"></i>
                                </button>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium ${isUnread ? 'text-gray-900' : 'text-gray-700'}">
                                            ${email.from_user_id ? 'From User' : 'You'}
                                        </span>
                                        ${isUnread ? '<span class="w-2 h-2 bg-blue-500 rounded-full"></span>' : ''}
                                    </div>
                                    <span class="text-xs text-gray-500">${timeAgo}</span>
                                </div>
                                <p class="text-sm font-medium ${isUnread ? 'text-gray-900' : 'text-gray-700'} truncate">
                                    ${email.subject}
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    ${email.message.substring(0, 100)}...
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }
        
        // Select email
        function selectEmail(emailId) {
            selectedEmailId = emailId;
            document.querySelectorAll('.email-list-item').forEach(item => {
                item.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            // Show email view
            document.getElementById('emailList').classList.add('hidden');
            document.getElementById('emailView').classList.remove('hidden');
            
            // Load email content
            fetch(`../email-api.php?action=email&email_id=${emailId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const email = data.data;
                        document.getElementById('emailSubject').textContent = email.subject;
                        
                        const container = document.getElementById('emailViewContent');
                        container.innerHTML = `
                            <div class="space-y-6">
                                <div class="border-b border-gray-200 pb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h2 class="text-xl font-semibold text-gray-900">${email.subject}</h2>
                                        <div class="flex items-center space-x-2">
                                            <button onclick="replyEmail()" class="text-gray-600 hover:text-gray-800 p-2">
                                                <i class="fas fa-reply"></i>
                                            </button>
                                            <button onclick="forwardEmail()" class="text-gray-600 hover:text-gray-800 p-2">
                                                <i class="fas fa-share"></i>
                                            </button>
                                            <button onclick="deleteEmail()" class="text-gray-600 hover:text-red-600 p-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <span><strong>From:</strong> ${email.sender_name || 'Unknown'}</span>
                                        <span><strong>To:</strong> ${email.to_email}</span>
                                        <span><strong>Date:</strong> ${new Date(email.created_at).toLocaleString()}</span>
                                    </div>
                                </div>
                                
                                <div class="prose max-w-none">
                                    ${email.message.replace(/\n/g, '<br>')}
                                </div>
                            </div>
                        `;
                    }
                });
        }
        
        // Close email view
        function closeEmailView() {
            document.getElementById('emailView').classList.add('hidden');
            document.getElementById('emailList').classList.remove('hidden');
            selectedEmailId = null;
        }
        
        // Toggle star
        function toggleStar(emailId, event) {
            event.stopPropagation();
            // Implementation for starring emails
        }
        
        // Reply email
        function replyEmail() {
            // Implementation for reply
            alert('Reply functionality coming soon!');
        }
        
        // Forward email
        function forwardEmail() {
            // Implementation for forward
            alert('Forward functionality coming soon!');
        }
        
        // Delete email
        function deleteEmail() {
            if (selectedEmailId && confirm('Are you sure you want to delete this email?')) {
                fetch(`../email-api.php?action=delete&email_id=${selectedEmailId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeEmailView();
                        if (currentView === 'inbox') loadInbox();
                        else if (currentView === 'sent') loadSent();
                    }
                });
            }
        }
        
        // Save draft
        function saveDraft() {
            alert('Draft functionality coming soon!');
        }
        
        // Refresh emails
        function refreshEmails() {
            if (currentView === 'inbox') loadInbox();
            else if (currentView === 'sent') loadSent();
        }
        
        // Search emails
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.trim();
            if (query.length > 2) {
                fetch(`../email-api.php?action=search&q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayEmails(data.data);
                        }
                    });
            } else if (query.length === 0) {
                if (currentView === 'inbox') loadInbox();
                else if (currentView === 'sent') loadSent();
            }
        });
        
        // Get time ago
        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + 'm ago';
            if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + 'h ago';
            return Math.floor(diffInSeconds / 86400) + 'd ago';
        }
    </script>
</body>
</html> 