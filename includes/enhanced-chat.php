<?php
// Enhanced Live Chat System for Better Customer Engagement
// This file contains the enhanced chat functionality

class EnhancedChat {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Save chat message to database
    public function saveMessage($userId, $message, $type = 'user', $sessionId = null) {
        if (!$sessionId) {
            $sessionId = session_id();
        }
        
        return $this->db->insert('chat_messages', [
            'session_id' => $sessionId,
            'user_id' => $userId,
            'message' => $message,
            'message_type' => $type,
            'timestamp' => date('Y-m-d H:i:s'),
            'is_read' => false
        ]);
    }
    
    // Get chat history
    public function getChatHistory($sessionId, $limit = 50) {
        return $this->db->fetchAll(
            "SELECT * FROM chat_messages WHERE session_id = ? ORDER BY timestamp DESC LIMIT ?",
            [$sessionId, $limit]
        );
    }
    
    // Mark messages as read
    public function markAsRead($sessionId) {
        return $this->db->query(
            "UPDATE chat_messages SET is_read = true WHERE session_id = ? AND message_type = 'user'",
            [$sessionId]
        );
    }
    
    // Get unread message count
    public function getUnreadCount($sessionId) {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM chat_messages WHERE session_id = ? AND message_type = 'user' AND is_read = false",
            [$sessionId]
        );
        return $result['count'] ?? 0;
    }
}

// Initialize chat system
$enhancedChat = new EnhancedChat();
?>

<!-- Enhanced Live Chat System -->
<div class="fixed bottom-8 right-8 z-50" id="enhancedChatContainer">
    <!-- Chat Toggle Button with Notification Badge -->
    <button id="enhancedChatToggle" class="floating-action-btn w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full shadow-2xl hover:shadow-blue-500/25 transition-all duration-300 hover:scale-110 flex items-center justify-center text-white group relative">
        <svg id="enhancedChatIcon" class="w-7 h-7 transition-transform duration-300 group-hover:rotate-45" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
        </svg>
        <svg id="enhancedCloseIcon" class="w-7 h-7 hidden transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
        
        <!-- Online Status Indicator -->
        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white animate-pulse"></div>
        
        <!-- Notification Badge -->
        <div id="chatNotificationBadge" class="absolute -top-2 -left-2 w-6 h-6 bg-red-500 text-white text-xs rounded-full flex items-center justify-center hidden">
            <span id="notificationCount">0</span>
        </div>
    </button>

    <!-- Enhanced Chat Window -->
    <div id="enhancedChatWindow" class="hidden absolute bottom-20 right-0 w-96 h-[500px] bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 flex flex-col">
        <!-- Enhanced Chat Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 rounded-t-2xl flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold">Live Chat Support</h3>
                    <p class="text-xs text-blue-100">Online • Responds in seconds</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <!-- Chat History Button -->
                <button id="chatHistoryBtn" class="text-white/80 hover:text-white transition-colors p-1" title="Chat History">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <!-- Minimize Button -->
                <button id="enhancedMinimizeChat" class="text-white/80 hover:text-white transition-colors p-1" title="Minimize">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Enhanced Chat Messages -->
        <div id="enhancedChatMessages" class="flex-1 p-4 overflow-y-auto space-y-4 max-h-80">
            <!-- Welcome Message -->
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="bg-slate-100 dark:bg-slate-700 rounded-2xl px-4 py-2 max-w-xs">
                    <p class="text-sm text-slate-700 dark:text-slate-300">👋 Hi! Welcome to BitSync Group. How can I help you today?</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Just now</p>
                </div>
            </div>

            <!-- Enhanced Quick Response Options -->
            <div class="flex flex-wrap gap-2">
                <button class="enhanced-quick-response bg-blue-50 dark:bg-slate-700 text-blue-600 dark:text-blue-400 px-3 py-1 rounded-full text-xs hover:bg-blue-100 dark:hover:bg-slate-600 transition-colors" data-message="I need help with web development">
                    Web Development
                </button>
                <button class="enhanced-quick-response bg-blue-50 dark:bg-slate-700 text-blue-600 dark:text-blue-400 px-3 py-1 rounded-full text-xs hover:bg-blue-100 dark:hover:bg-slate-600 transition-colors" data-message="Tell me about your services">
                    Our Services
                </button>
                <button class="enhanced-quick-response bg-blue-50 dark:bg-slate-700 text-blue-600 dark:text-blue-400 px-3 py-1 rounded-full text-xs hover:bg-blue-100 dark:hover:bg-slate-600 transition-colors" data-message="I want to get a quote">
                    Get Quote
                </button>
                <button class="enhanced-quick-response bg-blue-50 dark:bg-slate-700 text-blue-600 dark:text-blue-400 px-3 py-1 rounded-full text-xs hover:bg-blue-100 dark:hover:bg-slate-600 transition-colors" data-message="Schedule a consultation">
                    Schedule Call
                </button>
            </div>
        </div>

        <!-- Enhanced Chat Input -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-700">
            <!-- File Upload Area -->
            <div id="fileUploadArea" class="mb-3 p-3 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg text-center hidden">
                <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <p class="text-sm text-slate-600 dark:text-slate-400">Drop files here or click to upload</p>
                <input type="file" id="fileInput" class="hidden" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
            </div>
            
            <!-- Typing Indicator -->
            <div id="typingIndicator" class="hidden mb-3 flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="bg-slate-100 dark:bg-slate-700 rounded-2xl px-4 py-2">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
            
            <form id="enhancedChatForm" class="flex space-x-2">
                <!-- File Upload Button -->
                <button type="button" id="fileUploadBtn" class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 px-3 py-2 rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                
                <!-- Message Input -->
                <input type="text" id="enhancedChatInput" placeholder="Type your message..." class="flex-1 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white px-3 py-2 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border border-slate-200 dark:border-slate-600" maxlength="1000">
                
                <!-- Send Button -->
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 flex items-center">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </form>
            
            <!-- Character Counter -->
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2 text-right">
                <span id="charCounter">0</span>/1000
            </div>
        </div>
    </div>
</div>

<!-- Chat History Modal -->
<div id="chatHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold">Chat History</h3>
                <button id="closeHistoryModal" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            <div id="chatHistoryContent" class="p-4 overflow-y-auto max-h-96">
                <!-- Chat history will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Enhanced Live Chat System
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('enhancedChatToggle');
    const chatWindow = document.getElementById('enhancedChatWindow');
    const chatIcon = document.getElementById('enhancedChatIcon');
    const closeIcon = document.getElementById('enhancedCloseIcon');
    const minimizeChat = document.getElementById('enhancedMinimizeChat');
    const chatForm = document.getElementById('enhancedChatForm');
    const chatInput = document.getElementById('enhancedChatInput');
    const chatMessages = document.getElementById('enhancedChatMessages');
    const quickResponses = document.querySelectorAll('.enhanced-quick-response');
    const fileUploadBtn = document.getElementById('fileUploadBtn');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('fileInput');
    const typingIndicator = document.getElementById('typingIndicator');
    const charCounter = document.getElementById('charCounter');
    const notificationBadge = document.getElementById('chatNotificationBadge');
    const notificationCount = document.getElementById('notificationCount');
    const chatHistoryBtn = document.getElementById('chatHistoryBtn');
    const chatHistoryModal = document.getElementById('chatHistoryModal');
    const closeHistoryModal = document.getElementById('closeHistoryModal');
    const chatHistoryContent = document.getElementById('chatHistoryContent');

    let isChatOpen = false;
    let messageCount = 0;
    let unreadCount = 0;
    let typingTimeout;

    // Chat toggle functionality
    if (chatToggle) {
        chatToggle.addEventListener('click', () => {
            isChatOpen = !isChatOpen;
            if (isChatOpen) {
                chatWindow.classList.remove('hidden');
                chatIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
                chatInput.focus();
                hideNotificationBadge();
            } else {
                chatWindow.classList.add('hidden');
                chatIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }
        });
    }

    // Minimize chat
    if (minimizeChat) {
        minimizeChat.addEventListener('click', () => {
            isChatOpen = false;
            chatWindow.classList.add('hidden');
            chatIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        });
    }

    // Character counter
    if (chatInput) {
        chatInput.addEventListener('input', () => {
            const length = chatInput.value.length;
            charCounter.textContent = length;
            
            if (length > 800) {
                charCounter.classList.add('text-red-500');
            } else {
                charCounter.classList.remove('text-red-500');
            }
        });
    }

    // File upload functionality
    if (fileUploadBtn) {
        fileUploadBtn.addEventListener('click', () => {
            fileInput.click();
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', handleFileUpload);
    }

    // Drag and drop file upload
    if (fileUploadArea) {
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('border-blue-500', 'bg-blue-50');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('border-blue-500', 'bg-blue-50');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('border-blue-500', 'bg-blue-50');
            const files = e.dataTransfer.files;
            handleFiles(files);
        });
    }

    // Quick response buttons
    quickResponses.forEach(button => {
        button.addEventListener('click', () => {
            const message = button.getAttribute('data-message');
            sendUserMessage(message);
            button.style.display = 'none';
        });
    });

    // Chat form submission
    if (chatForm) {
        chatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (message) {
                sendUserMessage(message);
                chatInput.value = '';
                charCounter.textContent = '0';
            }
        });
    }

    // Chat history functionality
    if (chatHistoryBtn) {
        chatHistoryBtn.addEventListener('click', () => {
            loadChatHistory();
            chatHistoryModal.classList.remove('hidden');
        });
    }

    if (closeHistoryModal) {
        closeHistoryModal.addEventListener('click', () => {
            chatHistoryModal.classList.add('hidden');
        });
    }

    // Close modal when clicking outside
    chatHistoryModal.addEventListener('click', (e) => {
        if (e.target === chatHistoryModal) {
            chatHistoryModal.classList.add('hidden');
        }
    });

    // Send user message
    function sendUserMessage(message) {
        messageCount++;
        const messageElement = createMessageElement(message, 'user');
        chatMessages.appendChild(messageElement);
        scrollToBottom();
        
        // Show typing indicator
        setTimeout(() => {
            typingIndicator.classList.remove('hidden');
            scrollToBottom();
            
            // Remove typing indicator and send response
            setTimeout(() => {
                typingIndicator.classList.add('hidden');
                const response = generateEnhancedResponse(message);
                const responseElement = createMessageElement(response, 'bot');
                chatMessages.appendChild(responseElement);
                scrollToBottom();
            }, 1500 + Math.random() * 1000);
        }, 500);
    }

    // Handle file upload
    function handleFileUpload(e) {
        const files = e.target.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (file.size > 5 * 1024 * 1024) { // 5MB limit
                showFileError('File size too large. Please select a file smaller than 5MB.');
                return;
            }
            
            const fileElement = createFileMessage(file);
            chatMessages.appendChild(fileElement);
            scrollToBottom();
        });
    }

    // Create file message element
    function createFileMessage(file) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start space-x-3';
        
        const fileIcon = getFileIcon(file.type);
        const fileSize = formatFileSize(file.size);
        
        messageDiv.innerHTML = `
            <div class="flex-1"></div>
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl px-4 py-2 max-w-xs">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        ${fileIcon}
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">${file.name}</p>
                        <p class="text-xs text-blue-100">${fileSize}</p>
                    </div>
                </div>
                <p class="text-xs text-blue-100 mt-1">${getCurrentTime()}</p>
            </div>
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
            </div>
        `;
        
        return messageDiv;
    }

    // Get file icon based on type
    function getFileIcon(type) {
        if (type.includes('image')) {
            return '<path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>';
        } else if (type.includes('pdf')) {
            return '<path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 2h8v2H6V6zm0 4h8v2H6v-2z" clip-rule="evenodd"></path>';
        } else {
            return '<path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 2h8v2H6V6zm0 4h8v2H6v-2z" clip-rule="evenodd"></path>';
        }
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Show file error
    function showFileError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
        errorDiv.textContent = message;
        chatMessages.appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 5000);
    }

    // Create message element
    function createMessageElement(message, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start space-x-3';
        
        if (sender === 'user') {
            messageDiv.innerHTML = `
                <div class="flex-1"></div>
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl px-4 py-2 max-w-xs">
                    <p class="text-sm">${escapeHtml(message)}</p>
                    <p class="text-xs text-blue-100 mt-1">${getCurrentTime()}</p>
                </div>
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="bg-slate-100 dark:bg-slate-700 rounded-2xl px-4 py-2 max-w-xs">
                    <p class="text-sm text-slate-700 dark:text-slate-300">${escapeHtml(message)}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">${getCurrentTime()}</p>
                </div>
            `;
        }
        
        return messageDiv;
    }

    // Generate enhanced auto-response
    function generateEnhancedResponse(userMessage) {
        const message = userMessage.toLowerCase();
        
        if (message.includes('web development') || message.includes('website')) {
            return "Great! We specialize in modern web development. Here's what we can help you with:\n\n• React, Vue, Angular applications\n• E-commerce platforms\n• Custom CMS solutions\n• Progressive Web Apps\n• API development and integration\n\nWould you like to discuss your project requirements or see some examples of our work?";
        }
        
        if (message.includes('mobile') || message.includes('app')) {
            return "Excellent choice! Our mobile development services include:\n\n• iOS & Android native apps\n• Cross-platform solutions (React Native, Flutter)\n• App maintenance & updates\n• App Store optimization\n• Mobile UI/UX design\n\nWhat type of mobile app are you looking to build? I can help you choose the best approach.";
        }
        
        if (message.includes('quote') || message.includes('price') || message.includes('cost')) {
            return "I'd be happy to provide you with a detailed quote! To give you the most accurate estimate, I'll need to know:\n\n• Project scope and requirements\n• Timeline expectations\n• Budget considerations\n• Technical specifications\n\nWould you like to schedule a consultation call where we can discuss your project in detail?";
        }
        
        if (message.includes('schedule') || message.includes('call') || message.includes('consultation')) {
            return "Perfect! Let's schedule a consultation call. Here are our available options:\n\n• 30-minute discovery call (free)\n• 1-hour detailed consultation\n• Technical review session\n\nYou can also share your project details and files with me, and I'll prepare a comprehensive proposal for our call.";
        }
        
        if (message.includes('service') || message.includes('what do you do')) {
            return "BitSync Group offers comprehensive technology solutions:\n\n• Web & Mobile Development\n• Cloud & DevOps Services\n• AI & Machine Learning\n• Blockchain Solutions\n• Digital Transformation\n• IT Consulting\n\nWhich area interests you most? I can provide detailed information and case studies.";
        }
        
        if (message.includes('contact') || message.includes('phone')) {
            return "Absolutely! You can reach us at:\n\n📞 +234 (803) 381-8401\n📧 hello@bitsyncgroup.com\n📍 22 Airport road, Mafoluku Lagos\n\nWe're available Monday-Friday, 9 AM - 6 PM WAT. When would be a good time to call?";
        }
        
        if (message.includes('hello') || message.includes('hi') || message.includes('hey')) {
            return "Hello! 👋 Thanks for reaching out to BitSync Group. I'm here to help you with any questions about our services or to discuss your project needs. What can I assist you with today?";
        }
        
        // Default enhanced response
        const responses = [
            "Thank you for your message! I'd be happy to help you with that. Could you provide a bit more detail about your requirements so I can give you the most relevant information?",
            "That's an interesting question! Let me connect you with our team. What's the best way to reach you, and when would be a good time for a follow-up?",
            "I understand you're interested in our services. Would you like to schedule a consultation to discuss your project in detail? I can also share some relevant case studies.",
            "Thanks for reaching out! Our team will review your request and get back to you shortly. Is there anything specific you'd like to know about our process or timeline?"
        ];
        
        return responses[Math.floor(Math.random() * responses.length)];
    }

    // Load chat history
    function loadChatHistory() {
        // Simulate loading chat history
        chatHistoryContent.innerHTML = `
            <div class="space-y-4">
                <div class="text-center text-slate-500 text-sm">Loading chat history...</div>
            </div>
        `;
        
        // In a real implementation, this would fetch from the database
        setTimeout(() => {
            chatHistoryContent.innerHTML = `
                <div class="space-y-4">
                    <div class="text-center text-slate-500 text-sm">No previous chat history found.</div>
                </div>
            `;
        }, 1000);
    }

    // Show notification badge
    function showNotificationBadge(count = 1) {
        unreadCount += count;
        notificationCount.textContent = unreadCount;
        notificationBadge.classList.remove('hidden');
    }

    // Hide notification badge
    function hideNotificationBadge() {
        unreadCount = 0;
        notificationCount.textContent = '0';
        notificationBadge.classList.add('hidden');
    }

    // Helper functions
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Auto-open chat after 30 seconds if not opened
    setTimeout(() => {
        if (!isChatOpen && !localStorage.getItem('chatOpened')) {
            showNotificationBadge(1);
        }
    }, 30000);

    // Simulate incoming messages when chat is closed
    setInterval(() => {
        if (!isChatOpen && Math.random() < 0.1) { // 10% chance every interval
            showNotificationBadge(1);
        }
    }, 60000); // Check every minute
});
</script> 