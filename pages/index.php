<?php
$page_title = "Home";
$page_description = "BitSync Group is a global technology powerhouse delivering cutting-edge solutions in consumer electronics, enterprise systems, and innovative consulting services.";
?>

<div class="min-h-screen">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-50 via-purple-50 to-indigo-50 py-20 overflow-hidden">
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="mb-8">
                    <img 
                        src="assets/hero-tech.jpg" 
                        alt="BitSync Group Technology Solutions" 
                        class="mx-auto w-32 h-32 rounded-full shadow-lg object-cover"
                    />
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                    Powering the Future of
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Technology
                    </span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    BitSync Group is a global technology powerhouse delivering cutting-edge solutions 
                    in consumer electronics, enterprise systems, and innovative consulting services.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/services" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 transition-all duration-200">
                        Explore Services 
                        <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="/contact" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 w-20 h-20 bg-blue-200 rounded-full opacity-20 animate-bounce"></div>
        <div class="absolute top-40 right-20 w-16 h-16 bg-purple-200 rounded-full opacity-20 animate-pulse"></div>
        <div class="absolute bottom-20 left-1/3 w-12 h-12 bg-indigo-200 rounded-full opacity-20 animate-bounce"></div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-2">500+</div>
                    <div class="text-gray-600">Global Clients</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-2">50+</div>
                    <div class="text-gray-600">Countries Served</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-2">1000+</div>
                    <div class="text-gray-600">Projects Delivered</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-2">24/7</div>
                    <div class="text-gray-600">Support Available</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Our Core Services
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    From design to deployment, we deliver comprehensive technology solutions 
                    that drive innovation and growth.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Consumer Electronics -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 border-0 group">
                    <div class="p-6">
                        <div class="w-full h-48 mb-4 rounded-lg overflow-hidden bg-gradient-to-br from-blue-50 to-purple-50">
                            <img 
                                src="assets/consumer-electronics.jpg" 
                                alt="Consumer Electronics"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Consumer Electronics</h3>
                        <p class="text-gray-600">Cutting-edge devices and smart solutions for modern living</p>
                    </div>
                </div>

                <!-- Enterprise Solutions -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 border-0 group">
                    <div class="p-6">
                        <div class="w-full h-48 mb-4 rounded-lg overflow-hidden bg-gradient-to-br from-blue-50 to-purple-50">
                            <img 
                                src="assets/enterprise-solutions.jpg" 
                                alt="Enterprise Solutions"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Enterprise Solutions</h3>
                        <p class="text-gray-600">Scalable cloud infrastructure and business transformation</p>
                    </div>
                </div>

                <!-- Web & Mobile Development -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 border-0 group">
                    <div class="p-6">
                        <div class="w-full h-48 mb-4 rounded-lg overflow-hidden bg-gradient-to-br from-blue-50 to-purple-50">
                            <img 
                                src="assets/web-development.jpg" 
                                alt="Web & Mobile Development"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Web & Mobile Development</h3>
                        <p class="text-gray-600">Custom applications and digital experiences</p>
                    </div>
                </div>

                <!-- Blockchain Technology -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 border-0 group">
                    <div class="p-6">
                        <div class="w-full h-48 mb-4 rounded-lg overflow-hidden bg-gradient-to-br from-blue-50 to-purple-50">
                            <img 
                                src="assets/blockchain-tech.jpg" 
                                alt="Blockchain Technology"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Blockchain Technology</h3>
                        <p class="text-gray-600">Decentralized solutions and cryptocurrency integration</p>
                    </div>
                </div>

                <!-- System Integration -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 border-0 group">
                    <div class="p-6">
                        <div class="w-full h-48 mb-4 rounded-lg overflow-hidden bg-gradient-to-br from-blue-50 to-purple-50">
                            <img 
                                src="assets/system-integration.jpg" 
                                alt="System Integration"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">System Integration</h3>
                        <p class="text-gray-600">Seamless technology integration and optimization</p>
                    </div>
                </div>

                <!-- Consulting Services -->
                <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 border-0 group">
                    <div class="p-6">
                        <div class="w-full h-48 mb-4 rounded-lg overflow-hidden bg-gradient-to-br from-blue-50 to-purple-50">
                            <img 
                                src="assets/consulting-team.jpg" 
                                alt="Consulting Services"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Consulting Services</h3>
                        <p class="text-gray-600">Strategic technology consulting and implementation</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                        Why Choose BitSync Group?
                    </h2>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Global Reach</h3>
                                <p class="text-gray-600">Serving clients across 50+ countries with localized expertise and global standards.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Innovation First</h3>
                                <p class="text-gray-600">Cutting-edge technology solutions that keep you ahead of the competition.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Premium Quality</h3>
                                <p class="text-gray-600">Enterprise-grade solutions with uncompromising quality and reliability.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white">
                        <div class="mb-6">
                            <img 
                                src="assets/why-choose-us.jpg" 
                                alt="BitSync Technology Workspace" 
                                class="w-full h-48 object-cover rounded-lg"
                            />
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Ready to Transform Your Business?</h3>
                        <p class="mb-6">Join thousands of satisfied clients who trust BitSync Group for their technology needs.</p>
                        <a href="/contact" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50 transition-all duration-200">
                            Start Your Journey 
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 