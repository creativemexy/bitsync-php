<?php
$page_title = "Blog";
$page_description = "Stay updated with the latest technology trends, industry insights, and digital transformation strategies from BitSync Group's expert team.";
?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-slate-900 via-purple-900 to-pink-900 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
    <div class="absolute top-20 left-20 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl animate-blob"></div>
    <div class="absolute top-40 right-20 w-72 h-72 bg-pink-500/20 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-semibold text-purple-200 mb-6">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                </svg>
                Technology Insights
            </div>
            <h1 class="text-5xl md:text-7xl font-black mb-6 bg-gradient-to-r from-white to-purple-200 bg-clip-text text-transparent">
                Our Blog
            </h1>
            <p class="text-xl md:text-2xl text-purple-100 mb-8 max-w-4xl mx-auto leading-relaxed">
                Stay ahead with the latest technology trends, industry insights, and digital transformation strategies from our expert team.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#featured" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 hover:scale-105 shadow-2xl">
                    Read Articles
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                <a href="?page=contact" class="inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-bold rounded-xl hover:bg-white/20 transition-all duration-300 border border-white/20">
                    Subscribe
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Articles -->
<section id="featured" class="py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-6">
                Featured Articles
            </h2>
            <p class="text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">
                Discover insights, trends, and strategies that drive digital transformation and business innovation.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Article 1 -->
            <article class="group bg-white dark:bg-slate-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="relative overflow-hidden">
                    <img src="assets/web-development.jpg" alt="Web Development Trends" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-blue-600 text-white text-sm font-semibold rounded-full">Web Development</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 mb-3">
                        <span>March 15, 2024</span>
                        <span class="mx-2">•</span>
                        <span>5 min read</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3 group-hover:text-blue-600 transition-colors">
                        The Future of Web Development: 2024 Trends
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                        Explore the latest trends shaping web development, from AI-powered tools to advanced frameworks and performance optimization.
                    </p>
                    <a href="#" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                        Read More
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Article 2 -->
            <article class="group bg-white dark:bg-slate-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="relative overflow-hidden">
                    <img src="assets/blockchain-tech.jpg" alt="Blockchain Technology" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-green-600 text-white text-sm font-semibold rounded-full">Blockchain</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 mb-3">
                        <span>March 12, 2024</span>
                        <span class="mx-2">•</span>
                        <span>7 min read</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3 group-hover:text-blue-600 transition-colors">
                        DeFi Revolution: Transforming Finance
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                        How decentralized finance is reshaping traditional banking and creating new opportunities for financial innovation.
                    </p>
                    <a href="#" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                        Read More
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Article 3 -->
            <article class="group bg-white dark:bg-slate-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="relative overflow-hidden">
                    <img src="assets/consulting-services.jpg" alt="AI Machine Learning" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-purple-600 text-white text-sm font-semibold rounded-full">AI & ML</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 mb-3">
                        <span>March 10, 2024</span>
                        <span class="mx-2">•</span>
                        <span>6 min read</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3 group-hover:text-blue-600 transition-colors">
                        AI in Business: Practical Applications
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                        Real-world applications of artificial intelligence that are driving business transformation and competitive advantage.
                    </p>
                    <a href="#" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                        Read More
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Article 4 -->
            <article class="group bg-white dark:bg-slate-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="relative overflow-hidden">
                    <img src="assets/cloud-solutions.jpg" alt="Cloud Solutions" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-orange-600 text-white text-sm font-semibold rounded-full">Cloud</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 mb-3">
                        <span>March 8, 2024</span>
                        <span class="mx-2">•</span>
                        <span>4 min read</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3 group-hover:text-blue-600 transition-colors">
                        Cloud Migration Strategies
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                        Best practices for successful cloud migration and how to avoid common pitfalls during the transition process.
                    </p>
                    <a href="#" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                        Read More
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Article 5 -->
            <article class="group bg-white dark:bg-slate-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="relative overflow-hidden">
                    <img src="assets/mobile-development.jpg" alt="Mobile Development" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-pink-600 text-white text-sm font-semibold rounded-full">Mobile</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 mb-3">
                        <span>March 5, 2024</span>
                        <span class="mx-2">•</span>
                        <span>8 min read</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3 group-hover:text-blue-600 transition-colors">
                        Cross-Platform Development Guide
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                        A comprehensive guide to choosing between React Native, Flutter, and native development for your mobile app.
                    </p>
                    <a href="#" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                        Read More
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </article>

            <!-- Article 6 -->
            <article class="group bg-white dark:bg-slate-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="relative overflow-hidden">
                    <img src="assets/enterprise-solutions.jpg" alt="Digital Transformation" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-indigo-600 text-white text-sm font-semibold rounded-full">Strategy</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 mb-3">
                        <span>March 1, 2024</span>
                        <span class="mx-2">•</span>
                        <span>10 min read</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-3 group-hover:text-blue-600 transition-colors">
                        Digital Transformation Roadmap
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 mb-4 leading-relaxed">
                        A step-by-step roadmap for successful digital transformation and how to measure ROI effectively.
                    </p>
                    <a href="#" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700 transition-colors">
                        Read More
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- Newsletter Subscription -->
<section class="py-20 bg-slate-50 dark:bg-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-6">
            Stay Updated
        </h2>
        <p class="text-xl text-slate-600 dark:text-slate-300 mb-8 max-w-2xl mx-auto">
            Get the latest technology insights, industry trends, and expert analysis delivered to your inbox.
        </p>
        <div class="bg-white dark:bg-slate-700 rounded-2xl p-8 shadow-xl">
            <form class="flex flex-col sm:flex-row gap-4">
                <input type="email" placeholder="Enter your email address" class="flex-1 bg-slate-50 dark:bg-slate-600 text-slate-900 dark:text-white px-6 py-4 rounded-xl text-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border border-slate-200 dark:border-slate-500">
                <button type="submit" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 hover:scale-105 shadow-lg">
                    Subscribe
                </button>
            </form>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-4">
                No spam, unsubscribe at any time. We respect your privacy.
            </p>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-br from-purple-600 to-pink-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-black mb-6">
            Ready to Transform Your Business?
        </h2>
        <p class="text-xl text-purple-100 mb-8 max-w-3xl mx-auto">
            Let's discuss how our insights and expertise can help you achieve your digital transformation goals.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="?page=contact" class="inline-flex items-center px-8 py-4 bg-white text-purple-600 font-bold rounded-xl hover:bg-purple-50 transition-all duration-300 hover:scale-105 shadow-2xl">
                Get Consultation
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
            <a href="tel:+2348033818401" class="inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-bold rounded-xl hover:bg-white/20 transition-all duration-300 border border-white/20">
                Call +234 (803) 381-8401
                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
            </a>
        </div>
    </div>
</section> 