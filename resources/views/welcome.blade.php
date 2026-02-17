<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Digital Menu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --surface: #ffffff;
            --surface-soft: #f9fafb;
            --line: #f3f4f6;
            --text: #111827;
            --muted: #6b7280;
            --red: #e11d48;
            --yellow: #facc15;
            --yellow-strong: #eab308;
            --radius-lg: 24px;
            --radius-md: 16px;
            --radius-sm: 12px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        [data-theme="dark"] {
            --bg: #0f172a;
            --surface: #1e293b;
            --surface-soft: #334155;
            --line: #1e293b;
            --text: #f8fafc;
            --muted: #94a3b8;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.2);
            --shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: "Outfit", "Segoe UI", sans-serif;
            overflow-x: hidden;
        }

        body.drawer-open {
            overflow: hidden;
        }

        /* Safe area support for mobile devices */
        :root {
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --safe-left: env(safe-area-inset-left, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
        }

        .layout-wrapper {
            display: grid;
            grid-template-columns: 1fr;
            min-height: 100vh;
        }

        .main-content {
            padding: 1rem;
            max-width: 100%;
            margin: 0 auto;
        }

        .side-panel {
            display: none;
            background: #fff;
            border-left: 1px solid var(--line);
            padding: 1.5rem;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        @media (min-width: 1024px) {
            .layout-wrapper {
                grid-template-columns: 1fr 400px;
            }

            .side-panel {
                display: block;
            }

            .main-content {
                max-width: 1200px;
            }
        }

        .app-container {
            width: 100%;
            margin-bottom: 6rem;
        }

        .flash {
            position: fixed;
            top: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2000;
            width: min(400px, calc(100% - 2rem));
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            font-size: 0.9375rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.4s cubic-bezier(0.2, 0, 0, 1);
            pointer-events: auto;
        }

        @keyframes slideDown {
            from {
                transform: translate(-50%, -2rem);
                opacity: 0;
            }

            to {
                transform: translate(-50%, 0);
                opacity: 1;
            }
        }

        .flash-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .flash-success::before {
            content: "✓";
            display: grid;
            place-items: center;
            width: 24px;
            height: 24px;
            background: #10b981;
            color: #fff;
            border-radius: 999px;
            font-size: 0.75rem;
        }

        .flash-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #f87171;
        }

        .flash-error::before {
            content: "!";
            display: grid;
            place-items: center;
            width: 24px;
            height: 24px;
            background: #ef4444;
            color: #fff;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 900;
        }

        .hero {
            padding: 2.5rem 0.5rem 3rem;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem 0.5rem 0.6rem;
            border-radius: 999px;
            background: var(--surface-soft);
            border: 1px solid var(--line);
            font-size: 0.875rem;
            color: var(--text);
            font-weight: 600;
            margin-bottom: 2rem;
            margin-top: var(--safe-top);
        }

        .hero-badge-mark {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            display: inline-grid;
            place-items: center;
            background: var(--yellow);
            color: #000;
            font-family: "Sora", sans-serif;
            font-weight: 800;
        }

        .hero h1 {
            margin: 0;
            font-family: "Sora", sans-serif;
            font-size: clamp(2.5rem, 8vw, 4.5rem);
            line-height: 1.05;
            letter-spacing: -0.05em;
            font-weight: 800;
        }

        .hero p {
            margin: 1.5rem 0 0;
            color: var(--muted);
            max-width: 42ch;
            font-size: 1.125rem;
            line-height: 1.6;
        }

        .section {
            margin-top: 1rem;
        }

        .section-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 0.7rem;
            margin-bottom: 0.62rem;
            padding: 0 0.35rem;
        }

        .section-head h2 {
            margin: 0;
            font-family: "Sora", sans-serif;
            font-size: 1.75rem;
            letter-spacing: -0.03em;
            font-weight: 800;
        }

        .featured-track {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(280px, 85%);
            gap: 1.25rem;
            overflow-x: auto;
            padding: 0.5rem 0.5rem 1.5rem;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
        }

        .featured-track::-webkit-scrollbar {
            display: none;
        }

        .featured-card {
            scroll-snap-align: start;
            border-radius: var(--radius-lg);
            padding: 0;
            background: var(--surface);
            border: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            transition: all 0.4s cubic-bezier(0.2, 0, 0, 1);
            position: relative;
            overflow: hidden;
        }

        .featured-card.is-active {
            border-color: var(--yellow);
            box-shadow: 0 12px 30px -10px rgba(0, 0, 0, 0.1);
        }

        .featured-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            z-index: 10;
            font-size: 0.7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-radius: var(--radius-sm);
            padding: 0.5rem 0.75rem;
            background: #fff;
            color: var(--red);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .featured-media {
            width: 100%;
            aspect-ratio: 16 / 10;
            border-radius: 0;
            background: #fdfdfd;
            overflow: hidden;
        }

        .featured-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.2;
            font-family: "Sora", sans-serif;
            letter-spacing: -0.02em;
        }

        .featured-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            flex-grow: 1;
            background: #fff;
        }

        .featured-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-top: auto;
        }

        .featured-price {
            margin: 0;
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--text);
            font-family: "Sora", sans-serif;
        }

        .add-btn {
            border: 0;
            border-radius: var(--radius-md);
            background: var(--yellow);
            color: #008;
            font: inherit;
            font-weight: 800;
            font-size: 0.9375rem;
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 0 var(--yellow-strong);
            transform: translateY(-2px);
        }

        .add-btn:hover {
            background: var(--yellow-strong);
            transform: translateY(-1px);
        }

        .add-btn:active {
            transform: translateY(2px);
            box-shadow: 0 0 0 var(--yellow-strong);
        }

        .featured-qty {
            justify-self: start;
            color: var(--muted);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .category-tabs-wrap {
            position: sticky;
            top: calc(0.5rem + var(--safe-top));
            z-index: 100;
            background: var(--surface);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: var(--radius-md);
            border: 1px solid var(--line);
            margin: 0.5rem 0.5rem 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        [data-theme="dark"] .category-tabs-wrap {
            background: rgba(30, 41, 59, 0.8);
        }

        .category-tabs {
            display: flex;
            gap: 0.75rem;
            overflow-x: auto;
            padding: 0.75rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .category-tabs::-webkit-scrollbar {
            display: none;
        }

        .category-tab {
            border: 1px solid var(--line);
            background: var(--surface);
            color: var(--text);
            border-radius: 999px;
            padding: 0.75rem 1.25rem;
            text-decoration: none;
            white-space: nowrap;
            font-size: 1rem;
            font-weight: 700;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            /* Extra touch target */
        }

        .category-tab.is-active {
            background: #101010;
            color: #fff;
            border-color: #101010;
        }

        .menu-section {
            margin-top: 1.15rem;
            padding: 0 0.35rem;
            scroll-margin-top: 4.2rem;
        }

        .menu-section h3 {
            margin: 0;
            font-family: "Sora", "Outfit", sans-serif;
            font-size: 1.16rem;
        }

        .menu-section p {
            margin: 0.3rem 0 0;
            color: var(--muted);
            font-size: 0.84rem;
        }

        .items-grid {
            margin-top: 0.62rem;
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: 82%;
            gap: 1rem;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding: 0.5rem 0.5rem 1.5rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .items-grid::-webkit-scrollbar {
            display: none;
        }

        .item-card {
            scroll-snap-align: start;
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            background: var(--surface);
            border: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            transition: all 0.4s cubic-bezier(0.2, 0, 0, 1);
            position: relative;
        }

        .item-card:hover {
            border-color: var(--yellow);
            transform: translateY(-6px);
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
        }

        .item-media {
            width: 100%;
            aspect-ratio: 1;
            border-radius: var(--radius-md);
            overflow: hidden;
            background: var(--surface-soft);
            display: grid;
            place-items: center;
        }

        .item-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.2, 0, 0, 1);
        }

        .item-card:hover .item-media img {
            transform: scale(1.1);
        }

        .media-fallback {
            color: #9ca3af;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .item-name {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.2;
            font-family: "Sora", sans-serif;
            letter-spacing: -0.01em;
        }

        .item-desc {
            margin: 0.5rem 0 0;
            color: var(--muted);
            font-size: 0.9375rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 3.2em;
        }

        .item-foot {
            margin-top: 0.15rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.45rem;
        }

        .item-price {
            margin: 0;
            font-size: 0.94rem;
            font-weight: 700;
        }

        .qty-inline {
            display: inline-flex;
            align-items: center;
            gap: 0.28rem;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
            font-size: 1.125rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: #f0f0f0;
        }

        .qty-value {
            min-width: 20px;
            text-align: center;
            font-size: 0.86rem;
            font-weight: 700;
        }

        .empty-state {
            margin-top: 0.9rem;
            border-radius: 16px;
            border: 1px dashed var(--line);
            background: #fbfbfb;
            color: var(--muted);
            padding: 0.9rem;
            font-size: 0.9rem;
        }

        .floating-order-bar {
            position: fixed;
            left: 50%;
            bottom: max(0.75rem, var(--safe-bottom));
            width: min(760px, calc(100% - 1.5rem));
            z-index: 1000;
            transform: translate(-50%, calc(100% + 2rem));
            transition: transform 0.4s cubic-bezier(0.2, 0, 0, 1);
            pointer-events: none;
        }

        .floating-order-bar.is-visible {
            transform: translate(-50%, 0);
            pointer-events: auto;
        }

        .floating-order-trigger {
            width: 100%;
            border: 0;
            border-radius: 24px;
            background: #101010;
            color: #fff;
            padding: 1.125rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        }

        .floating-meta {
            display: grid;
            gap: 0.05rem;
            text-align: left;
        }

        .floating-meta strong {
            font-size: 1.125rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .floating-meta span {
            color: rgba(255, 255, 255, 0.76);
            font-size: 0.9375rem;
        }

        .floating-cta {
            border-radius: 999px;
            background: var(--yellow);
            color: #101010;
            font-size: 1rem;
            font-weight: 800;
            padding: 0.6rem 1.25rem;
            white-space: nowrap;
            box-shadow: 0 4px 0 var(--yellow-strong);
        }

        .drawer-overlay {
            position: fixed;
            inset: 0;
            background: rgba(16, 16, 16, 0.36);
            opacity: 0;
            pointer-events: none;
            transition: opacity 180ms ease;
            z-index: 22;
        }

        .drawer-overlay.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        .order-drawer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 100;
            background: #fff;
            border-radius: 32px 32px 0 0;
            border-top: 1px solid var(--line);
            max-height: min(92vh, 1000px);
            transform: translateY(104%);
            transition: transform 0.4s cubic-bezier(0.2, 0, 0, 1);
            display: grid;
            grid-template-rows: auto 1fr;
            box-shadow: 0 -20px 40px rgba(0, 0, 0, 0.1);
        }

        .order-drawer.is-open {
            transform: translateY(0);
        }

        .drawer-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--line);
        }

        .drawer-head h2 {
            margin: 0;
            font-size: 1.75rem;
            font-family: "Sora", sans-serif;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .drawer-head p {
            margin: 0.5rem 0 0;
            color: var(--muted);
            font-size: 1rem;
        }

        .drawer-body {
            overflow: auto;
            padding: 2rem;
        }

        .field-grid {
            display: grid;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .field {
            display: grid;
            gap: 0.625rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--text);
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            border: 2px solid var(--line);
            border-radius: var(--radius-md);
            background: var(--surface);
            color: var(--text);
            padding: 1rem 1.25rem;
            font: inherit;
            font-size: 16px;
            /* Prevents iOS auto-zoom */
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .field textarea {
            min-height: 120px;
            resize: vertical;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            outline: none;
            border-color: var(--yellow);
            box-shadow: 0 0 0 4px rgba(250, 204, 21, 0.1);
        }

        .field-error {
            margin: 0.25rem 0 0;
            color: var(--red);
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .order-summary {
            margin-top: 0;
            border: 2px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            background: var(--surface-soft);
        }

        .order-summary-head h3 {
            margin: 0;
            font-size: 1.25rem;
            font-family: "Sora", sans-serif;
            font-weight: 700;
        }

        .order-summary-head span {
            font-size: 0.77rem;
            color: var(--muted);
        }

        .summary-list {
            display: grid;
            gap: 0.38rem;
            max-height: 150px;
            overflow: auto;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 0.55rem;
            font-size: 0.84rem;
        }

        .summary-empty {
            margin: 0;
            font-size: 0.84rem;
            color: var(--muted);
        }

        .summary-total {
            margin-top: 0.56rem;
            padding-top: 0.56rem;
            border-top: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 0.5rem;
        }

        .summary-total span {
            color: var(--muted);
            font-size: 0.84rem;
        }

        .summary-total strong {
            font-family: "Sora", "Outfit", sans-serif;
            font-size: 1.46rem;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .confirm-btn {
            width: 100%;
            margin-top: 0.76rem;
            border: 0;
            border-radius: 18px;
            background: var(--yellow);
            color: #111;
            font: inherit;
            font-size: 1rem;
            font-weight: 700;
            padding: 0.76rem 0.9rem;
            cursor: pointer;
            transition: background-color 120ms ease;
        }

        .confirm-btn:hover:not(:disabled) {
            background: var(--yellow-strong);
        }

        .confirm-btn:disabled {
            opacity: 0.62;
            cursor: not-allowed;
        }

        .helper-note {
            margin: 0.5rem 0 0;
            color: var(--muted);
            font-size: 0.78rem;
        }

        @keyframes bump {
            0% {
                transform: scale(1);
            }

            45% {
                transform: scale(1.16);
            }

            100% {
                transform: scale(1);
            }
        }

        @media (min-width: 760px) {
            .app {
                padding: 1rem;
            }

            .featured-track {
                grid-auto-columns: minmax(260px, 44%);
            }

            .items-grid {
                grid-auto-flow: row;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                overflow-x: visible;
                padding: 0;
            }

            .order-drawer {
                left: 50%;
                right: auto;
                width: min(620px, calc(100% - 1rem));
                transform: translate(-50%, 104%);
                border-radius: 24px;
                border: 1px solid var(--line);
            }

            .order-drawer.is-open {
                transform: translate(-50%, 0);
                bottom: max(0.6rem, var(--safe-bottom));
            }
        }

        @media (min-width: 1080px) {
            .app {
                width: min(1200px, calc(100% - 1.2rem));
            }

            .items-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .featured-track {
                grid-auto-columns: minmax(290px, 32%);
            }
        }

        @keyframes pop {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.03);
            }

            100% {
                transform: scale(1);
            }
        }

        .item-card.is-popping,
        .featured-card.is-popping {
            animation: pop 0.4s cubic-bezier(0.2, 0, 0, 1);
            border-color: var(--yellow);
        }

        .cart-qty-btn {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--text);
            display: grid;
            place-items: center;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .theme-toggle {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            background: var(--surface-soft);
            border: 1px solid var(--line);
            color: var(--text);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .theme-toggle:hover {
            border-color: var(--yellow);
            transform: rotate(12deg);
        }

        [data-theme="dark"] .sun-icon {
            display: block !important;
        }

        [data-theme="light"] .moon-icon {
            display: block !important;
        }

        /* Ensure specific elements look good in dark mode */
        [data-theme="dark"] .order-drawer,
        [data-theme="dark"] .side-panel,
        [data-theme="dark"] .floating-order-bar,
        [data-theme="dark"] .featured-badge {
            background: var(--surface);
            color: var(--text);
        }

        [data-theme="dark"] .featured-badge {
            background: #fff;
            color: #000;
        }

        [data-theme="dark"] .field input,
        [data-theme="dark"] .field select,
        [data-theme="dark"] .field textarea {
            background: var(--bg);
            border-color: var(--line);
            color: var(--text);
        }

        [data-theme="dark"] .cart-qty-btn {
            background: var(--surface-soft);
            border-color: var(--line);
            color: var(--text);
        }

        [data-theme="dark"] .flash-success {
            background: #064e3b;
            color: #ecfdf5;
            border-color: #059669;
        }

        [data-theme="dark"] .flash-error {
            background: #7f1d1d;
            color: #fef2f2;
            border-color: #dc2626;
        }

        /* Order Confirmation Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 3000;
            display: grid;
            place-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s cubic-bezier(0.2, 0, 0, 1);
            padding: 1.5rem;
        }

        .modal-overlay.is-active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            background: var(--surface);
            width: min(480px, 100%);
            border-radius: 32px;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: translateY(20px) scale(0.95);
            transition: all 0.5s cubic-bezier(0.2, 0, 0, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            border: 1px solid var(--line);
        }

        .modal-overlay.is-active .modal-card {
            transform: translateY(0) scale(1);
        }

        .modal-icon {
            width: 80px;
            height: 80px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .modal-icon.success {
            background: #ecfdf5;
            color: #10b981;
            box-shadow: 0 0 0 10px #f0fdf4;
        }

        .modal-icon.error {
            background: #fef2f2;
            color: #ef4444;
            box-shadow: 0 0 0 10px #fef2f2;
        }

        .modal-title {
            font-family: "Sora", sans-serif;
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.04em;
        }

        .modal-desc {
            color: var(--muted);
            line-height: 1.6;
            margin: 0;
            font-size: 1.125rem;
        }

        .modal-btn {
            width: 100%;
            padding: 1.25rem;
            border-radius: var(--radius-md);
            background: var(--text);
            color: var(--bg);
            font-weight: 800;
            font-size: 1.125rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 1rem;
        }

        .modal-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Empty state back button */
        .back-to-menu-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            background: var(--yellow);
            color: #000;
            font-weight: 800;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 1.5rem;
            box-shadow: 0 4px 0 var(--yellow-strong);
        }

        .back-to-menu-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 0 var(--yellow-strong);
        }

        [data-theme="dark"] .modal-icon.success {
            background: #064e3b;
            color: #10b981;
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0.1);
        }

        [data-theme="dark"] .modal-icon.error {
            background: #7f1d1d;
            color: #f87171;
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0.1);
        }
    </style>
    <script>
        (() => {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>

<body>
    @php
        $oldQuantities = collect(old('quantities', []))->map(fn($value) => max(0, (int) $value));
        $flatItems = $categories->flatMap(fn($category) => $category->items)->values();
        $oldItemCount = $oldQuantities->sum();
        $oldSubtotal = $flatItems->sum(fn($item) => ((float) $item->price) * (int) $oldQuantities->get($item->id, 0));
        $selectedBranchId = (string) old('branch_id', $branches->first()?->id);

        $featuredItems = $flatItems->where('is_featured', true)->take(10);

        if ($featuredItems->isEmpty()) {
            $featuredItems = $flatItems->take(10);
        }
    @endphp

    <div class="layout-wrapper">
        <main class="main-content">
            <div class="app-container">
                @if (session('success'))
                    <div id="session-success-trigger" data-message="{{ session('success') }}" hidden></div>
                @endif

                @if ($errors->any())
                    <div id="session-error-trigger"
                        data-message="Please review your details and confirm the order again." hidden></div>
                @endif

                <header class="hero">
                    <div
                        style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
                        <span class="hero-badge">
                            <span class="hero-badge-mark">M</span>
                            <span>Digital Menu</span>
                        </span>
                        <button type="button" id="theme-toggle" class="theme-toggle" aria-label="Toggle theme">
                            <svg class="sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="display: none; width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.364l-.707.707M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg class="moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="display: none; width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                    </div>
                    <h1>Hey,<br>what's up?</h1>
                    <p>Pick from featured items, browse categories, then place your order in seconds.</p>
                </header>

                @if ($featuredItems->isNotEmpty())
                    <section class="section">
                        <div class="section-head">
                            <h2>Featured</h2>
                            <p>Popular right now</p>
                        </div>

                        <div class="featured-track" id="featured-track">
                            @foreach ($featuredItems as $item)
                                @php
                                    $featuredImage =
                                        $item->itemImages->firstWhere('is_primary', true)?->image_path ??
                                        $item->itemImages->first()?->image_path;
                                    $oldQuantity = (int) old("quantities.{$item->id}", 0);
                                @endphp

                                <article class="featured-card" data-item-card data-item-id="{{ $item->id }}"
                                    data-item-name="{{ e($item->name) }}"
                                    data-item-image="{{ $featuredImage ? asset('storage/' . $featuredImage) : '' }}"
                                    data-item-price="{{ number_format((float) $item->price, 2, '.', '') }}">
                                    <span class="featured-badge">{{ $loop->odd ? 'Popular' : "Chef's Choice" }}</span>

                                    <div class="featured-media">
                                        @if ($featuredImage)
                                            <img src="{{ asset('storage/' . $featuredImage) }}"
                                                alt="{{ $item->name }}" loading="lazy">
                                        @else
                                            <span class="media-fallback">No image</span>
                                        @endif
                                    </div>

                                    <div class="featured-content">
                                        <h3 class="featured-title">{{ $item->name }}</h3>

                                        <div class="featured-foot">
                                            <p class="featured-price">${{ number_format((float) $item->price, 2) }}</p>
                                            <button type="button" class="add-btn" data-add-btn
                                                data-qty-action="increase" data-item-id="{{ $item->id }}"
                                                aria-label="Add {{ $item->name }}">
                                                + Add
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($categories->isNotEmpty())
                    <section class="section">
                        <div class="category-tabs-wrap">
                            <nav class="category-tabs" aria-label="Menu categories">
                                @foreach ($categories as $category)
                                    <a href="#category-{{ $category->id }}" class="category-tab" data-category-tab
                                        data-category-id="category-{{ $category->id }}">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </nav>
                        </div>

                        @foreach ($categories as $category)
                            <section class="menu-section" id="category-{{ $category->id }}" data-category-section>
                                <h3>{{ $category->name }}</h3>
                                @if ($category->description)
                                    <p>{{ $category->description }}</p>
                                @endif

                                <div class="items-grid">
                                    @foreach ($category->items as $item)
                                        @php
                                            $primaryImage =
                                                $item->itemImages->firstWhere('is_primary', true) ??
                                                $item->itemImages->first();
                                            $oldQuantity = (int) old("quantities.{$item->id}", 0);
                                        @endphp

                                        <article class="item-card" data-item-card data-item-id="{{ $item->id }}"
                                            data-item-name="{{ e($item->name) }}"
                                            data-item-image="{{ $primaryImage?->image_path ? asset('storage/' . $primaryImage->image_path) : '' }}"
                                            data-item-price="{{ number_format((float) $item->price, 2, '.', '') }}">
                                            <div class="item-media">
                                                @if ($primaryImage?->image_path)
                                                    <img src="{{ asset('storage/' . $primaryImage->image_path) }}"
                                                        alt="{{ $item->name }}" loading="lazy">
                                                @else
                                                    <span class="media-fallback">No image</span>
                                                @endif
                                            </div>

                                            <div>
                                                <h4 class="item-name">{{ $item->name }}</h4>
                                                <p class="item-desc">
                                                    {{ $item->description ?: 'Freshly prepared and ready to order.' }}
                                                </p>
                                            </div>

                                            <div class="item-foot">
                                                <p class="item-price">${{ number_format((float) $item->price, 2) }}
                                                </p>
                                                <div class="qty-inline">
                                                    <button type="button" class="qty-btn" data-qty-action="decrease"
                                                        data-item-id="{{ $item->id }}"
                                                        aria-label="Decrease {{ $item->name }}">
                                                        -
                                                    </button>
                                                    <span class="qty-value"
                                                        data-qty-label="{{ $item->id }}">{{ $oldQuantity }}</span>
                                                    <button type="button" class="add-btn" data-add-btn
                                                        data-qty-action="increase" data-item-id="{{ $item->id }}"
                                                        aria-label="Add {{ $item->name }}">
                                                        + Add
                                                    </button>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </section>
                @else
                    <div class="empty-state">No active menu items are available yet.</div>
                @endif
            </div> <!-- app-container -->
        </main>

        <aside class="side-panel">
            <div class="drawer-head"
                style="padding: 0 0 1.5rem 0; border-bottom: 2px solid var(--line); margin-bottom: 2rem;">
                <h2 style="margin: 0; font-family: 'Sora', sans-serif; font-size: 1.75rem; font-weight: 800;">Your
                    Order
                </h2>
            </div>

            <div id="side-summary-content">
                <div class="order-summary" style="border: none; padding: 0; background: none; margin-top: 0;">
                    <div class="summary-list" id="side-summary-list"
                        style="max-height: none; gap: 1.25rem; display: flex; flex-direction: column;">
                        <p class="summary-empty"
                            style="padding: 3rem 0; text-align: center; color: var(--muted); font-size: 1rem;">Your
                            cart
                            is empty</p>
                    </div>

                    <div class="summary-total"
                        style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid var(--line); display: flex; justify-content: space-between; align-items: baseline;">
                        <span style="font-size: 1.125rem; color: var(--muted); font-weight: 600;">Subtotal</span>
                        <strong data-subtotal
                            style="font-size: 2.5rem; font-family: 'Sora', sans-serif; letter-spacing: -0.04em;">$0.00</strong>
                    </div>
                </div>

                <button type="button" class="confirm-btn" id="side-checkout-trigger"
                    style="margin-top: 2.5rem; padding: 1.25rem; width: 100%; border-radius: var(--radius-md); background: var(--yellow); font-weight: 900; font-size: 1.25rem; border: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 6px 0 var(--yellow-strong); transform: translateY(-4px);">
                    Checkout Now
                </button>
                <div
                    style="margin-top: 2rem; display: flex; align-items: center; justify-content: center; gap: 0.75rem; color: #059669; font-weight: 700; font-size: 0.875rem; background: #ecfdf5; padding: 0.75rem; border-radius: var(--radius-sm);">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                    Secure Order
                </div>
            </div>
        </aside>
    </div> <!-- layout-wrapper -->

    <div class="floating-order-bar {{ $oldItemCount > 0 ? 'is-visible' : '' }}" id="floating-order-bar">
        <button type="button" class="floating-order-trigger" id="open-order-drawer" aria-controls="order-drawer"
            aria-expanded="false">
            <span class="floating-meta">
                <strong><span class="floating-value" data-total-count>{{ $oldItemCount }}</span> items</strong>
                <span class="floating-value" data-subtotal>${{ number_format((float) $oldSubtotal, 2) }}</span>
            </span>
            <span class="floating-cta">Place Order</span>
        </button>
    </div>

    <div class="drawer-overlay {{ $errors->any() ? 'is-open' : '' }}" id="drawer-overlay"></div>

    <aside class="order-drawer {{ $errors->any() ? 'is-open' : '' }}" id="order-drawer"
        aria-hidden="{{ $errors->any() ? 'false' : 'true' }}">
        <header class="drawer-head">
            <div>
                <h2>Review Order</h2>
                <p>Confirm items, add details, and place order.</p>
            </div>
            <button type="button" class="drawer-close" id="close-order-drawer"
                aria-label="Close order drawer">✕</button>
        </header>

        <div class="drawer-body">
            <form id="order-form" action="{{ route('menu.order.store') }}" method="POST">
                @csrf

                <div aria-hidden="true" hidden>
                    @foreach ($categories as $category)
                        @foreach ($category->items as $item)
                            <input type="hidden" name="quantities[{{ $item->id }}]"
                                value="{{ max(0, (int) old("quantities.{$item->id}", 0)) }}"
                                data-qty-input="{{ $item->id }}">
                        @endforeach
                    @endforeach

                    <input type="hidden" name="telegram_init_data" value="{{ old('telegram_init_data', '') }}"
                        data-tg-field="telegram_init_data">
                    <input type="hidden" name="telegram_init_data_unsafe"
                        value="{{ old('telegram_init_data_unsafe', '') }}" data-tg-field="telegram_init_data_unsafe">
                    <input type="hidden" name="telegram_query_id" value="{{ old('telegram_query_id', '') }}"
                        data-tg-field="telegram_query_id">
                    <input type="hidden" name="telegram_auth_date" value="{{ old('telegram_auth_date', '') }}"
                        data-tg-field="telegram_auth_date">
                    <input type="hidden" name="telegram_start_param" value="{{ old('telegram_start_param', '') }}"
                        data-tg-field="telegram_start_param">
                    <input type="hidden" name="telegram_user_id" value="{{ old('telegram_user_id', '') }}"
                        data-tg-field="telegram_user_id">
                    <input type="hidden" name="telegram_username" value="{{ old('telegram_username', '') }}"
                        data-tg-field="telegram_username">
                    <input type="hidden" name="telegram_first_name" value="{{ old('telegram_first_name', '') }}"
                        data-tg-field="telegram_first_name">
                    <input type="hidden" name="telegram_last_name" value="{{ old('telegram_last_name', '') }}"
                        data-tg-field="telegram_last_name">
                    <input type="hidden" name="telegram_language_code"
                        value="{{ old('telegram_language_code', '') }}" data-tg-field="telegram_language_code">
                    <input type="hidden" name="telegram_chat_id" value="{{ old('telegram_chat_id', '') }}"
                        data-tg-field="telegram_chat_id">
                    <input type="hidden" name="telegram_chat_type" value="{{ old('telegram_chat_type', '') }}"
                        data-tg-field="telegram_chat_type">
                </div>

                <section class="order-summary" aria-live="polite">
                    <div class="order-summary-head">
                        <h3>Items</h3>
                        <span><span data-total-count>{{ $oldItemCount }}</span> selected</span>
                    </div>

                    <div class="summary-list" id="order-summary-list">
                        <div class="summary-empty" id="order-summary-empty"
                            style="text-align: center; padding: 2rem 0;">
                            <p style="margin: 0; color: var(--muted);">No items selected yet.</p>
                            <button type="button" class="back-to-menu-btn" onclick="closeDrawer()">
                                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Add Items
                            </button>
                        </div>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <strong data-subtotal>${{ number_format((float) $oldSubtotal, 2) }}</strong>
                    </div>
                </section>

                <div class="field-grid" style="margin-top: 0.72rem;">
                    <label class="field">
                        Full Name
                        <input type="text" name="name" value="{{ old('name') }}" required maxlength="255">
                        @error('name')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>

                    {{-- <label class="field">
                        Username (Optional)
                        <input type="text" name="username" value="{{ old('username') }}" maxlength="255"
                            placeholder="Auto-generated if empty">
                        @error('username')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label> --}}

                    <label class="field">
                        Phone Number
                        <input type="tel" name="phone" value="{{ old('phone') }}" required maxlength="40"
                            placeholder="e.g. +1 234 567 890">
                        @error('phone')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="field">
                        Branch
                        <select name="branch_id" required>
                            <option value="">Select branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected($selectedBranchId === (string) $branch->id)>
                                    {{ $branch->name }}@if ($branch->address)
                                        - {{ $branch->address }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="field">
                        Delivery Method
                        <select name="delivery_method" required>
                            <option value="self-pickup" @selected(old('delivery_method', 'self-pickup') === 'self-pickup')>Self Pickup</option>
                            <option value="delivery" @selected(old('delivery_method') === 'delivery')>Delivery</option>
                        </select>
                        @error('delivery_method')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="field">
                        Payment Method
                        <select name="payment_method">
                            <option value="cash" @selected(old('payment_method') === 'cash')>Cash</option>
                            <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer</option>
                        </select>
                        @error('payment_method')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>

                    <label class="field">
                        Special Instructions
                        <textarea name="special_instructions" maxlength="1000">{{ old('special_instructions') }}</textarea>
                        @error('special_instructions')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </label>
                </div>

                @error('quantities')
                    <p class="field-error" style="margin-top: 0.5rem;">{{ $message }}</p>
                @enderror

                <button type="submit" class="confirm-btn" data-submit-order
                    data-no-branches="{{ $branches->isEmpty() ? 1 : 0 }}" @disabled($branches->isEmpty() || $oldItemCount === 0)>
                    Confirm Order
                </button>

                @if ($branches->isEmpty())
                    <p class="helper-note">No branch is available yet, so ordering is disabled.</p>
                @else
                    <p class="helper-note">Telegram ID is intentionally skipped for now.</p>
                @endif
            </form>
        </div>
    </aside>

    <!-- Confirmation Modal -->
    <div class="modal-overlay" id="confirmation-modal">
        <article class="modal-card">
            <div id="modal-icon" class="modal-icon success">✓</div>
            <h2 id="modal-title" class="modal-title">Success!</h2>
            <p id="modal-desc" class="modal-desc">Your order has been placed successfully.</p>
            <button type="button" class="modal-btn" onclick="closeModal()">Got it!</button>
        </article>
    </div>

    <script>
        (() => {
            const featuredTrack = document.getElementById('featured-track');
            const featuredCards = Array.from(document.querySelectorAll('.featured-card'));
            const categoryTabs = Array.from(document.querySelectorAll('[data-category-tab]'));
            const categorySections = Array.from(document.querySelectorAll('[data-category-section]'));

            const qtyInputs = Array.from(document.querySelectorAll('[data-qty-input]'));
            const telegramFieldInputs = Array.from(document.querySelectorAll('[data-tg-field]'));
            const summaryList = document.getElementById('order-summary-list');
            const emptyRow = document.getElementById('order-summary-empty');
            const countEls = Array.from(document.querySelectorAll('[data-total-count]'));
            const subtotalEls = Array.from(document.querySelectorAll('[data-subtotal]'));

            const floatingBar = document.getElementById('floating-order-bar');
            const openDrawerButton = document.getElementById('open-order-drawer');
            const closeDrawerButton = document.getElementById('close-order-drawer');
            const drawerOverlay = document.getElementById('drawer-overlay');
            const orderDrawer = document.getElementById('order-drawer');
            const orderForm = document.getElementById('order-form');

            const submitButton = document.querySelector('[data-submit-order]');
            const hasNoBranches = submitButton?.dataset.noBranches === '1';
            const currency = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            });
            const shouldStartOpen = @json($errors->any());
            const telegramPrefillEndpoint = @json(route('telegram.customer-prefill'));
            const nameInput = document.querySelector('input[name="name"]');
            const phoneInput = document.querySelector('input[name="phone"]');
            const usernameInput = document.querySelector('input[name="username"]');

            // Modal Elements
            const modalOverlay = document.getElementById('confirmation-modal');
            const modalIcon = document.getElementById('modal-icon');
            const modalTitle = document.getElementById('modal-title');
            const modalDesc = document.getElementById('modal-desc');

            window.showModal = (title, message, type = 'success') => {
                modalTitle.textContent = title;
                modalDesc.textContent = message;
                modalIcon.textContent = type === 'success' ? '✓' : '!';
                modalIcon.className = `modal-icon ${type}`;
                modalOverlay.classList.add('is-active');
            };

            window.closeModal = () => {
                modalOverlay.classList.remove('is-active');
            };

            // Check for session triggers
            const successTrigger = document.getElementById('session-success-trigger');
            const errorTrigger = document.getElementById('session-error-trigger');

            if (successTrigger) {
                showModal('Order Placed!', successTrigger.dataset.message, 'success');
            } else if (errorTrigger) {
                showModal('Oops!', errorTrigger.dataset.message, 'error');
            }

            const itemMeta = new Map();
            const itemCards = Array.from(document.querySelectorAll('[data-item-card]'));
            const telegramFieldMap = new Map(
                telegramFieldInputs.map((input) => [input.dataset.tgField, input])
            );

            itemCards.forEach((card) => {
                const itemId = card.dataset.itemId;

                if (!itemId || itemMeta.has(itemId)) {
                    return;
                }

                itemMeta.set(itemId, {
                    id: itemId,
                    name: card.dataset.itemName ?? '',
                    image: card.dataset.itemImage ?? '',
                    price: Number.parseFloat(card.dataset.itemPrice ?? '0') || 0,
                });
            });

            let previousCount = null;
            let previousSubtotal = null;
            let customerPrefillRequested = false;

            const clampQty = (value) => {
                const number = Number.parseInt(value, 10);

                if (Number.isNaN(number) || number < 0) {
                    return 0;
                }

                if (number > 20) {
                    return 20;
                }

                return number;
            };

            const getInputForItem = (itemId) => document.querySelector(`[data-qty-input="${itemId}"]`);
            const getLabelsForItem = (itemId) => Array.from(document.querySelectorAll(`[data-qty-label="${itemId}"]`));
            const setTelegramField = (key, value) => {
                const field = telegramFieldMap.get(key);

                if (!field || value === null || value === undefined || value === '') {
                    return;
                }

                field.value = String(value);
            };
            const setInputIfEmpty = (input, value) => {
                if (!input || value === null || value === undefined) {
                    return;
                }

                if (input.value.trim() !== '') {
                    return;
                }

                const nextValue = String(value).trim();

                if (nextValue === '') {
                    return;
                }

                input.value = nextValue;
            };
            const requestCustomerPrefill = async (telegramUserId) => {
                if (customerPrefillRequested || !telegramUserId) {
                    return;
                }

                customerPrefillRequested = true;

                try {
                    const response = await fetch(
                        `${telegramPrefillEndpoint}?telegram_user_id=${encodeURIComponent(String(telegramUserId))}`, {
                            headers: {
                                'Accept': 'application/json',
                            },
                        }
                    );

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    const customer = payload?.customer;

                    if (!customer) {
                        return;
                    }

                    setInputIfEmpty(nameInput, customer.name);
                    setInputIfEmpty(phoneInput, customer.phone);
                    setInputIfEmpty(usernameInput, customer.username);
                } catch (error) {
                    // Skip prefill silently when lookup fails.
                }
            };

            const pulseElement = (element) => {
                element.classList.remove('bump');
                void element.offsetWidth;
                element.classList.add('bump');
            };

            const captureTelegramInitData = () => {
                const webApp = window.Telegram?.WebApp;

                if (!webApp) {
                    return;
                }

                const unsafe = webApp.initDataUnsafe ?? {};
                const user = unsafe.user ?? {};
                const chat = unsafe.chat ?? {};

                setTelegramField('telegram_init_data', webApp.initData ?? '');
                setTelegramField('telegram_query_id', unsafe.query_id);
                setTelegramField('telegram_auth_date', unsafe.auth_date);
                setTelegramField('telegram_start_param', unsafe.start_param);

                setTelegramField('telegram_user_id', user.id);
                setTelegramField('telegram_username', user.username);
                setTelegramField('telegram_first_name', user.first_name);
                setTelegramField('telegram_last_name', user.last_name);
                setTelegramField('telegram_language_code', user.language_code);

                setTelegramField('telegram_chat_id', chat.id);
                setTelegramField('telegram_chat_type', chat.type);

                try {
                    if (Object.keys(unsafe).length > 0) {
                        setTelegramField('telegram_init_data_unsafe', JSON.stringify(unsafe));
                    }
                } catch (error) {
                    // Keep raw init data even if unsafe payload cannot be serialized.
                }

                const fullName = [user.first_name, user.last_name]
                    .filter((part) => typeof part === 'string' && part.trim() !== '')
                    .join(' ')
                    .trim();

                setInputIfEmpty(nameInput, fullName);
                setInputIfEmpty(usernameInput, user.username);

                if (user.id !== null && user.id !== undefined && user.id !== '') {
                    requestCustomerPrefill(user.id);
                }
            };

            const setQty = (itemId, quantity) => {
                const input = getInputForItem(itemId);

                if (!input) {
                    return;
                }

                input.value = String(clampQty(quantity));
                getLabelsForItem(itemId).forEach((label) => pulseElement(label));
            };

            const openDrawer = () => {
                orderDrawer.classList.add('is-open');
                drawerOverlay.classList.add('is-open');
                document.body.classList.add('drawer-open');
                orderDrawer.setAttribute('aria-hidden', 'false');
                openDrawerButton?.setAttribute('aria-expanded', 'true');
            };

            const closeDrawer = () => {
                orderDrawer.classList.remove('is-open');
                drawerOverlay.classList.remove('is-open');
                document.body.classList.remove('drawer-open');
                orderDrawer.setAttribute('aria-hidden', 'true');
                openDrawerButton?.setAttribute('aria-expanded', 'false');
            };

            // Expose globally for the onclick handler
            window.closeDrawer = closeDrawer;

            const sideSummaryList = document.getElementById('side-summary-list');
            const sideSummaryEmpty = sideSummaryList?.querySelector('.summary-empty');
            const sideCheckoutTrigger = document.getElementById('side-checkout-trigger');

            const renderSummary = () => {
                let selectedCount = 0;
                let subtotal = 0;

                summaryList.querySelectorAll('.summary-line').forEach((line) => line.remove());
                if (sideSummaryList) {
                    sideSummaryList.querySelectorAll('.summary-line:not(.summary-empty)').forEach((line) => line
                        .remove());
                }

                qtyInputs.forEach((input) => {
                    const itemId = input.dataset.qtyInput;
                    const quantity = clampQty(input.value);
                    const meta = itemMeta.get(itemId);

                    getLabelsForItem(itemId).forEach((label) => {
                        label.textContent = String(quantity);
                    });

                    if (!meta || quantity < 1) {
                        return;
                    }

                    selectedCount += quantity;
                    const lineTotal = meta.price * quantity;
                    subtotal += lineTotal;

                    // Drawer Summary
                    const row = document.createElement('div');
                    row.className = 'summary-line';
                    row.style.display = 'flex';
                    row.style.alignItems = 'center';
                    row.style.gap = '1rem';
                    row.style.padding = '0.75rem 0';

                    const imgHtml = meta.image ?
                        `<img src="${meta.image}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 12px; border: 1px solid var(--line);">` :
                        `<div style="width: 48px; height: 48px; background: var(--surface-soft); border-radius: 12px; border: 1px solid var(--line); display: flex; align-items: center; justify-content: center; font-size: 0.6rem; color: var(--muted);">No img</div>`;

                    row.innerHTML = `
                        ${imgHtml}
                        <div style="flex-grow: 1;">
                            <span style="display: block; font-weight: 700;">${meta.name}</span>
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
                                <button type="button" class="cart-qty-btn" data-item-id="${meta.id}" data-qty-action="decrease">−</button>
                                <span style="font-weight: 800; font-size: 0.9rem; min-width: 20px; text-align: center;">${quantity}</span>
                                <button type="button" class="cart-qty-btn" data-item-id="${meta.id}" data-qty-action="increase">+</button>
                            </div>
                        </div>
                        <strong style="font-size: 1rem;">${currency.format(lineTotal)}</strong>
                    `;
                    summaryList.appendChild(row);

                    // Side Summary
                    if (sideSummaryList) {
                        const sideRow = document.createElement('div');
                        sideRow.className = 'summary-line';
                        sideRow.style.display = 'flex';
                        sideRow.style.justifyContent = 'space-between';
                        sideRow.style.alignItems = 'center';
                        sideRow.style.padding = '0.5rem 0';
                        sideRow.style.gap = '1rem';

                        sideRow.innerHTML = `
                            ${imgHtml}
                            <div style="flex-grow: 1; display: flex; flex-direction: column;">
                                <span style="font-weight: 700; font-size: 1rem;">${meta.name}</span>
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
                                    <button type="button" class="cart-qty-btn" data-item-id="${meta.id}" data-qty-action="decrease">−</button>
                                    <span style="font-weight: 800; font-size: 0.9rem; min-width: 20px; text-align: center;">${quantity}</span>
                                    <button type="button" class="cart-qty-btn" data-item-id="${meta.id}" data-qty-action="increase">+</button>
                                </div>
                            </div>
                            <strong style="font-size: 1.125rem; font-weight: 800;">${currency.format(lineTotal)}</strong>
                        `;
                        sideSummaryList.appendChild(sideRow);
                    }
                });

                if (emptyRow) {
                    emptyRow.style.display = selectedCount > 0 ? 'none' : 'block';
                }
                if (sideSummaryEmpty) {
                    sideSummaryEmpty.style.display = selectedCount > 0 ? 'none' : 'block';
                }

                countEls.forEach((element) => {
                    element.textContent = String(selectedCount);
                    if (previousCount !== null && previousCount !== selectedCount) {
                        pulseElement(element);
                    }
                });

                subtotalEls.forEach((element) => {
                    element.textContent = currency.format(subtotal);
                    if (previousSubtotal !== null && previousSubtotal !== subtotal) {
                        pulseElement(element);
                    }
                });

                floatingBar.classList.toggle('is-visible', selectedCount > 0);

                if (submitButton) {
                    submitButton.disabled = selectedCount === 0 || hasNoBranches;
                }

                if (sideCheckoutTrigger) {
                    sideCheckoutTrigger.disabled = selectedCount === 0 || hasNoBranches;
                    sideCheckoutTrigger.style.opacity = (selectedCount === 0 || hasNoBranches) ? '0.5' : '1';
                }

                previousCount = selectedCount;
                previousSubtotal = subtotal;
            };

            sideCheckoutTrigger?.addEventListener('click', openDrawer);

            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-qty-action]');

                if (!button) {
                    return;
                }

                const itemId = button.dataset.itemId;
                const input = getInputForItem(itemId);

                if (!input) {
                    return;
                }

                const current = clampQty(input.value);
                const isIncrease = button.dataset.qtyAction === 'increase';
                const next = isIncrease ? current + 1 : current - 1;
                setQty(itemId, next);

                if (isIncrease) {
                    const cards = document.querySelectorAll(`[data-item-id="${itemId}"]`);
                    cards.forEach(card => {
                        card.classList.remove('is-popping');
                        void card.offsetWidth;
                        card.classList.add('is-popping');
                        setTimeout(() => card.classList.remove('is-popping'), 400);
                    });
                }

                if (button.matches('[data-add-btn]')) {
                    pulseElement(button);
                }

                renderSummary();
            });

            openDrawerButton?.addEventListener('click', openDrawer);
            closeDrawerButton?.addEventListener('click', closeDrawer);
            drawerOverlay?.addEventListener('click', closeDrawer);
            orderForm?.addEventListener('submit', () => captureTelegramInitData());

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeDrawer();
                }
            });

            categoryTabs.forEach((tab) => {
                tab.addEventListener('click', (event) => {
                    event.preventDefault();

                    const targetId = tab.dataset.categoryId;
                    const target = targetId ? document.getElementById(targetId) : null;

                    if (!target) {
                        return;
                    }

                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            });

            if (categoryTabs[0]) {
                categoryTabs[0].classList.add('is-active');
            }

            // Theme Toggle Logic
            const themeToggle = document.getElementById('theme-toggle');
            const getTheme = () => document.documentElement.getAttribute('data-theme');
            const setTheme = (theme) => {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
            };

            themeToggle?.addEventListener('click', () => {
                const nextTheme = getTheme() === 'dark' ? 'light' : 'dark';
                setTheme(nextTheme);
            });

            // Auto-hide flash messages
            const flash = document.querySelector('.flash');
            if (flash) {
                setTimeout(() => {
                    flash.style.transition = 'all 0.4s cubic-bezier(0.2, 0, 0, 1)';
                    flash.style.transform = 'translate(-50%, -100%)';
                    flash.style.opacity = '0';
                    setTimeout(() => flash.remove(), 400);
                }, 4000);
            }

            if (window.IntersectionObserver) {
                if (featuredTrack && featuredCards.length > 0) {
                    const featuredObserver = new IntersectionObserver(
                        (entries) => {
                            entries.forEach((entry) => {
                                entry.target.classList.toggle('is-active', entry.intersectionRatio >= 0.65);
                            });
                        }, {
                            root: featuredTrack,
                            threshold: [0.45, 0.65, 0.85],
                        }
                    );

                    featuredCards.forEach((card) => featuredObserver.observe(card));
                }

                if (categorySections.length > 0 && categoryTabs.length > 0) {
                    const tabMap = new Map(
                        categoryTabs.map((tab) => [tab.dataset.categoryId, tab])
                    );

                    const activateTab = (sectionId) => {
                        categoryTabs.forEach((tab) => tab.classList.remove('is-active'));
                        tabMap.get(sectionId)?.classList.add('is-active');
                    };

                    const sectionObserver = new IntersectionObserver(
                        (entries) => {
                            const visible = entries
                                .filter((entry) => entry.isIntersecting)
                                .sort((a, b) => b.intersectionRatio - a.intersectionRatio);

                            if (visible[0]?.target?.id) {
                                activateTab(visible[0].target.id);
                            }
                        }, {
                            root: null,
                            threshold: [0.2, 0.45, 0.7],
                            rootMargin: '-25% 0px -60% 0px',
                        }
                    );

                    categorySections.forEach((section) => sectionObserver.observe(section));

                    if (categorySections[0]?.id) {
                        activateTab(categorySections[0].id);
                    }
                }
            }

            captureTelegramInitData();
            renderSummary();

            if (shouldStartOpen) {
                openDrawer();
            }
        })();
    </script>
</body>

</html>
