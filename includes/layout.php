<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - BitSync Group' : 'BitSync Group - Technology Solutions'; ?></title>
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
    </style>
</head>
<body class="min-h-screen bg-background">
    <?php include 'includes/header.php'; ?>
    
    <main>
        <?php include $page_file; ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html> 