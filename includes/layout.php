<?php
// Load content system
require_once 'includes/content.php';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'BitSync Group - Technology Solutions'; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'BitSync Group is a global technology powerhouse delivering cutting-edge solutions in consumer electronics, enterprise systems, and innovative consulting services.'; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="public/favicon.jpg">
    <link rel="icon" type="image/jpeg" sizes="16x16" href="public/favicon-16x16.jpg">
    <link rel="icon" type="image/jpeg" sizes="32x32" href="public/favicon-32x32.jpg">
    <link rel="apple-touch-icon" href="public/favicon.jpg">
    <link rel="shortcut icon" href="public/favicon.jpg">
    <link rel="manifest" href="public/manifest.json">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: "hsl(var(--background))",
                        foreground: "hsl(var(--foreground))",
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <style>
        .bg-grid-pattern {
            background-image: radial-gradient(circle, #000 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        .animate-bounce {
            animation: bounce 1s infinite;
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(-25%); animation-timing-function: cubic-bezier(0.8, 0, 1, 1); }
            50% { transform: translateY(0); animation-timing-function: cubic-bezier(0, 0, 0.2, 1); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        
        /* Premium Animations */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        
        .animate-blob {
            animation: blob 7s infinite;
        }
        
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        
        /* Premium Typography */
        .font-black {
            font-weight: 900;
        }
        
        /* Premium Gradients */
        .bg-gradient-to-br {
            background: linear-gradient(to bottom right, var(--tw-gradient-stops));
        }
        
        /* Premium Shadows */
        .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        /* Premium Backdrop Blur */
        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
        }
        
        /* Scroll Animations */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }
        
        .fade-in-up.animate {
            opacity: 1;
            transform: translateY(0);
        }
        
        .fade-in-left {
            opacity: 0;
            transform: translateX(-30px);
            transition: all 0.6s ease-out;
        }
        
        .fade-in-left.animate {
            opacity: 1;
            transform: translateX(0);
        }
        
        .fade-in-right {
            opacity: 0;
            transform: translateX(30px);
            transition: all 0.6s ease-out;
        }
        
        .fade-in-right.animate {
            opacity: 1;
            transform: translateX(0);
        }
        
        .scale-in {
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.6s ease-out;
        }
        
        .scale-in.animate {
            opacity: 1;
            transform: scale(1);
        }
        
        /* Parallax Effect */
        .parallax {
            transform: translateZ(0);
            will-change: transform;
        }
        
        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #2563eb, #7c3aed);
        }
        
        /* Hide scrollbar for testimonials carousel */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        /* Floating Action Button */
        .floating-action-btn {
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5);
        }
        
        /* Custom Cursor */
        .custom-cursor {
            backdrop-filter: blur(4px);
        }
        
        .custom-cursor.hover {
            transform: translate(-50%, -50%) scale(1.5);
            background: rgba(59, 130, 246, 0.3);
        }
        

    </style>
</head>
<body class="min-h-screen bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors duration-300">
    <?php include 'includes/header.php'; ?>
    
    <main>
        <?php include $page_file; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <!-- Premium JavaScript -->
    <script>
        // Scroll Animation Observer
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                }
            });
        }, observerOptions);

        // Observe all animation elements
        document.addEventListener('DOMContentLoaded', () => {
            const animatedElements = document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .scale-in');
            animatedElements.forEach(el => observer.observe(el));
        });

        // Parallax Effect
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.parallax');
            
            parallaxElements.forEach(element => {
                const speed = element.dataset.speed || 0.5;
                element.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });

        // Smooth Counter Animation
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            
            const timer = setInterval(() => {
                start += increment;
                if (start >= target) {
                    element.textContent = target + (element.dataset.suffix || '');
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(start) + (element.dataset.suffix || '');
                }
            }, 16);
        }

        // Counter Animation Observer
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.dataset.target);
                    animateCounter(entry.target, target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        // Observe counter elements
        document.addEventListener('DOMContentLoaded', () => {
            const counterElements = document.querySelectorAll('[data-target]');
            counterElements.forEach(el => counterObserver.observe(el));
        });

        // Mouse Follow Effect
        document.addEventListener('mousemove', (e) => {
            const cursor = document.querySelector('.custom-cursor');
            if (cursor) {
                cursor.style.left = e.clientX + 'px';
                cursor.style.top = e.clientY + 'px';
            }
        });

        // Add custom cursor to interactive elements
        document.addEventListener('DOMContentLoaded', () => {
            const interactiveElements = document.querySelectorAll('a, button, .interactive');
            interactiveElements.forEach(el => {
                el.addEventListener('mouseenter', () => {
                    const cursor = document.querySelector('.custom-cursor');
                    if (cursor) cursor.classList.add('hover');
                });
                el.addEventListener('mouseleave', () => {
                    const cursor = document.querySelector('.custom-cursor');
                    if (cursor) cursor.classList.remove('hover');
                });
            });
        });

        // Testimonials Carousel
        document.addEventListener('DOMContentLoaded', () => {
            const carousel = document.querySelector('.testimonials-carousel');
            const navButtons = document.querySelectorAll('.carousel-nav');
            
            if (carousel && navButtons.length > 0) {
                let currentSlide = 0;
                const totalSlides = document.querySelectorAll('.testimonial-card').length;
                
                function updateCarousel() {
                    const slideWidth = carousel.querySelector('.testimonial-card').offsetWidth + 32; // 32px for gap
                    carousel.scrollTo({
                        left: currentSlide * slideWidth,
                        behavior: 'smooth'
                    });
                    
                    // Update navigation buttons
                    navButtons.forEach((btn, index) => {
                        if (index === currentSlide) {
                            btn.classList.remove('bg-slate-300');
                            btn.classList.add('bg-blue-600');
                        } else {
                            btn.classList.remove('bg-blue-600');
                            btn.classList.add('bg-slate-300');
                        }
                    });
                }
                
                // Navigation button clicks
                navButtons.forEach((btn, index) => {
                    btn.addEventListener('click', () => {
                        currentSlide = index;
                        updateCarousel();
                    });
                });
                
                // Auto-play carousel
                setInterval(() => {
                    currentSlide = (currentSlide + 1) % totalSlides;
                    updateCarousel();
                }, 5000);
            }
        });

        // Dark Mode Toggle
        document.addEventListener('DOMContentLoaded', () => {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const sunIcon = document.getElementById('sunIcon');
            const moonIcon = document.getElementById('moonIcon');
            
            // Check for saved dark mode preference
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            if (isDarkMode) {
                document.documentElement.classList.add('dark');
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }
            
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', () => {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('darkMode', isDark);
                    
                    if (isDark) {
                        sunIcon.classList.add('hidden');
                        moonIcon.classList.remove('hidden');
                    } else {
                        sunIcon.classList.remove('hidden');
                        moonIcon.classList.add('hidden');
                    }
                });
            }
        });

        // Newsletter Signup
        document.addEventListener('DOMContentLoaded', () => {
            const newsletterForm = document.getElementById('newsletterForm');
            const newsletterMessage = document.getElementById('newsletterMessage');
            
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    
                    const emailInput = newsletterForm.querySelector('input[type="email"]');
                    const submitButton = newsletterForm.querySelector('button');
                    const email = emailInput.value.trim();
                    
                    if (!email) {
                        showNewsletterMessage('Please enter a valid email address.', 'error');
                        return;
                    }
                    
                    // Show loading state
                    const originalButtonText = submitButton.innerHTML;
                    submitButton.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                    submitButton.disabled = true;
                    
                    try {
                        const response = await fetch('newsletter-subscribe.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ email: email })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            showNewsletterMessage(data.message, 'success');
                            emailInput.value = '';
                            
                            // Show success icon briefly
                            submitButton.innerHTML = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>';
                            
                            setTimeout(() => {
                                submitButton.innerHTML = originalButtonText;
                                submitButton.disabled = false;
                            }, 2000);
                        } else {
                            showNewsletterMessage(data.message, 'error');
                            submitButton.innerHTML = originalButtonText;
                            submitButton.disabled = false;
                        }
                    } catch (error) {
                        console.error('Newsletter subscription error:', error);
                        showNewsletterMessage('An error occurred. Please try again.', 'error');
                        submitButton.innerHTML = originalButtonText;
                        submitButton.disabled = false;
                    }
                });
            }
            
            function showNewsletterMessage(message, type) {
                if (newsletterMessage) {
                    newsletterMessage.textContent = message;
                    newsletterMessage.className = `mt-3 text-sm ${type === 'success' ? 'text-green-400' : 'text-red-400'}`;
                    newsletterMessage.classList.remove('hidden');
                    
                    // Hide message after 5 seconds
                    setTimeout(() => {
                        newsletterMessage.classList.add('hidden');
                    }, 5000);
                }
            }
        });

        // Live Chat System
        document.addEventListener('DOMContentLoaded', () => {
            const chatToggle = document.getElementById('chatToggle');
            const chatWindow = document.getElementById('chatWindow');
            const chatIcon = document.getElementById('chatIcon');
            const closeIcon = document.getElementById('closeIcon');
            const minimizeChat = document.getElementById('minimizeChat');
            const chatForm = document.getElementById('chatForm');
            const chatInput = document.getElementById('chatInput');
            const chatMessages = document.getElementById('chatMessages');
            const quickResponses = document.querySelectorAll('.quick-response');

            let isChatOpen = false;
            let messageCount = 0;

            // Chat toggle functionality
            if (chatToggle) {
                chatToggle.addEventListener('click', () => {
                    isChatOpen = !isChatOpen;
                    if (isChatOpen) {
                        chatWindow.classList.remove('hidden');
                        chatIcon.classList.add('hidden');
                        closeIcon.classList.remove('hidden');
                        chatInput.focus();
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

            // Quick response buttons
            quickResponses.forEach(button => {
                button.addEventListener('click', () => {
                    const message = button.getAttribute('data-message');
                    sendUserMessage(message);
                    button.style.display = 'none'; // Hide after use
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
                    }
                });
            }

            // Send user message
            function sendUserMessage(message) {
                messageCount++;
                const messageElement = createMessageElement(message, 'user');
                chatMessages.appendChild(messageElement);
                scrollToBottom();
                
                // Simulate typing indicator
                setTimeout(() => {
                    const typingIndicator = createTypingIndicator();
                    chatMessages.appendChild(typingIndicator);
                    scrollToBottom();
                    
                    // Remove typing indicator and send auto-response
                    setTimeout(() => {
                        chatMessages.removeChild(typingIndicator);
                        const response = generateAutoResponse(message);
                        const responseElement = createMessageElement(response, 'bot');
                        chatMessages.appendChild(responseElement);
                        scrollToBottom();
                    }, 1500 + Math.random() * 1000);
                }, 500);
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

            // Create typing indicator
            function createTypingIndicator() {
                const typingDiv = document.createElement('div');
                typingDiv.className = 'flex items-start space-x-3';
                typingDiv.innerHTML = `
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
                `;
                return typingDiv;
            }

            // Generate auto-response based on user message
            function generateAutoResponse(userMessage) {
                const message = userMessage.toLowerCase();
                
                if (message.includes('web development') || message.includes('website')) {
                    return "Great! We specialize in modern web development. We can help you with:\n\n• React, Vue, Angular applications\n• E-commerce platforms\n• Custom CMS solutions\n• Progressive Web Apps\n\nWould you like to discuss your project requirements?";
                }
                
                if (message.includes('mobile') || message.includes('app')) {
                    return "Excellent choice! Our mobile development services include:\n\n• iOS & Android native apps\n• Cross-platform solutions (React Native, Flutter)\n• App maintenance & updates\n• App Store optimization\n\nWhat type of mobile app are you looking to build?";
                }
                
                if (message.includes('quote') || message.includes('price') || message.includes('cost')) {
                    return "I'd be happy to provide you with a quote! To give you the most accurate estimate, I'll need to know:\n\n• Project scope and requirements\n• Timeline expectations\n• Budget considerations\n\nWould you like to schedule a consultation call?";
                }
                
                if (message.includes('service') || message.includes('what do you do')) {
                    return "BitSync Group offers comprehensive technology solutions:\n\n• Web & Mobile Development\n• Cloud & DevOps Services\n• AI & Machine Learning\n• Blockchain Solutions\n• Digital Transformation\n• IT Consulting\n\nWhich area interests you most?";
                }
                
                if (message.includes('contact') || message.includes('call') || message.includes('phone')) {
                    return "Absolutely! You can reach us at:\n\n📞 +234 (803) 381-8401\n📧 hello@bitsyncgroup.com\n📍 22 Airport road, Mafoluku Lagos\n\nWe're available Monday-Friday, 9 AM - 6 PM WAT. When would be a good time to call?";
                }
                
                if (message.includes('hello') || message.includes('hi') || message.includes('hey')) {
                    return "Hello! 👋 Thanks for reaching out to BitSync Group. I'm here to help you with any questions about our services or to discuss your project needs. What can I assist you with today?";
                }
                
                // Default response
                const responses = [
                    "Thank you for your message! I'd be happy to help you with that. Could you provide a bit more detail about your requirements?",
                    "That's an interesting question! Let me connect you with our team. What's the best way to reach you?",
                    "I understand you're interested in our services. Would you like to schedule a consultation to discuss your project in detail?",
                    "Thanks for reaching out! Our team will review your request and get back to you shortly. Is there anything specific you'd like to know?"
                ];
                
                return responses[Math.floor(Math.random() * responses.length)];
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
                    chatToggle.click();
                    localStorage.setItem('chatOpened', 'true');
                }
            }, 30000);
        });
    </script>
    <!-- Custom Cursor -->
    <div class="custom-cursor fixed w-6 h-6 bg-blue-500/20 rounded-full pointer-events-none z-50 transition-all duration-200 ease-out transform -translate-x-1/2 -translate-y-1/2 mix-blend-difference"></div> 
</body>
</html> 