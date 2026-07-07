<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'CAF') . ' Dashboard')</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816' },
                        gold: { 50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600' }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
        @keyframes toastIn { from { opacity:0; transform:translateX(100%); } to { opacity:1; transform:translateX(0); } }
        @keyframes toastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(100%); } }
        @keyframes ajaxProgress { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
        .animate-fade { animation: fadeIn 0.3s ease-out both; }
        .toast-in { animation: toastIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
        .toast-out { animation: toastOut 0.3s ease-in both; }
        .ajax-loader { position:fixed; top:0; left:0; right:0; height:3px; background: linear-gradient(90deg, #024938, #f9ac00, #024938); background-size: 200% 100%; animation: ajaxProgress 1s linear infinite; z-index:9999; display:none; }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { background: rgba(255,255,255,0.06); }
        .sidebar-link.active { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .sidebar-submenu.open { max-height: 500px; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #01241f; }
        ::-webkit-scrollbar-thumb { background: #024938; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #f9ac00; }
    </style>
</head>
<body class="font-['Nunito',sans-serif] antialiased bg-gray-50 text-slate-800">

    {{-- AJAX Progress Bar --}}
    <div id="ajaxLoader" class="ajax-loader"></div>

    {{-- Toast Container --}}
    <div id="toastContainer" class="fixed top-5 right-5 z-[60] flex flex-col gap-3 w-full max-w-sm pointer-events-none"></div>

    {{-- Mobile Overlay --}}
    <div id="mobileOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="adminSidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-emerald-900 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
        {{-- Brand --}}
        <div class="h-16 flex items-center px-6 border-b border-emerald-800/50 flex-shrink-0">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16V8a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2zM3 10h18M8 14h2"/></svg>
            </div>
            <span class="ml-2 text-white font-bold text-sm tracking-wide">{{ strtoupper(config('app.name', 'CAF')) }}</span>
        </div>

        {{-- Menu --}}
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            {{-- Dashboard (all staff) --}}
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Dashboard</span>
            </a>

            @can('files.view')
            {{-- Shipments (Files) --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-files')" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.files*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    <span>Shipments</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" id="arrow-files" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-files" class="sidebar-submenu pl-11 space-y-0.5 {{ request()->routeIs('admin.files*') ? 'open' : '' }}">
                    <a href="{{ route('admin.files.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">All Files</a>
                    @can('files.create')
                    <a href="{{ route('admin.files.create') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Open New File</a>
                    @endcan
                </div>
            </div>
            @endcan

            @can('declarations.view')
            {{-- Customs --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-customs')" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.declarations*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Customs</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" id="arrow-customs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-customs" class="sidebar-submenu pl-11 space-y-0.5 {{ request()->routeIs('admin.declarations*') || request()->routeIs('admin.hs-codes*') ? 'open' : '' }}">
                    <a href="{{ route('admin.declarations.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Declarations (TANSAD)</a>
                    @can('hscodes.view')
                    <a href="{{ route('admin.hs-codes.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Tariff / HS Codes</a>
                    @endcan
                </div>
            </div>
            @endcan

            @can('documents.view')
            {{-- Documents --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-documents')" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.documents*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Documents</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" id="arrow-documents" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-documents" class="sidebar-submenu pl-11 space-y-0.5 {{ request()->routeIs('admin.documents*') ? 'open' : '' }}">
                    <a href="{{ route('admin.documents.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Document Register</a>
                    @can('documents.upload')
                    <a href="{{ route('admin.documents.create') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Upload Document</a>
                    @endcan
                </div>
            </div>
            @endcan

            @can('transport.view')
            {{-- Transport --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-transport')" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.transport*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>
                    <span>Transport</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" id="arrow-transport" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-transport" class="sidebar-submenu pl-11 space-y-0.5 {{ request()->routeIs('admin.transport*') ? 'open' : '' }}">
                    <a href="{{ route('admin.transport.trips.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Trips / Deliveries</a>
                    <a href="{{ route('admin.transport.trucks.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Trucks</a>
                    <a href="{{ route('admin.transport.drivers.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Drivers</a>
                    <a href="{{ route('admin.transport.transporters.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Transporters</a>
                </div>
            </div>
            @endcan

            @can('insurance.view')
            {{-- Insurance (Line 2) --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-insurance')" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.insurance*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/></svg>
                    <span>Insurance</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" id="arrow-insurance" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-insurance" class="sidebar-submenu pl-11 space-y-0.5 {{ request()->routeIs('admin.insurance*') ? 'open' : '' }}">
                    <a href="{{ route('admin.insurance.policies.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Policies</a>
                    <a href="{{ route('admin.insurance.claims.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Claims</a>
                    <a href="{{ route('admin.insurance.insurers.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Insurers</a>
                </div>
            </div>
            @endcan

            @can('finance.view')
            {{-- Finance --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-finance')" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.finance*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Finance</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" id="arrow-finance" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-finance" class="sidebar-submenu pl-11 space-y-0.5 {{ request()->routeIs('admin.invoices*') || request()->routeIs('admin.receipts*') || request()->routeIs('admin.disbursements*') || request()->routeIs('admin.petty-cash*') || request()->routeIs('admin.expenses*') || request()->routeIs('admin.charges*') ? 'open' : '' }}">
                    @can('invoices.view')
                    <a href="{{ route('admin.invoices.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Invoices</a>
                    @endcan
                    @can('receipts.view')
                    <a href="{{ route('admin.receipts.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Receipts / Payments</a>
                    @endcan
                    @can('disbursements.view')
                    <a href="{{ route('admin.disbursements.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Disbursements</a>
                    @endcan
                    @can('pettycash.view')
                    <a href="{{ route('admin.petty-cash.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Petty Cash</a>
                    @endcan
                    @can('expenses.view')
                    <a href="{{ route('admin.expenses.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Expenses</a>
                    @endcan
                    @can('charges.view')
                    <a href="{{ route('admin.charges.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Charge Items</a>
                    @endcan
                    @can('reports.finance')
                    <a href="{{ route('admin.reports.debtors') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Debtors (Aging)</a>
                    @endcan
                </div>
            </div>
            @endcan

            @can('clients.view')
            {{-- Clients --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-clients')" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.clients*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Clients</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" id="arrow-clients" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-clients" class="sidebar-submenu pl-11 space-y-0.5 {{ request()->routeIs('admin.clients*') ? 'open' : '' }}">
                    <a href="{{ route('admin.clients.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">All Clients</a>
                    @can('clients.create')
                    <a href="{{ route('admin.clients.create') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Add Client</a>
                    @endcan
                </div>
            </div>
            @endcan

            @can('partners.view')
            {{-- Partners --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-partners')" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.partners*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Partners</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" id="arrow-partners" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-partners" class="sidebar-submenu pl-11 space-y-0.5 {{ request()->routeIs('admin.partners*') ? 'open' : '' }}">
                    <a href="{{ route('admin.partners.lines.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Shipping Lines</a>
                    <a href="{{ route('admin.partners.terminals.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">ICDs / Terminals</a>
                    <a href="{{ route('admin.partners.agencies.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Government Agencies</a>
                </div>
            </div>
            @endcan

            @can('reports.view')
            {{-- Reports --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-reports')" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Reports</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" id="arrow-reports" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-reports" class="sidebar-submenu pl-11 space-y-0.5 {{ request()->routeIs('admin.reports*') ? 'open' : '' }}">
                    <a href="{{ route('admin.reports.operations') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Operations Report</a>
                    <a href="{{ route('admin.reports.revenue') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Revenue Report</a>
                    <a href="{{ route('admin.reports.disbursements') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Disbursement Report</a>
                    <a href="{{ route('admin.reports.debtors') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Debtors (Aging)</a>
                    <a href="{{ route('admin.reports.officer-performance') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Officer Performance</a>
                    <a href="{{ route('admin.reports.insurance') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Insurance Report</a>
                </div>
            </div>
            @endcan

            {{-- Notifications --}}
            <a href="#" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>Notifications &amp; SMS Log</span>
            </a>

            @can('settings.manage')
            {{-- Administration --}}
            <div class="sidebar-group">
                <button onclick="toggleMenu('menu-admin')" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs('admin.settings*') || request()->routeIs('admin.users*') || request()->routeIs('admin.roles*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Administration</span>
                    <svg class="w-4 h-4 ml-auto transition-transform" id="arrow-admin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="menu-admin" class="sidebar-submenu pl-11 space-y-0.5 {{ request()->routeIs('admin.settings*') ? 'open' : '' }}">
                    @can('users.manage')
                    <a href="{{ route('admin.settings.users.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Users</a>
                    @endcan
                    @can('roles.manage')
                    <a href="{{ route('admin.settings.roles.index') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Roles &amp; Permissions</a>
                    @endcan
                    @can('settings.manage')
                    <a href="{{ route('admin.settings.company') }}" class="block py-1.5 text-xs text-emerald-200/70 hover:text-white transition-colors">Company Settings</a>
                    @endcan
                </div>
            </div>
            @endcan

        </div>

        {{-- Bottom User --}}
        <div class="p-4 border-t border-emerald-800/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'Admin User' }}</p>
                    <p class="text-xs text-emerald-300/60">{{ Auth::user()->role_name ?? 'Staff' }}</p>
                </div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout').submit();" class="text-emerald-300/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </a>
                <form id="admin-logout" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">

        {{-- Header --}}
        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-bold text-gray-800">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3 sm:gap-4">
                {{-- Global Search --}}
                <div class="hidden md:flex items-center bg-gray-50 rounded-xl px-3 py-2 border border-gray-200 focus-within:border-emerald-300 focus-within:ring-2 focus-within:ring-emerald-100 transition-all">
                    <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="globalSearch" placeholder="Search File No / BL / TANSAD..." class="bg-transparent text-sm outline-none w-56 text-gray-700 placeholder-gray-400">
                </div>

                {{-- Notifications --}}
                <div class="relative" id="notifContainer">
                    <button onclick="toggleNotifications()" class="relative p-2 rounded-xl hover:bg-gray-100 text-gray-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-1 right-1 w-4 h-4 bg-gold-400 rounded-full text-[8px] font-bold text-white flex items-center justify-center shadow-sm">0</span>
                    </button>

                    {{-- Notification Dropdown --}}
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50 origin-top-right transition-all">
                        <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
                            <h4 class="text-sm font-bold text-gray-900">Notifications</h4>
                            <button onclick="markAllRead()" class="text-[11px] text-emerald-600 font-medium hover:text-emerald-700">Mark all read</button>
                        </div>
                        <div class="max-h-72 overflow-y-auto" id="notifList">
                            <div class="px-4 py-8 text-center">
                                <div class="w-12 h-12 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-2">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                </div>
                                <p class="text-xs text-gray-400">No notifications yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 p-6 animate-fade">
            @yield('content')
        </main>

    </div>

    {{-- Toast System --}}
    <script>
    (function() {
        const container = document.getElementById('toastContainer');

        function showToast(type, title, message) {
            const toast = document.createElement('div');
            toast.className = 'toast-in pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-lg border backdrop-blur-sm';

            let iconSvg, bgClass, borderClass;
            if (type === 'success') {
                iconSvg = '<svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                bgClass = 'bg-emerald-50/95'; borderClass = 'border-emerald-200';
            } else if (type === 'error') {
                iconSvg = '<svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                bgClass = 'bg-red-50/95'; borderClass = 'border-red-200';
            } else if (type === 'warning') {
                iconSvg = '<svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
                bgClass = 'bg-amber-50/95'; borderClass = 'border-amber-200';
            } else {
                iconSvg = '<svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                bgClass = 'bg-blue-50/95'; borderClass = 'border-blue-200';
            }

            toast.classList.add(...bgClass.split(' '), ...borderClass.split(' '));
            toast.innerHTML = iconSvg +
                '<div class="flex-1 min-w-0">' +
                    '<p class="text-sm font-semibold text-gray-800">' + title + '</p>' +
                    (message ? '<p class="text-sm text-gray-500 mt-0.5">' + message + '</p>' : '') +
                '</div>' +
                '<button onclick="this.parentElement.classList.add(\'toast-out\'); setTimeout(()=>this.parentElement.remove(), 300)" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>' +
                '</button>';

            container.appendChild(toast);
            setTimeout(() => { toast.classList.add('toast-out'); setTimeout(() => toast.remove(), 300); }, 5000);
        }

        window.showToast = showToast;

        @if(session('status'))
            showToast('success', 'Success', '{{ session('status') }}');
        @endif
        @if(session('error'))
            showToast('error', 'Error', '{{ session('error') }}');
        @endif
        @if(session('warning'))
            showToast('warning', 'Warning', '{{ session('warning') }}');
        @endif
        @if(session('info'))
            showToast('info', 'Info', '{{ session('info') }}');
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast('error', 'Validation Error', '{{ $error }}');
            @endforeach
        @endif
    })();
    </script>

    {{-- Sidebar & Notification Scripts --}}
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('mobileOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            const arrow = document.getElementById('arrow-' + id.replace('menu-', ''));
            menu.classList.toggle('open');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
        function toggleNotifications() {
            document.getElementById('notifDropdown').classList.toggle('hidden');
        }
        function markAllRead() {
            const badge = document.querySelector('#notifContainer .bg-gold-400');
            if (badge) badge.style.display = 'none';
        }
        document.addEventListener('click', function(e) {
            const container = document.getElementById('notifContainer');
            if (container && !container.contains(e.target)) {
                document.getElementById('notifDropdown')?.classList.add('hidden');
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // ── Global SweetAlert2 helpers ──
    const csrfToken = '{{ csrf_token() }}';

    // Confirm delete with SweetAlert2 — returns Promise<boolean>
    function swalConfirm(opts) {
        const defaults = {
            title: 'Are you sure?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl',
                confirmButton: 'rounded-lg font-bold text-sm px-5 py-2.5',
                cancelButton: 'rounded-lg font-medium text-sm px-5 py-2.5'
            }
        };
        return Swal.fire({ ...defaults, ...opts }).then(r => r.isConfirmed);
    }

    // Confirm action (non-delete) with SweetAlert2
    function swalConfirmAction(opts) {
        const defaults = {
            title: 'Confirm Action',
            text: 'Are you sure you want to proceed?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#024938',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl',
                confirmButton: 'rounded-lg font-bold text-sm px-5 py-2.5',
                cancelButton: 'rounded-lg font-medium text-sm px-5 py-2.5'
            }
        };
        return Swal.fire({ ...defaults, ...opts }).then(r => r.isConfirmed);
    }

    // Delete via AJAX with SweetAlert2 confirmation + success animation
    function confirmDelete(url, opts) {
        const cfg = {
            title: 'Delete?',
            text: 'Are you sure you want to delete this item?',
            successMsg: 'Deleted successfully!',
            errorMsg: 'Could not delete item.',
            redirect: null,
            ...opts
        };
        swalConfirm({ title: cfg.title, text: cfg.text }).then(confirmed => {
            if (!confirmed) return;
            Swal.fire({
                title: 'Deleting...',
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false,
                showConfirmButton: false
            });
            $.ajax({
                url: url,
                type: 'POST',
                data: { _method: 'DELETE', _token: csrfToken },
                success: function(r) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: r.message || cfg.successMsg,
                        icon: 'success',
                        timer: 1200,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-2xl' }
                    });
                    setTimeout(() => {
                        if (cfg.redirect) window.location.href = cfg.redirect;
                        else location.reload();
                    }, 1000);
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: cfg.errorMsg,
                        icon: 'error',
                        confirmButtonColor: '#dc2626',
                        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-lg font-bold text-sm px-5 py-2.5' }
                    });
                }
            });
        });
    }

    // Confirm action via AJAX with SweetAlert2 (for approve, close, convert, void, etc.)
    function confirmAction(url, opts) {
        const cfg = {
            title: 'Confirm',
            text: 'Are you sure?',
            method: 'POST',
            data: {},
            successMsg: 'Action completed successfully!',
            errorMsg: 'Action failed.',
            redirect: null,
            ...opts
        };
        swalConfirmAction({ title: cfg.title, text: cfg.text }).then(confirmed => {
            if (!confirmed) return;
            Swal.fire({
                title: 'Processing...',
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false,
                showConfirmButton: false
            });
            const ajaxData = { _token: csrfToken, ...cfg.data };
            if (cfg.method === 'DELETE') ajaxData._method = 'DELETE';
            $.ajax({
                url: url,
                type: 'POST',
                data: ajaxData,
                success: function(r) {
                    Swal.fire({
                        title: 'Success!',
                        text: r.message || cfg.successMsg,
                        icon: 'success',
                        timer: 1200,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-2xl' }
                    });
                    setTimeout(() => {
                        if (cfg.redirect) window.location.href = cfg.redirect;
                        else location.reload();
                    }, 1000);
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: cfg.errorMsg,
                        icon: 'error',
                        confirmButtonColor: '#dc2626',
                        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-lg font-bold text-sm px-5 py-2.5' }
                    });
                }
            });
        });
    }
    </script>
    @include('partials.demo-notice')
    @stack('scripts')
</body>
</html>
