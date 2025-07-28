<?php
$page_title = "Web Development";
$page_description = "Professional web development services including React, Vue, Angular applications, e-commerce platforms, custom CMS solutions, and Progressive Web Apps.";
?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-purple-900 text-white py-20 overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
    <div class="absolute top-20 left-20 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl animate-blob"></div>
    <div class="absolute top-40 right-20 w-72 h-72 bg-purple-500/20 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-semibold text-blue-200 mb-6">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                Web Development Services
            </div>
            <h1 class="text-5xl md:text-7xl font-black mb-6 bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">
                Web Development
            </h1>
            <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-4xl mx-auto leading-relaxed">
                Transform your digital presence with cutting-edge web solutions. From modern single-page applications to robust e-commerce platforms, we build websites that drive results.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="?page=contact" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 hover:scale-105 shadow-2xl">
                    Start Your Project
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                <a href="#services" class="inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-bold rounded-xl hover:bg-white/20 transition-all duration-300 border border-white/20">
                    Explore Services
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Services Overview -->
<section id="services" class="py-20 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-6">
                Our Web Development Services
            </h2>
            <p class="text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">
                We specialize in creating modern, scalable, and high-performance web applications that deliver exceptional user experiences.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- React Development -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">React Development</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Build dynamic, interactive user interfaces with React. We create single-page applications that provide seamless user experiences with fast loading times and smooth transitions.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                        <span>Component-based architecture</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                        <span>State management with Redux</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                        <span>Server-side rendering (SSR)</span>
                    </li>
                </ul>
            </div>

            <!-- Vue.js Development -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">Vue.js Development</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Leverage Vue.js for progressive web applications. We build scalable applications with Vue's intuitive framework, perfect for both small projects and enterprise solutions.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-green-600 rounded-full mr-3"></div>
                        <span>Progressive framework</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-green-600 rounded-full mr-3"></div>
                        <span>Vuex state management</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-green-600 rounded-full mr-3"></div>
                        <span>Nuxt.js SSR solutions</span>
                    </li>
                </ul>
            </div>

            <!-- Angular Development -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">Angular Development</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Enterprise-grade applications with Angular. We build robust, scalable applications with TypeScript, perfect for large teams and complex business requirements.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-red-600 rounded-full mr-3"></div>
                        <span>TypeScript integration</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-red-600 rounded-full mr-3"></div>
                        <span>Dependency injection</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-red-600 rounded-full mr-3"></div>
                        <span>Angular Material UI</span>
                    </li>
                </ul>
            </div>

            <!-- E-commerce Platforms -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">E-commerce Platforms</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Complete e-commerce solutions that drive sales. From custom shopping carts to integrated payment systems, we build online stores that convert visitors into customers.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-yellow-600 rounded-full mr-3"></div>
                        <span>Custom shopping carts</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-yellow-600 rounded-full mr-3"></div>
                        <span>Payment gateway integration</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-yellow-600 rounded-full mr-3"></div>
                        <span>Inventory management</span>
                    </li>
                </ul>
            </div>

            <!-- Custom CMS -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">Custom CMS</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Tailored content management systems that fit your workflow. We build intuitive CMS solutions that make content management effortless and efficient.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-indigo-600 rounded-full mr-3"></div>
                        <span>User-friendly admin panels</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-indigo-600 rounded-full mr-3"></div>
                        <span>Multi-user permissions</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-indigo-600 rounded-full mr-3"></div>
                        <span>SEO optimization tools</span>
                    </li>
                </ul>
            </div>

            <!-- Progressive Web Apps -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">Progressive Web Apps</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Next-generation web applications that work like native apps. PWAs provide offline functionality, push notifications, and app-like experiences.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-pink-600 rounded-full mr-3"></div>
                        <span>Offline functionality</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-pink-600 rounded-full mr-3"></div>
                        <span>Push notifications</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-pink-600 rounded-full mr-3"></div>
                        <span>App-like experience</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-20 bg-slate-50 dark:bg-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-6">
                Why Choose Our Web Development?
            </h2>
            <p class="text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">
                We combine technical expertise with creative design to deliver web solutions that exceed expectations.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Modern Technologies</h3>
                <p class="text-slate-600 dark:text-slate-300">Latest frameworks and tools for optimal performance</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Fast Performance</h3>
                <p class="text-slate-600 dark:text-slate-300">Optimized code for lightning-fast loading times</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Responsive Design</h3>
                <p class="text-slate-600 dark:text-slate-300">Perfect display on all devices and screen sizes</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">24/7 Support</h3>
                <p class="text-slate-600 dark:text-slate-300">Round-the-clock technical support and maintenance</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-black mb-6">
            Ready to Build Your Dream Website?
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
            Let's discuss your project requirements and create a web solution that drives your business forward.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="?page=contact" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition-all duration-300 hover:scale-105 shadow-2xl">
                Get Free Consultation
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