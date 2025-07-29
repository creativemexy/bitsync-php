<?php
$current_page = 'search';
$page_title = 'Search - BitSync Group';
$page_description = 'Search across all BitSync content, services, and resources.';

// Get search query from URL
$searchQuery = $_GET['q'] ?? '';
$selectedFilters = $_GET['filters'] ?? [];
$currentPage = max(1, intval($_GET['page'] ?? 1));
?>

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
    <!-- Hero Section -->
    <section class="relative py-20 overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20"></div>
            <div class="absolute top-0 left-0 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl animate-blob"></div>
            <div class="absolute top-0 right-0 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-0 left-1/2 w-72 h-72 bg-pink-500/10 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-black text-white mb-6">
                    Search <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">Everything</span>
                </h1>
                <p class="text-xl text-slate-300 mb-8 max-w-3xl mx-auto">
                    Find exactly what you're looking for across our services, case studies, blog posts, and more
                </p>

                <!-- Search Form -->
                <div class="max-w-2xl mx-auto">
                    <form id="searchForm" class="relative">
                        <div class="relative">
                            <input 
                                type="text" 
                                id="searchInput"
                                name="q"
                                value="<?php echo htmlspecialchars($searchQuery); ?>"
                                placeholder="Search for services, technologies, case studies..."
                                class="w-full px-6 py-4 text-lg bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl text-white placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/50 focus:border-transparent"
                                autocomplete="off"
                            >
                            <button 
                                type="submit"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Search Suggestions -->
                    <div id="searchSuggestions" class="mt-4 hidden">
                        <div class="text-left bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <h4 class="text-sm font-semibold text-slate-300 mb-2">Popular Searches</h4>
                            <div id="suggestionsList" class="space-y-1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="py-8 bg-white/5 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <span class="text-slate-300 font-medium">Filter by:</span>
                    
                    <!-- Content Type Filter -->
                    <select id="contentTypeFilter" class="bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Content</option>
                        <option value="page">Pages</option>
                        <option value="service">Services</option>
                        <option value="blog">Blog Posts</option>
                        <option value="case_study">Case Studies</option>
                        <option value="job">Job Openings</option>
                    </select>

                    <!-- Category Filter -->
                    <select id="categoryFilter" class="bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        <option value="web-development">Web Development</option>
                        <option value="mobile-development">Mobile Development</option>
                        <option value="cloud-solutions">Cloud Solutions</option>
                        <option value="blockchain">Blockchain</option>
                        <option value="ai-ml">AI & Machine Learning</option>
                        <option value="digital-transformation">Digital Transformation</option>
                    </select>
                </div>

                <div class="flex items-center space-x-4">
                    <span id="resultsCount" class="text-slate-300"></span>
                    <button id="clearFilters" class="text-slate-400 hover:text-white transition-colors">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Loading State -->
            <div id="loadingState" class="hidden">
                <div class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                    <span class="ml-3 text-slate-300">Searching...</span>
                </div>
            </div>

            <!-- No Results State -->
            <div id="noResultsState" class="hidden text-center py-12">
                <div class="max-w-md mx-auto">
                    <svg class="w-16 h-16 text-slate-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-slate-300 mb-2">No results found</h3>
                    <p class="text-slate-400 mb-6">Try adjusting your search terms or filters</p>
                    <button id="tryDifferentSearch" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300">
                        Try Different Search
                    </button>
                </div>
            </div>

            <!-- Results List -->
            <div id="resultsList" class="space-y-6"></div>

            <!-- Pagination -->
            <div id="pagination" class="mt-12 flex items-center justify-center space-x-2"></div>
        </div>
    </section>
</div>

<script>
class SearchManager {
    constructor() {
        this.currentQuery = '';
        this.currentFilters = {};
        this.currentPage = 1;
        this.isSearching = false;
        this.searchTimeout = null;
        
        this.initializeEventListeners();
        this.loadSearchSuggestions();
        
        // Perform initial search if query exists
        if (document.getElementById('searchInput').value) {
            this.performSearch();
        }
    }
    
    initializeEventListeners() {
        // Search form
        document.getElementById('searchForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.performSearch();
        });
        
        // Real-time search
        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.handleSearchInput(e.target.value);
        });
        
        // Filters
        document.getElementById('contentTypeFilter').addEventListener('change', (e) => {
            this.currentFilters.type = e.target.value;
            this.performSearch();
        });
        
        document.getElementById('categoryFilter').addEventListener('change', (e) => {
            this.currentFilters.category = e.target.value;
            this.performSearch();
        });
        
        // Clear filters
        document.getElementById('clearFilters').addEventListener('click', () => {
            this.clearFilters();
        });
        
        // Try different search
        document.getElementById('tryDifferentSearch').addEventListener('click', () => {
            document.getElementById('searchInput').focus();
        });
    }
    
    handleSearchInput(query) {
        this.currentQuery = query;
        
        // Clear previous timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        // Set new timeout for real-time search
        this.searchTimeout = setTimeout(() => {
            if (query.length >= 2) {
                this.performSearch();
            } else {
                this.hideResults();
            }
        }, 300);
    }
    
    async performSearch() {
        if (this.isSearching) return;
        
        this.isSearching = true;
        this.showLoading();
        
        try {
            const params = new URLSearchParams({
                q: this.currentQuery,
                page: this.currentPage,
                limit: 20
            });
            
            // Add filters
            Object.entries(this.currentFilters).forEach(([key, value]) => {
                if (value) {
                    params.append(`filters[${key}]`, value);
                }
            });
            
            const response = await fetch(`search-api.php?${params.toString()}`);
            const data = await response.json();
            
            if (data.success) {
                this.displayResults(data);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showError('Search failed. Please try again.');
        } finally {
            this.isSearching = false;
            this.hideLoading();
        }
    }
    
    displayResults(data) {
        const resultsList = document.getElementById('resultsList');
        const resultsCount = document.getElementById('resultsCount');
        
        // Update results count
        resultsCount.textContent = `${data.total} results found`;
        
        if (data.results.length === 0) {
            this.showNoResults();
            return;
        }
        
        this.hideNoResults();
        
        // Clear previous results
        resultsList.innerHTML = '';
        
        // Display results
        data.results.forEach(result => {
            const resultElement = this.createResultElement(result);
            resultsList.appendChild(resultElement);
        });
        
        // Display pagination
        this.displayPagination(data.pagination);
        
        // Update URL
        this.updateURL();
    }
    
    createResultElement(result) {
        const div = document.createElement('div');
        div.className = 'bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20 hover:border-white/30 transition-all duration-300';
        
        const typeIcon = this.getTypeIcon(result.type);
        const typeLabel = this.getTypeLabel(result.type);
        
        div.innerHTML = `
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                        ${typeIcon}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2 mb-2">
                        <h3 class="text-xl font-semibold text-white hover:text-blue-400 transition-colors">
                            <a href="${result.url}">${result.title}</a>
                        </h3>
                        <span class="px-2 py-1 text-xs font-medium bg-white/20 text-white rounded-full">
                            ${typeLabel}
                        </span>
                    </div>
                    <p class="text-slate-300 mb-3">${result.excerpt}</p>
                    <div class="flex items-center space-x-4 text-sm text-slate-400">
                        <span>${this.formatDate(result.created_at)}</span>
                        ${result.category ? `<span>• ${result.category}</span>` : ''}
                        ${result.author ? `<span>• ${result.author}</span>` : ''}
                    </div>
                </div>
            </div>
        `;
        
        return div;
    }
    
    getTypeIcon(type) {
        const icons = {
            'page': '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
            'service': '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
            'blog': '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>',
            'case_study': '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'job': '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path></svg>'
        };
        return icons[type] || icons['page'];
    }
    
    getTypeLabel(type) {
        const labels = {
            'page': 'Page',
            'service': 'Service',
            'blog': 'Blog',
            'case_study': 'Case Study',
            'job': 'Job'
        };
        return labels[type] || 'Content';
    }
    
    formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }
    
    displayPagination(pagination) {
        const paginationElement = document.getElementById('pagination');
        
        if (pagination.total_pages <= 1) {
            paginationElement.innerHTML = '';
            return;
        }
        
        let html = '';
        
        // Previous button
        if (pagination.has_prev) {
            html += `<button onclick="searchManager.goToPage(${pagination.page - 1})" class="px-3 py-2 text-slate-300 hover:text-white transition-colors">Previous</button>`;
        }
        
        // Page numbers
        for (let i = 1; i <= pagination.total_pages; i++) {
            if (i === pagination.page) {
                html += `<span class="px-3 py-2 bg-blue-600 text-white rounded-lg">${i}</span>`;
            } else {
                html += `<button onclick="searchManager.goToPage(${i})" class="px-3 py-2 text-slate-300 hover:text-white transition-colors">${i}</button>`;
            }
        }
        
        // Next button
        if (pagination.has_next) {
            html += `<button onclick="searchManager.goToPage(${pagination.page + 1})" class="px-3 py-2 text-slate-300 hover:text-white transition-colors">Next</button>`;
        }
        
        paginationElement.innerHTML = html;
    }
    
    goToPage(page) {
        this.currentPage = page;
        this.performSearch();
    }
    
    clearFilters() {
        this.currentFilters = {};
        document.getElementById('contentTypeFilter').value = '';
        document.getElementById('categoryFilter').value = '';
        this.performSearch();
    }
    
    showLoading() {
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('resultsList').classList.add('hidden');
    }
    
    hideLoading() {
        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('resultsList').classList.remove('hidden');
    }
    
    showNoResults() {
        document.getElementById('noResultsState').classList.remove('hidden');
        document.getElementById('resultsList').classList.add('hidden');
    }
    
    hideNoResults() {
        document.getElementById('noResultsState').classList.add('hidden');
        document.getElementById('resultsList').classList.remove('hidden');
    }
    
    hideResults() {
        document.getElementById('resultsList').classList.add('hidden');
        document.getElementById('noResultsState').classList.add('hidden');
        document.getElementById('loadingState').classList.add('hidden');
    }
    
    showError(message) {
        // You can implement a toast notification here
        console.error(message);
    }
    
    updateURL() {
        const params = new URLSearchParams();
        if (this.currentQuery) {
            params.set('q', this.currentQuery);
        }
        if (this.currentPage > 1) {
            params.set('page', this.currentPage);
        }
        Object.entries(this.currentFilters).forEach(([key, value]) => {
            if (value) {
                params.set(`filters[${key}]`, value);
            }
        });
        
        const newURL = params.toString() ? `?${params.toString()}` : window.location.pathname;
        window.history.pushState({}, '', newURL);
    }
    
    async loadSearchSuggestions() {
        try {
            const response = await fetch('search-api.php?q=');
            const data = await response.json();
            
            if (data.suggestions && data.suggestions.length > 0) {
                this.displaySuggestions(data.suggestions);
            }
        } catch (error) {
            console.error('Failed to load suggestions:', error);
        }
    }
    
    displaySuggestions(suggestions) {
        const suggestionsList = document.getElementById('suggestionsList');
        suggestionsList.innerHTML = '';
        
        suggestions.forEach(suggestion => {
            const div = document.createElement('div');
            div.className = 'text-slate-300 hover:text-white cursor-pointer transition-colors';
            div.textContent = suggestion;
            div.addEventListener('click', () => {
                document.getElementById('searchInput').value = suggestion;
                this.currentQuery = suggestion;
                this.performSearch();
            });
            suggestionsList.appendChild(div);
        });
        
        document.getElementById('searchSuggestions').classList.remove('hidden');
    }
}

// Initialize search manager
const searchManager = new SearchManager();
</script> 