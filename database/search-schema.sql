-- Search System Database Schema
-- Tables for search functionality and analytics

-- Search logs table
CREATE TABLE IF NOT EXISTS search_logs (
    id SERIAL PRIMARY KEY,
    search_term VARCHAR(255) NOT NULL,
    results_count INTEGER DEFAULT 0,
    filters JSONB,
    ip_address VARCHAR(45),
    user_agent TEXT,
    session_id VARCHAR(255),
    searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Search analytics table
CREATE TABLE IF NOT EXISTS search_analytics (
    id SERIAL PRIMARY KEY,
    date DATE NOT NULL,
    total_searches INTEGER DEFAULT 0,
    unique_searches INTEGER DEFAULT 0,
    zero_result_searches INTEGER DEFAULT 0,
    avg_results_per_search DECIMAL(10,2) DEFAULT 0,
    most_popular_term VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Search suggestions table
CREATE TABLE IF NOT EXISTS search_suggestions (
    id SERIAL PRIMARY KEY,
    suggestion VARCHAR(255) NOT NULL,
    search_count INTEGER DEFAULT 1,
    last_searched TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Search result clicks table
CREATE TABLE IF NOT EXISTS search_clicks (
    id SERIAL PRIMARY KEY,
    search_log_id INTEGER REFERENCES search_logs(id),
    result_url TEXT NOT NULL,
    result_title VARCHAR(500),
    result_type VARCHAR(50),
    click_position INTEGER,
    clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_search_logs_search_term ON search_logs(search_term);
CREATE INDEX IF NOT EXISTS idx_search_logs_searched_at ON search_logs(searched_at);
CREATE INDEX IF NOT EXISTS idx_search_logs_ip_address ON search_logs(ip_address);

CREATE INDEX IF NOT EXISTS idx_search_analytics_date ON search_analytics(date);

CREATE INDEX IF NOT EXISTS idx_search_suggestions_suggestion ON search_suggestions(suggestion);
CREATE INDEX IF NOT EXISTS idx_search_suggestions_search_count ON search_suggestions(search_count);

CREATE INDEX IF NOT EXISTS idx_search_clicks_search_log_id ON search_clicks(search_log_id);
CREATE INDEX IF NOT EXISTS idx_search_clicks_clicked_at ON search_clicks(clicked_at);

-- Create views for search analytics
CREATE OR REPLACE VIEW search_trends AS
SELECT 
    DATE(searched_at) as date,
    COUNT(*) as total_searches,
    COUNT(DISTINCT search_term) as unique_terms,
    COUNT(DISTINCT ip_address) as unique_users,
    AVG(results_count) as avg_results,
    COUNT(CASE WHEN results_count = 0 THEN 1 END) as zero_results
FROM search_logs 
WHERE searched_at >= NOW() - INTERVAL '30 days'
GROUP BY DATE(searched_at)
ORDER BY date DESC;

CREATE OR REPLACE VIEW popular_search_terms AS
SELECT 
    search_term,
    COUNT(*) as search_count,
    AVG(results_count) as avg_results,
    COUNT(DISTINCT ip_address) as unique_users,
    MAX(searched_at) as last_searched
FROM search_logs 
WHERE searched_at >= NOW() - INTERVAL '30 days'
GROUP BY search_term
ORDER BY search_count DESC
LIMIT 50;

CREATE OR REPLACE VIEW search_performance AS
SELECT 
    DATE(searched_at) as date,
    COUNT(*) as total_searches,
    COUNT(CASE WHEN results_count > 0 THEN 1 END) as successful_searches,
    COUNT(CASE WHEN results_count = 0 THEN 1 END) as failed_searches,
    ROUND(
        (COUNT(CASE WHEN results_count > 0 THEN 1 END)::DECIMAL / COUNT(*)) * 100, 
        2
    ) as success_rate
FROM search_logs 
WHERE searched_at >= NOW() - INTERVAL '30 days'
GROUP BY DATE(searched_at)
ORDER BY date DESC; 