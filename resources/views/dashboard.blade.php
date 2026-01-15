<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Analysis Dashboard - Stockapalooza</title>
    <style>
        /* Import Professional Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Professional Color Palette */
            --bg-primary: #0a0e1a;
            --bg-secondary: #111827;
            --bg-card: #1a1f35;
            --bg-card-hover: #1f2640;

            /* Text Colors */
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-tertiary: #94a3b8;

            /* Accent Colors */
            --accent-primary: #3b82f6;
            --accent-secondary: #6366f1;

            /* Financial Colors */
            --color-bullish: #22c55e;
            --color-bearish: #ef4444;
            --color-neutral: #f59e0b;

            /* Border & Shadow */
            --border-color: #1e293b;
            --border-accent: #334155;
            --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.3);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.3);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.4);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.5);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 0;
            line-height: 1.6;
            letter-spacing: -0.011em;
        }

        .container {
            max-width: 1920px;
            margin: 0 auto;
            padding: 24px 32px;
        }

        header {
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-card) 100%);
            border: 1px solid var(--border-accent);
            border-radius: 16px;
            padding: 40px 48px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary), var(--color-bullish));
        }

        header h1 {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
            background: linear-gradient(135deg, var(--text-primary), var(--text-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        header p {
            color: var(--text-secondary);
            font-size: 1rem;
            font-weight: 400;
        }

        .search-bar {
            background: var(--bg-card);
            border: 1px solid var(--border-accent);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-md);
        }

        .search-section {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .search-section input {
            flex: 1;
            min-width: 240px;
            padding: 12px 16px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-accent);
            border-radius: 10px;
            font-size: 0.9375rem;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .search-section input::placeholder {
            color: var(--text-tertiary);
        }

        .search-section input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: var(--bg-primary);
        }

        button {
            padding: 12px 24px;
            background: var(--accent-primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-sm);
        }

        button:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        button.btn-success {
            background: var(--color-bullish);
        }

        button.btn-success:hover {
            background: #16a34a;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        button.btn-danger {
            background: var(--color-bearish);
        }

        button.btn-danger:hover {
            background: #dc2626;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        button.btn-secondary {
            background: var(--border-accent);
            color: var(--text-secondary);
        }

        button.btn-secondary:hover {
            background: #475569;
            color: var(--text-primary);
        }

        .quick-picks {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .quick-pick-btn {
            padding: 8px 16px;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border: 1px solid var(--border-accent);
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .quick-pick-btn:hover {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
            color: white;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow-md);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            border-color: var(--border-accent);
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .card h3 {
            color: var(--text-primary);
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-accent);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--text-primary);
        }

        .compact-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        @media (max-width: 1200px) {
            .compact-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .compact-grid {
                grid-template-columns: 1fr;
            }
        }

        .stock-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .stock-name h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .stock-symbol {
            color: var(--accent-primary);
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.125rem;
        }

        .price-box {
            text-align: right;
        }

        .current-price {
            font-size: 3rem;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .price-change {
            font-size: 1.25rem;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .price-change.positive { color: var(--color-bullish); }
        .price-change.negative { color: var(--color-bearish); }
        .price-change.positive::before { content: '▲'; font-size: 0.75em; }
        .price-change.negative::before { content: '▼'; font-size: 0.75em; }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            margin: 4px 6px 4px 0;
            line-height: 1.4;
        }

        .badge.success {
            background: rgba(34, 197, 94, 0.15);
            color: var(--color-bullish);
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .badge.danger {
            background: rgba(239, 68, 68, 0.15);
            color: var(--color-bearish);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .badge.warning {
            background: rgba(245, 158, 11, 0.15);
            color: var(--color-neutral);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .badge.info {
            background: rgba(59, 130, 246, 0.15);
            color: var(--accent-primary);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        .badge.secondary {
            background: rgba(100, 116, 139, 0.15);
            color: var(--text-secondary);
            border: 1px solid var(--border-accent);
        }

        .metric-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 16px;
            margin: 16px 0;
        }

        .metric {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            padding: 16px;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .metric:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-accent);
        }

        .metric-label {
            font-size: 0.8125rem;
            color: var(--text-tertiary);
            margin-bottom: 6px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            line-height: 1.2;
        }

        .metric-small {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 4px;
            font-weight: 400;
        }

        .entry-exit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin: 20px 0;
        }

        .entry-exit-zone {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            padding: 20px;
            border-radius: 12px;
            border-left: 3px solid var(--accent-primary);
            transition: all 0.2s ease;
        }

        .entry-exit-zone:hover {
            background: var(--bg-card-hover);
            border-color: var(--border-accent);
            box-shadow: var(--shadow-md);
        }

        .zone-label {
            font-size: 0.8125rem;
            color: var(--text-tertiary);
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        .zone-price {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            margin-bottom: 8px;
            line-height: 1;
        }

        .zone-desc {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .zone-distance {
            font-size: 0.875rem;
            margin-top: 12px;
            padding: 6px 12px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            display: inline-block;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .loading {
            text-align: center;
            padding: 80px 20px;
            color: var(--accent-primary);
        }

        .spinner {
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--accent-primary);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 24px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-accent);
            border-radius: 6px;
            border: 2px solid var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        /* Selection Styling */
        ::selection {
            background: rgba(59, 130, 246, 0.3);
            color: var(--text-primary);
        }

        /* Responsive Typography */
        @media (max-width: 768px) {
            header h1 {
                font-size: 1.75rem;
            }

            .current-price {
                font-size: 2rem;
            }

            .price-change {
                font-size: 1rem;
            }

            .container {
                padding: 16px 20px;
            }

            .card {
                padding: 20px;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .error-box {
            background: #7f1d1d;
            border: 2px solid #dc2626;
            color: #fca5a5;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .support-resistance {
            margin: 15px 0;
        }

        .level-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .level-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            padding: 12px;
            border-radius: 6px;
        }

        .level-item.support {
            border-left: 4px solid #10b981;
        }

        .level-item.resistance {
            border-left: 4px solid #ef4444;
        }

        .reason-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .reason-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            background: #0f172a;
            border-radius: 6px;
        }

        .reason-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .reason-icon.positive { background: #10b981; }
        .reason-icon.negative { background: #ef4444; }
        .reason-icon.warning { background: #f59e0b; }
        .reason-icon.neutral { background: #6b7280; }

        .favorite-btn {
            padding: 10px 20px;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .favorites-sidebar {
            position: fixed;
            right: 0;
            top: 0;
            width: 300px;
            height: 100vh;
            background: #1e293b;
            box-shadow: -5px 0 15px rgba(0,0,0,0.3);
            padding: 20px;
            overflow-y: auto;
            transform: translateX(100%);
            transition: transform 0.3s;
            z-index: 1000;
        }

        .favorites-sidebar.open {
            transform: translateX(0);
        }

        .favorites-item {
            background: #0f172a;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .favorites-item:hover {
            background: #334155;
        }

        .toggle-favorites {
            position: fixed;
            right: 20px;
            top: 20px;
            z-index: 999;
            padding: 12px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .stock-header {
                flex-direction: column;
                gap: 15px;
            }

            .price-box {
                text-align: left;
            }

            .favorites-sidebar {
                width: 100%;
            }
        }

        .swing-indicators {
            margin: 15px 0;
        }

        .indicator-item {
            background: #0f172a;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .indicator-label {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-bottom: 5px;
        }

        .indicator-value {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .phase-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: center;
        }

        .phase-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .phase-desc {
            font-size: 1rem;
            opacity: 0.95;
        }

        .executive-summary {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            border: 2px solid #475569;
        }

        .executive-title {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .executive-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 15px;
        }

        .executive-box {
            background: #0f172a;
            padding: 12px 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            transition: transform 0.2s;
        }

        .executive-box:hover {
            transform: translateY(-2px);
        }

        .executive-box.highlight {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #064e3b 0%, #0f172a 100%);
        }

        .executive-box.warning {
            border-left-color: #f59e0b;
        }

        .executive-box.danger {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #7f1d1d 0%, #0f172a 100%);
        }

        .executive-label {
            font-size: 0.7rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .executive-value {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .executive-desc {
            font-size: 0.75rem;
            color: #cbd5e1;
            line-height: 1.3;
        }

        .quick-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #334155;
        }

        .action-button {
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .action-button.primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .action-button.primary:hover {
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            transform: translateY(-2px);
        }

        .action-button.secondary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .action-button.secondary:hover {
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
        }

        /* Mobile-Responsive Classes for Dynamic Content - Tailwind-Inspired */
        .institutional-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        @media (min-width: 768px) {
            .institutional-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1280px) {
            .institutional-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .institutional-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        @media (max-width: 767px) {
            .institutional-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        .institutional-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.5rem;
        }

        @media (max-width: 480px) {
            .institutional-card-header {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        .institutional-card-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            gap: 0.5rem;
        }

        @media (max-width: 480px) {
            .institutional-card-stats {
                flex-direction: column;
                gap: 0.75rem;
            }
        }

        .near-miss-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            width: 100%;
        }

        .near-miss-tab-button {
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 400;
            font-size: 0.875rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            min-height: 44px;
            min-width: 44px;
        }

        .near-miss-tab-button:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        .near-miss-tab-button:active {
            transform: scale(0.95);
        }

        @media (min-width: 768px) {
            .near-miss-tab-button {
                font-size: 1rem;
                padding: 0.75rem 1.5rem;
            }
        }

        @media (max-width: 767px) {
            .near-miss-tab-button {
                flex: 1;
                min-width: 0;
                justify-content: center;
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
            }
        }

        .near-miss-card-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.5rem;
        }

        @media (min-width: 640px) {
            .near-miss-card-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .near-miss-additional-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 0.375rem;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(100, 116, 139, 0.2);
            font-size: 0.75rem;
        }

        @media (min-width: 640px) {
            .near-miss-additional-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body>
    <button class="toggle-favorites" onclick="toggleFavorites()">
        ⭐ Favorites (<span id="favCount">0</span>)
    </button>

    <div class="favorites-sidebar" id="favoritesSidebar">
        <h3 style="margin-bottom: 20px;">My Favorites</h3>
        <div id="favoritesList"></div>
    </div>

    <div class="container">
        <div class="search-bar" style="margin-top: 50px;">
            <div class="search-section">
                <select id="marketSelector" style="padding: 10px 15px; border-radius: 8px; border: 2px solid #4b5563; background: #1e293b; color: white; font-size: 0.9rem; margin-right: 10px; cursor: pointer;">
                    <option value="auto">🌐 Auto-detect</option>
                    <option value="idx">🇮🇩 Indonesia (IDX)</option>
                    <option value="us">🇺🇸 United States</option>
                </select>
                <input
                    type="text"
                    id="stockSymbol"
                    placeholder="Enter stock symbol (e.g., BBCA, AAPL, TSLA)"
                    onkeypress="if(event.key==='Enter') loadDashboard()"
                >
                <button onclick="loadDashboard()">Analyze</button>
                <button class="btn-secondary" onclick="window.location.href='/'">Simple View</button>
                <button class="btn-danger" onclick="clearSearchHistory()" style="margin-left: 10px;">Clear History</button>
            </div>

            <!-- Buy Opportunities Scanner -->
            <div id="buyOpportunities" style="margin-bottom: 30px;"></div>

            <!-- Institutional Stocks Scanner -->
            <div id="institutionalStocks" style="margin-bottom: 30px;"></div>

            <!-- Market Phase Scanner (Wyckoff Cycles) -->
            <div id="marketPhaseScanner" style="margin-bottom: 30px;"></div>

            <!-- 1. Indonesia Suggestions -->
            <div class="quick-picks" style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong style="margin-right: 10px;">🇮🇩 Indonesia - Suggestions:</strong>
                </div>
                <div style="overflow-x: auto; white-space: nowrap;">
                    <button class="quick-pick-btn" onclick="quickAnalyze('BBCA')">BBCA</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('BBRI')">BBRI</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('BMRI')">BMRI</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('TLKM')">TLKM</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('ASII')">ASII</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('UNVR')">UNVR</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('HMSP')">HMSP</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('ICBP')">ICBP</button>
                </div>
            </div>

            <!-- 2. Indonesia History -->
            <div class="quick-picks" style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong style="margin-right: 10px;">🇮🇩 Indonesia - Recent Searches:</strong>
                </div>
                <div id="idxHistory" style="overflow-x: auto; white-space: nowrap;">
                    <p style="color: #94a3b8; font-size: 0.9rem;">No Indonesia stocks searched yet</p>
                </div>
            </div>

            <!-- 3. US Suggestions -->
            <div class="quick-picks" style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong style="margin-right: 10px;">🇺🇸 United States - Suggestions:</strong>
                </div>
                <div style="overflow-x: auto; white-space: nowrap;">
                    <button class="quick-pick-btn" onclick="quickAnalyze('AAPL', 'us')">AAPL</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('MSFT', 'us')">MSFT</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('GOOGL', 'us')">GOOGL</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('TSLA', 'us')">TSLA</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('NVDA', 'us')">NVDA</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('META', 'us')">META</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('AMZN', 'us')">AMZN</button>
                    <button class="quick-pick-btn" onclick="quickAnalyze('NFLX', 'us')">NFLX</button>
                </div>
            </div>

            <!-- 4. US History -->
            <div class="quick-picks" style="margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong style="margin-right: 10px;">🇺🇸 United States - Recent Searches:</strong>
                </div>
                <div id="usHistory" style="overflow-x: auto; white-space: nowrap;">
                    <p style="color: #94a3b8; font-size: 0.9rem;">No US stocks searched yet</p>
                </div>
            </div>
        </div>

        <div id="dashboard"></div>
    </div>

    <script>
        let currentSymbol = '';
        let isFavorite = false;

        // Search history management
        function saveToHistory(symbol, market) {
            let history = JSON.parse(localStorage.getItem('searchHistory') || '[]');

            // Remove if already exists (to move to front)
            history = history.filter(item => item.symbol !== symbol);

            // Add to front
            history.unshift({
                symbol: symbol,
                market: market || 'auto',
                timestamp: new Date().toISOString()
            });

            // Keep only last 10
            history = history.slice(0, 10);

            localStorage.setItem('searchHistory', JSON.stringify(history));
            updateHistoryDisplay();
        }

        function getSearchHistory() {
            return JSON.parse(localStorage.getItem('searchHistory') || '[]');
        }

        function clearSearchHistory() {
            if (confirm('Clear all search history?')) {
                localStorage.removeItem('searchHistory');
                updateHistoryDisplay();
            }
        }

        function updateHistoryDisplay() {
            const history = getSearchHistory();
            const idxHistoryDiv = document.getElementById('idxHistory');
            const usHistoryDiv = document.getElementById('usHistory');

            // Group by market - prioritize explicit market value over pattern matching
            const grouped = {
                us: [],
                idx: []
            };

            history.forEach(item => {
                if (item.market === 'idx') {
                    // Explicitly marked as Indonesian stock
                    grouped.idx.push(item);
                } else if (item.market === 'us') {
                    // Explicitly marked as US stock
                    grouped.us.push(item);
                } else {
                    // Auto-detect for 'auto' market setting
                    if (item.symbol.includes('.JK') || item.symbol.length === 4) {
                        grouped.idx.push(item);
                    } else {
                        grouped.us.push(item);
                    }
                }
            });

            // Update Indonesia history
            if (grouped.idx.length === 0) {
                idxHistoryDiv.innerHTML = '<p style="color: #94a3b8; font-size: 0.9rem;">No Indonesia stocks searched yet</p>';
            } else {
                const idxHtml = grouped.idx.map(item => {
                    const cleanSymbol = item.symbol.replace('.JK', '');
                    return `<button class="quick-pick-btn" onclick="quickAnalyze('${cleanSymbol}', 'idx')">${cleanSymbol}</button>`;
                }).join('');
                idxHistoryDiv.innerHTML = idxHtml;
            }

            // Update US history
            if (grouped.us.length === 0) {
                usHistoryDiv.innerHTML = '<p style="color: #94a3b8; font-size: 0.9rem;">No US stocks searched yet</p>';
            } else {
                const usHtml = grouped.us.map(item => {
                    return `<button class="quick-pick-btn" onclick="quickAnalyze('${item.symbol}', 'us')">${item.symbol}</button>`;
                }).join('');
                usHistoryDiv.innerHTML = usHtml;
            }
        }

        function getTimeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);

            if (seconds < 60) return 'just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `${minutes}m ago`;
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `${hours}h ago`;
            const days = Math.floor(hours / 24);
            return `${days}d ago`;
        }

        async function loadDashboard() {
            const symbol = document.getElementById('stockSymbol').value.trim().toUpperCase();
            const market = document.getElementById('marketSelector').value;

            if (!symbol) {
                alert('Please enter a stock symbol');
                return;
            }

            currentSymbol = symbol;

            // Save to history
            saveToHistory(symbol, market);

            showLoading();

            try {
                const marketParam = market !== 'auto' ? `?market=${market}` : '';
                const response = await fetch(`/api/dashboard/${symbol}${marketParam}`);
                const data = await response.json();

                if (data.success) {
                    displayDashboard(data.data);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Error fetching dashboard data: ' + error.message);
            }
        }

        function quickAnalyze(symbol, market = null) {
            document.getElementById('stockSymbol').value = symbol;
            if (market && market !== 'auto') {
                document.getElementById('marketSelector').value = market;
            }
            loadDashboard();
        }

        // Load buy opportunities with optional force refresh (async, non-blocking)
        async function loadBuyOpportunities(forceRefresh = false) {
            const container = document.getElementById('buyOpportunities');

            // Show compact loading indicator that doesn't block the page
            container.innerHTML = `
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="spinner" style="border-color: #fff transparent transparent transparent; width: 24px; height: 24px; border-width: 3px;"></div>
                        <div>
                            <div style="color: #fff; font-weight: bold;">🔍 ${forceRefresh ? 'Force scanning all Indonesian stocks...' : 'Loading buy opportunities...'}</div>
                            <div style="color: rgba(255,255,255,0.7); font-size: 0.8rem; margin-top: 3px;">
                                ${forceRefresh ? 'This may take a moment. Feel free to use other features while waiting.' : 'Loading from cache...'}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            try {
                const url = forceRefresh ? '/api/scan-opportunities?market=auto&refresh=true' : '/api/scan-opportunities?market=auto';
                const response = await fetch(url);
                const data = await response.json();

                if (data.success) {
                    displayBuyOpportunities(data);

                    // Show success notification if force refresh
                    if (forceRefresh) {
                        const oppCount = data.opportunities_found || 0;
                        const nearMissCount = data.near_misses_found || 0;
                        showNotification(`✅ Scanned ${data.scanned} stocks! Found ${oppCount} buy opportunities and ${nearMissCount} near-misses`, 'success');
                    }
                } else {
                    container.innerHTML = `
                        <div style="text-align: center; padding: 20px; background: #1e293b; border-radius: 12px; border: 2px solid #334155;">
                            <p style="color: #ef4444;">⚠️ Error loading scanner results</p>
                            <button onclick="loadBuyOpportunities(true)" style="background: rgba(96, 165, 250, 0.2); border: 1px solid #3b82f6; color: #60a5fa; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                                🔄 Retry
                            </button>
                        </div>
                    `;
                }
            } catch (error) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 20px; background: #1e293b; border-radius: 12px; border: 2px solid #ef4444;">
                        <p style="color: #ef4444;">⚠️ Error loading opportunities: ${error.message}</p>
                        <button onclick="loadBuyOpportunities()" style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #ef4444; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                            🔄 Retry
                        </button>
                    </div>
                `;
            }
        }

        // Load buy opportunities filtered by market
        async function loadBuyOpportunitiesByMarket(market, forceRefresh = false) {
            console.log('🚀 loadBuyOpportunitiesByMarket called with:', { market, forceRefresh });

            const container = document.getElementById('buyOpportunities');
            console.log('📦 Container found:', container ? 'YES' : 'NO');

            const marketInfo = {
                idx: { name: '🇮🇩 Indonesia', shortName: 'IDX' },
                sgx: { name: '🇸🇬 Singapore', shortName: 'SGX' },
                us: { name: '🇺🇸 United States', shortName: 'US' }
            };
            const currentMarket = marketInfo[market] || { name: 'All Markets', shortName: 'ALL' };
            console.log('🌍 Current market:', currentMarket);

            // Show loading indicator
            container.innerHTML = `
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-4 shadow-lg">
                    <div class="flex items-center gap-3">
                        <div class="spinner !border-white !border-t-transparent !w-6 !h-6 !border-[3px]"></div>
                        <div>
                            <div class="text-white font-bold">🔍 Loading ${currentMarket.name} opportunities...</div>
                            <div class="text-white/70 text-xs mt-1">
                                ${forceRefresh ? 'Refreshing data...' : 'Loading from cache...'}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            try {
                const url = forceRefresh ? '/api/scan-opportunities?market=auto&refresh=true' : '/api/scan-opportunities?market=auto';
                console.log('🌐 Fetching from URL:', url);

                const response = await fetch(url);
                console.log('✅ Response status:', response.status);

                const data = await response.json();
                console.log('📊 API Data received:', {
                    success: data.success,
                    opportunities: data.data?.length || 0,
                    nearMisses: data.near_misses?.length || 0,
                    scanned: data.scanned
                });

                if (data.success) {
                    console.log('✨ Calling displayBuyOpportunities with market:', market);
                    displayBuyOpportunities(data, market);

                    // Show success notification if force refresh
                    if (forceRefresh) {
                        showNotification(`✅ Refreshed ${currentMarket.shortName} market data!`, 'success');
                    }
                } else {
                    container.innerHTML = `
                        <div class="text-center p-5 bg-slate-800 rounded-xl border-2 border-slate-600">
                            <p class="text-red-500">⚠️ Error loading scanner results</p>
                            <button onclick="loadBuyOpportunitiesByMarket('${market}')" class="bg-blue-500 bg-opacity-20 border border-blue-500 text-blue-400 px-4 py-2 rounded-md cursor-pointer mt-2.5 hover:bg-opacity-30 transition">
                                🔄 Retry
                            </button>
                        </div>
                    `;
                }
            } catch (error) {
                container.innerHTML = `
                    <div class="text-center p-5 bg-slate-800 rounded-xl border-2 border-red-500">
                        <p class="text-red-500">⚠️ Error loading opportunities: ${error.message}</p>
                        <button onclick="loadBuyOpportunitiesByMarket('${market}')" class="bg-red-500 bg-opacity-20 border border-red-500 text-red-500 px-4 py-2 rounded-md cursor-pointer mt-2.5 hover:bg-opacity-30 transition">
                            🔄 Retry
                        </button>
                    </div>
                `;
            }
        }

        // Simple notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : '#3b82f6'};
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function displayBuyOpportunities(data, selectedMarket = null) {
            console.log('🎨 displayBuyOpportunities called with:', { selectedMarket, hasData: !!data });

            const container = document.getElementById('buyOpportunities');
            console.log('📦 Container in display function:', container ? 'FOUND' : 'NOT FOUND');

            let opportunities = data.data || [];
            let nearMisses = data.near_misses || [];
            const scanned = data.scanned || 0;
            const cachedAt = data.cached_at || null;
            const instanceId = 'nearMiss_' + Date.now();

            console.log('📋 Before filtering:', {
                opportunities: opportunities.length,
                nearMisses: nearMisses.length
            });

            // Filter by market if specified
            if (selectedMarket) {
                opportunities = opportunities.filter(opp => {
                    const market = opp.market === 'auto' ? 'us' : opp.market;
                    return market === selectedMarket;
                });
                nearMisses = nearMisses.filter(nm => {
                    const market = nm.market === 'auto' ? 'us' : nm.market;
                    return market === selectedMarket;
                });
            }

            const opportunitiesFound = opportunities.length;
            const nearMissesFound = nearMisses.length;

            console.log('📋 After filtering:', {
                opportunities: opportunitiesFound,
                nearMisses: nearMissesFound
            });

            // Market info for display
            const marketInfo = {
                idx: { name: '🇮🇩 Indonesia', shortName: 'IDX', color: '#ef4444', flag: '🇮🇩' },
                sgx: { name: '🇸🇬 Singapore', shortName: 'SGX', color: '#8b5cf6', flag: '🇸🇬' },
                us: { name: '🇺🇸 United States', shortName: 'US', color: '#3b82f6', flag: '🇺🇸' }
            };
            const currentMarketInfo = selectedMarket ? marketInfo[selectedMarket] : null;
            console.log('🎯 Current market info:', currentMarketInfo);

            const marketTitle = currentMarketInfo ? `- ${currentMarketInfo.name}` : '';
            const subtitle = selectedMarket ? `Showing stocks from ${currentMarketInfo.shortName} market` : 'Expanded Scanner (150 stocks: 50 IDX + 50 SGX + 50 US)';
            const cachedInfo = cachedAt ? `<p style="margin: 4px 0 0 0; color: rgba(255,255,255,0.7); font-size: 0.75rem;">📅 Cached: ${cachedAt} • Auto-refreshes every 3 hours</p>` : '';
            const closeButton = selectedMarket ? '<button onclick="showBuyOpportunitiesPlaceholder()" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background=\'rgba(255,255,255,0.3)\'" onmouseout="this.style.background=\'rgba(255,255,255,0.2)\'">✖️ Close</button>' : '';
            const refreshMarket = selectedMarket || 'all';

            let html = `
                <div style="background: linear-gradient(135deg, #6366f1 0%, #9333ea 100%); border-radius: 12px; padding: 20px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);">
                    <!-- Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h2 style="margin: 0; color: #fff; font-size: 1.25rem; font-weight: bold;">🎯 Buy Opportunities Scanner ${marketTitle}</h2>
                            <p style="margin: 4px 0 0 0; color: rgba(255,255,255,0.9); font-size: 0.875rem;">
                                ${subtitle}
                            </p>
                            ${cachedInfo}
                        </div>
                        <div style="display: flex; gap: 8px;">
                            ${closeButton}
                            <button onclick="loadBuyOpportunitiesByMarket('${refreshMarket}')" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                                🔄 Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Scan Info Cards -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 20px;">
                        <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; border-radius: 8px; padding: 12px; text-align: center;">
                            <div style="font-size: 1.875rem; font-weight: bold; color: #10b981;">${opportunitiesFound}</div>
                            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.8);">BUY Opportunities</div>
                        </div>
                        <div style="background: rgba(245, 158, 11, 0.2); border: 1px solid #f59e0b; border-radius: 8px; padding: 12px; text-align: center;">
                            <div style="font-size: 1.875rem; font-weight: bold; color: #f59e0b;">${nearMissesFound}</div>
                            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.8);">Near-Misses</div>
                        </div>
                        <div style="background: rgba(96, 165, 250, 0.2); border: 1px solid #3b82f6; border-radius: 8px; padding: 12px; text-align: center;">
                            <div style="font-size: 1.875rem; font-weight: bold; color: #60a5fa;">${scanned}</div>
                            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.8);">Stocks Scanned</div>
                        </div>
                    </div>
            `;

            // Display BUY Opportunities
            if (opportunities.length > 0) {
                html += `
                    <h3 style="color: #fff; margin: 20px 0;">✅ BUY Opportunities</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; margin-bottom: 30px;">
                `;

                opportunities.forEach(opp => {
                    const bgColor = opp.action === 'STRONG BUY' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(34, 197, 94, 0.1)';
                    const borderColor = opp.action === 'STRONG BUY' ? '#10b981' : '#22c55e';
                    const actionBgColor = opp.action === 'STRONG BUY' ? '#10b981' : '#22c55e';

                    html += `
                        <div onclick="quickAnalyze('${opp.symbol}', '${opp.market}')" style="background: ${bgColor}; border: 2px solid ${borderColor}; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.3)';" onmouseout="this.style.transform=''; this.style.boxShadow='';">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                <div>
                                    <div style="font-size: 1.2rem; font-weight: bold; color: #fff;">${opp.symbol}</div>
                                    <div style="font-size: 0.75rem; color: rgba(255,255,255,0.7);">${opp.name.substring(0, 25)}${opp.name.length > 25 ? '...' : ''}</div>
                                </div>
                                <div style="background: ${actionBgColor}; color: #000; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">
                                    ${opp.action}
                                </div>
                            </div>
                            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.8); margin-bottom: 5px;">
                                📊 Score: <strong>${opp.score.toFixed(0)}/100</strong> • ${opp.confidence}
                            </div>
                        </div>
                    `;
                });

                html += `</div>`;
            } else {
                html += `
                    <div style="background: rgba(100, 116, 139, 0.1); border: 1px solid #64748b; border-radius: 8px; padding: 15px; text-align: center; margin-bottom: 30px;">
                        <p style="color: #94a3b8; margin: 0;">😔 No BUY opportunities found in current market conditions</p>
                    </div>
                `;
            }

            // Display Near-Misses (Simplified - no tabs needed since already filtered)
            if (nearMisses.length > 0) {
                html += `
                    <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h3 style="color: #f59e0b; margin: 0; font-size: 1.125rem; font-weight: bold;">⚠️ Near-Miss Stocks</h3>
                            <span style="font-size: 0.75rem; color: rgba(255,255,255,0.6);">Score 15-74/100</span>
                        </div>
                        <p style="font-size: 0.875rem; color: rgba(255,255,255,0.7); margin: 0 0 15px 0;">
                            Stocks that almost made the BUY list. These require further monitoring.
                        </p>
                        <div style="display: grid; gap: 10px;">
                `;

                nearMisses.slice(0, 10).forEach((stock, stockIndex) => {
                    const actionBg = stock.action === 'BUY' ? 'rgba(16, 185, 129, 0.2)' : stock.action === 'SELL' ? 'rgba(239, 68, 68, 0.2)' : 'rgba(245, 158, 11, 0.2)';
                    const actionColor = stock.action === 'BUY' ? '#10b981' : stock.action === 'SELL' ? '#ef4444' : '#f59e0b';
                    const rsiColor = stock.rsi > 70 ? '#ef4444' : stock.rsi < 30 ? '#10b981' : '#fff';
                    const macdColor = stock.macd_signal === 'BULLISH' ? '#10b981' : '#ef4444';
                    const severityIcon = (severity) => {
                        return severity === 'major' ? '❌' : severity === 'moderate' ? '⚠️' : 'ℹ️';
                    };

                    html += `
                        <div onclick="quickAnalyze('${stock.symbol}', '${stock.market}')"
                             style="background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(100, 116, 139, 0.3); border-radius: 8px; padding: 12px; cursor: pointer; transition: all 0.2s;"
                             onmouseover="this.style.borderColor='#f59e0b'; this.style.transform='translateX(4px)';"
                             onmouseout="this.style.borderColor='rgba(100, 116, 139, 0.3)'; this.style.transform=''">

                            <!-- Header -->
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-slate-600 text-xs font-mono">#${stockIndex + 1}</span>
                                        <span class="text-white font-bold text-base">${stock.symbol}</span>
                                        <span class="bg-slate-600 bg-opacity-30 text-slate-400 px-1.5 py-0.5 rounded text-[0.65rem]">
                                            ${(stock.market || 'auto').toUpperCase()}
                                        </span>
                                    </div>
                                    <div class="text-slate-600 text-xs">${stock.name ? stock.name.substring(0, 35) : ''}${stock.name && stock.name.length > 35 ? '...' : ''}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-amber-500 font-bold text-lg">${stock.score}/100</div>
                                    <div class="${actionBg} ${actionColor} px-2 py-0.5 rounded text-[0.7rem] mt-0.5">
                                        ${stock.action}
                                    </div>
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="near-miss-card-grid mb-2 p-2 bg-slate-900 bg-opacity-50 rounded-md">
                                <div>
                                    <div class="text-slate-600 text-[0.65rem]">Price</div>
                                    <div class="text-white text-sm font-semibold">${stock.price ? stock.price.toLocaleString() : 'N/A'}</div>
                                </div>
                                ${stock.rsi ? `
                                <div>
                                    <div class="text-slate-600 text-[0.65rem]">RSI</div>
                                    <div class="${rsiColor} text-sm font-semibold">
                                        ${stock.rsi.toFixed(1)}
                                    </div>
                                </div>
                                ` : ''}
                                ${stock.institutional_percent ? `
                                <div>
                                    <div class="text-slate-600 text-[0.65rem]">Inst. %</div>
                                    <div class="text-blue-400 text-sm font-semibold">${stock.institutional_percent}%</div>
                                </div>
                                ` : ''}
                            </div>

                            <!-- Summary -->
                            ${stock.summary ? `
                            <div class="bg-slate-600 bg-opacity-20 p-2 rounded-md mb-2">
                                <div class="text-white opacity-90 text-xs">${stock.summary}</div>
                            </div>
                            ` : ''}

                            <!-- Detailed Reasons -->
                            ${stock.near_miss_reasons && stock.near_miss_reasons.length > 0 ? `
                            <div class="flex flex-col gap-1">
                                ${stock.near_miss_reasons.slice(0, 3).map(reason => `
                                    <div class="flex items-start gap-1.5 text-[0.7rem]">
                                        <span class="flex-shrink-0">${severityIcon(reason.severity)}</span>
                                        <span class="text-slate-400 leading-tight">${reason.issue}</span>
                                    </div>
                                `).join('')}
                            </div>
                            ` : ''}

                            <!-- Additional Info -->
                            <div class="grid grid-cols-3 gap-1.5 mt-2 pt-2 border-t border-slate-600 border-opacity-20 text-[0.7rem]">
                                ${stock.macd_signal ? `
                                <div>
                                    <span class="text-slate-600">MACD:</span>
                                    <span class="${macdColor} font-semibold ml-0.5">
                                        ${stock.macd_signal}
                                    </span>
                                </div>
                                ` : ''}
                                ${stock.divergence && stock.divergence !== 'NONE' ? `
                                <div>
                                    <span class="text-slate-600">Div:</span>
                                    <span class="text-amber-500 font-semibold ml-0.5">${stock.divergence}</span>
                                </div>
                                ` : ''}
                                ${stock.accumulation_phase ? `
                                <div>
                                    <span class="text-slate-600">Phase:</span>
                                    <span class="text-blue-400 font-semibold ml-0.5 text-[0.65rem]">${stock.accumulation_phase.substring(0, 10)}</span>
                                </div>
                                ` : ''}
                            </div>

                            <!-- Click Hint -->
                            <div class="text-center mt-2 pt-2 border-t border-slate-600 border-opacity-20">
                                <span class="text-slate-600 text-[0.7rem]">👆 Click to view full analysis</span>
                            </div>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            }

            html += `
                    <p class="text-center text-white opacity-60 text-xs mt-5 mb-0">
                        💡 Click any stock to see full technical analysis
                    </p>
                </div>
            `;

            console.log('📝 Generated HTML length:', html.length, 'characters');
            console.log('📝 First 500 chars of HTML:', html.substring(0, 500));

            container.innerHTML = html;
            console.log('✅ HTML set to container. Container now has', container.children.length, 'children');
        }

        // Scan ALL stocks comprehensively (~200 Indonesian stocks)
        async function scanAllStocks() {
            const container = document.getElementById('buyOpportunities');

            container.innerHTML = `
                <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; padding: 20px; text-align: center;">
                    <div class="spinner" style="border-color: #fff transparent transparent transparent; width: 40px; height: 40px; margin: 0 auto;"></div>
                    <h3 style="color: #fff; margin: 20px 0 10px 0;">📊 Comprehensive Stock Scan</h3>
                    <p style="color: rgba(255,255,255,0.9); margin: 5px 0;">Scanning ALL ${200}+ Indonesian stocks...</p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.85rem;">This may take 1-2 minutes. Please wait...</p>
                </div>
            `;

            try {
                const response = await fetch('/api/scan-all-stocks?market=idx');
                const data = await response.json();

                if (data.success) {
                    displayComprehensiveScanResults(data);
                    showNotification(`✅ Scanned ${data.scanned} stocks! Found ${data.top_10_buy.length} buy opportunities and ${data.top_5_scalping.length} scalping stocks`, 'success');
                } else {
                    throw new Error('Scan failed');
                }
            } catch (error) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 20px; background: #1e293b; border-radius: 12px; border: 2px solid #ef4444;">
                        <p style="color: #ef4444;">⚠️ Error scanning all stocks: ${error.message}</p>
                        <button onclick="loadBuyOpportunities()" style="background: rgba(96, 165, 250, 0.2); border: 1px solid #3b82f6; color: #60a5fa; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                            ← Back to Preset Scan
                        </button>
                    </div>
                `;
            }
        }

        // Display results from comprehensive scan
        function displayComprehensiveScanResults(data) {
            const container = document.getElementById('buyOpportunities');
            const top10Buy = data.top_10_buy || [];
            const top5Scalping = data.top_5_scalping || [];

            let html = `
                <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h2 style="margin: 0; color: #fff;">📊 Comprehensive Scan Results</h2>
                                <p style="margin: 5px 0 0 0; color: rgba(255,255,255,0.9); font-size: 0.9rem;">
                                    Scanned ${data.scanned} stocks • Found ${data.all_buy_opportunities.length} BUY opportunities
                                </p>
                            </div>
                            <button onclick="loadBuyOpportunities()" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer;">
                                ← Back to Preset
                            </button>
                        </div>
                    </div>

                    <h3 style="color: #fff; margin: 15px 0 10px 0; border-bottom: 2px solid rgba(255,255,255,0.3); padding-bottom: 8px;">
                        🎯 Top 10 BUY/BULLISH Stocks
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; margin-bottom: 20px;">
            `;

            top10Buy.forEach(stock => {
                html += `
                    <div onclick="quickAnalyze('${stock.symbol}', 'idx')" style="background: rgba(16, 185, 129, 0.2); border: 2px solid #10b981; border-radius: 8px; padding: 15px; cursor: pointer;"
                         onmouseover="this.style.transform='translateY(-2px)'"
                         onmouseout="this.style.transform=''">
                        <div style="font-size: 1.1rem; font-weight: bold; color: #fff; margin-bottom: 5px;">${stock.symbol}</div>
                        <div style="font-size: 0.75rem; color: rgba(255,255,255,0.7); margin-bottom: 8px;">${stock.name}</div>
                        <div style="color: #10b981; font-weight: bold; font-size: 0.85rem;">${stock.action}</div>
                        <div style="color: rgba(255,255,255,0.8); font-size: 0.8rem; margin-top: 5px;">Score: ${stock.score}/100</div>
                    </div>
                `;
            });

            html += `
                    </div>
                    <h3 style="color: #fff; margin: 15px 0 10px 0; border-bottom: 2px solid rgba(255,255,255,0.3); padding-bottom: 8px;">
                        ⚡ Top 5 SCALPING Stocks (High Volatility)
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
            `;

            top5Scalping.forEach(stock => {
                html += `
                    <div onclick="quickAnalyze('${stock.symbol}', 'idx')" style="background: rgba(251, 191, 36, 0.2); border: 2px solid #fbbf24; border-radius: 8px; padding: 15px; cursor: pointer;"
                         onmouseover="this.style.transform='translateY(-2px)'"
                         onmouseout="this.style.transform=''">
                        <div style="font-size: 1.1rem; font-weight: bold; color: #fff; margin-bottom: 5px;">${stock.symbol}</div>
                        <div style="font-size: 0.75rem; color: rgba(255,255,255,0.7); margin-bottom: 8px;">${stock.name}</div>
                        <div style="color: #fbbf24; font-weight: bold; font-size: 0.85rem;">Volatility: ${stock.volatility.toFixed(1)}%</div>
                        <div style="color: rgba(255,255,255,0.8); font-size: 0.8rem; margin-top: 5px;">Swing Score: ${stock.swing_score}/100</div>
                    </div>
                `;
            });

            html += `
                    </div>
                    <p style="text-align: center; color: rgba(255,255,255,0.8); font-size: 0.8rem; margin-top: 15px; margin-bottom: 0;">
                        💡 Click any stock to see full analysis • Scanned ${data.total_stocks} total stocks
                    </p>
                </div>
            `;

            container.innerHTML = html;
        }

        // Load institutional stocks (smart money)
        async function loadInstitutionalStocks() {
            const container = document.getElementById('institutionalStocks');

            container.innerHTML = `
                <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 12px; padding: 20px; text-align: center;">
                    <div class="spinner" style="border-color: #fff transparent transparent transparent; width: 40px; height: 40px; margin: 0 auto;"></div>
                    <h3 style="color: #fff; margin: 20px 0 10px 0;">🏦 Scanning for Institutional Stocks</h3>
                    <p style="color: rgba(255,255,255,0.9); margin: 5px 0;">Finding stocks with smart money accumulation...</p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.85rem;">Scanning top 50 big cap stocks...</p>
                </div>
            `;

            try {
                const response = await fetch('/api/scan-institutional-stocks?market=idx&min_institutional=60');
                const data = await response.json();

                if (data.success && data.institutional_stocks_found > 0) {
                    displayInstitutionalStocks(data);
                    showNotification(`✅ Found ${data.institutional_stocks_found} institutional stocks!`, 'success');
                } else {
                    container.innerHTML = `
                        <div style="text-align: center; padding: 20px; background: #1e293b; border-radius: 12px; border: 2px solid #334155;">
                            <p style="color: #94a3b8;">No strong institutional stocks found at ${data.min_institutional_threshold}% threshold</p>
                            <p style="color: #64748b; font-size: 0.85rem; margin-top: 10px;">Try again later or check individual stocks</p>
                        </div>
                    `;
                }
            } catch (error) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 20px; background: #1e293b; border-radius: 12px; border: 2px solid #ef4444;">
                        <p style="color: #ef4444;">⚠️ Error scanning institutional stocks: ${error.message}</p>
                        <button onclick="loadInstitutionalStocks()" style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #ef4444; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                            🔄 Retry
                        </button>
                    </div>
                `;
            }
        }

        // Display institutional stocks results
        function displayInstitutionalStocks(data) {
            const container = document.getElementById('institutionalStocks');
            const stocks = data.top_10 || [];

            let html = `
                <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    <div style="margin-bottom: 20px;">
                        <div class="institutional-header">
                            <div>
                                <h2 style="margin: 0; color: #fff;">🏦 Institutional Stocks (Smart Money)</h2>
                                <p style="margin: 5px 0 0 0; color: rgba(255,255,255,0.9); font-size: 0.9rem;">
                                    Found ${data.institutional_stocks_found} stocks with institutional accumulation ≥${data.min_institutional_threshold}%
                                </p>
                                <p style="margin: 5px 0 0 0; color: rgba(255,255,255,0.7); font-size: 0.75rem;">
                                    These stocks show signs of professional/institutional investor activity
                                </p>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <button onclick="loadInstitutionalStocks()" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; transition: all 0.2s;"
                                        onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                                        onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                                    🔄 Refresh
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="institutional-grid">
            `;

            stocks.forEach(stock => {
                const actionColor = stock.action.includes('BUY') ? '#10b981' : (stock.action.includes('HOLD') ? '#f59e0b' : '#ef4444');
                const institutionalColor = stock.institutional_percent >= 80 ? '#10b981' : (stock.institutional_percent >= 70 ? '#3b82f6' : '#8b5cf6');

                html += `
                    <div onclick="quickAnalyze('${stock.symbol}', 'idx')" style="background: rgba(99, 102, 241, 0.15); border: 2px solid ${institutionalColor}; border-radius: 10px; padding: 18px; cursor: pointer; transition: all 0.2s;"
                         onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.4)'"
                         onmouseout="this.style.transform=''; this.style.boxShadow=''">

                        <!-- Header -->
                        <div class="institutional-card-header" style="margin-bottom: 12px;">
                            <div>
                                <div style="font-size: 1.3rem; font-weight: bold; color: #fff; margin-bottom: 3px;">${stock.symbol}</div>
                                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.7);">${stock.name.substring(0, 30)}${stock.name.length > 30 ? '...' : ''}</div>
                            </div>
                            <div style="background: ${institutionalColor}; color: #000; padding: 6px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: bold;">
                                ${stock.institutional_percent.toFixed(0)}% Inst.
                            </div>
                        </div>

                        <!-- Price & Action -->
                        <div class="institutional-card-stats">
                            <div>
                                <div style="font-size: 0.75rem; color: rgba(255,255,255,0.6);">Price</div>
                                <div style="font-size: 1.1rem; font-weight: bold; color: #fff;">
                                    Rp ${stock.price.toLocaleString()}
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.75rem; color: rgba(255,255,255,0.6);">Action</div>
                                <div style="font-size: 0.9rem; font-weight: bold; color: ${actionColor};">
                                    ${stock.action}
                                </div>
                            </div>
                        </div>

                        <!-- Institutional Indicators -->
                        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.85); margin-bottom: 6px;">
                            <strong>Pattern:</strong> ${stock.volume_pattern}
                        </div>
                        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.85); margin-bottom: 6px;">
                            <strong>Phase:</strong> ${stock.accumulation_phase}
                        </div>
                        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.85); margin-bottom: 6px;">
                            <strong>Strength:</strong> ${stock.accumulation_strength}/100 • ${stock.accumulation_days} days
                        </div>
                        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.85);">
                            <strong>Volatility:</strong> ${stock.price_stability}
                        </div>
                    </div>
                `;
            });

            html += `
                    </div>
                    <p style="text-align: center; color: rgba(255,255,255,0.8); font-size: 0.8rem; margin-top: 15px; margin-bottom: 0;">
                        💡 Click any stock to see full analysis • Higher % = More institutional involvement
                    </p>
                </div>
            `;

            container.innerHTML = html;
        }

        function showBuyOpportunitiesPlaceholder() {
            const container = document.getElementById('buyOpportunities');
            container.innerHTML = `
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-5 shadow-lg">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="text-left">
                            <div class="flex items-center gap-2.5 mb-1">
                                <span class="text-3xl">🎯</span>
                                <h2 class="m-0 text-white text-xl font-semibold">Buy Opportunities Scanner</h2>
                            </div>
                            <p class="text-white opacity-85 text-sm m-0">
                                Select a country to view BUY opportunities and near-miss stocks
                            </p>
                        </div>

                        <!-- Country Selection Buttons -->
                        <div class="flex gap-2.5 flex-wrap">
                            <button onclick="loadBuyOpportunitiesByMarket('idx')"
                                    class="bg-gradient-to-br from-red-500 to-red-600 border-2 border-red-500 text-white px-4 py-2.5 rounded-lg cursor-pointer text-sm font-bold transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 min-w-[140px] flex items-center justify-center gap-1.5">
                                <span class="text-xl">🇮🇩</span>
                                <span>Indonesia</span>
                            </button>

                            <button onclick="loadBuyOpportunitiesByMarket('sgx')"
                                    class="bg-gradient-to-br from-purple-500 to-purple-600 border-2 border-purple-500 text-white px-4 py-2.5 rounded-lg cursor-pointer text-sm font-bold transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 min-w-[140px] flex items-center justify-center gap-1.5">
                                <span class="text-xl">🇸🇬</span>
                                <span>Singapore</span>
                            </button>

                            <button onclick="loadBuyOpportunitiesByMarket('us')"
                                    class="bg-gradient-to-br from-blue-500 to-blue-600 border-2 border-blue-500 text-white px-4 py-2.5 rounded-lg cursor-pointer text-sm font-bold transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 min-w-[140px] flex items-center justify-center gap-1.5">
                                <span class="text-xl">🇺🇸</span>
                                <span>United States</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Market Phase Scanner State
        let marketPhaseData = null;
        let marketPhaseHistory = null;
        let activePhaseFilter = 'all';
        let showingHistory = false;

        // Load Market Phase Scanner
        async function loadMarketPhaseScanner(forceRefresh = false) {
            const container = document.getElementById('marketPhaseScanner');

            container.innerHTML = `
                <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; padding: 20px; text-align: center;">
                    <div class="spinner" style="border-color: #fff transparent transparent transparent; width: 40px; height: 40px; margin: 0 auto;"></div>
                    <h3 style="color: #fff; margin: 20px 0 10px 0;">🔄 Scanning Market Phases</h3>
                    <p style="color: rgba(255,255,255,0.9); margin: 5px 0;">Analyzing Wyckoff market cycles...</p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.85rem;">Scanning top IDX stocks...</p>
                </div>
            `;

            try {
                const url = forceRefresh
                    ? '/api/scan-market-phases?market=idx&limit=5&refresh=true'
                    : '/api/scan-market-phases?market=idx&limit=5';
                const response = await fetch(url);
                const data = await response.json();

                if (data.success) {
                    marketPhaseData = data;
                    displayMarketPhaseScanner(data);
                    showNotification(`✅ Found stocks in ${Object.values(data.phase_counts).reduce((a, b) => a + b, 0)} phases!`, 'success');
                } else {
                    container.innerHTML = `
                        <div style="text-align: center; padding: 20px; background: #1e293b; border-radius: 12px; border: 2px solid #ef4444;">
                            <p style="color: #ef4444;">⚠️ No phase data found</p>
                            <button onclick="loadMarketPhaseScanner(true)" style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #ef4444; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                                🔄 Retry
                            </button>
                        </div>
                    `;
                }
            } catch (error) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 20px; background: #1e293b; border-radius: 12px; border: 2px solid #ef4444;">
                        <p style="color: #ef4444;">⚠️ Error loading phases: ${error.message}</p>
                        <button onclick="loadMarketPhaseScanner(true)" style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #ef4444; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                            🔄 Retry
                        </button>
                    </div>
                `;
            }
        }

        // Load analysis history
        async function loadAnalysisHistory() {
            try {
                const response = await fetch('/api/analysis-history?limit=15');
                const data = await response.json();
                if (data.success) {
                    marketPhaseHistory = data;
                    if (showingHistory) {
                        displayMarketPhaseScanner(marketPhaseData);
                    }
                }
            } catch (error) {
                console.error('Failed to load history:', error);
            }
        }

        // Get phase icon
        function getPhaseIcon(phase) {
            const icons = {
                'MARKUP': '📈',
                'MARKDOWN': '📉',
                'DISTRIBUTION': '🔻',
                'ACCUMULATION': '💰'
            };
            return icons[phase] || '❓';
        }

        // Get phase colors
        function getPhaseColors(phase) {
            const colors = {
                'MARKUP': { bg: 'rgba(34, 197, 94, 0.2)', border: '#22c55e', text: '#22c55e' },
                'MARKDOWN': { bg: 'rgba(239, 68, 68, 0.2)', border: '#ef4444', text: '#ef4444' },
                'DISTRIBUTION': { bg: 'rgba(245, 158, 11, 0.2)', border: '#f59e0b', text: '#f59e0b' },
                'ACCUMULATION': { bg: 'rgba(59, 130, 246, 0.2)', border: '#3b82f6', text: '#3b82f6' }
            };
            return colors[phase] || { bg: 'rgba(107, 114, 128, 0.2)', border: '#6b7280', text: '#6b7280' };
        }

        // Get phase description
        function getPhaseDescription(phase) {
            const descriptions = {
                'MARKUP': 'Price rising with volume support. Uptrend in progress.',
                'MARKDOWN': 'Price falling with volume. Downtrend in progress.',
                'DISTRIBUTION': 'Smart money distributing at higher prices.',
                'ACCUMULATION': 'Smart money accumulating at lower prices.'
            };
            return descriptions[phase] || '';
        }

        // Display Market Phase Scanner
        function displayMarketPhaseScanner(data) {
            const container = document.getElementById('marketPhaseScanner');
            const phases = data.phases || {};
            const phaseCounts = data.phase_counts || {};

            // Build phase filter tabs
            let filterTabs = `
                <button onclick="filterPhase('all')" style="padding: 8px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s; margin-right: 8px; margin-bottom: 8px; ${activePhaseFilter === 'all' ? 'background: #3b82f6; color: white; border: none;' : 'background: #334155; color: #cbd5e1; border: 1px solid #475569;'}">
                    All Phases
                </button>
            `;

            ['MARKUP', 'ACCUMULATION', 'DISTRIBUTION', 'MARKDOWN'].forEach(phase => {
                const colors = getPhaseColors(phase);
                const isActive = activePhaseFilter === phase;
                filterTabs += `
                    <button onclick="filterPhase('${phase}')" style="padding: 8px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s; margin-right: 8px; margin-bottom: 8px; display: inline-flex; align-items: center; gap: 6px; ${isActive ? `background: ${colors.bg}; color: ${colors.text}; border: 2px solid ${colors.border};` : 'background: #334155; color: #cbd5e1; border: 1px solid #475569;'}">
                        <span>${getPhaseIcon(phase)}</span>
                        <span>${phase}</span>
                        <span style="opacity: 0.7; font-size: 0.75rem;">(${phaseCounts[phase] || 0})</span>
                    </button>
                `;
            });

            // Build history panel if showing
            let historyPanel = '';
            if (showingHistory && marketPhaseHistory) {
                const history = marketPhaseHistory.history || [];
                const phaseSummary = marketPhaseHistory.phase_summary || {};

                let summaryBoxes = '';
                ['MARKUP', 'ACCUMULATION', 'DISTRIBUTION', 'MARKDOWN'].forEach(phase => {
                    const colors = getPhaseColors(phase);
                    summaryBoxes += `
                        <div style="text-align: center; padding: 10px; border-radius: 8px; background: ${colors.bg}; border: 1px solid ${colors.border};">
                            <span>${getPhaseIcon(phase)}</span>
                            <p style="font-size: 1.25rem; font-weight: bold; margin: 5px 0; color: ${colors.text};">${phaseSummary[phase] || 0}</p>
                            <p style="font-size: 0.7rem; opacity: 0.7; margin: 0;">${phase}</p>
                        </div>
                    `;
                });

                let historyItems = '';
                history.forEach(item => {
                    const colors = getPhaseColors(item.phase);
                    historyItems += `
                        <div onclick="quickAnalyze('${item.symbol}')" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #334155; border-radius: 8px; cursor: pointer; transition: all 0.2s; margin-bottom: 8px;" onmouseover="this.style.background='#3f4c63'" onmouseout="this.style.background='#334155'">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span>${getPhaseIcon(item.phase)}</span>
                                <div>
                                    <span style="font-weight: 600;">${item.symbol}</span>
                                    <span style="font-size: 0.75rem; color: #94a3b8; margin-left: 8px;">${item.stock_name ? item.stock_name.substring(0, 20) : ''}</span>
                                </div>
                            </div>
                            <div style="text-align: right; font-size: 0.75rem;">
                                <div style="color: ${colors.text};">${item.phase}</div>
                                <div style="color: #6b7280;">${new Date(item.created_at).toLocaleDateString()}</div>
                            </div>
                        </div>
                    `;
                });

                historyPanel = `
                    <div style="margin-bottom: 16px; background: #1e293b; border-radius: 12px; padding: 16px; border: 1px solid #334155;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                            <h4 style="font-weight: 600; color: #3b82f6; margin: 0;">📋 Analysis History (Last 3 months)</h4>
                            <button onclick="toggleHistory()" style="background: none; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">&times;</button>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 16px;">
                            ${summaryBoxes}
                        </div>
                        <div style="max-height: 240px; overflow-y: auto;">
                            ${history.length > 0 ? historyItems : '<p style="text-align: center; color: #6b7280; padding: 20px;">No analysis history yet. Analyze some stocks to build your history!</p>'}
                        </div>
                    </div>
                `;
            }

            // Build phase sections
            let phaseSections = '';
            ['MARKUP', 'ACCUMULATION', 'DISTRIBUTION', 'MARKDOWN'].forEach(phase => {
                const stocks = phases[phase] || [];
                if (stocks.length === 0) return;
                if (activePhaseFilter !== 'all' && activePhaseFilter !== phase) return;

                const colors = getPhaseColors(phase);

                let stockRows = '';
                stocks.forEach((stock, idx) => {
                    const changeColor = stock.change_percent >= 0 ? '#22c55e' : '#ef4444';
                    const changeSign = stock.change_percent >= 0 ? '+' : '';
                    stockRows += `
                        <div onclick="quickAnalyze('${stock.symbol}')" style="padding: 12px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: all 0.2s; border-bottom: 1px solid rgba(255,255,255,0.05);" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="font-size: 0.75rem; color: #6b7280; font-family: monospace; width: 16px;">${idx + 1}</span>
                                <div>
                                    <div style="font-weight: 600;">${stock.symbol}</div>
                                    <div style="font-size: 0.75rem; color: #94a3b8; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${stock.name}</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 16px; font-size: 0.875rem;">
                                <div style="text-align: right;">
                                    <div style="font-family: monospace;">${stock.price ? stock.price.toLocaleString() : 'N/A'}</div>
                                    <div style="font-size: 0.75rem; color: ${changeColor};">${changeSign}${stock.change_percent}%</div>
                                </div>
                                <div style="text-align: right; display: none;" class="hidden-mobile">
                                    <div style="font-size: 0.75rem; color: #94a3b8;">Score</div>
                                    <div style="font-weight: 600;">${stock.score}/100</div>
                                </div>
                                <div style="text-align: right; display: none;" class="hidden-mobile">
                                    <div style="font-size: 0.75rem; color: #94a3b8;">Inst.</div>
                                    <div style="font-weight: 600; color: #3b82f6;">${(stock.institutional_percent || 0).toFixed(0)}%</div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                phaseSections += `
                    <div style="border: 2px solid ${colors.border}; border-radius: 12px; overflow: hidden; margin-bottom: 16px; background: ${colors.bg};">
                        <div style="padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 1.25rem;">${getPhaseIcon(phase)}</span>
                                <span style="font-weight: bold; color: ${colors.text};">${phase}</span>
                                <span style="font-size: 0.75rem; color: #94a3b8;">(${stocks.length} stocks)</span>
                            </div>
                            <p style="font-size: 0.75rem; color: #94a3b8; margin: 4px 0 0 0;">${getPhaseDescription(phase)}</p>
                        </div>
                        <div>
                            ${stockRows}
                        </div>
                    </div>
                `;
            });

            // Check if no stocks found
            if (!phaseSections) {
                phaseSections = `
                    <div style="text-align: center; padding: 32px; color: #6b7280;">
                        <p style="font-size: 2rem; margin-bottom: 16px;">🔍</p>
                        <p>No stocks in defined phases. Try refreshing the scan.</p>
                    </div>
                `;
            }

            container.innerHTML = `
                <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 12px; padding: 20px; border: 1px solid #334155;">
                    <!-- Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h2 style="margin: 0; color: #fff; font-size: 1.25rem; font-weight: bold;">🔄 Market Phase Scanner (Wyckoff)</h2>
                            <p style="margin: 4px 0 0 0; color: rgba(255,255,255,0.7); font-size: 0.875rem;">
                                Top 5 stocks in each Wyckoff market cycle phase
                            </p>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button onclick="toggleHistory()" style="background: ${showingHistory ? 'rgba(59, 130, 246, 0.2)' : 'rgba(255,255,255,0.1)'}; border: 1px solid ${showingHistory ? '#3b82f6' : 'rgba(255,255,255,0.2)'}; color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; transition: all 0.2s; font-size: 1rem;">
                                📋
                            </button>
                            <button onclick="loadMarketPhaseScanner(true)" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; transition: all 0.2s; font-size: 1rem;">
                                🔄
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    <p style="font-size: 0.875rem; color: #94a3b8; margin-bottom: 16px;">
                        <span style="color: #22c55e; font-weight: 600;">MARKUP</span> = uptrend,
                        <span style="color: #ef4444; font-weight: 600;">MARKDOWN</span> = downtrend,
                        <span style="color: #f59e0b; font-weight: 600;">DISTRIBUTION</span> = topping,
                        <span style="color: #3b82f6; font-weight: 600;">ACCUMULATION</span> = bottoming.
                    </p>

                    <!-- Filter Tabs -->
                    <div style="margin-bottom: 16px;">
                        ${filterTabs}
                    </div>

                    <!-- History Panel -->
                    ${historyPanel}

                    <!-- Phase Sections -->
                    ${phaseSections}

                    <!-- Footer -->
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #6b7280; padding-top: 12px; border-top: 1px solid #334155;">
                        <span>Scanned: ${data.scanned || 0} stocks</span>
                        <span>${data.cached_at || ''}</span>
                    </div>
                </div>
            `;
        }

        // Filter phase
        function filterPhase(phase) {
            activePhaseFilter = phase;
            if (marketPhaseData) {
                displayMarketPhaseScanner(marketPhaseData);
            }
        }

        // Toggle history panel
        function toggleHistory() {
            showingHistory = !showingHistory;
            if (showingHistory && !marketPhaseHistory) {
                loadAnalysisHistory();
            }
            if (marketPhaseData) {
                displayMarketPhaseScanner(marketPhaseData);
            }
        }

        // Initialize on page load - always show history first
        window.addEventListener('DOMContentLoaded', () => {
            updateHistoryDisplay();
            // Show placeholder for Buy Opportunities (will load when country selected)
            showBuyOpportunitiesPlaceholder();
            loadInstitutionalStocks();  // Auto-load institutional stocks
            loadMarketPhaseScanner();   // Auto-load market phase scanner
        });

        function showLoading() {
            document.getElementById('dashboard').innerHTML = `
                <div class="card loading">
                    <div class="spinner"></div>
                    <p>Loading comprehensive dashboard...</p>
                </div>
            `;
        }

        function showError(message) {
            document.getElementById('dashboard').innerHTML = `
                <div class="error-box">
                    <h3>⚠️ Error</h3>
                    <p>${message}</p>
                </div>
            `;
        }

        function displayDashboard(data) {
            const {stock_info, overall_analysis, entry_exit, swing_analysis, accumulation, is_favorite} = data;
            isFavorite = is_favorite;

            const priceChangeClass = stock_info.change_percent >= 0 ? 'positive' : 'negative';
            const priceChangeSign = stock_info.change_percent >= 0 ? '+' : '';

            const favoriteBtn = isFavorite
                ? `<button class="favorite-btn btn-danger" onclick="removeFavorite()">⭐ Remove</button>`
                : `<button class="favorite-btn btn-success" onclick="addFavorite()">☆ Add</button>`;

            // Get key values for executive summary
            const bestEntry = entry_exit.entry_recommendation.conservative || entry_exit.entry_recommendation.moderate;
            const bestExit = entry_exit.exit_recommendation.target_2 || entry_exit.exit_recommendation.target_1;
            const overallRec = overall_analysis.recommendation;
            const accPhase = accumulation.phase;
            const swingRating = swing_analysis.swing_rating;
            const metrics = overall_analysis.metrics || {};

            // Determine executive box classes
            const getExecutiveBoxClass = (action) => {
                if (action.includes('STRONG BUY') || action.includes('BUY')) return 'highlight';
                if (action.includes('SELL')) return 'danger';
                return 'warning';
            };

            // Calculate stop loss and risk-reward
            const stopLoss = (bestEntry.price * 0.95).toFixed(0);
            const riskAmount = bestEntry.price - stopLoss;
            const rewardAmount = bestExit.price - bestEntry.price;
            const riskRewardRatio = riskAmount > 0 ? (rewardAmount / riskAmount).toFixed(2) : 0;

            // Generate AI Analysis Summary
            const generateAnalysisSummary = () => {
                const macd = metrics.technical?.macd;
                const divergence = metrics.technical?.divergence;
                const week52 = metrics.technical?.['52_week'];
                const mfi = metrics.technical?.mfi;
                const stochastic = metrics.technical?.stochastic;

                let summary = `${stock_info.symbol} `;

                // Overall sentiment
                if (overallRec.action.includes('STRONG BUY')) {
                    summary += `shows <strong style="color: #10b981;">STRONG BUY signals</strong>. `;
                } else if (overallRec.action.includes('BUY')) {
                    summary += `shows <strong style="color: #10b981;">BUY signals</strong>. `;
                } else if (overallRec.action.includes('HOLD')) {
                    summary += `presents <strong style="color: #f59e0b;">MIXED signals</strong>. `;
                } else {
                    summary += `shows <strong style="color: #ef4444;">SELL signals</strong>. `;
                }

                // MACD + Divergence (most important combo)
                if (macd && divergence) {
                    if (macd.signal === 'BULLISH' && divergence.divergence === 'BEARISH') {
                        summary += `⚠️ <strong style="color: #f97316;">Critical conflict:</strong> While momentum is currently positive (MACD bullish), a <strong style="color: #ef4444;">bearish divergence warns momentum is weakening</strong> - the rally may be running out of steam. `;
                    } else if (macd.signal === 'BEARISH' && divergence.divergence === 'BULLISH') {
                        summary += `💎 <strong style="color: #10b981;">Hidden opportunity:</strong> Despite downward pressure (MACD bearish), a <strong style="color: #10b981;">bullish divergence suggests selling is exhausting</strong> - reversal up may be near. `;
                    } else if (macd.signal === 'BULLISH' && divergence.divergence === 'BULLISH') {
                        summary += `🚀 <strong style="color: #10b981;">Strong confirmation:</strong> Both MACD and divergence align bullish - momentum is strong and healthy. `;
                    } else if (macd.signal === 'BEARISH' && divergence.divergence === 'BEARISH') {
                        summary += `📉 <strong style="color: #ef4444;">Double bearish:</strong> Both MACD and divergence signal weakness - downtrend is confirmed. `;
                    } else if (macd.signal === 'TURNING_UP') {
                        summary += `🔄 Momentum is <strong style="color: #22c55e;">turning positive</strong> (MACD). `;
                    } else if (macd.signal === 'TURNING_DOWN') {
                        summary += `🔄 Momentum is <strong style="color: #f97316;">turning negative</strong> (MACD). `;
                    } else if (macd.signal === 'BULLISH') {
                        summary += `Momentum is <strong style="color: #10b981;">positive</strong> (MACD bullish). `;
                    } else if (macd.signal === 'BEARISH') {
                        summary += `Momentum is <strong style="color: #ef4444;">negative</strong> (MACD bearish). `;
                    }
                }

                // 52-week position context
                if (week52) {
                    if (week52.position === 'NEAR_HIGH') {
                        summary += `Stock is near its <strong style="color: #f59e0b;">52-week high</strong> (${week52.percent_in_range.toFixed(0)}%) - limited upside or breakout potential? `;
                    } else if (week52.position === 'NEAR_LOW') {
                        summary += `Stock is near its <strong style="color: #10b981;">52-week low</strong> (${week52.percent_in_range.toFixed(0)}%) - potential value opportunity or falling knife? `;
                    } else if (week52.position === 'UPPER_RANGE') {
                        summary += `Trading in <strong>upper range</strong> (${week52.percent_in_range.toFixed(0)}% of 52-week range). `;
                    } else if (week52.position === 'LOWER_RANGE') {
                        summary += `Trading in <strong>lower range</strong> (${week52.percent_in_range.toFixed(0)}% of 52-week range). `;
                    }
                }

                // Institutional activity
                const instPercent = accumulation.participants.institutional_percent;
                if (instPercent > 60) {
                    summary += `<strong style="color: #10b981;">Institutions heavily involved</strong> (${instPercent.toFixed(0)}% institutional). `;
                } else if (instPercent < 40) {
                    summary += `Retail-dominated (${instPercent.toFixed(0)}% institutional). `;
                }

                // Money flow
                if (mfi) {
                    if (mfi.signal === 'BULLISH' || mfi.signal === 'OVERSOLD') {
                        summary += `Money is <strong style="color: #10b981;">flowing IN</strong> (MFI ${mfi.mfi.toFixed(0)}). `;
                    } else if (mfi.signal === 'BEARISH' || mfi.signal === 'OVERBOUGHT') {
                        summary += `Money is <strong style="color: #ef4444;">flowing OUT</strong> (MFI ${mfi.mfi.toFixed(0)}). `;
                    }
                }

                // Stochastic timing
                if (stochastic) {
                    if (stochastic.signal === 'OVERSOLD') {
                        summary += `📍 <strong style="color: #10b981;">Oversold zone</strong> - potential bounce timing. `;
                    } else if (stochastic.signal === 'OVERBOUGHT') {
                        summary += `📍 <strong style="color: #ef4444;">Overbought zone</strong> - potential pullback. `;
                    } else if (stochastic.signal === 'BULLISH_CROSS') {
                        summary += `📍 <strong style="color: #10b981;">Bullish crossover</strong> detected - entry signal. `;
                    } else if (stochastic.signal === 'BEARISH_CROSS') {
                        summary += `📍 <strong style="color: #ef4444;">Bearish crossover</strong> detected - exit signal. `;
                    }
                }

                // Risk-reward assessment
                if (riskRewardRatio >= 2) {
                    summary += `<strong style="color: #10b981;">Excellent</strong> risk:reward (1:${riskRewardRatio}). `;
                } else if (riskRewardRatio >= 1.5) {
                    summary += `Good risk:reward (1:${riskRewardRatio}). `;
                } else if (riskRewardRatio < 1) {
                    summary += `<strong style="color: #ef4444;">Poor</strong> risk:reward (1:${riskRewardRatio}). `;
                }

                // Final recommendation
                summary += `<br><br><strong>💡 Action:</strong> `;
                if (overallRec.action.includes('STRONG BUY')) {
                    summary += `<strong style="color: #10b981; font-size: 1.1rem;">STRONG BUY</strong> - Multiple positive signals align.`;
                } else if (overallRec.action.includes('BUY')) {
                    summary += `<strong style="color: #10b981; font-size: 1.1rem;">BUY</strong> - Positive factors outweigh risks.`;
                } else if (overallRec.action.includes('HOLD')) {
                    if (divergence?.divergence === 'BEARISH' || macd?.signal === 'TURNING_DOWN') {
                        summary += `<strong style="color: #f59e0b; font-size: 1.1rem;">HOLD/CAUTION</strong> - Wait for conflicting signals to clear.`;
                    } else if (divergence?.divergence === 'BULLISH' || macd?.signal === 'TURNING_UP') {
                        summary += `<strong style="color: #f59e0b; font-size: 1.1rem;">HOLD/WATCH</strong> - Potential opportunity developing, wait for confirmation.`;
                    } else {
                        summary += `<strong style="color: #f59e0b; font-size: 1.1rem;">HOLD</strong> - Mixed signals, neutral stance advised.`;
                    }
                } else {
                    summary += `<strong style="color: #ef4444; font-size: 1.1rem;">SELL</strong> - Negative factors dominate.`;
                }

                return summary;
            };

            const analysisSummary = generateAnalysisSummary();

            // Generate Stock Snapshot Bullet Points (Newbie-Friendly)
            const generateStockSnapshot = () => {
                const bullets = [];
                const marketContext = metrics.market_context || {};
                const technical = metrics.technical || {};
                const valuation = metrics.valuation || {};
                const financial = metrics.financial || {};
                const dividend = metrics.dividend || {};

                // 1. Price Direction - Is it going UP or DOWN?
                if (technical.adx) {
                    const adx = technical.adx;
                    let explanation = '';
                    if (adx.signal.includes('UPTREND')) {
                        explanation = adx.trend_strength === 'Strong' || adx.trend_strength === 'Very Strong'
                            ? 'Price is strongly going UP 📈 Good time to consider buying!'
                            : 'Price is slowly going UP 📈 Trend is weak';
                    } else if (adx.signal.includes('DOWNTREND')) {
                        explanation = adx.trend_strength === 'Strong' || adx.trend_strength === 'Very Strong'
                            ? 'Price is strongly going DOWN 📉 Be careful!'
                            : 'Price is slowly going DOWN 📉 Weak downtrend';
                    } else {
                        explanation = 'Price is moving sideways ↔️ No clear direction yet';
                    }
                    const trendColor = adx.signal.includes('UPTREND') ? '#10b981' : adx.signal.includes('DOWNTREND') ? '#ef4444' : '#94a3b8';
                    bullets.push(`<strong style="color: ${trendColor};">📊 Price Direction:</strong> ${explanation}`);
                }

                // 2. Price Stability - Does price jump around a lot?
                if (technical.atr) {
                    const atr = technical.atr;
                    let explanation = '';
                    if (atr.volatility_category === 'Extreme') {
                        explanation = '⚡ <span style="color: #ef4444;">Very jumpy!</span> Price can move ±' + atr.atr_percent.toFixed(0) + '% daily. High risk - only for experienced traders';
                    } else if (atr.volatility_category === 'High') {
                        explanation = '⚡ <span style="color: #f59e0b;">Quite jumpy.</span> Price moves ±' + atr.atr_percent.toFixed(0) + '% daily. Moderate risk - be cautious';
                    } else if (atr.volatility_category === 'Low') {
                        explanation = '😌 <span style="color: #10b981;">Stable & calm.</span> Price moves ±' + atr.atr_percent.toFixed(0) + '% daily. Good for beginners';
                    } else {
                        explanation = '📊 Normal movement. Price moves ±' + atr.atr_percent.toFixed(0) + '% daily';
                    }
                    bullets.push(`<strong>🎢 Price Stability:</strong> ${explanation}`);
                }

                // 3. Easy to Buy/Sell? - Can you trade it easily?
                if (marketContext.liquidity) {
                    const liq = marketContext.liquidity;
                    let explanation = '';
                    if (liq.category.includes('Very Liquid')) {
                        explanation = '💧 <span style="color: #10b981;">Super easy!</span> Lots of buyers & sellers. You can buy/sell anytime without issues';
                    } else if (liq.category.includes('Liquid')) {
                        explanation = '💦 <span style="color: #22c55e;">Easy enough.</span> Good trading volume. Usually no problem buying or selling';
                    } else if (liq.category.includes('Illiquid')) {
                        explanation = '🏜️ <span style="color: #ef4444;">Hard to trade!</span> Not many buyers/sellers. May be difficult to exit when you want';
                    } else {
                        explanation = '📊 <span style="color: #f59e0b;">Moderate.</span> Average trading volume. Sometimes need to wait for buyers/sellers';
                    }
                    bullets.push(`<strong>💱 Easy to Trade?</strong> ${explanation}`);
                }

                // 4. Halal Status - For Muslim investors
                if (marketContext.sharia_compliance) {
                    const sharia = marketContext.sharia_compliance;
                    if (sharia.is_compliant) {
                        bullets.push(`<strong style="color: #10b981;">🕌 Halal Investment:</strong> Yes! This stock is approved by OJK (safe for Muslim investors)`);
                    } else {
                        bullets.push(`<strong style="color: #94a3b8;">🏦 Halal Status:</strong> Not in halal list (not approved for Islamic investment)`);
                    }
                }

                // 5. Government-Owned? - Does the government own this company?
                if (marketContext.bumn_status && marketContext.bumn_status.is_bumn) {
                    const bumn = marketContext.bumn_status;
                    const ownership = bumn.bumn_info?.ownership || 0;
                    const tier = bumn.bumn_info?.tier || 'unknown';
                    let explanation = '';
                    if (tier === 'strategic') {
                        explanation = '🏛️ <span style="color: #3b82f6;">Government owns ' + ownership.toFixed(0) + '%</span> - Very important company! Lower risk of bankruptcy';
                    } else {
                        explanation = '🏛️ <span style="color: #3b82f6;">Government owns ' + ownership.toFixed(0) + '%</span> - State-owned company with government support';
                    }
                    bullets.push(`<strong>🏢 Ownership:</strong> ${explanation}`);
                }

                // 6. Sector Timing - Is this industry popular right now?
                if (marketContext.sector_rotation) {
                    const sector = marketContext.sector_rotation;
                    let explanation = '';
                    if (sector.sector_status === 'HOT') {
                        explanation = '🔥 <span style="color: #ef4444;">' + sector.sector + ' sector is HOT!</span> Everyone wants to buy stocks in this industry right now (+' + sector.momentum_1month_percent.toFixed(0) + '%)';
                    } else if (sector.sector_status === 'WARMING') {
                        explanation = '🌡️ <span style="color: #f59e0b;">' + sector.sector + ' sector is warming up.</span> Industry is getting popular (+' + sector.momentum_1month_percent.toFixed(0) + '%)';
                    } else if (sector.sector_status === 'COOLING') {
                        explanation = '❄️ <span style="color: #3b82f6;">' + sector.sector + ' sector is cooling down.</span> Industry interest is fading (' + sector.momentum_1month_percent.toFixed(0) + '%)';
                    } else if (sector.sector_status === 'COLD') {
                        explanation = '🧊 <span style="color: #6366f1;">' + sector.sector + ' sector is cold.</span> People are avoiding this industry (' + sector.momentum_1month_percent.toFixed(0) + '%)';
                    } else {
                        explanation = '📊 ' + sector.sector + ' sector is stable. No major interest or decline';
                    }
                    bullets.push(`<strong>🏭 Industry Trend:</strong> ${explanation}`);
                }

                // 7. Stock Price - Is it cheap or expensive?
                if (valuation.pe_ratio !== undefined && valuation.pe_ratio !== null) {
                    const pe = valuation.pe_ratio;
                    let explanation = '';
                    if (pe < 15) {
                        explanation = '💎 <span style="color: #10b981;">CHEAP!</span> Stock price is low compared to company profits. Could be a bargain';
                    } else if (pe < 25) {
                        explanation = '💰 <span style="color: #f59e0b;">Fair price.</span> Stock is reasonably priced - not too cheap, not too expensive';
                    } else {
                        explanation = '💸 <span style="color: #ef4444;">EXPENSIVE!</span> Stock price is high compared to profits. You\'re paying a premium';
                    }
                    bullets.push(`<strong>🏷️ Price Tag:</strong> ${explanation}`);
                }

                // 8. Company Debt - Does the company owe a lot of money?
                if (financial.debt_to_equity !== undefined && financial.debt_to_equity !== null) {
                    const debt = financial.debt_to_equity;
                    let explanation = '';
                    if (debt < 0.5) {
                        explanation = '💪 <span style="color: #10b981;">Very healthy!</span> Company has low debt. Less risk of financial trouble';
                    } else if (debt < 1.0) {
                        explanation = '⚖️ <span style="color: #f59e0b;">Moderate debt.</span> Company has some loans but manageable. Normal for most businesses';
                    } else {
                        explanation = '⚠️ <span style="color: #ef4444;">High debt!</span> Company owes a lot of money. Higher risk - watch carefully';
                    }
                    bullets.push(`<strong>💳 Company Debt:</strong> ${explanation}`);
                }

                // 9. Passive Income - Does it pay you dividends?
                if (dividend.yield !== undefined && dividend.yield > 0) {
                    const divYield = dividend.yield;
                    let explanation = '';
                    if (divYield > 5) {
                        explanation = '💰 <span style="color: #10b981;">Excellent!</span> Pays ' + divYield.toFixed(1) + '% per year. Great for passive income!';
                    } else if (divYield > 3) {
                        explanation = '💵 <span style="color: #22c55e;">Good.</span> Pays ' + divYield.toFixed(1) + '% per year. Nice extra income while holding';
                    } else {
                        explanation = '💸 Pays ' + divYield.toFixed(1) + '% per year. Small dividend but better than nothing';
                    }
                    bullets.push(`<strong>💵 Dividend (Free Money?):</strong> ${explanation}`);
                }

                // 10. Big Player Interest - Do professionals invest in this?
                if (metrics.ownership) {
                    const instPercent = (metrics.ownership.institutional_percent * 100);
                    if (instPercent > 0) {
                        let explanation = '';
                        if (instPercent > 50) {
                            explanation = '🏦 <span style="color: #10b981;">YES!</span> ' + instPercent.toFixed(0) + '% owned by big institutions (banks, funds). Smart money trusts this stock';
                        } else if (instPercent > 30) {
                            explanation = '📊 <span style="color: #22c55e;">Good support.</span> ' + instPercent.toFixed(0) + '% owned by institutions. Professional investors are interested';
                        } else if (instPercent > 10) {
                            explanation = '👥 Mostly retail investors (' + instPercent.toFixed(0) + '% institutions). Regular people like you own this';
                        } else {
                            explanation = '👥 Very few institutions (' + instPercent.toFixed(0) + '%). Mostly owned by small retail traders';
                        }
                        bullets.push(`<strong>🎯 Professional Interest:</strong> ${explanation}`);
                    }
                }

                return bullets;
            };

            const stockSnapshot = generateStockSnapshot();

            let html = `
                <!-- EXECUTIVE SUMMARY -->
                <div class="executive-summary">
                    <div class="executive-title">⚡ Decision Dashboard</div>
                    <p style="font-size: 0.85rem; color: #94a3b8; text-align: center; margin: -10px 0 15px 0;">🎯 THE BIG PICTURE! Everything you need to know at a glance - like a report card for the stock!</p>

                    <!-- AI Analysis Summary -->
                    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 2px solid #334155; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                        <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">
                            📊 AI Analysis Summary
                        </div>
                        <div style="font-size: 0.95rem; line-height: 1.6; color: #e2e8f0;">
                            ${analysisSummary}
                        </div>
                    </div>

                    <!-- Stock Snapshot Bullets -->
                    ${stockSnapshot.length > 0 ? `
                    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: 2px solid #334155; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                        <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">
                            📋 Stock Snapshot
                        </div>
                        <div style="font-size: 0.9rem; line-height: 1.8; color: #e2e8f0;">
                            ${stockSnapshot.map(bullet => `<div style="margin-bottom: 8px;">• ${bullet}</div>`).join('')}
                        </div>
                    </div>
                    ` : ''}

                    <div class="executive-grid">
                        <!-- Overall Recommendation -->
                        <div class="executive-box ${getExecutiveBoxClass(overallRec.action)}">
                            <div class="executive-label">Action</div>
                            <div class="executive-value" style="color: ${overallRec.color === 'success' ? '#10b981' : overallRec.color === 'danger' ? '#ef4444' : '#f59e0b'}; font-size: 1.5rem;">
                                ${overallRec.action}
                            </div>
                            <div class="executive-desc">Score: ${overall_analysis.score.toFixed(0)}/100 • ${overallRec.confidence}</div>
                        </div>

                        <!-- Entry Price -->
                        <div class="executive-box">
                            <div class="executive-label">💰 Entry Price</div>
                            <div class="executive-value" style="color: #10b981">
                                Rp ${bestEntry.price.toLocaleString()}
                            </div>
                            <div class="executive-desc">${bestEntry.distance_percent > 0 ? '↑' : '↓'} ${Math.abs(bestEntry.distance_percent).toFixed(1)}% from current</div>
                        </div>

                        <!-- Exit Target -->
                        <div class="executive-box">
                            <div class="executive-label">🎯 Exit Target</div>
                            <div class="executive-value" style="color: #3b82f6">
                                Rp ${bestExit.price.toLocaleString()}
                            </div>
                            <div class="executive-desc" style="color: #10b981;">+${bestExit.potential_gain_percent.toFixed(1)}% gain</div>
                        </div>

                        <!-- Stop Loss -->
                        <div class="executive-box">
                            <div class="executive-label">🛑 Stop Loss</div>
                            <div class="executive-value" style="color: #ef4444">
                                Rp ${stopLoss}
                            </div>
                            <div class="executive-desc">5% below entry (ATR-based)</div>
                        </div>

                        <!-- Risk-Reward -->
                        <div class="executive-box ${riskRewardRatio >= 2 ? 'highlight' : ''}">
                            <div class="executive-label">⚖️ Risk:Reward</div>
                            <div class="executive-value" style="color: ${riskRewardRatio >= 2 ? '#10b981' : riskRewardRatio >= 1.5 ? '#f59e0b' : '#ef4444'}">
                                1:${riskRewardRatio}
                            </div>
                            <div class="executive-desc">${riskRewardRatio >= 2 ? 'Excellent' : riskRewardRatio >= 1.5 ? 'Good' : 'Fair'}</div>
                        </div>

                        <!-- Phase -->
                        <div class="executive-box ${accPhase.current_phase === 'ACCUMULATION' ? 'highlight' : accPhase.current_phase === 'DISTRIBUTION' ? 'danger' : ''}">
                            <div class="executive-label">📊 Phase</div>
                            <div class="executive-value" style="color: ${accPhase.current_phase === 'ACCUMULATION' ? '#10b981' : accPhase.current_phase === 'DISTRIBUTION' ? '#ef4444' : '#f59e0b'}; font-size: 1rem;">
                                ${accPhase.current_phase}
                            </div>
                            <div class="executive-desc">Strength: ${accumulation.strength.score}/100</div>
                        </div>

                        <!-- Swing Rating -->
                        <div class="executive-box">
                            <div class="executive-label">📈 Swing</div>
                            <div class="executive-value" style="color: #f59e0b">
                                ${swingRating.score}/100
                            </div>
                            <div class="executive-desc">±${swing_analysis.swing_size.average_swing_percent.toFixed(1)}% avg</div>
                        </div>

                        <!-- Participant Type -->
                        <div class="executive-box ${accumulation.participants.primary_type.includes('Institutional') ? 'highlight' : ''}">
                            <div class="executive-label">👥 Money Type</div>
                            <div class="executive-value" style="color: ${accumulation.participants.primary_type.includes('Institutional') ? '#10b981' : '#f59e0b'}; font-size: 0.9rem;">
                                ${accumulation.participants.primary_type.replace('Dominant', '').replace('Leaning', '')}
                            </div>
                            <div class="executive-desc">${accumulation.participants.institutional_percent.toFixed(0)}% inst</div>
                        </div>

                        <!-- MACD Momentum (PRO) -->
                        <div class="executive-box ${
                            overall_analysis.metrics?.technical?.macd?.signal === 'BULLISH' || overall_analysis.metrics?.technical?.macd?.signal === 'TURNING_UP' ? 'highlight' :
                            overall_analysis.metrics?.technical?.macd?.signal === 'BEARISH' || overall_analysis.metrics?.technical?.macd?.signal === 'TURNING_DOWN' ? 'danger' : ''
                        }">
                            <div class="executive-label">🚀 MACD</div>
                            <div class="executive-value" style="color: ${
                                overall_analysis.metrics?.technical?.macd?.signal === 'BULLISH' ? '#10b981' :
                                overall_analysis.metrics?.technical?.macd?.signal === 'TURNING_UP' ? '#22c55e' :
                                overall_analysis.metrics?.technical?.macd?.signal === 'BEARISH' ? '#ef4444' :
                                overall_analysis.metrics?.technical?.macd?.signal === 'TURNING_DOWN' ? '#f97316' : '#94a3b8'
                            }; font-size: 0.85rem;">
                                ${overall_analysis.metrics?.technical?.macd ? overall_analysis.metrics.technical.macd.signal.replace(/_/g, ' ') : 'N/A'}
                            </div>
                            <div class="executive-desc">Momentum trend</div>
                        </div>

                        <!-- Divergence Alert (PRO) -->
                        <div class="executive-box ${
                            overall_analysis.metrics?.technical?.divergence?.divergence === 'BULLISH' ? 'highlight' :
                            overall_analysis.metrics?.technical?.divergence?.divergence === 'BEARISH' ? 'danger' : ''
                        }">
                            <div class="executive-label">⚠️ Divergence</div>
                            <div class="executive-value" style="color: ${
                                overall_analysis.metrics?.technical?.divergence?.divergence === 'BULLISH' ? '#10b981' :
                                overall_analysis.metrics?.technical?.divergence?.divergence === 'BEARISH' ? '#ef4444' : '#94a3b8'
                            }; font-size: 0.85rem;">
                                ${overall_analysis.metrics?.technical?.divergence?.divergence || 'NONE'}
                            </div>
                            <div class="executive-desc">${overall_analysis.metrics?.technical?.divergence?.divergence === 'BULLISH' ? '🟢 Buy signal' : overall_analysis.metrics?.technical?.divergence?.divergence === 'BEARISH' ? '🔴 Sell signal' : 'No warning'}</div>
                        </div>

                        <!-- 52-Week Position (PRO) -->
                        <div class="executive-box ${
                            overall_analysis.metrics?.technical?.['52_week']?.position === 'NEAR_LOW' ? 'highlight' :
                            overall_analysis.metrics?.technical?.['52_week']?.position === 'NEAR_HIGH' ? 'danger' : ''
                        }">
                            <div class="executive-label">📍 52-Week</div>
                            <div class="executive-value" style="color: ${
                                overall_analysis.metrics?.technical?.['52_week']?.percent_in_range < 25 ? '#10b981' :
                                overall_analysis.metrics?.technical?.['52_week']?.percent_in_range > 75 ? '#f59e0b' : '#94a3b8'
                            }; font-size: 1.2rem;">
                                ${overall_analysis.metrics?.technical?.['52_week']?.percent_in_range?.toFixed(0) || 'N/A'}%
                            </div>
                            <div class="executive-desc">${overall_analysis.metrics?.technical?.['52_week']?.position?.replace(/_/g, ' ') || 'In range'}</div>
                        </div>
                    </div>

                </div>

                <!-- Stock Header (Compact) -->
                <div class="card" style="padding: 15px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                        <div>
                            <h2 style="font-size: 1.5rem; margin: 0;">${stock_info.name}</h2>
                            <p class="stock-symbol" style="margin: 5px 0;">${stock_info.symbol}</p>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 2rem; font-weight: bold;">Rp ${stock_info.current_price.toLocaleString()}</div>
                            <div class="price-change ${priceChangeClass}" style="font-size: 1rem;">
                                ${priceChangeSign}${stock_info.change_percent.toFixed(2)}%
                            </div>
                        </div>
                        <div>
                            ${favoriteBtn}
                        </div>
                    </div>
                </div>

                <!-- KEY DECISION METRICS (COMPACT) -->
                <div class="compact-grid">
                    <!-- Technical Indicators -->
                    <div class="card">
                        <h3>📈 Technical Indicators</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">📊 Like a thermometer for stocks! Shows if the stock is "hot" (overbought), "cold" (oversold), or just right!</p>
                        <div class="metric-row" style="grid-template-columns: 1fr 1fr;">
                            <div class="metric">
                                <div class="metric-label">RSI (14)</div>
                                <div class="metric-value" style="color: ${overall_analysis.metrics?.technical?.rsi < 30 ? '#10b981' : overall_analysis.metrics?.technical?.rsi > 70 ? '#ef4444' : '#f59e0b'}">
                                    ${overall_analysis.metrics?.technical?.rsi?.toFixed(0) || 'N/A'}
                                </div>
                                <div class="metric-small">${overall_analysis.metrics?.technical?.rsi < 30 ? 'Oversold' : overall_analysis.metrics?.technical?.rsi > 70 ? 'Overbought' : 'Neutral'}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">MA Status</div>
                                <div class="metric-value" style="font-size: 0.85rem; color: ${overall_analysis.metrics?.technical?.above_sma ? '#10b981' : '#ef4444'}">
                                    ${overall_analysis.metrics?.technical?.above_sma ? '↑ Above' : '↓ Below'}
                                </div>
                                <div class="metric-small">20-day SMA</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Volatility</div>
                                <div class="metric-value" style="font-size: 0.9rem;">
                                    ${swing_analysis.volatility.volatility_rating}
                                </div>
                                <div class="metric-small">${swing_analysis.volatility.daily_volatility_percent.toFixed(1)}% daily</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Trend</div>
                                <div class="metric-value" style="font-size: 0.85rem; color: ${swing_analysis.swing_pattern.pattern.includes('Higher') ? '#10b981' : '#ef4444'}">
                                    ${swing_analysis.swing_pattern.pattern.includes('Higher') ? '📈 Up' : swing_analysis.swing_pattern.pattern.includes('Lower') ? '📉 Down' : '↔️ Side'}
                                </div>
                                <div class="metric-small">${swing_analysis.swing_pattern.pattern.substring(0, 15)}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Fundamental Ratios -->
                    <div class="card">
                        <h3>💰 Fundamentals</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">💎 Is this stock a good VALUE? Like comparing toy prices - are we getting a good deal or paying too much?</p>
                        <div class="metric-row" style="grid-template-columns: 1fr 1fr;">
                            <div class="metric">
                                <div class="metric-label">P/E Ratio</div>
                                <div class="metric-value" style="color: ${metrics.valuation?.pe_ratio < 15 ? '#10b981' : metrics.valuation?.pe_ratio < 25 ? '#f59e0b' : '#ef4444'}">
                                    ${metrics.valuation?.pe_ratio?.toFixed(1) || 'N/A'}
                                </div>
                                <div class="metric-small">${metrics.valuation?.pe_ratio < 15 ? 'Cheap' : metrics.valuation?.pe_ratio < 25 ? 'Fair' : 'Expensive'}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">P/B Ratio</div>
                                <div class="metric-value" style="color: ${metrics.valuation?.pb_ratio < 1.5 ? '#10b981' : metrics.valuation?.pb_ratio < 3 ? '#f59e0b' : '#ef4444'}">
                                    ${metrics.valuation?.pb_ratio?.toFixed(1) || 'N/A'}
                                </div>
                                <div class="metric-small">${metrics.valuation?.pb_ratio < 1.5 ? 'Underval' : metrics.valuation?.pb_ratio < 3 ? 'Fair' : 'Overval'}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">ROE</div>
                                <div class="metric-value" style="color: ${metrics.profitability?.roe > 15 ? '#10b981' : metrics.profitability?.roe > 10 ? '#f59e0b' : '#ef4444'}">
                                    ${metrics.profitability?.roe?.toFixed(1) || 'N/A'}%
                                </div>
                                <div class="metric-small">${metrics.profitability?.roe > 15 ? 'Strong' : metrics.profitability?.roe > 10 ? 'Good' : 'Weak'}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">EPS</div>
                                <div class="metric-value" style="font-size: 0.9rem; color: ${metrics.profitability?.eps > 0 ? '#10b981' : '#ef4444'}">
                                    ${metrics.profitability?.eps?.toFixed(0) || 'N/A'}
                                </div>
                                <div class="metric-small">${metrics.profitability?.eps > 0 ? 'Profit' : 'Loss'}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Volume & Liquidity -->
                    <div class="card">
                        <h3>📊 Volume & Liquidity</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">🏪 How BUSY is the stock store? Lots of buyers = popular! Shows how many people are trading this stock.</p>
                        <div class="metric-row" style="grid-template-columns: 1fr 1fr;">
                            <div class="metric">
                                <div class="metric-label">Volume Ratio</div>
                                <div class="metric-value" style="color: ${accumulation.current_volume_vs_average.ratio > 1.2 ? '#10b981' : accumulation.current_volume_vs_average.ratio > 0.8 ? '#f59e0b' : '#ef4444'}">
                                    ${accumulation.current_volume_vs_average.ratio}x
                                </div>
                                <div class="metric-small">${accumulation.current_volume_vs_average.status}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Market Cap</div>
                                <div class="metric-value" style="font-size: 0.85rem;">
                                    ${metrics.valuation?.market_cap ? (metrics.valuation.market_cap / 1000000000000).toFixed(1) + 'T' : 'N/A'}
                                </div>
                                <div class="metric-small">${metrics.valuation?.market_cap > 100000000000000 ? 'Large' : metrics.valuation?.market_cap > 10000000000000 ? 'Mid' : 'Small'}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Accum Duration</div>
                                <div class="metric-value" style="color: ${accumulation.duration.days > 15 ? '#10b981' : '#f59e0b'}">
                                    ${accumulation.duration.days}d
                                </div>
                                <div class="metric-small">${accumulation.duration.status}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Magnitude</div>
                                <div class="metric-value" style="font-size: 0.9rem; color: ${accumulation.magnitude.vs_average_percent > 25 ? '#10b981' : '#f59e0b'}">
                                    ${accumulation.magnitude.size}
                                </div>
                                <div class="metric-small">${accumulation.magnitude.vs_average_percent > 0 ? '+' : ''}${accumulation.magnitude.vs_average_percent}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Indicators -->
                    <div class="card" style="grid-column: span 3;">
                        <h3>🎯 Professional Indicators</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">⭐ What the PROS use! Advanced signals that professional traders watch every day!</p>
                        <div class="metric-row" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                            <!-- MACD -->
                            ${metrics.technical?.macd ? `
                                <div class="metric" style="border-left: 3px solid ${
                                    metrics.technical.macd.signal === 'BULLISH' ? '#10b981' :
                                    metrics.technical.macd.signal === 'BEARISH' ? '#ef4444' :
                                    metrics.technical.macd.signal === 'TURNING_UP' ? '#22c55e' :
                                    metrics.technical.macd.signal === 'TURNING_DOWN' ? '#f97316' : '#94a3b8'
                                }; padding-left: 10px;">
                                    <div class="metric-label">MACD (Momentum)</div>
                                    <div class="metric-value" style="font-size: 0.85rem; color: ${
                                        metrics.technical.macd.signal === 'BULLISH' || metrics.technical.macd.signal === 'TURNING_UP' ? '#10b981' :
                                        metrics.technical.macd.signal === 'BEARISH' || metrics.technical.macd.signal === 'TURNING_DOWN' ? '#ef4444' : '#f59e0b'
                                    }">
                                        ${metrics.technical.macd.signal.replace(/_/g, ' ')}
                                    </div>
                                    <div class="metric-small">${metrics.technical.macd.interpretation}</div>
                                </div>
                            ` : '<div class="metric"><div class="metric-label">MACD</div><div class="metric-value">N/A</div></div>'}

                            <!-- Stochastic Oscillator -->
                            ${metrics.technical?.stochastic ? `
                                <div class="metric" style="border-left: 3px solid ${
                                    metrics.technical.stochastic.signal === 'OVERSOLD' || metrics.technical.stochastic.signal === 'BULLISH_CROSS' ? '#10b981' :
                                    metrics.technical.stochastic.signal === 'OVERBOUGHT' || metrics.technical.stochastic.signal === 'BEARISH_CROSS' ? '#ef4444' : '#94a3b8'
                                }; padding-left: 10px;">
                                    <div class="metric-label">Stochastic %K</div>
                                    <div class="metric-value" style="color: ${
                                        metrics.technical.stochastic.k < 20 ? '#10b981' :
                                        metrics.technical.stochastic.k > 80 ? '#ef4444' : '#f59e0b'
                                    }">
                                        ${metrics.technical.stochastic.k.toFixed(0)}
                                    </div>
                                    <div class="metric-small">${metrics.technical.stochastic.signal.replace(/_/g, ' ')}</div>
                                </div>
                            ` : '<div class="metric"><div class="metric-label">Stochastic</div><div class="metric-value">N/A</div></div>'}

                            <!-- Money Flow Index -->
                            ${metrics.technical?.mfi ? `
                                <div class="metric" style="border-left: 3px solid ${
                                    metrics.technical.mfi.signal === 'OVERSOLD' || metrics.technical.mfi.signal === 'BULLISH' ? '#10b981' :
                                    metrics.technical.mfi.signal === 'OVERBOUGHT' || metrics.technical.mfi.signal === 'BEARISH' ? '#ef4444' : '#94a3b8'
                                }; padding-left: 10px;">
                                    <div class="metric-label">MFI (Money Flow)</div>
                                    <div class="metric-value" style="color: ${
                                        metrics.technical.mfi.mfi < 20 ? '#10b981' :
                                        metrics.technical.mfi.mfi > 80 ? '#ef4444' : '#f59e0b'
                                    }">
                                        ${metrics.technical.mfi.mfi.toFixed(0)}
                                    </div>
                                    <div class="metric-small">${metrics.technical.mfi.interpretation.substring(0, 40)}</div>
                                </div>
                            ` : '<div class="metric"><div class="metric-label">MFI</div><div class="metric-value">N/A</div></div>'}

                            <!-- 52-Week Position -->
                            ${metrics.technical?.['52_week'] ? `
                                <div class="metric" style="border-left: 3px solid ${
                                    metrics.technical['52_week'].position === 'NEAR_HIGH' ? '#ef4444' :
                                    metrics.technical['52_week'].position === 'NEAR_LOW' ? '#10b981' :
                                    metrics.technical['52_week'].position === 'UPPER_RANGE' ? '#f59e0b' :
                                    metrics.technical['52_week'].position === 'LOWER_RANGE' ? '#22c55e' : '#94a3b8'
                                }; padding-left: 10px;">
                                    <div class="metric-label">52-Week Position</div>
                                    <div class="metric-value" style="font-size: 0.85rem; color: ${
                                        metrics.technical['52_week'].percent_in_range > 75 ? '#f59e0b' : '#10b981'
                                    }">
                                        ${metrics.technical['52_week'].percent_in_range.toFixed(0)}%
                                    </div>
                                    <div class="metric-small">${metrics.technical['52_week'].position.replace(/_/g, ' ')}</div>
                                </div>
                            ` : '<div class="metric"><div class="metric-label">52-Week</div><div class="metric-value">N/A</div></div>'}

                            <!-- Divergence Alert -->
                            ${metrics.technical?.divergence ? `
                                <div class="metric" style="border-left: 3px solid ${
                                    metrics.technical.divergence.divergence === 'BULLISH' ? '#10b981' :
                                    metrics.technical.divergence.divergence === 'BEARISH' ? '#ef4444' : '#94a3b8'
                                }; padding-left: 10px;">
                                    <div class="metric-label">RSI Divergence</div>
                                    <div class="metric-value" style="font-size: 0.75rem; color: ${
                                        metrics.technical.divergence.divergence === 'BULLISH' ? '#10b981' :
                                        metrics.technical.divergence.divergence === 'BEARISH' ? '#ef4444' : '#94a3b8'
                                    }">
                                        ${metrics.technical.divergence.divergence === 'NONE' ? 'None' : metrics.technical.divergence.signal}
                                    </div>
                                    <div class="metric-small">${metrics.technical.divergence.divergence !== 'NONE' ? '⚠️ ' + metrics.technical.divergence.divergence : 'No divergence'}</div>
                                </div>
                            ` : '<div class="metric"><div class="metric-label">Divergence</div><div class="metric-value">N/A</div></div>'}

                            <!-- Debt to Equity -->
                            ${metrics.valuation?.debt_to_equity ? `
                                <div class="metric" style="border-left: 3px solid ${
                                    metrics.valuation.debt_to_equity < 50 ? '#10b981' :
                                    metrics.valuation.debt_to_equity < 100 ? '#f59e0b' : '#ef4444'
                                }; padding-left: 10px;">
                                    <div class="metric-label">Debt/Equity</div>
                                    <div class="metric-value" style="color: ${
                                        metrics.valuation.debt_to_equity < 50 ? '#10b981' :
                                        metrics.valuation.debt_to_equity < 100 ? '#f59e0b' : '#ef4444'
                                    }">
                                        ${metrics.valuation.debt_to_equity.toFixed(0)}%
                                    </div>
                                    <div class="metric-small">${
                                        metrics.valuation.debt_to_equity < 50 ? 'Low risk' :
                                        metrics.valuation.debt_to_equity < 100 ? 'Moderate' : 'High risk'
                                    }</div>
                                </div>
                            ` : '<div class="metric"><div class="metric-label">D/E Ratio</div><div class="metric-value">N/A</div></div>'}
                        </div>
                    </div>
                </div>

                <!-- Accumulation Analysis (Compact) -->
                <div class="compact-grid">
                    <!-- Accumulation Phase Card -->
                    <div class="card" style="grid-column: span 2;">
                        <div style="background: ${getPhaseColor(accumulation.phase.current_phase)}; padding: 15px; border-radius: 8px; text-align: center;">
                            <div style="font-size: 1.3rem; font-weight: bold; margin-bottom: 8px;">${accumulation.phase.current_phase}</div>
                            <div style="font-size: 0.85rem; opacity: 0.95;">${accumulation.phase.description}</div>
                        </div>
                        <h3 style="margin-top: 15px;">📊 Accumulation Metrics</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">🐘 Are the BIG SMART elephants (rich people) buying? When elephants dance, we follow! Strong = good sign!</p>
                        <div class="metric-row" style="grid-template-columns: 1fr 1fr 1fr;">
                            <div class="metric">
                                <div class="metric-label">Strength</div>
                                <div class="metric-value">${accumulation.strength.score}</div>
                                <div class="metric-small">${accumulation.strength.strength}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">OBV</div>
                                <div class="metric-value" style="font-size: 0.9rem;">${accumulation.obv_analysis.trend}</div>
                                <div class="metric-small">${accumulation.obv_analysis.interpretation.substring(0, 12)}</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Vol Ratio</div>
                                <div class="metric-value">${accumulation.current_volume_vs_average.ratio}x</div>
                                <div class="metric-small">${accumulation.current_volume_vs_average.status.substring(0, 10)}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Participant Analysis -->
                    <div class="card">
                        <h3>👥 Participants</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">🏦 WHO is buying? Big banks (green) = smart! Regular people (red) = be careful! Follow the smart money!</p>
                        <div style="text-align: center; margin: 15px 0;">
                            <div style="font-size: 1.1rem; font-weight: bold; color: ${accumulation.participants.primary_type.includes('Institutional') ? '#10b981' : '#f59e0b'};">
                                ${accumulation.participants.primary_type.replace('Dominant', '').replace('Leaning', '')}
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px; margin: 12px 0;">
                            <div style="flex: ${accumulation.participants.institutional_percent}; background: #10b981; height: 25px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold;">
                                ${accumulation.participants.institutional_percent >= 15 ? accumulation.participants.institutional_percent.toFixed(0) + '%' : ''}
                            </div>
                            <div style="flex: ${accumulation.participants.retail_percent}; background: #ef4444; height: 25px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold;">
                                ${accumulation.participants.retail_percent >= 15 ? accumulation.participants.retail_percent.toFixed(0) + '%' : ''}
                            </div>
                        </div>
                        <div style="font-size: 0.75rem; color: #94a3b8; text-align: center;">
                            🏦 ${accumulation.participants.institutional_percent.toFixed(0)}% | 👤 ${accumulation.participants.retail_percent.toFixed(0)}%
                        </div>
                    </div>
                </div>


                <!-- Entry/Exit & Swing (Compact 3-column) -->
                <div class="compact-grid">
                    <!-- Entry Zones -->
                    <div class="card">
                        <h3>🎯 Entry Zones</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">🛒 WHEN to BUY? Like waiting for your favorite toy to go on SALE! Green = good deal!</p>
                        ${Object.entries(entry_exit.entry_recommendation).map(([type, zone]) => `
                            <div style="background: #0f172a; padding: 10px; border-radius: 6px; margin-bottom: 8px; border-left: 3px solid #10b981;">
                                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">${type}</div>
                                <div style="font-size: 1.1rem; font-weight: bold; margin: 4px 0;">Rp ${zone.price.toLocaleString()}</div>
                                <div style="font-size: 0.75rem; color: ${zone.distance_percent > 0 ? '#10b981' : '#ef4444'};">
                                    ${zone.distance_percent > 0 ? '↑' : '↓'} ${Math.abs(zone.distance_percent).toFixed(1)}% from current
                                </div>
                            </div>
                        `).join('')}
                        <div style="margin-top: 10px; padding: 10px; background: #0f172a; border-radius: 6px; font-size: 0.8rem;">
                            <strong>Action:</strong> ${entry_exit.position_recommendation.recommended_action}
                        </div>
                    </div>

                    <!-- Exit Targets -->
                    <div class="card">
                        <h3>🚀 Exit Targets</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">💰 WHEN to SELL for PROFIT? Like selling your toys for MORE money than you paid! Take your profits!</p>
                        ${Object.entries(entry_exit.exit_recommendation).map(([type, zone]) => `
                            <div style="background: #0f172a; padding: 10px; border-radius: 6px; margin-bottom: 8px; border-left: 3px solid #3b82f6;">
                                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase;">${type}</div>
                                <div style="font-size: 1.1rem; font-weight: bold; margin: 4px 0;">Rp ${zone.price.toLocaleString()}</div>
                                <div style="font-size: 0.75rem; color: #10b981;">
                                    ↑ +${zone.potential_gain_percent.toFixed(1)}% gain
                                </div>
                            </div>
                        `).join('')}
                    </div>

                    <!-- Swing Analysis -->
                    <div class="card">
                        <h3>📈 Swing Analysis</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">🎢 Is this a FUN ROLLER COASTER? Big swings = exciting but bumpy! Shows how much the price goes up & down.</p>
                        <div style="text-align: center; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; margin-bottom: 12px;">
                            <div style="font-size: 1.8rem; font-weight: bold;">${swing_analysis.swing_rating.score}/100</div>
                            <div style="font-size: 0.85rem; opacity: 0.95;">${swing_analysis.swing_rating.rating}</div>
                        </div>
                        <div class="metric-row" style="grid-template-columns: 1fr 1fr;">
                            <div class="metric">
                                <div class="metric-label">Avg Swing</div>
                                <div class="metric-value" style="font-size: 1rem;">${swing_analysis.swing_size.average_swing_percent.toFixed(1)}%</div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">Pattern</div>
                                <div class="metric-value" style="font-size: 0.75rem;">${swing_analysis.swing_pattern.pattern.includes('Higher') ? '📈' : swing_analysis.swing_pattern.pattern.includes('Lower') ? '📉' : '↔️'}</div>
                            </div>
                        </div>
                        <div style="margin-top: 10px; font-size: 0.75rem; color: #94a3b8;">
                            <strong>Bollinger:</strong> ${swing_analysis.bollinger_bands.squeeze_status}<br>
                            <strong>Position:</strong> ${swing_analysis.bollinger_bands.price_position_percent.toFixed(0)}% in bands
                        </div>
                    </div>
                </div>

                <!-- Support & Resistance (Compact) -->
                <div class="compact-grid">
                    <div class="card">
                        <h3 style="color: #10b981;">📍 Support</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">🛡️ SAFETY NET! Like a trampoline - price bounces UP when it hits these levels!</p>
                        ${entry_exit.support_levels.length > 0
                            ? entry_exit.support_levels.map((level, i) => `
                                <div style="background: #0f172a; padding: 8px 10px; border-radius: 4px; margin-bottom: 6px; border-left: 3px solid #10b981; display: flex; justify-content: space-between;">
                                    <span style="font-size: 0.75rem; color: #94a3b8;">S${i + 1}</span>
                                    <strong style="font-size: 0.9rem;">Rp ${level.toLocaleString()}</strong>
                                </div>
                            `).join('')
                            : '<p style="color: #94a3b8; font-size: 0.8rem;">No support detected</p>'
                        }
                    </div>

                    <div class="card">
                        <h3 style="color: #ef4444;">📍 Resistance</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">🚧 CEILING BLOCK! Like hitting your head on the ceiling - price has trouble going higher!</p>
                        ${entry_exit.resistance_levels.length > 0
                            ? entry_exit.resistance_levels.map((level, i) => `
                                <div style="background: #0f172a; padding: 8px 10px; border-radius: 4px; margin-bottom: 6px; border-left: 3px solid #ef4444; display: flex; justify-content: space-between;">
                                    <span style="font-size: 0.75rem; color: #94a3b8;">R${i + 1}</span>
                                    <strong style="font-size: 0.9rem;">Rp ${level.toLocaleString()}</strong>
                                </div>
                            `).join('')
                            : '<p style="color: #94a3b8; font-size: 0.8rem;">No resistance detected</p>'
                        }
                    </div>

                    <div class="card">
                        <h3>🔢 Fibonacci</h3>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin: -5px 0 12px 0;">📏 MAGIC RULER! Special levels where stock often takes a break or turns around - like steps on stairs!</p>
                        <div style="font-size: 0.75rem;">
                            ${Object.entries(entry_exit.fibonacci_levels).slice(0, 5).map(([level, price]) => `
                                <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #334155;">
                                    <span style="color: #94a3b8;">${level.replace('level_', '').replace('_', '.')}%</span>
                                    <strong>Rp ${price.toLocaleString()}</strong>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>

                <!-- Analysis Categories (Compact) -->
                <div class="card">
                    <h3>📋 Analysis Breakdown</h3>
                    <div class="metric-row" style="grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));">
                        ${Object.entries(overall_analysis.analysis).map(([category, data]) => `
                            <div class="metric">
                                <div class="metric-label">${category.substring(0, 12)}</div>
                                <div class="metric-value" style="font-size: 1.1rem;">${data.score.toFixed(0)}%</div>
                                <div style="margin-top: 5px; height: 5px; background: #0f172a; border-radius: 2px; overflow: hidden;">
                                    <div style="width: ${data.score}%; height: 100%; background: ${data.score >= 70 ? '#10b981' : data.score >= 50 ? '#f59e0b' : '#ef4444'};"></div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;

            document.getElementById('dashboard').innerHTML = html;
        }

        function getPhaseColor(phase) {
            const colors = {
                'ACCUMULATION': 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                'MARKUP': 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
                'DISTRIBUTION': 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                'MARKDOWN': 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                'NEUTRAL/CONSOLIDATION': 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)'
            };
            return colors[phase] || 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        }

        function getActionBadgeClass(action) {
            if (action.includes('BUY')) return 'success';
            if (action.includes('SELL')) return 'danger';
            if (action.includes('WAIT') || action.includes('HOLD')) return 'warning';
            return 'secondary';
        }

        function getSignalClass(type) {
            const classes = {
                'BUY': 'positive',
                'SELL': 'negative',
                'ALERT': 'warning',
                'HOLD': 'neutral'
            };
            return classes[type] || 'neutral';
        }

        function getSignalIcon(type) {
            const icons = {
                'BUY': '↑',
                'SELL': '↓',
                'ALERT': '⚠',
                'HOLD': '='
            };
            return icons[type] || 'i';
        }

        function getReasonIcon(type) {
            const icons = {
                'positive': '✓',
                'negative': '✗',
                'warning': '!',
                'neutral': 'i'
            };
            return icons[type] || 'i';
        }

        async function addFavorite() {
            try {
                const symbol = currentSymbol;
                const name = document.querySelector('.stock-name h2').textContent;

                const response = await fetch('/api/favorites', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({symbol, name})
                });

                const data = await response.json();
                if (data.success) {
                    alert('Added to favorites!');
                    loadDashboard();
                    loadFavorites();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Error adding to favorites: ' + error.message);
            }
        }

        async function removeFavorite() {
            try {
                const response = await fetch(`/api/favorites/${currentSymbol}`, {
                    method: 'DELETE'
                });

                const data = await response.json();
                if (data.success) {
                    alert('Removed from favorites!');
                    loadDashboard();
                    loadFavorites();
                }
            } catch (error) {
                alert('Error removing from favorites: ' + error.message);
            }
        }

        async function loadFavorites() {
            try {
                const response = await fetch('/api/favorites');
                const data = await response.json();

                if (data.success) {
                    document.getElementById('favCount').textContent = data.count;

                    if (data.count === 0) {
                        document.getElementById('favoritesList').innerHTML = '<p style="color: #94a3b8;">No favorites yet</p>';
                    } else {
                        const html = data.data.map(fav => `
                            <div class="favorites-item" onclick="loadFavoriteStock('${fav.symbol.replace('.JK', '')}')">
                                <strong>${fav.symbol}</strong>
                                <div style="font-size: 0.85rem; color: #94a3b8; margin-top: 5px;">${fav.name}</div>
                            </div>
                        `).join('');
                        document.getElementById('favoritesList').innerHTML = html;
                    }
                }
            } catch (error) {
                console.error('Error loading favorites:', error);
            }
        }

        function loadFavoriteStock(symbol) {
            document.getElementById('stockSymbol').value = symbol;
            loadDashboard();
            toggleFavorites();
        }

        function toggleFavorites() {
            const sidebar = document.getElementById('favoritesSidebar');
            sidebar.classList.toggle('open');
        }

        // Load favorites on page load
        window.addEventListener('load', () => {
            loadFavorites();
            // Auto-load BBCA as example
            document.getElementById('stockSymbol').value = 'BBCA';
            loadDashboard();
        });
    </script>
</body>
</html>
