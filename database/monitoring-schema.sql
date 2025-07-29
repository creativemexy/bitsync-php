-- Monitoring System Database Schema
-- Tables for tracking performance, errors, analytics, and system health

-- Request logs table
CREATE TABLE IF NOT EXISTS request_logs (
    id SERIAL PRIMARY KEY,
    request_id VARCHAR(255) NOT NULL,
    url TEXT,
    method VARCHAR(10),
    ip_address VARCHAR(45),
    user_agent TEXT,
    referer TEXT,
    started_at TIMESTAMP,
    ended_at TIMESTAMP,
    duration_ms DECIMAL(10,2),
    memory_start BIGINT,
    memory_used BIGINT,
    memory_peak BIGINT,
    status_code INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- System errors table
CREATE TABLE IF NOT EXISTS system_errors (
    id SERIAL PRIMARY KEY,
    request_id VARCHAR(255),
    error_type VARCHAR(255),
    error_message TEXT,
    error_file VARCHAR(500),
    error_line INTEGER,
    error_trace TEXT,
    context JSONB,
    url TEXT,
    user_agent TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Page views tracking
CREATE TABLE IF NOT EXISTS page_views (
    id SERIAL PRIMARY KEY,
    request_id VARCHAR(255),
    page VARCHAR(255),
    title VARCHAR(500),
    url TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referer TEXT,
    session_id VARCHAR(255),
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Form submissions tracking
CREATE TABLE IF NOT EXISTS form_submissions (
    id SERIAL PRIMARY KEY,
    request_id VARCHAR(255),
    form_type VARCHAR(100),
    success BOOLEAN,
    data JSONB,
    ip_address VARCHAR(45),
    user_agent TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Performance metrics
CREATE TABLE IF NOT EXISTS performance_metrics (
    id SERIAL PRIMARY KEY,
    request_id VARCHAR(255),
    metric_name VARCHAR(100),
    metric_value DECIMAL(10,2),
    metric_unit VARCHAR(20),
    url TEXT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- System health snapshots
CREATE TABLE IF NOT EXISTS system_health (
    id SERIAL PRIMARY KEY,
    database_status JSONB,
    disk_space JSONB,
    memory_usage JSONB,
    uptime VARCHAR(255),
    active_users INTEGER,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User sessions tracking
CREATE TABLE IF NOT EXISTS user_sessions (
    id SERIAL PRIMARY KEY,
    session_id VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT true
);

-- API usage tracking
CREATE TABLE IF NOT EXISTS api_usage (
    id SERIAL PRIMARY KEY,
    endpoint VARCHAR(255),
    method VARCHAR(10),
    ip_address VARCHAR(45),
    user_agent TEXT,
    response_time DECIMAL(10,2),
    status_code INTEGER,
    request_size INTEGER,
    response_size INTEGER,
    called_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Security events
CREATE TABLE IF NOT EXISTS security_events (
    id SERIAL PRIMARY KEY,
    event_type VARCHAR(100),
    severity VARCHAR(20),
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    url TEXT,
    details JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_request_logs_request_id ON request_logs(request_id);
CREATE INDEX IF NOT EXISTS idx_request_logs_started_at ON request_logs(started_at);
CREATE INDEX IF NOT EXISTS idx_request_logs_ip_address ON request_logs(ip_address);

CREATE INDEX IF NOT EXISTS idx_system_errors_created_at ON system_errors(created_at);
CREATE INDEX IF NOT EXISTS idx_system_errors_error_type ON system_errors(error_type);

CREATE INDEX IF NOT EXISTS idx_page_views_viewed_at ON page_views(viewed_at);
CREATE INDEX IF NOT EXISTS idx_page_views_page ON page_views(page);
CREATE INDEX IF NOT EXISTS idx_page_views_ip_address ON page_views(ip_address);

CREATE INDEX IF NOT EXISTS idx_form_submissions_submitted_at ON form_submissions(submitted_at);
CREATE INDEX IF NOT EXISTS idx_form_submissions_form_type ON form_submissions(form_type);

CREATE INDEX IF NOT EXISTS idx_performance_metrics_recorded_at ON performance_metrics(recorded_at);
CREATE INDEX IF NOT EXISTS idx_performance_metrics_metric_name ON performance_metrics(metric_name);

CREATE INDEX IF NOT EXISTS idx_user_sessions_session_id ON user_sessions(session_id);
CREATE INDEX IF NOT EXISTS idx_user_sessions_last_activity ON user_sessions(last_activity);

CREATE INDEX IF NOT EXISTS idx_api_usage_called_at ON api_usage(called_at);
CREATE INDEX IF NOT EXISTS idx_api_usage_endpoint ON api_usage(endpoint);

CREATE INDEX IF NOT EXISTS idx_security_events_created_at ON security_events(created_at);
CREATE INDEX IF NOT EXISTS idx_security_events_event_type ON security_events(event_type);
CREATE INDEX IF NOT EXISTS idx_security_events_severity ON security_events(severity);

-- Create a view for recent activity
CREATE OR REPLACE VIEW recent_activity AS
SELECT 
    'page_view' as activity_type,
    viewed_at as activity_time,
    page as description,
    ip_address
FROM page_views 
WHERE viewed_at >= NOW() - INTERVAL '24 hours'
UNION ALL
SELECT 
    'form_submission' as activity_type,
    submitted_at as activity_time,
    form_type as description,
    ip_address
FROM form_submissions 
WHERE submitted_at >= NOW() - INTERVAL '24 hours'
UNION ALL
SELECT 
    'error' as activity_type,
    created_at as activity_time,
    error_type as description,
    ip_address
FROM system_errors 
WHERE created_at >= NOW() - INTERVAL '24 hours'
ORDER BY activity_time DESC;

-- Create a view for daily statistics
CREATE OR REPLACE VIEW daily_stats AS
SELECT 
    DATE(viewed_at) as date,
    COUNT(*) as page_views,
    COUNT(DISTINCT ip_address) as unique_visitors,
    COUNT(DISTINCT session_id) as sessions
FROM page_views 
WHERE viewed_at >= NOW() - INTERVAL '30 days'
GROUP BY DATE(viewed_at)
ORDER BY date DESC;

-- Create a view for performance summary
CREATE OR REPLACE VIEW performance_summary AS
SELECT 
    metric_name,
    AVG(metric_value) as avg_value,
    MAX(metric_value) as max_value,
    MIN(metric_value) as min_value,
    COUNT(*) as sample_count
FROM performance_metrics 
WHERE recorded_at >= NOW() - INTERVAL '24 hours'
GROUP BY metric_name
ORDER BY avg_value DESC; 