<?php
$page_title = "Analytics Dashboard - BitSync Group";
$page_description = "Comprehensive analytics dashboard providing valuable insights into website performance, user behavior, and business metrics.";
?>

<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <section class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Analytics Dashboard</h1>
                    <p class="text-gray-600">Real-time insights and performance metrics</p>
                </div>
                <div class="flex items-center space-x-4">
                    <select class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option>Last 7 days</option>
                        <option>Last 30 days</option>
                        <option>Last 90 days</option>
                        <option>Last year</option>
                    </select>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                        Export Report
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Metrics -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Visitors -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Visitors</p>
                            <p class="text-2xl font-bold text-gray-900">24,521</p>
                            <p class="text-sm text-green-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L12 7z" clip-rule="evenodd"></path>
                                </svg>
                                +12.5%
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Page Views -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Page Views</p>
                            <p class="text-2xl font-bold text-gray-900">89,432</p>
                            <p class="text-sm text-green-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L12 7z" clip-rule="evenodd"></path>
                                </svg>
                                +8.2%
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Conversion Rate -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Conversion Rate</p>
                            <p class="text-2xl font-bold text-gray-900">3.2%</p>
                            <p class="text-sm text-green-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L12 7z" clip-rule="evenodd"></path>
                                </svg>
                                +0.8%
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Bounce Rate -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Bounce Rate</p>
                            <p class="text-2xl font-bold text-gray-900">42.1%</p>
                            <p class="text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 12.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L12 13z" clip-rule="evenodd"></path>
                                </svg>
                                +2.1%
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Charts Section -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Traffic Overview -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Traffic Overview</h3>
                    <div class="h-64 flex items-end justify-between space-x-2">
                        <div class="flex-1 bg-blue-100 rounded-t" style="height: 60%"></div>
                        <div class="flex-1 bg-blue-200 rounded-t" style="height: 80%"></div>
                        <div class="flex-1 bg-blue-300 rounded-t" style="height: 45%"></div>
                        <div class="flex-1 bg-blue-400 rounded-t" style="height: 90%"></div>
                        <div class="flex-1 bg-blue-500 rounded-t" style="height: 70%"></div>
                        <div class="flex-1 bg-blue-600 rounded-t" style="height: 85%"></div>
                        <div class="flex-1 bg-blue-700 rounded-t" style="height: 95%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span>Mon</span>
                        <span>Tue</span>
                        <span>Wed</span>
                        <span>Thu</span>
                        <span>Fri</span>
                        <span>Sat</span>
                        <span>Sun</span>
                    </div>
                </div>

                <!-- Page Performance -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Pages</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Home</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-600">12,450 views</span>
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 85%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Services</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-600">8,920 views</span>
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 65%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Contact</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-600">6,340 views</span>
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 45%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">About</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-600">4,210 views</span>
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="bg-orange-500 h-2 rounded-full" style="width: 30%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- User Behavior -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Device Distribution -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Device Distribution</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Desktop</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">58%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Mobile</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">35%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Tablet</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">7%</span>
                        </div>
                    </div>
                </div>

                <!-- Geographic Distribution -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Locations</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">United States</span>
                            <span class="text-sm font-medium text-gray-900">42%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">United Kingdom</span>
                            <span class="text-sm font-medium text-gray-900">18%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Canada</span>
                            <span class="text-sm font-medium text-gray-900">12%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Australia</span>
                            <span class="text-sm font-medium text-gray-900">8%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Others</span>
                            <span class="text-sm font-medium text-gray-900">20%</span>
                        </div>
                    </div>
                </div>

                <!-- Traffic Sources -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Traffic Sources</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Organic Search</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">45%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Direct</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">28%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Social Media</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">15%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Referral</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">12%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Real-time Activity -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Real-time Activity</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">New visitor from United States</span>
                        <span class="text-xs text-gray-500 ml-auto">2 min ago</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Contact form submitted</span>
                        <span class="text-xs text-gray-500 ml-auto">5 min ago</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Newsletter subscription</span>
                        <span class="text-xs text-gray-500 ml-auto">8 min ago</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Page view on Services</span>
                        <span class="text-xs text-gray-500 ml-auto">12 min ago</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Simple chart animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars
    const progressBars = document.querySelectorAll('.bg-blue-500, .bg-green-500, .bg-purple-500, .bg-orange-500');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
            bar.style.transition = 'width 1s ease-in-out';
        }, 100);
    });

    // Real-time updates simulation
    setInterval(() => {
        const activities = [
            'New visitor from United States',
            'Page view on Home',
            'Contact form submitted',
            'Newsletter subscription',
            'Search query: "web development"',
            'Download whitepaper'
        ];
        
        const activity = activities[Math.floor(Math.random() * activities.length)];
        const colors = ['bg-green-500', 'bg-blue-500', 'bg-purple-500', 'bg-orange-500'];
        const color = colors[Math.floor(Math.random() * colors.length)];
        
        const activityContainer = document.querySelector('.space-y-3');
        if (activityContainer) {
            const newActivity = document.createElement('div');
            newActivity.className = 'flex items-center space-x-3 p-3 bg-gray-50 rounded-lg';
            newActivity.innerHTML = `
                <div class="w-2 h-2 ${color} rounded-full"></div>
                <span class="text-sm text-gray-700">${activity}</span>
                <span class="text-xs text-gray-500 ml-auto">Just now</span>
            `;
            
            activityContainer.insertBefore(newActivity, activityContainer.firstChild);
            
            // Remove old activities if too many
            const activitiesList = activityContainer.children;
            if (activitiesList.length > 5) {
                activityContainer.removeChild(activitiesList[activitiesList.length - 1]);
            }
        }
    }, 10000); // Update every 10 seconds
});
</script> 