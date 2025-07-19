<header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <img src="assets/bitsync-logo.jpg" alt="BitSync Group" class="h-8 w-8 rounded">
                    <span class="text-xl font-bold text-gray-900">BitSync Group</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="/" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Home</a>
                <a href="/about" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">About</a>
                <a href="/services" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Services</a>
                <a href="/solutions" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Solutions</a>
                <a href="/contact" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Contact</a>
            </nav>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="mobile-menu-button text-gray-700 hover:text-blue-600 focus:outline-none focus:text-blue-600" onclick="toggleMobileMenu()">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="mobile-menu hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t">
                <a href="/" class="text-gray-700 hover:text-blue-600 block px-3 py-2 text-base font-medium transition-colors">Home</a>
                <a href="/about" class="text-gray-700 hover:text-blue-600 block px-3 py-2 text-base font-medium transition-colors">About</a>
                <a href="/services" class="text-gray-700 hover:text-blue-600 block px-3 py-2 text-base font-medium transition-colors">Services</a>
                <a href="/solutions" class="text-gray-700 hover:text-blue-600 block px-3 py-2 text-base font-medium transition-colors">Solutions</a>
                <a href="/contact" class="text-gray-700 hover:text-blue-600 block px-3 py-2 text-base font-medium transition-colors">Contact</a>
            </div>
        </div>
    </div>
</header>

<script>
function toggleMobileMenu() {
    const mobileMenu = document.querySelector('.mobile-menu');
    mobileMenu.classList.toggle('hidden');
}
</script> 