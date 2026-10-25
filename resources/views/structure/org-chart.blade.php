@extends('layout.master')

@section('title')
    {{ __('org_chart') }} — {{ config('app.name') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('org_chart') }}
@endsection

@section('css')
    @include('components.admin.structure-styles')
    <style>
        .org-chart-page {
            --org-color-root: #4b5563;
            --org-line: #b7c0cd;
            --org-color-dept: #d92d20;
            --org-color-section: #1b84ff;
            --org-color-branch: #d99a00;
            --org-color-council: #0f766e;
            --org-node-border: #e4e8f0;
            --org-soft-bg: #f6f8fb;
            --org-card-shadow: 0 12px 32px rgba(24, 28, 50, 0.08);
            --org-node-shadow: 0 8px 22px rgba(24, 28, 50, 0.07);
        }

        .org-page-heading {
            gap: 1rem;
        }

        .org-page-icon {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef6ff;
            color: var(--org-color-section);
            font-size: 1.45rem;
            flex-shrink: 0;
        }

        .org-overview-panel,
        .org-control-panel,
        .org-chart-shell {
            border: 1px solid #ebedf3;
            border-radius: 12px;
            background: #fff;
            box-shadow: var(--org-card-shadow);
        }

        .org-overview-panel {
            overflow: hidden;
        }

        .org-overview-main {
            min-height: 112px;
            background:
                linear-gradient(135deg, rgba(27, 132, 255, 0.10), rgba(15, 118, 110, 0.08)),
                #fff;
        }

        .org-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(120px, 1fr));
            border-top: 1px solid #ebedf3;
        }

        .org-stat-item {
            padding: 1rem 1.25rem;
            border-inline-start: 1px solid #ebedf3;
        }

        .org-stat-item:first-child {
            border-inline-start: 0;
        }

        .org-stat-value {
            font-size: 1.35rem;
            line-height: 1;
            font-weight: 800;
            color: #181c32;
        }

        .org-stat-label {
            display: block;
            margin-top: 0.35rem;
            color: #7e8299;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .org-control-panel {
            padding: 1rem;
        }

        .org-chart-search-slot {
            min-height: 75px;
        }

        .org-chart-search {
            position: fixed;
            top: 74px;
            left: var(--org-search-left, 12px);
            width: var(--org-search-width, calc(100% - 24px));
            z-index: 1050;
            display: block !important;
            visibility: visible !important;
            opacity: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #dfe5ee;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 8px 24px rgba(24, 28, 50, 0.12);
            backdrop-filter: blur(8px);
        }

        .org-chart-search .form-control {
            min-height: 42px;
            border-color: #dfe5ee;
        }

        .org-chart-search-status {
            min-width: 4rem;
            color: #7e8299;
            font-size: 0.78rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .org-node.org-search-match {
            border-color: var(--org-color-section);
            box-shadow: 0 0 0 4px rgba(27, 132, 255, 0.18), 0 12px 28px rgba(27, 132, 255, 0.2);
            animation: org-search-pulse 1.4s ease-in-out 2;
        }

        @keyframes org-search-pulse {
            50% {
                transform: translateY(-3px) scale(1.01);
            }
        }

        .org-toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .org-toolbar-actions .btn-group,
        .org-toolbar-actions .dropdown {
            margin-bottom: 0.35rem;
        }

        .org-zoom-meter {
            min-width: 280px;
            max-width: 460px;
            padding: 0.55rem 0.75rem;
            border: 1px solid #ebedf3;
            border-radius: 8px;
            background: #f8fafc;
        }

        .org-legend-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(120px, 1fr));
            gap: 0.6rem;
            margin-top: 0.85rem;
        }

        .org-legend-chip {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 0.75rem;
            border: 1px solid #edf0f5;
            border-radius: 8px;
            background: #fbfcfe;
            color: #3f4254;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .org-chart-shell-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #edf0f5;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .org-chart-root-node {
            max-width: 420px;
            margin: 0 auto 1rem;
            border: 1px solid var(--org-node-border);
            border-top: 4px solid var(--org-color-root);
            border-radius: 10px;
            background: #fff;
            box-shadow: var(--org-node-shadow);
        }

        .org-chart-root-node.is-council {
            border-top-color: var(--org-color-council);
        }

        .org-chart-root-node.is-mayor {
            border-top-color: var(--org-color-branch);
        }

        .org-root-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.65rem;
            background: #f3f6f9;
            color: var(--org-color-root);
        }

        .is-council .org-root-icon {
            background: #e8f6f5;
            color: var(--org-color-council);
        }

        .is-mayor .org-root-icon {
            background: #fff7df;
            color: var(--org-color-branch);
        }

        .org-chart-trunk {
            width: 3px;
            height: 26px;
            margin: 0 auto;
            background: var(--org-line);
            border-radius: 2px;
        }

        .org-chart-depts-scroll {
            overflow-x: auto;
            overflow-y: visible;
            padding: 0.5rem 0 2.25rem;
            -webkit-overflow-scrolling: touch;
        }

        .org-chart-depts-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .org-chart-depts-scroll::-webkit-scrollbar-track {
            background: #edf0f5;
            border-radius: 4px;
        }

        .org-chart-depts-scroll::-webkit-scrollbar-thumb {
            background: #c8d0dc;
            border-radius: 4px;
        }

        .org-chart-depts {
            display: flex;
            flex-wrap: nowrap;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 1.5rem;
            width: max-content;
            margin-inline: auto;
            position: relative;
            padding-top: 26px;
        }

        .org-chart-depts::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--org-line) 6%, var(--org-line) 94%, transparent);
            border-radius: 2px;
        }

        .org-chart-dept-col {
            flex: 0 0 auto;
            width: 322px;
            max-width: 322px;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            padding-top: 26px;
            position: relative;
        }

        .org-chart-dept-col::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 3px;
            height: 26px;
            background: var(--org-line);
            border-radius: 2px;
        }

        .org-node {
            border: 1px solid var(--org-node-border);
            border-radius: 10px;
            background: #fff;
            box-shadow: var(--org-node-shadow);
            overflow: hidden;
            transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
        }

        .org-node:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(24, 28, 50, 0.12);
            border-color: #d7deea;
        }

        .org-node-dept {
            border-top: 4px solid var(--org-color-dept);
        }

        .org-node-section {
            border-top: 4px solid var(--org-color-section);
        }

        .org-node-branch {
            border-top: 3px solid var(--org-color-branch);
        }

        .org-node-title {
            font-weight: 800;
            font-size: 0.98rem;
            line-height: 1.45;
            color: #181c32;
        }

        .org-node-sub {
            font-size: 0.8rem;
            color: #7e8299;
            line-height: 1.75;
        }

        .org-node-body {
            font-size: 0.85rem;
        }

        .org-node-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .org-node-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            margin-top: 0.8rem;
        }

        .org-mini-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.55rem;
            border-radius: 6px;
            background: #f3f6f9;
            color: #5e6278;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .org-employee-list {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 190px;
            overflow-y: auto;
            border: 1px solid #edf0f5;
            border-radius: 8px;
            background: #fbfcfe;
        }

        .org-employee-list li {
            padding: 0.45rem 0.65rem;
            border-bottom: 1px dashed #eff2f5;
            font-size: 0.8rem;
            color: #3f4254;
        }

        .org-employee-list li:last-child {
            border-bottom: 0;
        }

        .org-section-stack {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .org-branch-stack {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px dashed #dfe5ee;
        }

        .org-branch-label {
            font-size: 0.74rem;
            font-weight: 800;
            color: #7e8299;
            margin-bottom: 0.45rem;
            text-transform: uppercase;
        }

        .org-hint-alert {
            font-size: 0.85rem;
        }

        .org-chart-zoom-toolbar .form-control-range {
            max-width: 220px;
        }

        .org-chart-zoom-outer {
            overflow: auto;
            max-height: calc(100vh - 230px);
            border-top: 1px solid #edf0f5;
            background:
                linear-gradient(#eef2f7 1px, transparent 1px),
                linear-gradient(90deg, #eef2f7 1px, transparent 1px),
                var(--org-soft-bg);
            background-size: 28px 28px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .org-chart-zoom-inner {
            transform-origin: top center;
            transition: transform 0.12s ease-out, zoom 0.12s ease-out;
            padding: 2.25rem;
            display: inline-block;
            min-width: 100%;
        }

        .org-side-units {
            display: flex;
            justify-content: center;
            align-items: stretch;
            gap: 1rem;
            margin: 0 auto 1rem;
            position: relative;
            max-width: 760px;
        }

        .org-side-units::before {
            content: '';
            position: absolute;
            top: -14px;
            left: 13%;
            right: 13%;
            height: 3px;
            background: var(--org-line);
            border-radius: 2px;
        }

        .org-side-unit {
            flex: 1 1 0;
            min-width: 190px;
            position: relative;
        }

        .org-side-unit::before {
            content: '';
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            width: 3px;
            height: 14px;
            background: var(--org-line);
        }

        .org-empty-state {
            padding: 4rem 1rem;
            text-align: center;
            background: var(--org-soft-bg);
            border-top: 1px solid #edf0f5;
        }

        .org-empty-state i {
            font-size: 3rem;
            color: #b5b5c3;
            margin-bottom: 1rem;
        }

        .org-chart-footer {
            padding: 0.9rem 1.5rem;
            border-top: 1px solid #edf0f5;
            background: #fff;
        }

        .org-toggle-btn {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            flex-shrink: 0;
        }

        @media (max-width: 991.98px) {

            .org-stat-grid,
            .org-legend-strip {
                grid-template-columns: repeat(2, minmax(120px, 1fr));
            }

            .org-stat-item:nth-child(3) {
                border-inline-start: 0;
            }

            .org-zoom-meter {
                min-width: 100%;
            }

            .org-side-units {
                flex-wrap: wrap;
                max-width: 100%;
            }

            .org-side-units::before,
            .org-side-unit::before {
                display: none;
            }

            .org-chart-depts {
                flex-wrap: wrap;
                justify-content: center;
                width: 100%;
                gap: 3rem 1.25rem;
            }

            .org-chart-depts::before {
                display: none;
            }

            .org-chart-dept-col {
                width: 100%;
                max-width: 340px;
            }
        }

        @media (max-width: 575.98px) {
            .org-chart-search {
                top: 58px;
            }

            .org-page-heading {
                align-items: flex-start !important;
            }

            .org-stat-grid,
            .org-legend-strip {
                grid-template-columns: 1fr;
            }

            .org-stat-item,
            .org-stat-item:nth-child(3) {
                border-inline-start: 0;
                border-top: 1px solid #ebedf3;
            }

            .org-stat-item:first-child {
                border-top: 0;
            }

            .org-control-panel,
            .org-chart-shell-header {
                padding: 1rem;
            }

            .org-chart-zoom-inner {
                padding: 1.25rem;
            }

            .org-side-unit {
                min-width: 100%;
            }
        }

        .org-legend-swatch {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            flex-shrink: 0;
            border: 1px solid rgba(0, 0, 0, 0.12);
        }

        .org-legend-swatch.bg-root {
            background: var(--org-color-root);
        }

        .org-legend-swatch.bg-dept {
            background: var(--org-color-dept);
        }

        .org-legend-swatch.bg-section {
            background: var(--org-color-section);
        }

        .org-legend-swatch.bg-branch {
            background: var(--org-color-branch);
        }

        .org-legend-swatch.bg-council {
            background: var(--org-color-council);
        }

        .org-legend-row {
            gap: 0.5rem;
        }

        .toggle-dept,
        .toggle-section,
        .toggle-branch {
            transition: all 0.2s ease;
            border-radius: 6px;
        }

        .toggle-dept:hover,
        .toggle-section:hover,
        .toggle-branch:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .org-section-stack,
        .org-branch-stack {
            transition: all 0.3s ease;
        }

        @media print {

            /* صفحة أفقية (Landscape) ليستوعب المخطط الأفقي الكامل بصف واحد */
            @page {
                size: A4 landscape;
                margin: 3mm;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html,
            body {
                width: 100% !important;
                min-width: 0 !important;
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
            }

            #kt_header_mobile,
            #kt_aside,
            #kt_body>.flex-root>.page>*:not(#kt_wrapper),
            #kt_wrapper>*:not(#kt_content),
            #kt_content>*:not(.d-flex.flex-column-fluid),
            #kt_body>.flex-root>.page>.aside,
            #kt_wrapper>.header,
            #kt_subheader,
            #kt_wrapper>.footer,
            #kt_quick_user,
            #kt_quick_panel,
            .org-page-heading,
            .org-page-heading+.nav.nav-pills-custom,
            .nav.nav-pills-custom,
            .org-chart-search-slot,
            .org-overview-panel,
            .org-control-panel,
            .org-chart-shell-header,
            .org-chart-footer,
            .org-chart-page>.org-chart-tabs {
                display: none !important;
            }

            #kt_body>.flex-root,
            #kt_body>.flex-root>.page,
            #kt_wrapper,
            #kt_content,
            #kt_content>.d-flex.flex-column-fluid,
            #kt_content .content-max-width-wrapper,
            .org-chart-page,
            .org-chart-page .org-chart-shell {
                display: block !important;
                width: 100% !important;
                max-width: none !important;
                min-height: 0 !important;
                height: auto !important;
            }

            #kt_body,
            #kt_body>.flex-root,
            #kt_body>.flex-root>.page,
            #kt_wrapper {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                left: 0 !important;
                right: 0 !important;
                transform: none !important;
            }

            #kt_wrapper {
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            #kt_body #kt_wrapper,
            #kt_body>.flex-root>.page {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                flex: none !important;
            }

            #kt_body>.flex-root>.page,
            #kt_wrapper,
            #kt_content,
            #kt_content>.d-flex.flex-column-fluid,
            #kt_content .content-max-width-wrapper {
                width: 100% !important;
                max-width: none !important;
                flex: none !important;
            }

            #kt_content .content-max-width-wrapper {
                position: static !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                height: auto !important;
                max-height: none !important;
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
            }

            .org-chart-page {
                position: static !important;
                width: 100% !important;
                max-width: none !important;
                height: auto !important;
                max-height: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
            }

            .org-chart-shell {
                border: 0 !important;
                box-shadow: none !important;
                width: 100% !important;
                margin: 0 !important;
                page-break-before: avoid !important;
                page-break-after: auto !important;
            }

            .org-chart-zoom-outer {
                border: none !important;
                box-shadow: none !important;
                max-height: none !important;
                overflow: visible !important;
                width: 100% !important;
                position: static !important;
                background: #fff !important;
                height: auto !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                display: block !important;
            }

            #orgChartZoomInner {
                transform: scale(1.0) !important;
                transform-origin: top center !important;
                padding: 0 !important;
                width: max-content !important;
                min-width: 0 !important;
                margin: 0 auto !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                display: block !important;
            }

            .org-chart-depts-scroll {
                overflow: visible !important;
                width: 100% !important;
            }

            #orgChartZoomInner .org-section-stack,
            #orgChartZoomInner .org-branch-stack,
            #orgChartZoomInner .org-employee-list {
                display: block !important;
                visibility: visible !important;
            }

            #orgChartZoomInner .org-section-stack {
                display: flex !important;
            }

            /* عرض المخطط بشكل أفقي في صفحة واحدة مع جميع الأعمدة */
            .org-chart-depts {
                display: flex !important;
                flex-wrap: nowrap !important;
                justify-content: flex-start !important;
                gap: 0.25rem !important;
                visibility: visible !important;
                height: auto !important;
                min-height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                width: max-content !important;
                padding-top: 26px !important;
                margin-inline: auto !important;
            }

            .org-chart-depts::before {
                display: block !important;
            }

            .org-chart-dept-col {
                page-break-inside: avoid;
                display: flex !important;
                visibility: visible !important;
                flex: 0 0 auto !important;
                width: 260px !important;
                max-width: 260px !important;
                padding-top: 26px !important;
                flex-direction: column !important;
            }

            .org-chart-dept-col .org-node,
            .org-chart-dept-col .org-node-body {
                font-size: 0.85rem !important;
            }

            .org-chart-dept-col .p-3,
            .org-chart-dept-col .p-4 {
                padding: 0.55rem 0.4rem !important;
            }

            .org-node-title {
                font-size: 0.95rem !important;
                font-weight: 800 !important;
            }

            .org-node-sub,
            .org-mini-badge,
            .org-branch-label,
            .org-employee-list li {
                font-size: 0.8rem !important;
            }

            .org-node-meta {
                gap: 0.2rem !important;
                margin-top: 0.25rem !important;
            }

            .org-employee-list {
                max-height: none !important;
            }

            /* تقليص الفراغات الرأسية غير الضرورية عند الطباعة لتقليل الارتفاع الكلي،
                       مما يسمح للنظام برفع نسبة التكبير التلقائية مع البقاء ضمن صفحة واحدة */
            .org-node-title {
                line-height: 1.15 !important;
            }

            .org-node-sub {
                line-height: 1.25 !important;
                margin-top: 0.15rem !important;
            }

            .org-mini-badge {
                padding: 0.1rem 0.35rem !important;
            }

            .org-employee-list li {
                padding: 0.15rem 0.35rem !important;
            }

            .org-section-stack {
                gap: 0.3rem !important;
            }

            .org-branch-stack {
                margin-top: 0.25rem !important;
                padding-top: 0.25rem !important;
            }

            .org-chart-dept-col .mt-3 {
                margin-top: 0.35rem !important;
            }

            .org-chart-dept-col .mb-3 {
                margin-bottom: 0.35rem !important;
            }

            .org-chart-dept-col .mb-2 {
                margin-bottom: 0.2rem !important;
            }

            .org-chart-dept-col .mt-2 {
                margin-top: 0.2rem !important;
            }

            .org-chart-trunk {
                height: 12px !important;
            }

            .org-chart-dept-col::before {
                height: 12px !important;
            }

            .org-chart-depts,
            .org-chart-dept-col {
                padding-top: 14px !important;
            }

            .org-chart-root-node {
                max-width: 300px !important;
                font-size: 0.65rem !important;
            }

            /* توسيط النصوص داخل بطاقات الدوائر والأقسام والشُعب والوحدات الجانبية عند الطباعة */
            .org-chart-dept-col .text-right,
            .org-chart-dept-col .org-node-title,
            .org-chart-dept-col .org-node-sub,
            .org-chart-dept-col .org-branch-label,
            .org-side-unit .org-node-title {
                text-align: center !important;
            }

            .org-side-unit .p-3 {
                padding-top: 0.75rem !important;
                padding-bottom: 0.75rem !important;
                min-height: 60px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .org-chart-dept-col .org-node-head {
                justify-content: center !important;
                flex-direction: column !important;
                align-items: center !important;
            }

            .org-chart-dept-col .org-node-meta {
                justify-content: center !important;
            }

            .org-chart-dept-col .org-employee-list li {
                text-align: center !important;
            }

            .org-chart-root-node .org-node-title {
                font-size: 0.6rem !important;
            }

            .org-chart-root-node .org-node-sub {
                font-size: 0.52rem !important;
            }

            .org-chart-footer {
                display: none !important;
            }

            .org-toggle-btn {
                display: none !important;
            }
        }

        /* ===== النافذة المنبثقة لهيكلية العقدة (الضغط على أي عقدة بالمخطط) ===== */
        .org-node[data-node-type] {
            cursor: pointer;
        }

        .org-node[data-node-type]:hover {
            border-color: var(--org-color-section);
            box-shadow: 0 10px 26px rgba(27, 132, 255, 0.16);
        }

        .org-hier-manager {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            background: var(--org-soft-bg);
            color: #3f4254;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .org-hier-stat-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.85rem;
            border-radius: 20px;
            background: #eef6ff;
            color: var(--org-color-section);
            font-size: 0.78rem;
            font-weight: 800;
        }

        .org-hier-stat-chip.is-muted {
            background: #f3f6f9;
            color: #3f4254;
        }

        .org-hier-parent-link {
            cursor: pointer;
            border: 1px dashed #c8d0dc;
            background: #fbfcfe;
        }

        .org-hier-parent-link:hover {
            border-style: solid;
            border-color: var(--org-color-section);
        }

        .org-hier-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.7rem 0.35rem;
            border-bottom: 1px solid #eef0f5;
        }

        .org-hier-item:last-child {
            border-bottom: 0;
        }

        .org-hier-item-icon {
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #f3f6f9;
            color: var(--org-color-section);
            font-size: 0.9rem;
        }

        .org-hier-item-icon.is-branch {
            color: var(--org-color-branch);
        }

        .org-hier-item-icon.is-employee {
            color: #3f4254;
        }

        .org-hier-item-meta {
            color: #7e8299;
            font-size: 0.74rem;
            font-weight: 700;
        }

        #orgHierarchyModal .modal-content,
        #orgEditModal .modal-content {
            border-radius: 12px;
        }

        @media print {

            #orgHierarchyModal,
            #orgEditModal,
            #orgCreateModal {
                display: none !important;
            }
        }

        /* ===== شريط أزرار الإضافة داخل نافذة الهيكلية ===== */
        .org-hier-actionbar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .org-hier-nested {
            margin-inline-start: 1rem;
            padding-inline-start: 0.75rem;
            border-inline-start: 2px solid #eef0f5;
            width: calc(100% - 1rem);
        }

        .org-hier-sublabel {
            font-size: 0.72rem;
            font-weight: 800;
            color: #7e8299;
            letter-spacing: 0.2px;
            margin: 0.35rem 0 0.15rem;
        }

        .org-hier-employee-row {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.3rem 0.25rem;
            font-size: 0.8rem;
            color: #3f4254;
            font-weight: 600;
        }

        .org-hier-employee-row i {
            color: #b5b5c3;
            font-size: 0.7rem;
        }

        .org-hier-item-link {
            cursor: pointer;
            border-bottom: 1px dashed transparent;
        }

        .org-hier-item-link:hover {
            color: var(--org-color-section);
            border-bottom-color: var(--org-color-section);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid org-chart-page">
        @php
            $departmentsCount = $departments->count();
            $sectionsCount = 0;
            $branchesCount = 0;
            $employeesCount = 0;

            foreach ($departments as $department) {
                $sectionsCount += $department->sections->count();
                $employeesCount += $departmentEmployeesByDepartment->get($department->id, collect())->count();

                foreach ($department->sections as $section) {
                    $branchesCount += $section->branches->count();
                    $employeesCount += $sectionEmployeesBySection->get($section->id, collect())->count();

                    foreach ($section->branches as $branch) {
                        $employeesCount += $branchEmployeesByBranch->get($branch->id, collect())->count();
                    }
                }
            }
        @endphp

        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-5 org-page-heading">
            <div class="d-flex align-items-center">
                <span class="org-page-icon ml-3">
                    <i class="fas fa-sitemap"></i>
                </span>
                <div>
                    <h1 class="h3 mb-1 text-dark font-weight-bolder">{{ __('org_chart') }}</h1>
                    <span
                        class="text-muted font-weight-bold font-size-sm">{{ __('Municipality of Jabalia Al-Nazlah') }}</span>
                </div>
            </div>
        </div>

        <x-admin.structure-tabs active="chart" />

        <div class="org-chart-search-slot mb-4">
            <div class="org-chart-search" role="search" aria-label="{{ __('Search') }}">
                <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center" style="gap: 0.75rem;">
                    <label for="orgChartSearch" class="mb-0 text-dark font-weight-bolder text-nowrap">
                        <i class="fas fa-search text-primary ml-2"></i>{{ __('Search') }}
                    </label>
                    <div class="input-group flex-grow-1">
                        <input type="search" class="form-control" id="orgChartSearch"
                            placeholder="{{ __('Search') }} {{ __('departments') }} / {{ __('sections') }} / {{ __('branches') }}"
                            autocomplete="off" aria-controls="orgChartSearchResults">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-light" id="orgChartSearchClear"
                                title="{{ __('clear') }}" aria-label="{{ __('clear') }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <span class="org-chart-search-status" id="orgChartSearchResults" aria-live="polite"></span>
                </div>
            </div>
        </div>

        <div class="org-overview-panel mb-4">
            <div class="org-overview-main p-5 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="text-primary font-weight-bold font-size-sm mb-2">{{ __('Organizational Chart') }}</div>
                    <h2 class="h4 text-dark font-weight-bolder mb-2">{{ __('Dynamic System Structure') }}</h2>
                    <div class="text-muted font-weight-bold font-size-sm org-hint-alert">
                        <i class="fas fa-info-circle ml-2 text-primary"></i>
                        {{ __('org_chart_hint') }}
                    </div>
                </div>
                <div class="text-lg-left text-right">
                    <span class="badge badge-light-primary font-weight-bold px-4 py-3">
                        {{ __('Municipality of Jabalia Al-Nazlah') }}
                    </span>
                </div>
            </div>
            <div class="org-stat-grid">
                <div class="org-stat-item">
                    <span class="org-stat-value">{{ $departmentsCount }}</span>
                    <span class="org-stat-label">{{ __('departments') }}</span>
                </div>
                <div class="org-stat-item">
                    <span class="org-stat-value">{{ $sectionsCount }}</span>
                    <span class="org-stat-label">{{ __('sections') }}</span>
                </div>
                <div class="org-stat-item">
                    <span class="org-stat-value">{{ $branchesCount }}</span>
                    <span class="org-stat-label">{{ __('branches') }}</span>
                </div>
                <div class="org-stat-item">
                    <span class="org-stat-value">{{ $employeesCount }}</span>
                    <span class="org-stat-label">{{ __('employees') }}</span>
                </div>
            </div>
        </div>

        <div class="org-control-panel mb-4">
            <div
                class="d-flex flex-column flex-xl-row align-items-stretch align-items-xl-center justify-content-xl-between">
                <div class="org-toolbar-actions mb-3 mb-xl-0">
                    <div class="btn-group" role="group" aria-label="zoom">
                        <button type="button" class="btn btn-sm btn-light-danger font-weight-bold" id="orgChartZoomOut"
                            title="{{ __('org_chart_zoom_out') }}">
                            <i class="fas fa-minus ml-1"></i>{{ __('org_chart_zoom_out') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-light-primary font-weight-bold" id="orgChartZoomIn"
                            title="{{ __('org_chart_zoom_in') }}">
                            <i class="fas fa-plus ml-1"></i>{{ __('org_chart_zoom_in') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-light-dark font-weight-bold" id="orgChartZoomReset"
                            title="{{ __('org_chart_zoom_reset') }}">
                            <i class="fas fa-rotate-left ml-1"></i>{{ __('org_chart_zoom_reset') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-light-info font-weight-bold" id="orgChartZoomFit"
                            title="{{ __('org_chart_zoom_fit') }}">
                            <i class="fas fa-expand-arrows-alt ml-1"></i>{{ __('org_chart_zoom_fit') }}
                        </button>
                    </div>

                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-light-success font-weight-bold dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{ __('actions') }}">
                            <i class="fas fa-cog ml-1"></i>{{ __('actions') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="javascript:void(0)" id="orgChartPrint">
                                <i class="fas fa-print text-success ml-2"></i>
                                {{ __('print') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)" id="orgChartExportWord">
                                <i class="fas fa-file-word text-primary ml-2"></i>
                                {{ __('Export Word') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)" id="orgChartExportPdfA3">
                                <i class="fas fa-file-pdf text-danger ml-2"></i>
                                {{ __('Export PDF A3') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)" id="orgChartSettleEmployees">
                                <i class="fas fa-sync-alt text-info ml-2"></i>
                                {{ __('تحديث تسكين الموظفين') }}
                            </a>
                        </div>
                    </div>

                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-light-info font-weight-bold dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            title="{{ __('display control') }}">
                            <i class="fas fa-eye ml-1"></i>{{ __('display') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="javascript:void(0)" id="showAll">
                                <i class="fas fa-eye text-info ml-2"></i>
                                {{ __('show all') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)" id="hideAll">
                                <i class="fas fa-eye-slash text-warning ml-2"></i>
                                {{ __('hide all') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)" id="toggleDepartments">
                                <i class="fas fa-building text-primary ml-2"></i>
                                {{ __('toggle departments') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)" id="toggleSections">
                                <i class="fas fa-layer-group text-success ml-2"></i>
                                {{ __('toggle sections') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)" id="toggleBranches">
                                <i class="fas fa-code-branch text-secondary ml-2"></i>
                                {{ __('toggle branches') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="org-zoom-meter d-flex align-items-center flex-grow-1 flex-xl-grow-0">
                    <label for="orgChartZoomSlider"
                        class="mb-0 ml-2 font-size-sm text-muted text-nowrap">{{ __('org_chart_zoom_level') }}</label>
                    <input type="range" class="custom-range mx-3 flex-grow-1" id="orgChartZoomSlider" min="20"
                        max="200" step="5" value="100" aria-valuemin="20" aria-valuemax="200">
                    <span class="font-weight-bold font-size-sm text-nowrap text-primary" id="orgChartZoomPct"
                        style="min-width: 3.25rem;">100%</span>
                </div>
            </div>

            <div class="org-legend-strip">
                <div class="org-legend-chip">
                    <span class="org-legend-swatch bg-root"></span>
                    <span>{{ __('org_chart_legend_root') }}</span>
                </div>
                <div class="org-legend-chip">
                    <span class="org-legend-swatch bg-council"></span>
                    <span>{{ __('Municipal Council') }}</span>
                </div>
                <div class="org-legend-chip">
                    <span class="org-legend-swatch bg-dept"></span>
                    <span>{{ __('org_chart_legend_department') }}</span>
                </div>
                <div class="org-legend-chip">
                    <span class="org-legend-swatch bg-section"></span>
                    <span>{{ __('org_chart_legend_section') }}</span>
                </div>
                <div class="org-legend-chip">
                    <span class="org-legend-swatch bg-branch"></span>
                    <span>{{ __('org_chart_legend_branch') }}</span>
                </div>
            </div>
        </div>

        <div class="org-chart-shell">
            <div class="org-chart-shell-header">
                <div class="text-right">
                    <h5 class="mb-1 text-dark font-weight-bolder">{{ __('Organizational Chart') }}</h5>
                    <span class="text-muted font-weight-bold font-size-sm">{{ __('Note') }}:
                        {{ __('Above is the official organizational chart. Below is the dynamic system structure.') }}</span>
                </div>
                <span class="badge badge-light-info font-weight-bold px-4 py-3">{{ __('Structural') }}</span>
            </div>

            @if ($departments->isEmpty())
                <div class="org-empty-state">
                    <i class="fas fa-sitemap"></i>
                    <p class="text-muted font-weight-bold mb-0">{{ __('no_records') }}</p>
                </div>
            @else
                <div class="org-chart-zoom-outer">
                    <div class="org-chart-zoom-inner" id="orgChartZoomInner">
                        <div class="org-chart-root-node p-4 text-center">
                            <span class="org-root-icon">
                                <i class="fas fa-landmark"></i>
                            </span>
                            <div class="org-node-title font-size-h5">{{ __('Municipality of Jabalia Al-Nazlah') }}</div>
                            <div class="org-node-sub mt-1">{{ __('Structural') }}</div>
                        </div>

                        <div class="org-chart-trunk"></div>

                        <div class="org-chart-root-node is-council p-3 text-center" style="max-width: 320px;">
                            <span class="org-root-icon">
                                <i class="fas fa-users"></i>
                            </span>
                            <div class="org-node-title font-size-h6 text-primary">{{ __('Municipal Council') }}</div>
                        </div>

                        <div class="org-chart-trunk"></div>

                        <div class="org-side-units">
                            <div class="org-node org-node-section org-side-unit">
                                <div class="p-3 text-center">
                                    <div class="org-node-title font-size-h6">{{ __('External Audit') }}</div>
                                </div>
                            </div>
                            <div class="org-node org-node-section org-side-unit">
                                <div class="p-3 text-center">
                                    <div class="org-node-title font-size-h6">
                                        {{ __('Municipal Council Committees Section') }}</div>
                                </div>
                            </div>
                            <div class="org-node org-node-section org-side-unit">
                                <div class="p-3 text-center">
                                    <div class="org-node-title font-size-h6">{{ __('Internal Control Unit') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="org-chart-trunk"></div>

                        <div class="org-chart-root-node is-mayor p-3 text-center" style="max-width: 300px;">
                            <span class="org-root-icon">
                                <i class="fas fa-user-tie"></i>
                            </span>
                            <div class="org-node-title font-size-h6" style="color: var(--org-color-branch);">
                                {{ __('Mayor') }}</div>
                        </div>

                        <div class="org-chart-trunk"></div>

                        <div class="org-chart-depts-scroll">
                            <div class="org-chart-depts">
                                @foreach ($departments as $department)
                                    @php
                                        $deptEmps = $departmentEmployeesByDepartment->get($department->id, collect());
                                    @endphp
                                    <div class="org-chart-dept-col">
                                        <div class="org-node org-node-dept mb-3" data-node-type="department"
                                            data-node-id="{{ $department->id }}"
                                            data-org-search-name="{{ $department->name }}">
                                            <div class="p-4">
                                                <div class="org-node-head">
                                                    <div class="text-right flex-grow-1">
                                                        <div class="org-node-title">{{ $department->name }}</div>
                                                        <div class="org-node-meta">
                                                            <span class="org-mini-badge">
                                                                <i class="fas fa-layer-group text-danger"></i>
                                                                {{ $department->sections->count() }} {{ __('sections') }}
                                                            </span>
                                                            @if ($deptEmps->isNotEmpty())
                                                                <span class="org-mini-badge">
                                                                    <i class="fas fa-users text-muted"></i>
                                                                    {{ $deptEmps->count() }} {{ __('employees') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <button
                                                        class="btn btn-sm btn-outline-primary toggle-dept org-toggle-btn"
                                                        data-target="dept-{{ $department->id }}"
                                                        title="{{ __('show/hide') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <div class="org-node-sub mt-3">
                                                    <i class="fas fa-user-tie ml-1 text-danger"></i>
                                                    {{ __('department director') }}:
                                                    @forelse ($department->directors->sortBy(fn($d) => $d->director_type === 'permanent' ? 0 : 1)->values() as $director)
                                                        <span
                                                            class="badge badge-light-{{ $director->director_type === 'acting' ? 'warning' : 'success' }} mr-1">
                                                            {{ __($director->director_type) }}:
                                                            {{ $director->employee?->name ?? __('vacant') }}
                                                        </span>
                                                    @empty
                                                        <span
                                                            class="text-dark font-weight-bold">{{ __('vacant') }}</span>
                                                    @endforelse
                                                </div>
                                                @if ($deptEmps->isNotEmpty())
                                                    <div class="mt-3 text-right">
                                                        <div class="org-branch-label text-danger font-weight-bold">
                                                            {{ __('employees') }} ({{ __('department') }})</div>
                                                        <ul class="org-employee-list">
                                                            @foreach ($deptEmps as $emp)
                                                                <li>
                                                                    <i class="fas fa-user text-muted ml-1"></i>
                                                                    {{ $emp->name }}
                                                                    @if ($emp->job)
                                                                        <span class="text-muted"> -
                                                                            {{ $emp->job->name }}</span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="org-section-stack flex-grow-1 w-100" id="dept-{{ $department->id }}">
                                            @forelse ($department->sections as $section)
                                                @php
                                                    $secEmps = $sectionEmployeesBySection->get($section->id, collect());
                                                @endphp
                                                <div class="org-node org-node-section" data-node-type="section"
                                                    data-node-id="{{ $section->id }}"
                                                    data-org-search-name="{{ $section->name }}">
                                                    <div class="p-3 org-node-body">
                                                        <div class="org-node-head">
                                                            <div class="text-right flex-grow-1">
                                                                <div class="org-node-title font-size-h6">
                                                                    {{ $section->name }}</div>
                                                                <div class="org-node-meta">
                                                                    <span class="org-mini-badge">
                                                                        <i class="fas fa-code-branch text-primary"></i>
                                                                        {{ $section->branches->count() }}
                                                                        {{ __('branches') }}
                                                                    </span>
                                                                    <span class="org-mini-badge">
                                                                        <i class="fas fa-users text-muted"></i>
                                                                        {{ $secEmps->count() }} {{ __('employees') }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <button
                                                                class="btn btn-sm btn-outline-info toggle-section org-toggle-btn"
                                                                data-target="section-{{ $section->id }}"
                                                                title="{{ __('show/hide') }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </div>
                                                        <div class="org-node-sub mt-3">
                                                            {{ __('section director') }}:
                                                            @forelse ($section->directors->sortBy(fn($d) => $d->director_type === 'permanent' ? 0 : 1)->values() as $director)
                                                                <span
                                                                    class="badge badge-light-{{ $director->director_type === 'acting' ? 'warning' : 'success' }} mr-1">
                                                                    {{ __($director->director_type) }}:
                                                                    {{ $director->employee?->name ?? __('vacant') }}
                                                                </span>
                                                            @empty
                                                                <span
                                                                    class="text-dark font-weight-bold">{{ __('vacant') }}</span>
                                                            @endforelse
                                                        </div>

                                                        <div class="mt-3">
                                                            <div class="org-branch-label text-primary font-weight-bold">
                                                                {{ __('employees') }} ({{ __('section') }})</div>
                                                            @if ($secEmps->isEmpty())
                                                                <span
                                                                    class="text-muted font-size-sm">{{ __('no_records') }}</span>
                                                            @else
                                                                <ul class="org-employee-list">
                                                                    @foreach ($secEmps as $emp)
                                                                        <li>
                                                                            <i class="fas fa-user text-muted ml-1"></i>
                                                                            {{ $emp->name }}
                                                                            @if ($emp->job)
                                                                                <span class="text-muted"> -
                                                                                    {{ $emp->job->name }}</span>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </div>

                                                        @if ($section->branches->isNotEmpty())
                                                            <div class="org-branch-stack"
                                                                id="section-{{ $section->id }}">
                                                                <div class="org-branch-label">{{ __('branches') }}</div>
                                                                @foreach ($section->branches as $branch)
                                                                    @php
                                                                        $brEmps = $branchEmployeesByBranch->get(
                                                                            $branch->id,
                                                                            collect(),
                                                                        );
                                                                    @endphp
                                                                    <div class="org-node org-node-branch mb-2"
                                                                        data-node-type="branch"
                                                                        data-node-id="{{ $branch->id }}"
                                                                        data-org-search-name="{{ $branch->name }}">
                                                                        <div class="p-3">
                                                                            <div class="org-node-head">
                                                                                <div class="text-right flex-grow-1">
                                                                                    <div
                                                                                        class="org-node-title font-size-sm">
                                                                                        {{ $branch->name }}</div>
                                                                                    <div class="org-node-meta">
                                                                                        <span class="org-mini-badge">
                                                                                            <i
                                                                                                class="fas fa-users text-muted"></i>
                                                                                            {{ $brEmps->count() }}
                                                                                            {{ __('employees') }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                                <button
                                                                                    class="btn btn-sm btn-outline-warning toggle-branch org-toggle-btn"
                                                                                    data-target="branch-{{ $branch->id }}"
                                                                                    title="{{ __('show/hide') }}">
                                                                                    <i class="fas fa-eye"></i>
                                                                                </button>
                                                                            </div>
                                                                            <div class="org-node-sub mt-2">
                                                                                {{ __('branch director') }}:
                                                                                @forelse ($branch->directors->sortBy(fn($d) => $d->director_type === 'permanent' ? 0 : 1)->values() as $director)
                                                                                    <span
                                                                                        class="badge badge-light-{{ $director->director_type === 'acting' ? 'warning' : 'success' }} mr-1">
                                                                                        {{ __($director->director_type) }}:
                                                                                        {{ $director->employee?->name ?? __('vacant') }}
                                                                                    </span>
                                                                                @empty
                                                                                    <span
                                                                                        class="text-dark font-weight-bold">{{ __('vacant') }}</span>
                                                                                @endforelse
                                                                            </div>
                                                                            <div class="mt-2">
                                                                                <div class="org-branch-label">
                                                                                    {{ __('employees') }}</div>
                                                                                @if ($brEmps->isEmpty())
                                                                                    <span
                                                                                        class="text-muted font-size-sm">{{ __('no_records') }}</span>
                                                                                @else
                                                                                    <ul class="org-employee-list"
                                                                                        id="branch-{{ $branch->id }}">
                                                                                        @foreach ($brEmps as $emp)
                                                                                            <li>
                                                                                                <i
                                                                                                    class="fas fa-user text-muted ml-1"></i>
                                                                                                {{ $emp->name }}
                                                                                                @if ($emp->job)
                                                                                                    <span
                                                                                                        class="text-muted">
                                                                                                        -
                                                                                                        {{ $emp->job->name }}</span>
                                                                                                @endif
                                                                                            </li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-muted font-size-sm text-center py-3">
                                                    {{ __('no_records') }} ({{ __('sections') }})</div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="org-chart-footer text-center">
                <small class="text-muted">{{ __('Municipality of Jabalia Al-Nazlah') }} -
                    {{ __('Organizational Structure') }}</small>
            </div>
        </div>
    </div>

    {{-- ======== نافذة عرض هيكلية العقدة (دائرة/قسم/شعبة) عند الضغط على أي عقدة بالمخطط ======== --}}
    <div class="modal fade" id="orgHierarchyModal" tabindex="-1" role="dialog"
        aria-labelledby="orgHierarchyModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info py-4">
                    <h5 class="modal-title text-white font-weight-bolder" id="orgHierarchyModalTitle">
                        <i class="fas fa-sitemap ml-2"></i><span
                            id="orgHierarchyTitleText">{{ __('hierarchy_details') }}</span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"
                        aria-label="{{ __('close') }}">
                        <i aria-hidden="true" class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-4" id="orgHierarchyBody">
                    <div class="text-center py-10">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted font-weight-bold mt-4 mb-0">{{ __('loading') }}</p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex flex-wrap" style="gap:0.5rem;">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">{{ __('close') }}</button>
                    <button type="button" class="btn btn-light-success font-weight-bold d-none"
                        id="orgHierarchyPrintBtn">
                        <i class="fas fa-print ml-1"></i>{{ __('print') }}
                    </button>
                    <button type="button" class="btn btn-primary font-weight-bold d-none" id="orgHierarchyEditBtn">
                        <i class="fas fa-pen ml-1"></i>{{ __('edit') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->check() &&
            (auth()->user()->can('update departments') ||
                auth()->user()->can('update sections') ||
                auth()->user()->can('update branches')))
        {{-- ======== نافذة التعديل العامة للأنواع الثلاثة (تُفتح بعد إغلاق نافذة الهيكلية لمنع تراكب المودالات) ======== --}}
        <div class="modal fade" id="orgEditModal" tabindex="-1" role="dialog" aria-labelledby="orgEditModalTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-info py-4">
                        <h5 class="modal-title text-white font-weight-bolder" id="orgEditModalTitle">
                            <i class="fas fa-pen ml-2"></i>{{ __('edit') }}: <span id="orgEditNodeName"></span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal"
                            aria-label="{{ __('close') }}">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                    <form id="orgEditForm" novalidate>
                        <input type="hidden" id="orgEditType" value="" />
                        <input type="hidden" id="orgEditNodeId" value="" />
                        <div class="modal-body p-4">
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-solid" id="orgEditName"
                                    name="name" maxlength="255" required />
                                <div class="d-none text-danger font-size-sm mt-1" id="orgEditNameError"></div>
                            </div>
                            <div class="form-group d-none" id="orgEditParentGroup">
                                <label class="font-weight-bold" id="orgEditParentLabel"></label>
                                <select class="form-control form-control-solid" id="orgEditParentSelect"
                                    required></select>
                                <div class="d-none text-danger font-size-sm mt-1" id="orgEditParentError"></div>
                            </div>
                            <div class="alert alert-danger d-none mb-0" id="orgEditGeneralError"></div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light-primary font-weight-bold"
                                data-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-info font-weight-bold px-10"
                                id="orgEditSaveBtn">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if (auth()->check() &&
            (auth()->user()->can('store sections') ||
                auth()->user()->can('store branches') ||
                auth()->user()->can('store employees') ||
                auth()->user()->can('update employees')))
        {{-- ======== نافذة الإضافة العامة (قسم تحت دائرة / شعبة تحت قسم / موظف تحت أي وحدة) ======== --}}
        <div class="modal fade" id="orgCreateModal" tabindex="-1" role="dialog" aria-labelledby="orgCreateModalTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success py-4">
                        <h5 class="modal-title text-white font-weight-bolder" id="orgCreateModalTitle">
                            <i class="fas fa-plus ml-2"></i><span id="orgCreateTitleText">{{ __('Add') }}</span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal"
                            aria-label="{{ __('close') }}">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                    <form id="orgCreateForm" novalidate>
                        <input type="hidden" id="orgCreateTarget" value="" />
                        <input type="hidden" id="orgCreateParentField" value="" />
                        <input type="hidden" id="orgCreateParentValue" value="" />
                        <div class="modal-body p-4">
                            <div class="alert alert-light-success font-weight-bold mb-4" id="orgCreateParentInfo"></div>
                            <div class="form-group" id="orgCreateNameField">
                                <label class="font-weight-bold">{{ __('name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-solid" id="orgCreateName"
                                    name="name" maxlength="255" required />
                            </div>
                            <div class="d-none" id="orgCreateEmployeeFields">
                                <div class="form-group">
                                    <label class="font-weight-bold">{{ __('select_employee') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control form-control-solid selectpicker w-100"
                                        data-live-search="true" data-live-search-placeholder="{{ __('select_employee') }}"
                                        title="{{ __('select_employee') }}" id="orgCreateEmployee"
                                        name="employee_id" required>
                                        <option value="">{{ __('select_employee') }}</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-muted">
                                        <i class="fas fa-sitemap ml-1"></i>{{ __('attached_to') }}
                                    </label>
                                    <div class="alert alert-light-info font-weight-bold mb-0 py-2"
                                        id="orgCreateEmployeePath">
                                        {{ __('employee_assignment_required') }}
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-danger d-none mb-0" id="orgCreateErrors"></div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light-primary font-weight-bold"
                                data-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-success font-weight-bold px-10"
                                id="orgCreateSaveBtn">{{ __('Add') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    @if (auth()->check() &&
            (auth()->user()->can('store departmentdirector') ||
                auth()->user()->can('update departmentdirector') ||
                auth()->user()->can('delete departmentdirector') ||
                auth()->user()->can('store sectiondirector') ||
                auth()->user()->can('update sectiondirector') ||
                auth()->user()->can('delete sectiondirector') ||
                auth()->user()->can('store branchdirector') ||
                auth()->user()->can('update branchdirector') ||
                auth()->user()->can('delete branchdirector')))
        {{-- ======== نافذة تعيين/تغيير المدير (دائرة/قسم/شعبة) من داخل نافذة الهيكلية ======== --}}
        <div class="modal fade" id="orgDirectorModal" tabindex="-1" role="dialog"
            aria-labelledby="orgDirectorModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning py-4">
                        <h5 class="modal-title text-white font-weight-bolder" id="orgDirectorModalTitle">
                            <i class="fas fa-user-tie ml-2"></i><span
                                id="orgDirectorTitleText">{{ __('current_director') }}</span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal"
                            aria-label="{{ __('close') }}">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                    <form id="orgDirectorForm" novalidate>
                        <input type="hidden" id="orgDirectorType" value="" />
                        <input type="hidden" id="orgDirectorUnitId" value="" />
                        <div class="modal-body p-4">
                            <div class="alert alert-light-warning font-weight-bold mb-4" id="orgDirectorCurrentInfo">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('director_type') }}</label>
                                <select class="form-control" id="orgDirectorTypeSelect" name="director_type" required>
                                    <option value="permanent">{{ __('permanent') }}</option>
                                    <option value="acting">{{ __('acting') }}</option>
                                </select>
                            </div>
                            <div class="form-group d-none" id="orgDirectorConvertGroup">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="orgDirectorConvertExisting" name="convert_existing">
                                    <label class="form-check-label font-weight-bold" for="orgDirectorConvertExisting">
                                        {{ __('convert_existing_director') }}
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">{{ __('employee') }} <span
                                        class="text-danger">*</span></label>
                                <select class="form-control selectpicker w-100" data-live-search="true"
                                    title="{{ __('select_employee') }}" id="orgDirectorEmployee" name="employee_id"
                                    required></select>
                                <input type="hidden" id="orgDirectorConvertedEmployee" value="">
                                <div class="d-none text-danger font-size-sm mt-1" id="orgDirectorError"></div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light-primary font-weight-bold"
                                data-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-warning font-weight-bold px-10"
                                id="orgDirectorSaveBtn">{{ __('Save') }}</button>
                            <button type="button" class="btn btn-light-danger font-weight-bold d-none"
                                id="orgDirectorDeleteBtn">
                                <i class="fas fa-trash ml-1"></i>{{ __('Delete') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        (function() {
            var inner = document.getElementById('orgChartZoomInner');
            var slider = document.getElementById('orgChartZoomSlider');
            var pctEl = document.getElementById('orgChartZoomPct');
            var btnIn = document.getElementById('orgChartZoomIn');
            var btnOut = document.getElementById('orgChartZoomOut');
            var btnReset = document.getElementById('orgChartZoomReset');
            var btnFit = document.getElementById('orgChartZoomFit');
            var btnPrint = document.getElementById('orgChartPrint');
            var btnExportWord = document.getElementById('orgChartExportWord');
            var btnExportPdfA3 = document.getElementById('orgChartExportPdfA3');
            var outer = document.querySelector('.org-chart-zoom-outer');
            var searchInput = document.getElementById('orgChartSearch');
            var searchClear = document.getElementById('orgChartSearchClear');
            var searchResults = document.getElementById('orgChartSearchResults');
            var searchSlot = document.querySelector('.org-chart-search-slot');
            var searchBar = document.querySelector('.org-chart-search');

            function syncSearchPosition() {
                if (!searchSlot || !searchBar) return;
                var slotRect = searchSlot.getBoundingClientRect();
                searchBar.style.left = slotRect.left + 'px';
                searchBar.style.width = slotRect.width + 'px';
            }

            syncSearchPosition();
            window.addEventListener('resize', syncSearchPosition);

            var scale = 1;
            var minS = 0.2;
            var maxS = 2;
            var step = 0.1;

            function clamp(v, a, b) {
                return Math.max(a, Math.min(b, v));
            }

            function apply() {
                var lowerScale = document.body.classList.contains('org-printing') ? 0.05 : minS;
                scale = clamp(scale, lowerScale, maxS);

                if (document.body.classList.contains('org-printing')) {
                    inner.style.zoom = scale;
                    inner.style.transform = 'none';
                } else if ('zoom' in inner.style) {
                    inner.style.zoom = scale;
                    inner.style.transform = 'none';
                } else {
                    inner.style.transform = 'scale(' + scale + ')';
                }

                var p = Math.round(scale * 100);
                slider.value = String(p);
                if (pctEl) pctEl.textContent = p + '%';
            }

            function fitToWidth() {
                if (!outer || !inner) return;
                var availableWidth = outer.clientWidth - 40;
                var contentWidth = inner.scrollWidth;
                if (contentWidth > 0) {
                    scale = availableWidth / contentWidth;
                    if (scale > 1) scale = 1;
                    apply();
                }
            }

            function normalizeSearchText(value) {
                return String(value || '')
                    .normalize('NFKC')
                    .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
                    .replace(/[إأآا]/g, 'ا')
                    .replace(/ى/g, 'ي')
                    .replace(/ة/g, 'ه')
                    .toLocaleLowerCase();
            }

            function revealNodeAncestors(node) {
                var ancestor = node.parentElement;
                while (ancestor && ancestor !== inner) {
                    if (ancestor.classList.contains('org-section-stack') || ancestor.classList.contains(
                            'org-branch-stack')) {
                        ancestor.style.display = '';
                    }
                    ancestor = ancestor.parentElement;
                }
            }

            function clearSearchMatches() {
                document.querySelectorAll('.org-search-match').forEach(function(node) {
                    node.classList.remove('org-search-match');
                });
            }

            function searchOrgChart() {
                if (!searchInput) return;

                var query = normalizeSearchText(searchInput.value).trim();
                clearSearchMatches();

                if (!query) {
                    if (searchResults) searchResults.textContent = '';
                    return;
                }

                var matches = Array.from(document.querySelectorAll('.org-node[data-org-search-name]')).filter(function(
                    node) {
                    return normalizeSearchText(node.dataset.orgSearchName).includes(query);
                });

                if (searchResults) {
                    searchResults.textContent = matches.length ? matches.length + ' {{ __('results') }}' :
                        '{{ __('no_records') }}';
                }
                if (!matches.length) return;

                matches.forEach(function(node) {
                    node.classList.add('org-search-match');
                    revealNodeAncestors(node);
                });

                matches[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                    inline: 'center'
                });
            }

            searchInput && searchInput.addEventListener('input', searchOrgChart);
            searchInput && searchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchOrgChart();
                }
            });
            searchClear && searchClear.addEventListener('click', function() {
                if (!searchInput) return;
                searchInput.value = '';
                searchInput.focus();
                searchOrgChart();
            });

            btnPrint && btnPrint.addEventListener('click', function() {
                fitChartForPrint();
                window.setTimeout(function() {
                    window.print();
                }, 100);
            });

            btnExportWord && btnExportWord.addEventListener('click', function() {
                window.location.href = '{{ route('structure.org-chart.export.word') }}';
            });

            btnExportPdfA3 && btnExportPdfA3.addEventListener('click', function() {
                window.location.href = '{{ route('structure.org-chart.export.pdf.a3') }}';
            });

            var btnSettleEmployees = document.getElementById('orgChartSettleEmployees');
            btnSettleEmployees && btnSettleEmployees.addEventListener('click', function() {
                if (!confirm('{{ __('تأكيد تحديث تسكين الموظفين بناءً على تعيينات المديرين؟') }}')) {
                    return;
                }
                var btn = this;
                var originalText = btn.innerHTML;
                btn.innerHTML = '<i class=\"fas fa-spinner fa-spin ml-2\"></i> {{ __('جارٍ التحديث...') }}';
                btn.setAttribute('disabled', 'disabled');

                fetch('{{ route('structure.org-chart.settle-employees') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.ok) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('{{ __('حدث خطأ أثناء التحديث') }}');
                    }
                })
                .catch(function() {
                    alert('{{ __('حدث خطأ أثناء التواصل مع الخادم') }}');
                })
                .finally(function() {
                    btn.innerHTML = originalText;
                    btn.removeAttribute('disabled');
                });
            });

            // الطباعة: تصغير المخطط حسب العرض، مع السماح بامتداده رأسيًا على عدة صفحات.
            var savedPrintScale = null;
            var savedPrintVisibility = [];

            function fitChartForPrint() {
                if (!inner) {
                    return;
                }
                if (savedPrintScale === null) {
                    savedPrintScale = scale;
                }
                if (!savedPrintVisibility.length) {
                    document.querySelectorAll('.org-section-stack, .org-branch-stack, .org-employee-list').forEach(
                        function(element) {
                            savedPrintVisibility.push({
                                element: element,
                                display: element.style.display
                            });
                            element.style.display = '';
                        });
                }
                document.body.classList.add('org-printing');
                scale = 1;
                apply();
                var departmentsRow = inner.querySelector('.org-chart-depts');
                var contentWidth = Math.max(
                    inner.scrollWidth || 0,
                    inner.offsetWidth || 0,
                    departmentsRow ? departmentsRow.scrollWidth : 0
                );
                var contentHeight = Math.max(
                    inner.scrollHeight || 0,
                    inner.offsetHeight || 0
                );
                /*
                    صندوق الطباعة ثابت (لا يُؤخذ من getBoundingClientRect):
                    صفحة A4 أفقية 297×210مم بهامش 5مم من كل جهة (يطابق @page).
                    التحويل عند 96dpi: 1مم ≈ 3.7795px.
                    بعض المتصفحات (كروم) تُشغّل beforeprint قبل تطبيق أنماط الطباعة،
                    لذلك يعتمد الحساب على أبعاد ثابتة.
                    نحسب النسبة اللازمة للعرض وللارتفاع معًا، ونأخذ الأصغر بينهما،
                    لضمان دخول المخطط بالكامل (السلسلة العلوية + صف الدوائر) داخل
                    صفحة واحدة دون أي فيض يدفعه لصفحات إضافية.
                */
                var printBoxWidth = ((297 - 6) * 96) / 25.4; /* ≈ 1099px */
                var printBoxHeight = ((210 - 6) * 96) / 25.4; /* ≈ 771px */
                var widthScale = contentWidth > 0 ? printBoxWidth / contentWidth : 1;
                var heightScale = contentHeight > 0 ? printBoxHeight / contentHeight : 1;
                scale = Math.max(Math.min(widthScale, heightScale, 1), 0.05);
                apply();
            }

            function restoreChartAfterPrint() {
                if (savedPrintScale !== null) {
                    document.body.classList.remove('org-printing');
                    scale = savedPrintScale;
                    savedPrintScale = null;
                    savedPrintVisibility.forEach(function(item) {
                        item.element.style.display = item.display;
                    });
                    savedPrintVisibility = [];
                    apply();
                }
            }
            window.addEventListener('beforeprint', fitChartForPrint);
            window.addEventListener('afterprint', restoreChartAfterPrint);
            if (window.matchMedia) {
                var printMql = window.matchMedia('print');
                var printMqlHandler = function(m) {
                    if (m.matches) {
                        fitChartForPrint();
                    } else {
                        restoreChartAfterPrint();
                    }
                };
                if (printMql.addEventListener) {
                    printMql.addEventListener('change', printMqlHandler);
                } else if (printMql.addListener) {
                    printMql.addListener(printMqlHandler);
                }
            }

            if (inner && slider) {
                btnFit && btnFit.addEventListener('click', fitToWidth);

                window.addEventListener('load', function() {
                    setTimeout(fitToWidth, 300);
                });

                btnIn && btnIn.addEventListener('click', function() {
                    scale = clamp(scale + step, minS, maxS);
                    apply();
                });
                btnOut && btnOut.addEventListener('click', function() {
                    scale = clamp(scale - step, minS, maxS);
                    apply();
                });
                btnReset && btnReset.addEventListener('click', function() {
                    scale = 1;
                    apply();
                });
                slider.addEventListener('input', function() {
                    var v = parseInt(slider.value, 10);
                    if (isNaN(v)) return;
                    scale = clamp(v / 100, minS, maxS);
                    apply();
                });
            }

            function isHidden(el) {
                return el ? el.style.display === 'none' : true;
            }

            function setToggleState(button, target, hidden) {
                if (!target) return;
                target.style.display = hidden ? 'none' : '';

                var icon = button ? button.querySelector('i') : null;
                if (icon) icon.className = hidden ? 'fas fa-eye-slash' : 'fas fa-eye';
            }

            document.addEventListener('click', function(e) {
                if (e.target.closest('.toggle-dept')) {
                    const button = e.target.closest('.toggle-dept');
                    const target = document.getElementById(button.dataset.target);
                    setToggleState(button, target, !isHidden(target));
                }

                if (e.target.closest('.toggle-section')) {
                    const button = e.target.closest('.toggle-section');
                    const target = document.getElementById(button.dataset.target);
                    setToggleState(button, target, !isHidden(target));
                }

                if (e.target.closest('.toggle-branch')) {
                    const button = e.target.closest('.toggle-branch');
                    const target = document.getElementById(button.dataset.target);
                    setToggleState(button, target, !isHidden(target));
                }
            });

            document.getElementById('showAll')?.addEventListener('click', function() {
                document.querySelectorAll('.org-section-stack, .org-branch-stack, .org-employee-list').forEach(
                    el => {
                        el.style.display = '';
                    });
                document.querySelectorAll('.toggle-dept, .toggle-section, .toggle-branch').forEach(btn => {
                    const icon = btn.querySelector('i');
                    if (icon) icon.className = 'fas fa-eye';
                });
            });

            document.getElementById('hideAll')?.addEventListener('click', function() {
                document.querySelectorAll('.org-section-stack, .org-branch-stack, .org-employee-list').forEach(
                    el => {
                        el.style.display = 'none';
                    });
                document.querySelectorAll('.toggle-dept, .toggle-section, .toggle-branch').forEach(btn => {
                    const icon = btn.querySelector('i');
                    if (icon) icon.className = 'fas fa-eye-slash';
                });
            });

            document.getElementById('toggleDepartments')?.addEventListener('click', function() {
                document.querySelectorAll('[id^="dept-"]').forEach(el => {
                    const isVisible = el.style.display !== 'none';
                    el.style.display = isVisible ? 'none' : '';

                    const button = document.querySelector(`[data-target="${el.id}"]`);
                    if (button) {
                        const icon = button.querySelector('i');
                        if (icon) icon.className = isVisible ? 'fas fa-eye-slash' : 'fas fa-eye';
                    }
                });
            });

            document.getElementById('toggleSections')?.addEventListener('click', function() {
                document.querySelectorAll('[id^="section-"]').forEach(el => {
                    const isVisible = el.style.display !== 'none';
                    el.style.display = isVisible ? 'none' : '';

                    const button = document.querySelector(`[data-target="${el.id}"]`);
                    if (button) {
                        const icon = button.querySelector('i');
                        if (icon) icon.className = isVisible ? 'fas fa-eye-slash' : 'fas fa-eye';
                    }
                });
            });

            document.getElementById('toggleBranches')?.addEventListener('click', function() {
                document.querySelectorAll('[id^="branch-"]').forEach(el => {
                    const isVisible = el.style.display !== 'none';
                    el.style.display = isVisible ? 'none' : '';

                    const button = document.querySelector(`[data-target="${el.id}"]`);
                    if (button) {
                        const icon = button.querySelector('i');
                        if (icon) icon.className = isVisible ? 'fas fa-eye-slash' : 'fas fa-eye';
                    }
                });
            });
        })();
    </script>

    {{-- ======== إعدادات النافذة المنبثقة لهيكلية العقد ======== --}}
    <script>
        window.ORG_CHART_CONFIG = {
            csrfToken: '{{ csrf_token() }}',
            routes: {
                hierarchy: '{{ url('structure/org-chart/hierarchy') }}',
                editData: '{{ url('structure/org-chart/edit-data') }}',
                updateUrl: '{{ url('structure/org-chart/update') }}',
                createData: '{{ url('structure/org-chart/create-data') }}',
                storeUrl: '{{ url('structure/org-chart/store') }}',
                directorData: '{{ url('structure/org-chart/director-data') }}',
                storeDirectorUrl: '{{ url('structure/org-chart/store-director') }}',
                deleteDirectorUrl: '{{ url('structure/org-chart/delete-director') }}',
                deleteUrl: '{{ url('structure/org-chart/delete') }}'
            },
            canUpdate: {
                department: {{ auth()->check() && auth()->user()->can('update departments') ? 'true' : 'false' }},
                section: {{ auth()->check() && auth()->user()->can('update sections') ? 'true' : 'false' }},
                branch: {{ auth()->check() && auth()->user()->can('update branches') ? 'true' : 'false' }}
            },
            canDelete: {
                section: {{ auth()->check() && auth()->user()->can('delete sections') ? 'true' : 'false' }},
                branch: {{ auth()->check() && auth()->user()->can('delete branches') ? 'true' : 'false' }}
            },
            canStore: {
                section: {{ auth()->check() && auth()->user()->can('store sections') ? 'true' : 'false' }},
                branch: {{ auth()->check() && auth()->user()->can('store branches') ? 'true' : 'false' }},
                employee: {{ auth()->check() && (auth()->user()->can('store employees') || auth()->user()->can('update employees')) ? 'true' : 'false' }}
            },
            canDirector: {
                department: {{ auth()->check() && (auth()->user()->can('store departmentdirector') || auth()->user()->can('update departmentdirector') || auth()->user()->can('delete departmentdirector')) ? 'true' : 'false' }},
                section: {{ auth()->check() && (auth()->user()->can('store sectiondirector') || auth()->user()->can('update sectiondirector') || auth()->user()->can('delete sectiondirector')) ? 'true' : 'false' }},
                branch: {{ auth()->check() && (auth()->user()->can('store branchdirector') || auth()->user()->can('update branchdirector') || auth()->user()->can('delete branchdirector')) ? 'true' : 'false' }}
            },
            canDeleteDirector: {
                department: {{ auth()->check() && auth()->user()->can('delete departmentdirector') ? 'true' : 'false' }},
                section: {{ auth()->check() && auth()->user()->can('delete sectiondirector') ? 'true' : 'false' }},
                branch: {{ auth()->check() && auth()->user()->can('delete branchdirector') ? 'true' : 'false' }}
            },
            labels: {
                loading: '{{ __('loading') }}',
                viewHint: '{{ __('view_hierarchy_hint') }}',
                errorLoading: '{{ __('error_loading_data') }}',
                errorSaving: '{{ __('error_saving_data') }}',
                noPermission: '{{ __('no_update_permission') }}',
                totalEmployees: '{{ __('total_employees') }}',
                directEmployees: '{{ __('direct_employees') }}',
                responsibleManager: '{{ __('responsible_manager') }}',
                attachedTo: '{{ __('attached_to') }}',
                noChildElements: '{{ __('no_child_elements') }}',
                employees: '{{ __('employees') }}',
                sections: '{{ __('sections') }}',
                branches: '{{ __('branches') }}',
                edit: '{{ __('edit') }}',
                delete: '{{ __('delete') }}',
                confirmDelete: '{{ __('confirm_delete') }}',
                addSection: '{{ __('add_section') }}',
                addBranch: '{{ __('add_branch') }}',
                addEmployee: '{{ __('add_employee') }}',
                chooseSection: '{{ __('choose_section') }}',
                branchNeedsSection: '{{ __('branch_needs_section') }}',
                chooseGender: '{{ __('choose_gender') }}',
                chooseJob: '{{ __('choose_job') }}',
                nationalId: '{{ __('national_id') }}',
                male: '{{ __('male') }}',
                female: '{{ __('female') }}',
                genderLabel: '{{ __('gender') }}',
                mobile: '{{ __('mobile') }}',
                birthdate: '{{ __('birthdate') }}',
                savedSuccessfully: '{{ __('saved successfully') }}',
                changeDirector: '{{ __('change_director') }}',
                selectEmployee: '{{ __('select_employee') }}',
                currentDirector: '{{ __('current_director') }}',
                undefinedValue: '{{ __('vacant') }}',
                convertExistingDirector: '{{ __('convert_existing_director') }}'
            },
            icons: {
                department: 'fas fa-building',
                section: 'fas fa-th-list',
                branch: 'fas fa-code-branch',
                employee: 'fas fa-user',
                manager: 'fas fa-user-tie'
            },
            typeLabels: {
                department: '{{ __('department') }}',
                section: '{{ __('section') }}',
                branch: '{{ __('branch') }}'
            },
            directorLabels: {
                department: '{{ __('assign_director_department') }}',
                section: '{{ __('assign_director_section') }}',
                branch: '{{ __('assign_director_branch') }}'
            }
        };
    </script>

    {{-- ======== منطق النافذة المنبثقة: عرض الهيكلية ← تعديل ← حفظ ← إعادة الفتح محدّثة ======== --}}
    <script>
        (function() {
            'use strict';

            var cfg = window.ORG_CHART_CONFIG;
            if (!cfg || typeof window.jQuery === 'undefined') {
                return;
            }
            cfg.routes = cfg.routes || {};
            cfg.routes.print = "{{ url('/structure/org-chart/print') }}";

            var $ = window.jQuery;
            var $modal = $('#orgHierarchyModal');
            var $body = $('#orgHierarchyBody');
            var $titleText = $('#orgHierarchyTitleText');
            var $editBtn = $('#orgHierarchyEditBtn');
            var $printBtn = $('#orgHierarchyPrintBtn');
            var $editModal = $('#orgEditModal');

            // زر الطباعة: يفتح صفحة طباعة مستقلة لهيكلية الدائرة/القسم/الشعبة بالترويسة الرسمية
            $printBtn.off('click').on('click', function() {
                var t = $(this).data('type') || (hierarchyData && hierarchyData.node ?
                    hierarchyData.node.type : current.type);
                var i = $(this).data('id') || (hierarchyData && hierarchyData.node ?
                    hierarchyData.node.id : current.id);
                if (!t || !i) {
                    return;
                }
                window.open(orgHierarchyPrintUrl(t, i), '_blank');
            });

            var current = {
                type: null,
                id: null
            };
            var hierarchyData = null;
            var pendingReopen =
            false; // (مُهمَل): كان يُستخدم لإعادة فتح نافذة الهيكلية بعد حفظ التعديل — استُبدل بتحديث الصفحة

            function esc(value) {
                return $('<div/>').text(value == null ? '' : String(value)).html();
            }

            function endpoint(action, type, id) {
                return cfg.routes[action] + '/' + encodeURIComponent(type) + '/' + encodeURIComponent(id);
            }

            function notify(level, message) {
                if (typeof window.toastr !== 'undefined' && window.toastr[level]) {
                    window.toastr[level](message);
                }
            }

            // تحديث الصفحة بالكامل بعد نجاح أي عملية تعديل (حفظ/إضافة/حذف/تعيين مدير)
            // لضمان انعكاس التغييرات في كل أجزاء الصفحة مع مهلة قصيرة لعرض رسالة النجاح
            function reloadPageSoon(delay) {
                window.setTimeout(function() {
                    window.location.reload();
                }, delay || 900);
            }

            function loaderHtml() {
                return '<div class="text-center py-10"><div class="spinner-border text-primary" role="status"></div>' +
                    '<p class="text-muted font-weight-bold mt-4 mb-0">' + esc(cfg.labels.loading) + '</p></div>';
            }

            function renderError() {
                $titleText.text('');
                $editBtn.addClass('d-none').removeData('type').removeData('id');
                $body.html(
                    '<div class="alert alert-danger text-center mb-0"><i class="fas fa-exclamation-triangle ml-2"></i>' +
                    esc(cfg.labels.errorLoading) + '</div>');
            }

            function openHierarchy(type, id) {
                current = {
                    type: String(type),
                    id: String(id)
                };
                $titleText.text(cfg.labels.loading);
                $editBtn.addClass('d-none').removeData('type').removeData('id');
                $body.html(loaderHtml());
                $modal.modal('show');

                $.ajax({
                        url: endpoint('hierarchy', current.type, current.id),
                        type: 'GET',
                        dataType: 'json'
                    })
                    .done(function(response) {
                        if (!response || !response.ok) {
                            renderError();
                            notify('error', cfg.labels.errorLoading);
                            return;
                        }
                        render(response);
                    })
                    .fail(function() {
                        renderError();
                        notify('error', cfg.labels.errorLoading);
                    });
            }

            function statChip(innerHtml, isMuted) {
                return '<span class="org-hier-stat-chip' + (isMuted ? ' is-muted' : '') + '">' + innerHtml + '</span>';
            }

            function employeeRow(emp) {
                var jobHtml = emp.job ? ' <span class="text-muted font-weight-normal">— ' + esc(emp.job) + '</span>' :
                    '';
                return '<div class="org-hier-employee-row"><i class="fas fa-user"></i><span>' + esc(emp.name) +
                    jobHtml + '</span></div>';
            }

            function employeesBlock(employees) {
                if (!employees || !employees.length) {
                    return '';
                }
                var html = '<div class="org-hier-sublabel"><i class="fas fa-users ml-1"></i> ' + esc(cfg.labels
                    .directEmployees) + ' (' + employees.length + ')</div>';
                employees.forEach(function(emp) {
                    html += employeeRow(emp);
                });
                return html;
            }

            function buildActionBar(nodeType) {
                var defs = {
                    department: [
                        ['section', cfg.labels.addSection],
                        ['branch', cfg.labels.addBranch],
                        ['employee', cfg.labels.addEmployee]
                    ],
                    section: [
                        ['branch', cfg.labels.addBranch],
                        ['employee', cfg.labels.addEmployee]
                    ],
                    branch: [
                        ['employee', cfg.labels.addEmployee]
                    ]
                };
                var buttons = '';
                (defs[nodeType] || []).forEach(function(pair) {
                    if (cfg.canStore[pair[0]]) {
                        buttons +=
                            '<button type="button" class="btn btn-sm btn-light-success font-weight-bold org-create-btn" data-target="' +
                            pair[0] + '">' +
                            '<i class="fas fa-plus ml-1"></i>' + esc(pair[1]) + '</button>';
                    }
                });
                return buttons ? '<div class="org-hier-actionbar">' + buttons + '</div>' : '';
            }

            // عرض عنصر بشكل تكراري: العنصر نفسه ثم موظفيه المباشرين ثم عناصره التابعة الأدنى (شجرة كاملة)
            function renderItem(item, depth) {
                var type = item.type || (depth > 0 ? 'branch' : 'section');
                var iconClass = 'org-hier-item-icon' + (type === 'branch' ? ' is-branch' : '');
                var icon = type === 'branch' ? cfg.icons.branch : cfg.icons.section;

                var html = '<div class="org-hier-item">';
                html += '<div class="d-flex align-items-center flex-grow-1 text-right" style="min-width:0;">';
                html += '<span class="' + iconClass + ' ml-2"><i class="' + icon + '"></i></span>';
                html += '<div class="flex-grow-1" style="min-width:0;">';

                // اسم العنصر قابل للنقر لفتح نافذته المنبثقة الخاصة (كل وحدة على حدة كما هي)
                html +=
                    '<div><span role="button" class="font-weight-bolder text-dark org-hier-item-link hier-open-link" data-type="' +
                    type + '" data-id="' + item.id + '" title="' + esc(cfg.labels.viewHint) + '">' + esc(item.name) +
                    '</span></div>';

                var parts = [esc(cfg.labels.responsibleManager) + ': ' + esc(item.manager)];
                (item.meta || []).forEach(function(m) {
                    parts.push(esc(m.key === 'branches' ? cfg.labels.branches : cfg.labels.directEmployees) +
                        ': ' + m.value);
                });
                html += '<div class="org-hier-item-meta">' + parts.join(' &bull; ') + '</div>';
                html += '</div></div>';

                if (type === 'section' && cfg.canStore.branch) {
                    html +=
                        '<button type="button" class="btn btn-sm btn-light-success font-weight-bold flex-shrink-0 mr-2 org-create-inline-btn" data-target="branch" data-unit-type="section" data-unit-id="' +
                        item.id + '" title="' + esc(cfg.labels.addBranch) + '">' +
                        '<i class="fas fa-plus ml-1"></i>' + esc(cfg.labels.addBranch) + '</button>';
                }

                if (cfg.canUpdate[type]) {
                    html +=
                        '<button type="button" class="btn btn-sm btn-light-primary font-weight-bold flex-shrink-0 hier-edit-btn" data-type="' +
                        type + '" data-id="' + item.id + '">' +
                        '<i class="fas fa-pen ml-1"></i>' + esc(cfg.labels.edit) + '</button>';
                }

                if (cfg.canDelete[type]) {
                    html +=
                        '<button type="button" class="btn btn-sm btn-light-danger font-weight-bold flex-shrink-0 hier-delete-btn" data-type="' +
                        type + '" data-id="' + item.id + '" data-name="' + esc(item.name || '') + '">' +
                        '<i class="fas fa-trash ml-1"></i>' + esc(cfg.labels.delete) + '</button>';
                }

                html += '</div>';

                var nested = '';
                nested += employeesBlock(item.employees);

                if (item.branches && item.branches.length) {
                    nested += '<div class="org-hier-sublabel"><i class="fas fa-code-branch ml-1"></i> ' + esc(cfg.labels
                        .branches) + ' (' + item.branches.length + ')</div>';
                    item.branches.forEach(function(branch) {
                        nested += renderItem(branch, depth + 1);
                    });
                }

                if (nested) {
                    html += '<div class="org-hier-nested py-1">' + nested + '</div>';
                }

                return html;
            }

            function render(response) {
                var node = response.node || {};
                var stats = response.stats || {};
                var children = response.children || {};

                current = {
                    type: node.type,
                    id: node.id
                };
                $titleText.text(node.name || '');

                var html = '';
                html += '<div class="org-hier-manager mb-3"><i class="' + (cfg.icons[node.type] || cfg.icons
                        .department) + ' text-primary ml-2"></i>' +
                    '<span class="text-dark font-weight-bolder">' + esc(node.name) + '</span>' +
                    '<span class="badge badge-secondary font-weight-bold mr-2">' + esc(cfg.typeLabels[node.type] ||
                        '') + '</span></div>';

                var hasManager = node.manager && node.manager !== cfg.labels.undefinedValue;
                html += '<div class="org-hier-manager mb-3"><i class="' + cfg.icons.manager +
                    ' text-warning ml-2"></i><span>' +
                    esc(cfg.labels.responsibleManager) + ': <span class="text-dark font-weight-bolder">' + esc(node
                        .manager) + '</span></span>';
                if (cfg.canDirector[node.type]) {
                    var directorBtnLabel = hasManager ? cfg.labels.changeDirector : (cfg.directorLabels[node.type] ||
                        '');
                    html +=
                        '<button type="button" class="btn btn-sm btn-light-warning font-weight-bold flex-shrink-0 org-director-btn" style="margin-inline-start:auto;" title="' +
                        esc(directorBtnLabel) + '">' +
                        '<i class="fas fa-user-tie ml-1"></i>' + esc(directorBtnLabel) + '</button>';
                }
                html += '</div>';

                html += '<div class="d-flex flex-wrap align-items-center mb-4" style="gap:0.5rem;">';
                html += statChip('<i class="fas fa-users"></i> ' + esc(cfg.labels.totalEmployees) + ': <b>' + (stats
                    .total_employees || 0) + '</b>', false);
                html += statChip('<i class="fas fa-user"></i> ' + esc(cfg.labels.directEmployees) + ': <b>' + (stats
                    .direct_employees || 0) + '</b>', true);
                if (response.parent) {
                    html += '<span role="button" class="org-hier-stat-chip org-hier-parent-link" data-type="' + response
                        .parent.type + '" data-id="' + response.parent.id + '" title="' + esc(cfg.labels.viewHint) +
                        '">' +
                        '<i class="fas fa-level-up-alt"></i> ' + esc(cfg.labels.attachedTo) + ': <b>' + esc(response
                            .parent.name) + '</b></span>';
                }
                html += '</div>';

                // شريط أزرار الإضافة حسب نوع العقدة المفتوحة وصلاحيات المستخدم
                html += buildActionBar(node.type);

                if (children.kind === 'tree') {
                    // شجرة كاملة: العناصر التابعة مع موظفيها المباشرين + الموظفون المباشرون لهذه العقدة
                    var hasAny = (children.items && children.items.length) || (children.employees && children.employees
                        .length);
                    if (!hasAny) {
                        html += '<div class="text-muted font-size-sm py-3 text-center border rounded bg-light">' + esc(
                            cfg.labels.noChildElements) + '</div>';
                    } else {
                        html += '<div class="border rounded p-2 bg-white">';
                        if (children.items && children.items.length) {
                            html += '<div class="org-hier-sublabel"><i class="' + cfg.icons.section + ' ml-1"></i> ' +
                                esc(children.label || '') + ' (' + children.items.length + ')</div>';
                            children.items.forEach(function(item) {
                                html += renderItem(item, 0);
                            });
                        }
                        if (children.employees && children.employees.length) {
                            if (children.items && children.items.length) {
                                html += '<hr class="my-2" />';
                            }
                            html += employeesBlock(children.employees);
                        }
                        html += '</div>';
                    }
                } else {
                    var listIcon = children.kind === 'employees' ? cfg.icons.employee : (children.kind === 'branches' ?
                        cfg.icons.branch : cfg.icons.section);
                    html += '<div class="font-weight-bolder text-primary mb-2"><i class="' + listIcon + ' ml-2"></i>' +
                        esc(children.label || '') + '</div>';

                    if (!children.items || !children.items.length) {
                        html += '<div class="text-muted font-size-sm py-3 text-center border rounded bg-light">' + esc(
                            cfg.labels.noChildElements) + '</div>';
                    } else {
                        html += '<div class="border rounded p-2 bg-white">';
                        if (children.kind === 'employees') {
                            children.items.forEach(function(emp) {
                                html += employeeRow(emp);
                            });
                        } else {
                            children.items.forEach(function(item) {
                                html += renderItem(item, 0);
                            });
                        }
                        html += '</div>';
                    }
                }

                $body.html(html);
                hierarchyData = response;

                if (node.type && cfg.canUpdate[node.type]) {
                    $editBtn.removeClass('d-none').data('type', node.type).data('id', node.id);
                } else {
                    $editBtn.addClass('d-none').removeData('type').removeData('id');
                }

                $printBtn.removeClass('d-none').data('type', node.type).data('id', node.id);
            }

            function orgHierarchyPrintUrl(type, id) {
                var base = (cfg.routes && cfg.routes.print) ? cfg.routes.print : '';
                if (!base) {
                    return '#';
                }
                return base.replace(/\/$/, '') + '/' + encodeURIComponent(type) + '/' + encodeURIComponent(id);
            }

            function clearFieldErrors() {
                $('#orgEditName').removeClass('is-invalid');
                $('#orgEditNameError').addClass('d-none').text('');
                $('#orgEditParentSelect').removeClass('is-invalid');
                $('#orgEditParentError').addClass('d-none').text('');
                $('#orgEditGeneralError').addClass('d-none').text('');
            }

            function showSaveError(xhr) {
                var errors = xhr && xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : null;
                var generalMsg = null;

                if (errors) {
                    Object.keys(errors).forEach(function(field) {
                        var msg = Array.isArray(errors[field]) ? errors[field].join(' - ') : String(errors[
                            field]);
                        if (field === 'name') {
                            $('#orgEditName').addClass('is-invalid');
                            $('#orgEditNameError').removeClass('d-none').text(msg);
                        } else if (field === 'department_id' || field === 'section_id') {
                            $('#orgEditParentSelect').addClass('is-invalid');
                            $('#orgEditParentError').removeClass('d-none').text(msg);
                        } else {
                            generalMsg = msg;
                        }
                    });
                } else {
                    generalMsg = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : cfg
                        .labels.errorSaving;
                }

                if (generalMsg) {
                    $('#orgEditGeneralError').removeClass('d-none').text(generalMsg);
                    notify('error', generalMsg);
                }
            }

            function fillEditForm(response) {
                var node = response.node || {};
                $('#orgEditType').val(node.type);
                $('#orgEditNodeId').val(node.id);
                $('#orgEditNodeName').text(node.name || '');
                $('#orgEditName').val(node.name || '');
                clearFieldErrors();

                var parents = response.parents;
                var $group = $('#orgEditParentGroup');
                var $select = $('#orgEditParentSelect');

                if (parents && parents.field) {
                    $group.removeClass('d-none');
                    $('#orgEditParentLabel').text(parents.label || '');
                    $select.empty().attr('name', parents.field);
                    (parents.items || []).forEach(function(item) {
                        $select.append($('<option/>', {
                            value: item.id,
                            text: item.name
                        }));
                    });
                    if (node.parent_id) {
                        $select.val(String(node.parent_id));
                    }
                } else {
                    $group.addClass('d-none');
                    $select.empty().removeAttr('name');
                }
            }

            function requestEdit(type, id) {
                if (!type || !id) {
                    return;
                }
                if (!cfg.canUpdate[type]) {
                    notify('warning', cfg.labels.noPermission);
                    return;
                }

                $.ajax({
                        url: endpoint('editData', type, id),
                        type: 'GET',
                        dataType: 'json'
                    })
                    .done(function(response) {
                        if (!response || !response.ok) {
                            notify('error', cfg.labels.errorLoading);
                            return;
                        }
                        fillEditForm(response);
                        // تسلسل إلزامي: إغلاق نافذة الهيكلية أولاً ثم فتح نافذة التعديل بعد اكتمال الإغلاق (منع تراكب المودالات)
                        $modal.one('hidden.bs.modal', function() {
                            $editModal.modal('show');
                        });
                        $modal.modal('hide');
                    })
                    .fail(function() {
                        notify('error', cfg.labels.errorLoading);
                    });
            }

            function syncChartNodeTitle(type, id, newName) {
                if (!newName) {
                    return;
                }
                $('.org-node[data-node-type="' + type + '"][data-node-id="' + id + '"] .org-node-title').first().text(
                    newName);
            }

            // الضغط على أي عقدة بالمخطط (دائرة/قسم/شعبة) مع استثناء الأزرار والروابط داخل العقدة
            $(document).on('click', '.org-node[data-node-type][data-node-id]', function(e) {
                if ($(e.target).closest('button, a, select, input, textarea, label, .dropdown-menu').length) {
                    return;
                }
                openHierarchy($(this).attr('data-node-type'), $(this).attr('data-node-id'));
            });

            // الانتقال إلى العنصر الأب من داخل النافذة
            $(document).on('click', '#orgHierarchyBody .org-hier-parent-link', function() {
                openHierarchy($(this).attr('data-type'), $(this).attr('data-id'));
            });

            // زر تعديل العنصر المفتوح حالياً
            $editBtn.on('click', function() {
                requestEdit($(this).data('type'), $(this).data('id'));
            });

            // أزرار تعديل العناصر التابعة داخل النافذة
            $(document).on('click', '#orgHierarchyBody .hier-edit-btn', function() {
                requestEdit($(this).attr('data-type'), $(this).attr('data-id'));
            });

            // أزرار حذف العناصر التابعة (قسم/شعبة) داخل النافذة
            $(document).on('click', '#orgHierarchyBody .hier-delete-btn', function() {
                var type = $(this).attr('data-type');
                var id = $(this).attr('data-id');
                var name = $(this).attr('data-name') || '';
                if (!type || !id || !cfg.canDelete[type]) {
                    return;
                }

                if (!confirm(cfg.labels.confirmDelete + (name ? '\n' + name : ''))) {
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true);

                $.ajax({
                    url: cfg.routes.deleteUrl + '/' + encodeURIComponent(type) + '/' +
                        encodeURIComponent(id),
                    type: 'DELETE',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': cfg.csrfToken
                    }
                }).done(function(response) {
                    if (!response || !response.ok) {
                        notify('error', (response && response.message) ? response.message : cfg.labels
                            .errorSaving);
                        $btn.prop('disabled', false);
                        return;
                    }
                    notify('success', response.message || '');
                    // تحديث الصفحة بعد نجاح الحذف (العنصر المحذوف اختفى)
                    reloadPageSoon();
                }).fail(function(xhr) {
                    notify('error', (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr
                        .responseJSON.message : cfg.labels.errorSaving);
                    $btn.prop('disabled', false);
                });
            });

            // حفظ التعديل: PUT ثم إغلاق نافذة التعديل وإعادة فتح نافذة الهيكلية محدّثة (بدون تحديث الصفحة)
            $('#orgEditForm').on('submit', function(e) {
                e.preventDefault();

                var type = $('#orgEditType').val();
                var id = $('#orgEditNodeId').val();
                if (!type || !id) {
                    return;
                }

                clearFieldErrors();

                var payload = {
                    name: $('#orgEditName').val()
                };
                var parentField = $('#orgEditParentSelect').attr('name');
                if (parentField) {
                    payload[parentField] = $('#orgEditParentSelect').val();
                }

                var $btn = $('#orgEditSaveBtn');
                $btn.prop('disabled', true).addClass('spinner spinner-light spinner-right');

                $.ajax({
                    url: endpoint('updateUrl', type, id),
                    type: 'PUT',
                    data: payload,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': cfg.csrfToken
                    }
                }).done(function(response) {
                    if (!response || !response.ok) {
                        showSaveError(null);
                        return;
                    }
                    syncChartNodeTitle(type, id, response.node && response.node.name);
                    notify('success', response.message || '');
                    // تحديث الصفحة بعد نجاح حفظ التعديل (بدلاً من إعادة فتح نافذة الهيكلية)
                    reloadPageSoon();
                }).fail(showSaveError).always(function() {
                    $btn.prop('disabled', false).removeClass('spinner spinner-light spinner-right');
                });
            });

            // تلميح عند التمرير فوق العقد القابلة للضغط
            $('.org-node[data-node-type]').attr('title', cfg.labels.viewHint);

            // ===== الإضافة من داخل نافذة الهيكلية (قسم/شعبة/موظف) =====
            var $createModal = $('#orgCreateModal');

            function clearCreateErrors() {
                $('#orgCreateForm').find('.form-control').removeClass('is-invalid');
                $('#orgCreateErrors').addClass('d-none').text('');
            }

            function showCreateErrors(xhr) {
                var errors = xhr && xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : null;
                var messages = [];

                if (errors) {
                    Object.keys(errors).forEach(function(field) {
                        var msg = Array.isArray(errors[field]) ? errors[field].join(' - ') : String(errors[
                            field]);
                        var $input = $('#orgCreateForm').find('[name="' + field + '"]');
                        if ($input.length) {
                            $input.addClass('is-invalid');
                        }
                        messages.push(msg);
                    });
                } else {
                    messages.push((xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : cfg
                        .labels.errorSaving);
                }

                if (messages.length) {
                    $('#orgCreateErrors').removeClass('d-none').text(messages.join(' | '));
                    notify('error', messages[0]);
                }
            }

            function fillCreateForm(response) {
                clearCreateErrors();

                $('#orgCreateTarget').val(response.target);
                $('#orgCreateParentField').val(response.parent.field);
                $('#orgCreateParentValue').val(response.parent.value);

                // سيُضاف العنصر الجديد تحت هذه الوحدة مباشرة
                $('#orgCreateParentInfo').html('<i class="fas fa-level-down-alt ml-2 text-success"></i>' +
                    esc(cfg.labels.attachedTo) + ': <b>' + esc(response.parent.unit_name) + '</b>');

                $('#orgCreateName').val('');
                $('#orgCreateTitleText').text(
                    response.target === 'section' ? cfg.labels.addSection :
                    (response.target === 'branch' ? cfg.labels.addBranch : cfg.labels.addEmployee)
                );

                var isEmployee = response.target === 'employee';
                $('#orgCreateEmployeeFields').toggleClass('d-none', !isEmployee);
                $('#orgCreateNameField').toggleClass('d-none', isEmployee);

                if (isEmployee) {
                    // اختيار موظف من الموجودين بدل إنشاء موظف جديد
                    var $employee = $('#orgCreateEmployee').empty();
                    $employee.append($('<option/>', {
                        value: '',
                        text: cfg.labels.selectEmployee
                    }));
                    (response.employees || []).forEach(function(emp) {
                        $employee.append($('<option/>', {
                            value: emp.id,
                            text: emp.name
                        }));
                    });

                    // تحديث selectpicker (إعادة الحساب + بوكس البحث) بعد تعبئة الخيارات
                    if (typeof $.fn.selectpicker !== 'undefined') {
                        $employee.selectpicker('refresh');
                    }
                    $employee.removeClass('is-invalid');

                    // عرض التبعية الكاملة التي سيتلقاها الموظف بعد الاختيار
                    // (دائرة فقط / قسم+دائرة / شعبة+قسم+دائرة)
                    $('#orgCreateEmployeePath').html(buildEmployeePath(response));
                }
            }

            /**
             * بناء نص التبعية الكاملة للموظف من كيان الوحدة المفتوحة:
             * شعبة => شعبة + قسم + دائرة، قسم => قسم + دائرة، دائرة => الدائرة فقط.
             */
            function buildEmployeePath(response) {
                var parts = [];
                var unitType = (response.parent && (response.parent.unit_type || response.parent.type)) || '';
                var unitName = response.parent ? response.parent.unit_name : '';

                if (unitType === 'branch') {
                    var branch = (response.assignment && response.assignment.branch) || null;
                    var section = (response.assignment && response.assignment.section) || null;
                    var department = (response.assignment && response.assignment.department) || null;
                    if (branch) {
                        parts.push(cfg.typeLabels.branch + ': <b>' + esc(branch.name) + '</b>');
                    }
                    if (section) {
                        parts.push(cfg.typeLabels.section + ': <b>' + esc(section.name) + '</b>');
                    }
                    if (department) {
                        parts.push(cfg.typeLabels.department + ': <b>' + esc(department.name) + '</b>');
                    }
                } else if (unitType === 'section') {
                    var sSection = (response.assignment && response.assignment.section) || null;
                    var sDepartment = (response.assignment && response.assignment.department) || null;
                    if (sSection) {
                        parts.push(cfg.typeLabels.section + ': <b>' + esc(sSection.name) + '</b>');
                    }
                    if (sDepartment) {
                        parts.push(cfg.typeLabels.department + ': <b>' + esc(sDepartment.name) + '</b>');
                    }
                } else {
                    parts.push(cfg.typeLabels.department + ': <b>' + esc(unitName) + '</b>');
                }

                return parts.length ?
                    '<i class="fas fa-sitemap ml-1 text-info"></i>' + parts.join(
                        ' <span class="mx-1 text-muted">←</span> ') :
                    esc(cfg.labels.attachedTo) + ': <b>' + esc(unitName) + '</b>';
            }

            function requestCreate(target, unitType, unitId) {
                if (!target) {
                    return;
                }
                if (!cfg.canStore[target]) {
                    notify('warning', cfg.labels.noPermission);
                    return;
                }

                // الوحدة المرجعية: المُمرَّرة صراحةً (أزرار الشجرة) أو العقدة المفتوحة حالياً
                var ut = unitType || current.type;
                var uid = unitId || current.id;
                if (!ut || !uid) {
                    return;
                }

                $.ajax({
                    url: cfg.routes.createData + '/' + encodeURIComponent(target) + '/' + encodeURIComponent(
                        ut) + '/' + encodeURIComponent(uid),
                    type: 'GET',
                    dataType: 'json'
                }).done(function(response) {
                    if (!response || !response.ok) {
                        notify('error', cfg.labels.errorLoading);
                        return;
                    }
                    fillCreateForm(response);
                    // تسلسل إلزامي: إغلاق نافذة الهيكلية أولاً ثم فتح نافذة الإضافة بعد اكتمال الإغلاق
                    $modal.one('hidden.bs.modal', function() {
                        $createModal.modal('show');
                    });
                    $modal.modal('hide');
                    // عند إغلاق نافذة الإضافة (حفظاً أو إلغاءً) نعود لنافذة الهيكلية محدّثة
                    $createModal.one('hidden.bs.modal', function() {
                        if (current.type && current.id) {
                            openHierarchy(current.type, current.id);
                        }
                    });
                }).fail(function() {
                    notify('error', cfg.labels.errorLoading);
                });
            }

            // إضافة شعبة من مستوى الدائرة مباشرة: اختيار القسم من أقسام الدائرة المعروضة حالياً بالشجرة
            function openCreateBranchForDepartment() {
                if (!cfg.canStore.branch) {
                    notify('warning', cfg.labels.noPermission);
                    return;
                }

                var sections = [];
                if (hierarchyData && hierarchyData.children && hierarchyData.children.kind === 'tree') {
                    (hierarchyData.children.items || []).forEach(function(s) {
                        if (s && s.id != null) {
                            sections.push({
                                id: s.id,
                                name: s.name
                            });
                        }
                    });
                }

                if (!sections.length) {
                    notify('warning', cfg.labels.branchNeedsSection);
                    return;
                }

                clearCreateErrors();
                $('#orgCreateTarget').val('branch');
                $('#orgCreateParentField').val('section_id');
                $('#orgCreateParentValue').val(sections[0].id);

                var options = '';
                sections.forEach(function(s) {
                    options += '<option value="' + esc(s.id) + '">' + esc(s.name) + '</option>';
                });

                $('#orgCreateParentInfo').html('<i class="fas fa-level-down-alt ml-2 text-success"></i>' +
                    esc(cfg.labels.chooseSection) +
                    ': <select id="orgCreateSectionSelect" class="form-control form-control-solid d-inline-block w-auto mr-2">' +
                    options + '</select>');

                $('#orgCreateName').val('');
                $('#orgCreateTitleText').text(cfg.labels.addBranch);
                $('#orgCreateEmployeeFields').addClass('d-none');
                $('#orgCreateNameField').removeClass('d-none');

                // مزامنة الحقل المخفي مع تغيّر اختيار القسم
                $('#orgCreateSectionSelect').on('change', function() {
                    $('#orgCreateParentValue').val($(this).val());
                });

                $modal.one('hidden.bs.modal', function() {
                    $createModal.modal('show');
                });
                $modal.modal('hide');
                $createModal.one('hidden.bs.modal', function() {
                    if (current.type && current.id) {
                        openHierarchy(current.type, current.id);
                    }
                });
            }

            // أزرار الإضافة داخل نافذة الهيكلية
            $(document).on('click', '#orgHierarchyBody .org-create-btn', function() {
                var target = $(this).attr('data-target');
                if (target === 'branch' && current.type === 'department') {
                    openCreateBranchForDepartment();
                    return;
                }
                requestCreate(target);
            });

            // أزرار الإضافة السريعة بجانب عناصر الشجرة (مثل: شعبة تحت قسم محدد)
            $(document).on('click', '#orgHierarchyBody .org-create-inline-btn', function() {
                requestCreate($(this).attr('data-target'), $(this).attr('data-unit-type'), $(this).attr(
                    'data-unit-id'));
            });

            // النقر على اسم عنصر تابع يفتح نافذته الخاصة (كل وحدة على حدة كما هي)
            $(document).on('click', '#orgHierarchyBody .hier-open-link', function() {
                openHierarchy($(this).attr('data-type'), $(this).attr('data-id'));
            });

            // حفظ الإضافة: POST ثم العودة لنافذة الهيكلية محدّثة بالعنصر الجديد (بدون تحديث الصفحة)
            $('#orgCreateForm').on('submit', function(e) {
                e.preventDefault();

                var target = $('#orgCreateTarget').val();
                if (!target) {
                    return;
                }

                clearCreateErrors();

                var payload = {};
                var parentField = $('#orgCreateParentField').val();
                if (parentField) {
                    payload[parentField] = $('#orgCreateParentValue').val();
                }

                if (target === 'employee') {
                    // إلحاق موظف موجود بالوحدة المختارة (التبعية الكاملة تُحسب على الخادم)
                    payload.employee_id = $('#orgCreateEmployee').val();
                    if (!payload.employee_id) {
                        $('#orgCreateErrors').removeClass('d-none').text(cfg.labels.selectEmployee);
                        notify('error', cfg.labels.selectEmployee);
                        $('#orgCreateEmployee').addClass('is-invalid');
                        return;
                    }
                } else {
                    payload.name = $('#orgCreateName').val();
                }

                // عند إضافة شعبة من مستوى الدائرة: القسم يُقرأ من قائمة الاختيار الظاهرة
                if (target === 'branch') {
                    var $sectionSelect = $('#orgCreateSectionSelect');
                    if ($sectionSelect.length) {
                        payload.section_id = $sectionSelect.val();
                    }
                    if (!payload.section_id) {
                        $('#orgCreateErrors').removeClass('d-none').text(cfg.labels.chooseSection);
                        notify('error', cfg.labels.chooseSection);
                        return;
                    }
                }

                var $btn = $('#orgCreateSaveBtn');
                $btn.prop('disabled', true).addClass('spinner spinner-white spinner-right');

                $.ajax({
                    url: cfg.routes.storeUrl + '/' + encodeURIComponent(target),
                    type: 'POST',
                    data: payload,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': cfg.csrfToken
                    }
                }).done(function(response) {
                    if (!response || !response.ok) {
                        showCreateErrors(null);
                        return;
                    }
                    notify('success', response.message || '');
                    // تحديث الصفحة بعد نجاح الإضافة (بدلاً من إعادة فتح نافذة الهيكلية)
                    reloadPageSoon();
                }).fail(showCreateErrors).always(function() {
                    $btn.prop('disabled', false).removeClass('spinner spinner-white spinner-right');
                });
            });

            // ===== تعيين/تغيير المدير (دائرة/قسم/شعبة) من داخل نافذة الهيكلية =====
            var $directorModal = $('#orgDirectorModal');
            var directorAssignments = {};

            function clearDirectorErrors() {
                $('#orgDirectorEmployee').removeClass('is-invalid');
                $('#orgDirectorError').addClass('d-none').text('');
            }

            function showDirectorErrors(xhr) {
                var errors = xhr && xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : null;
                var msgs = [];

                if (errors) {
                    Object.keys(errors).forEach(function(field) {
                        var msg = Array.isArray(errors[field]) ? errors[field].join(' - ') : String(errors[
                            field]);
                        if (field === 'employee_id') {
                            $('#orgDirectorEmployee').addClass('is-invalid');
                        }
                        msgs.push(msg);
                    });
                } else {
                    msgs.push((xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : cfg
                        .labels.errorSaving);
                }

                if (msgs.length) {
                    $('#orgDirectorError').removeClass('d-none').text(msgs.join(' | '));
                    notify('error', msgs[0]);
                }
            }

            function fillDirectorForm(response) {
                clearDirectorErrors();

                $('#orgDirectorType').val(response.node.type);
                $('#orgDirectorUnitId').val(response.node.id);
                directorAssignments = {
                    permanent: response.current,
                    acting: response.acting
                };
                $('#orgDirectorTitleText').text(response.current ? cfg.labels.changeDirector : (cfg.directorLabels[
                    response.node.type] || ''));

                // المدير الحالي (إن وجد) وإلا إشارة إلى عدم وجود مدير
                $('#orgDirectorCurrentInfo').html('<i class="fas fa-user-tie ml-2 text-warning"></i>' +
                    esc(cfg.labels.currentDirector) + ': <b>' +
                    esc(response.current ? response.current.employee_name : cfg.labels.undefinedValue) +
                    '</b> | ' + esc(cfg.labels.actingDirector || '{{ __('acting') }}') + ': <b>' +
                    esc(response.acting ? response.acting.employee_name : cfg.labels.undefinedValue) + '</b>');

                var $sel = $('#orgDirectorEmployee').empty();
                $sel.append($('<option/>', {
                    value: '',
                    text: cfg.labels.selectEmployee
                }));
                (response.employees || []).forEach(function(emp) {
                    $sel.append($('<option/>', {
                        value: emp.id,
                        text: emp.name
                    }));
                });
                if (response.current) {
                    $sel.val(String(response.current.employee_id));
                }
                if (typeof $.fn.selectpicker !== 'undefined') {
                    $sel.selectpicker('refresh');
                }
                $('#orgDirectorTypeSelect').val(response.current ? 'permanent' : (response.acting ? 'acting' :
                    'permanent'));
                $('#orgDirectorConvertExisting').prop('checked', false);
                $('#orgDirectorConvertedEmployee').val('');
                updateDirectorDeleteButton();
                updateDirectorConvertOption();
            }

            function updateDirectorDeleteButton() {
                var assignment = directorAssignments[$('#orgDirectorTypeSelect').val()];
                var canDelete = cfg.canDeleteDirector[$('#orgDirectorType').val()];
                $('#orgDirectorDeleteBtn').toggleClass('d-none', !assignment || !canDelete);
            }

            function updateDirectorConvertOption() {
                var selectedType = $('#orgDirectorTypeSelect').val();
                var targetMissing = !directorAssignments[selectedType];
                var oppositeType = selectedType === 'permanent' ? 'acting' : 'permanent';
                var canConvert = targetMissing && !!directorAssignments[oppositeType];
                $('#orgDirectorConvertGroup').toggleClass('d-none', !canConvert);
                if (!canConvert) {
                    $('#orgDirectorConvertExisting').prop('checked', false);
                }
                syncConvertedEmployee();
            }

            function syncConvertedEmployee() {
                var selectedType = $('#orgDirectorTypeSelect').val();
                var oppositeType = selectedType === 'permanent' ? 'acting' : 'permanent';
                var isConverting = $('#orgDirectorConvertExisting').is(':checked');
                var previousAssignment = directorAssignments[oppositeType];
                var $employee = $('#orgDirectorEmployee');

                if (isConverting && previousAssignment) {
                    var employeeId = String(previousAssignment.employee_id);
                    $employee.val(employeeId).prop('disabled', true);
                    $('#orgDirectorConvertedEmployee').val(employeeId);
                } else {
                    $employee.prop('disabled', false);
                    $('#orgDirectorConvertedEmployee').val('');
                    var currentAssignment = directorAssignments[selectedType];
                    $employee.val(currentAssignment ? String(currentAssignment.employee_id) : '');
                }

                if (typeof $.fn.selectpicker !== 'undefined') {
                    $employee.selectpicker('refresh');
                }
            }

            $('#orgDirectorTypeSelect').on('change', function() {
                var assignment = directorAssignments[$(this).val()];
                $('#orgDirectorEmployee').val(assignment ? String(assignment.employee_id) : '');
                updateDirectorDeleteButton();
                updateDirectorConvertOption();
                if (typeof $.fn.selectpicker !== 'undefined') {
                    $('#orgDirectorEmployee').selectpicker('refresh');
                }
            });

            $('#orgDirectorConvertExisting').on('change', function() {
                syncConvertedEmployee();
            });

            function requestDirector() {
                if (!current.type || !current.id) {
                    return;
                }
                if (!cfg.canDirector[current.type]) {
                    notify('warning', cfg.labels.noPermission);
                    return;
                }

                $.ajax({
                    url: cfg.routes.directorData + '/' + encodeURIComponent(current.type) + '/' +
                        encodeURIComponent(current.id),
                    type: 'GET',
                    dataType: 'json'
                }).done(function(response) {
                    if (!response || !response.ok) {
                        notify('error', cfg.labels.errorLoading);
                        return;
                    }
                    fillDirectorForm(response);
                    // تسلسل إلزامي: إغلاق نافذة الهيكلية أولاً ثم فتح نافذة المدير بعد اكتمال الإغلاق
                    $modal.one('hidden.bs.modal', function() {
                        $directorModal.modal('show');
                    });
                    $modal.modal('hide');
                    // عند إغلاق نافذة المدير (حفظاً أو إلغاءً) نعود لنافذة الهيكلية محدّثة
                    $directorModal.one('hidden.bs.modal', function() {
                        if (current.type && current.id) {
                            openHierarchy(current.type, current.id);
                        }
                    });
                }).fail(function() {
                    notify('error', cfg.labels.errorLoading);
                });
            }

            // زر تعيين/تغيير المدير داخل صف المدير
            $(document).on('click', '#orgHierarchyBody .org-director-btn', function() {
                requestDirector();
            });

            // إعادة حساب عرض selectpicker بعد ظهور النافذة (ضروري للعرض داخل المودالات)
            $directorModal.on('shown.bs.modal', function() {
                if (typeof $.fn.selectpicker !== 'undefined') {
                    $('#orgDirectorEmployee').selectpicker('refresh');
                }
            });

            // إعادة حساب عرض selectpicker لنافذة الإضافة بعد ظهورها
            // (ضروري لعرض بوكس البحث بعرض صحيح داخل المودال)
            $createModal.on('shown.bs.modal', function() {
                if (typeof $.fn.selectpicker !== 'undefined') {
                    $('#orgCreateEmployee').selectpicker('refresh');
                }
            });

            // إزالة حالة الخطأ عند اختيار موظف من القائمة
            $('#orgCreateEmployee').on('change', function() {
                $(this).removeClass('is-invalid');
            });

            // حفظ المدير: POST ثم العودة لنافذة الهيكلية محدّثة بالمدير الجديد (بدون تحديث الصفحة)
            $('#orgDirectorForm').on('submit', function(e) {
                e.preventDefault();

                var type = $('#orgDirectorType').val();
                var unitId = $('#orgDirectorUnitId').val();
                if (!type || !unitId) {
                    return;
                }

                clearDirectorErrors();

                var $btn = $('#orgDirectorSaveBtn');
                $btn.prop('disabled', true).addClass('spinner spinner-white spinner-right');

                $.ajax({
                    url: cfg.routes.storeDirectorUrl + '/' + encodeURIComponent(type) + '/' +
                        encodeURIComponent(unitId),
                    type: 'POST',
                    data: {
                        employee_id: $('#orgDirectorConvertExisting').is(':checked') ?
                            $('#orgDirectorConvertedEmployee').val() : $('#orgDirectorEmployee').val(),
                        director_type: $('#orgDirectorTypeSelect').val(),
                        convert_existing: $('#orgDirectorConvertExisting').is(':checked') ? 1 : 0
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': cfg.csrfToken
                    }
                }).done(function(response) {
                    if (!response || !response.ok) {
                        showDirectorErrors(null);
                        return;
                    }
                    notify('success', response.message || '');
                    // تحديث الصفحة بعد نجاح تعيين/تغيير المدير
                    reloadPageSoon();
                }).fail(showDirectorErrors).always(function() {
                    $btn.prop('disabled', false).removeClass('spinner spinner-white spinner-right');
                });
            });

            $('#orgDirectorDeleteBtn').on('click', function() {
                var type = $('#orgDirectorType').val();
                var unitId = $('#orgDirectorUnitId').val();
                var directorType = $('#orgDirectorTypeSelect').val();
                if (!type || !unitId || !directorAssignments[directorType]) {
                    return;
                }

                if (!confirm('{{ __('confirm_message') }}')) {
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).addClass('spinner spinner-dark spinner-right');

                $.ajax({
                    url: cfg.routes.deleteDirectorUrl + '/' + encodeURIComponent(type) + '/' +
                        encodeURIComponent(unitId),
                    type: 'DELETE',
                    data: {
                        director_type: directorType
                    },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': cfg.csrfToken
                    }
                }).done(function(response) {
                    notify('success', response.message || '');
                    // تحديث الصفحة بعد نجاح حذف المدير
                    reloadPageSoon();
                }).fail(showDirectorErrors).always(function() {
                    $btn.prop('disabled', false).removeClass('spinner spinner-dark spinner-right');
                });
            });

            window.ORG_UI = {
                openHierarchy: openHierarchy
            };
        })();
    </script>
@endsection
