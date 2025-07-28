<?php
$page_title = "Cloud Solutions";
$page_description = "Professional cloud solutions and DevOps services including AWS, Azure, Google Cloud, containerization, CI/CD pipelines, and infrastructure automation.";
?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
    <div class="absolute top-20 left-20 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl animate-blob"></div>
    <div class="absolute top-40 right-20 w-72 h-72 bg-indigo-500/20 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-semibold text-blue-200 mb-6">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
                Cloud Solutions & DevOps
            </div>
            <h1 class="text-5xl md:text-7xl font-black mb-6 bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">
                Cloud Solutions
            </h1>
            <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-4xl mx-auto leading-relaxed">
                Scale your business with enterprise-grade cloud infrastructure. From AWS to Azure, we provide comprehensive cloud solutions that drive efficiency and growth.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="?page=contact" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 hover:scale-105 shadow-2xl">
                    Get Cloud Solution
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
                Our Cloud Solutions
            </h2>
            <p class="text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto">
                Comprehensive cloud services that optimize your infrastructure, reduce costs, and improve performance.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- AWS Services -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-yellow-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">AWS Services</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Amazon Web Services solutions for scalable, secure, and cost-effective cloud infrastructure.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-orange-600 rounded-full mr-3"></div>
                        <span>EC2 & Lambda services</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-orange-600 rounded-full mr-3"></div>
                        <span>S3 storage solutions</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-orange-600 rounded-full mr-3"></div>
                        <span>RDS database management</span>
                    </li>
                </ul>
            </div>

            <!-- Azure Services -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">Azure Services</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Microsoft Azure cloud solutions for enterprise applications and hybrid cloud environments.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                        <span>Virtual Machines</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                        <span>App Service & Functions</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                        <span>Cosmos DB & SQL Database</span>
                    </li>
                </ul>
            </div>

            <!-- Google Cloud -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">Google Cloud</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Google Cloud Platform services for AI/ML, data analytics, and scalable applications.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-red-600 rounded-full mr-3"></div>
                        <span>Compute Engine</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-red-600 rounded-full mr-3"></div>
                        <span>Cloud Functions</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-red-600 rounded-full mr-3"></div>
                        <span>BigQuery & AI services</span>
                    </li>
                </ul>
            </div>

            <!-- DevOps & CI/CD -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">DevOps & CI/CD</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Streamline your development process with automated pipelines and infrastructure as code.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-green-600 rounded-full mr-3"></div>
                        <span>Jenkins & GitHub Actions</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-green-600 rounded-full mr-3"></div>
                        <span>Terraform & Ansible</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-green-600 rounded-full mr-3"></div>
                        <span>Docker & Kubernetes</span>
                    </li>
                </ul>
            </div>

            <!-- Containerization -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">Containerization</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Containerized applications with Docker and orchestration with Kubernetes for scalable deployments.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                        <span>Docker containerization</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                        <span>Kubernetes orchestration</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                        <span>Microservices architecture</span>
                    </li>
                </ul>
            </div>

            <!-- Cloud Security -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">Cloud Security</h3>
                <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                    Comprehensive security solutions to protect your cloud infrastructure and data.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-red-600 rounded-full mr-3"></div>
                        <span>Identity & Access Management</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-red-600 rounded-full mr-3"></div>
                        <span>Network security & firewalls</span>
                    </li>
                    <li class="flex items-center text-sm text-slate-600 dark:text-slate-300">
                        <div class="w-2 h-2 bg-red-600 rounded-full mr-3"></div>
                        <span>Compliance & monitoring</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 to-indigo-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-black mb-6">
            Ready to Scale with Cloud?
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
            Let's optimize your infrastructure and accelerate your business growth with our cloud solutions.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="?page=contact" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition-all duration-300 hover:scale-105 shadow-2xl">
                Get Cloud Assessment
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