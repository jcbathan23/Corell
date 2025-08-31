<?php
require_once 'auth.php';
requireAdmin(); // Only admin users can access this page
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
  <title>Service Network & Route Planner | CORE II</title>
  <style>
    :root {
      --sidebar-width: 280px;
      --primary-color: #4e73df;
      --secondary-color: #f8f9fc;
      --dark-bg: #1a1a2e;
      --dark-card: #16213e;
      --text-light: #f8f9fa;
      --text-dark: #212529;
      --success-color: #1cc88a;
      --info-color: #36b9cc;
      --warning-color: #f6c23e;
      --border-radius: 0.75rem;
      --shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }



    body {
      font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
      overflow-x: hidden;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      color: var(--text-dark);
      transition: all 0.3s;
      min-height: 100vh;
    }

    body.dark-mode { background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%); color: var(--text-light); }

    /* Modern Sidebar */
    .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; left: 0; top: 0; background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%); color: white; padding: 0; transition: all 0.3s ease; z-index: 1000; transform: translateX(0); box-shadow: 4px 0 20px rgba(0,0,0,0.1); backdrop-filter: blur(10px); }

    .sidebar.collapsed {
      transform: translateX(-100%);
    }

    .sidebar .logo { padding: 2rem 1.5rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); }

    .sidebar .logo img {
      max-width: 100%;
      height: auto;
      filter: brightness(1.1);
    }

    .system-name { padding: 1rem 1.5rem; font-size: 1.1rem; font-weight: 700; color: rgba(255,255,255,0.95); text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 1.5rem; background: rgba(255,255,255,0.03); letter-spacing: 1px; text-transform: uppercase; }

    .sidebar-nav {
      padding: 0 1rem;
    }

    .sidebar-nav .nav-item {
      margin-bottom: 0.5rem;
    }

    .sidebar-nav .nav-link { display: flex; align-items: center; color: rgba(255,255,255,0.8); padding: 1rem 1.25rem; text-decoration: none; border-radius: 0.75rem; transition: all 0.3s ease; font-weight: 500; border: 1px solid transparent; position: relative; overflow: hidden; }
    .sidebar-nav .nav-link::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent); transition: left 0.5s; }
    .sidebar-nav .nav-link:hover::before { left: 100%; }

    .sidebar-nav .nav-link:hover { background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.2); transform: translateX(5px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }

    .sidebar-nav .nav-link.active { background: linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0.05)); color: white; border-color: rgba(255,255,255,0.3); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }

    .sidebar-nav .nav-link i {
      margin-right: 0.75rem;
      font-size: 1.1rem;
      width: 20px;
      text-align: center;
    }
    .sidebar-nav .nav-link .peso-icon { display: inline-block; margin-right: 0.75rem; font-size: 1.1rem; width: 20px; text-align: center; font-weight: 700; }

    .admin-feature {
      background: rgba(0,0,0,0.1);
      border-left: 3px solid rgba(255,255,255,0.3);
    }

    .admin-feature:hover {
      background: rgba(0,0,0,0.2);
      border-left-color: rgba(255,255,255,0.6);
    }

    .sidebar-footer { position: absolute; bottom: 0; width: 100%; padding: 1rem; border-top: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.1); backdrop-filter: blur(10px); }

    .sidebar-footer .nav-link {
      justify-content: center;
      padding: 0.75rem;
      border-radius: 0.5rem;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-footer .nav-link:hover {
      background: rgba(255,255,255,0.1);
      border-color: rgba(255,255,255,0.2);
    }

    .sidebar-footer .nav-link i {
      margin-right: 0;
    }

    /* Enhanced transitions */
    .sidebar.transitioning {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Hover effects for better UX */
    .sidebar-nav .nav-link:active {
      transform: scale(0.98);
      transition: transform 0.1s ease;
    }

    /* Main Content */
    .content { margin-left: var(--sidebar-width); padding: 2rem; transition: all 0.3s ease; min-height: 100vh; }

    .content.expanded {
      margin-left: 0;
    }

    /* Header */
    .header { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); padding: 1.5rem 2rem; border-radius: var(--border-radius); box-shadow: 0 8px 32px rgba(0,0,0,0.1); margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.2); }

    .dark-mode .header { background: rgba(44, 62, 80, 0.9); color: var(--text-light); border: 1px solid rgba(255,255,255,0.1); }

    .hamburger { font-size: 1.5rem; cursor: pointer; padding: 0.75rem; border-radius: 0.5rem; transition: all 0.3s; background: rgba(0,0,0,0.05); }
    .hamburger:hover { background: rgba(0,0,0,0.1); }

    .system-title { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 2.2rem; font-weight: 800; }

    /* Dashboard Cards */
    .dashboard-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }

    .card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2); border-radius: var(--border-radius); box-shadow: 0 8px 32px rgba(0,0,0,0.1); padding: 2rem; transition: all 0.3s; position: relative; overflow: hidden; }
    .card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .card:nth-child(2)::before { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .card:nth-child(3)::before { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .card:nth-child(4)::before { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }

    .dark-mode .card { background: rgba(44, 62, 80, 0.9); color: var(--text-light); border: 1px solid rgba(255,255,255,0.1); }

    .card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }

    .stat-value { font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .card:nth-child(2) .stat-value { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .card:nth-child(3) .stat-value { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .card:nth-child(4) .stat-value { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

    /* Form Section */
    .form-section { background: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.2); border-radius: var(--border-radius); box-shadow: 0 8px 32px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 1.5rem; display: none; }
    .dark-mode .form-section { background: rgba(44, 62, 80, 0.9); color: var(--text-light); border: 1px solid rgba(255,255,255,0.1); }

    .dark-mode .form-section {
      background-color: var(--dark-card);
      color: var(--text-light);
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
    }

    .dark-mode .form-group input,
    .dark-mode .form-group select,
    .dark-mode .form-group textarea {
      background-color: #2a3a5a;
      border-color: #3a4b6e;
      color: var(--text-light);
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 0.5rem;
      margin-top: 1rem;
    }

    /* Buttons */
    .btn {
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s;
    }

    .btn-primary {
      background-color: var(--primary-color);
      color: white;
    }

    .btn-primary:hover {
      background-color: #3a5bc7;
    }

    .btn-secondary {
      background-color: #6c757d;
      color: white;
    }

    .btn-secondary:hover {
      background-color: #5a6268;
    }

    .btn-success {
      background-color: var(--success-color);
      color: white;
    }

    .btn-info {
      background-color: var(--info-color);
      color: white;
    }

    .btn-warning {
      background-color: var(--warning-color);
      color: white;
    }

    .toggle-form-btn {
      background-color: var(--primary-color);
      color: white;
      margin-bottom: 1.5rem;
    }

    .toggle-form-btn:hover {
      background-color: #3a5bc7;
    }

    /* Table Section */
    .table-section { background: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.2); border-radius: var(--border-radius); box-shadow: 0 8px 32px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 1.5rem; }
    .dark-mode .table-section { background: rgba(44, 62, 80, 0.9); color: var(--text-light); border: 1px solid rgba(255,255,255,0.1); }

    .dark-mode .table-section {
      background-color: var(--dark-card);
      color: var(--text-light);
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 0.75rem;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    /* Remove bottom border from all table headers */
    th, thead, tr {
        border-bottom: none !important;
    }

    .dark-mode th,
    .dark-mode td {
      border-bottom-color: #3a4b6e;
    }

    thead {
      background-color: var(--primary-color);
      color: white;
    }

    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }
  

    .dark-mode .route-map-section {
      background-color: var(--dark-card);
      color: var(--text-light);
    }

    .dark-mode .map-container {
      background-color: #2a3a5a;
      border-color: #3a4b6e;
    }

    /* Header Controls */
    .header-controls {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    /* Theme Toggle */
    .theme-toggle-container { display: flex; align-items: center; gap: 0.5rem; }

    .theme-switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .theme-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); transition: .4s; border-radius: 34px; }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .slider { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

    input:checked + .slider:before {
      transform: translateX(26px);
    }

    /* Responsive */
    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(-100%);
        box-shadow: 2px 0 20px rgba(0,0,0,0.3);
      }
      
      .sidebar.show {
        transform: translateX(0);
      }
      
      .content { margin-left: 0; padding: 1rem; }
      .dashboard-cards { grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
    }

    @media (max-width: 576px) {
      .sidebar { width: 100%; max-width: 320px; }
      .dashboard-cards { grid-template-columns: 1fr; }
      .header { flex-direction: column; gap: 1rem; text-align: center; }
    }

    /* Modern Loading Screen */
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(245, 247, 250, 0.95) 0%, rgba(195, 207, 226, 0.98) 100%);
      backdrop-filter: blur(20px);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      opacity: 0;
      visibility: hidden;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .dark-mode .loading-overlay {
      background: linear-gradient(135deg, rgba(26, 26, 46, 0.95) 0%, rgba(22, 33, 62, 0.98) 100%);
    }
    .loading-overlay.show {
      opacity: 1;
      visibility: visible;
    }
    .loading-container {
      text-align: center;
      position: relative;
    }
    .loading-logo {
      width: 80px;
      height: 80px;
      margin-bottom: 2rem;
      animation: logoFloat 3s ease-in-out infinite;
    }
    .loading-spinner {
      width: 60px;
      height: 60px;
      border: 3px solid rgba(102, 126, 234, 0.2);
      border-top: 3px solid #667eea;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 1.5rem;
      position: relative;
    }
    .loading-spinner::before {
      content: '';
      position: absolute;
      top: -3px;
      left: -3px;
      right: -3px;
      bottom: -3px;
      border: 3px solid transparent;
      border-top: 3px solid rgba(102, 126, 234, 0.4);
      border-radius: 50%;
      animation: spin 1.5s linear infinite reverse;
    }
    .loading-text {
      font-size: 1.2rem;
      font-weight: 600;
      color: #667eea;
      margin-bottom: 0.5rem;
      opacity: 0;
      animation: textFadeIn 0.5s ease-out 0.3s forwards;
    }
    .loading-subtext {
      font-size: 0.9rem;
      color: #6c757d;
      opacity: 0;
      animation: textFadeIn 0.5s ease-out 0.6s forwards;
    }
    .dark-mode .loading-text { color: #667eea; }
    .dark-mode .loading-subtext { color: #adb5bd; }
    .loading-progress {
      width: 200px;
      height: 4px;
      background: rgba(102, 126, 234, 0.2);
      border-radius: 2px;
      margin: 1rem auto 0;
      overflow: hidden;
      position: relative;
    }
    .loading-progress-bar {
      height: 100%;
      background: linear-gradient(90deg, #667eea, #764ba2);
      border-radius: 2px;
      width: 0%;
      animation: progressFill 2s ease-in-out infinite;
    }
    .loading-dots {
      display: flex;
      justify-content: center;
      gap: 0.5rem;
      margin-top: 1rem;
    }
    .loading-dot {
      width: 8px;
      height: 8px;
      background: #667eea;
      border-radius: 50%;
      animation: dotPulse 1.4s ease-in-out infinite both;
    }
    .loading-dot:nth-child(2) { animation-delay: 0.2s; }
    .loading-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    @keyframes logoFloat { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
    @keyframes textFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes progressFill { 0% { width: 0%; } 50% { width: 70%; } 100% { width: 100%; } }
    @keyframes dotPulse {
      0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
      40% { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body>
  <!-- Modern Loading Overlay -->
  <div class="loading-overlay" id="loadingOverlay">
    <div class="loading-container">
      <img src="slatelogo.png" alt="SLATE Logo" class="loading-logo">
      <div class="loading-spinner"></div>
      <div class="loading-text" id="loadingText">Loading...</div>
      <div class="loading-subtext" id="loadingSubtext">Please wait while we prepare service network data</div>
      <div class="loading-progress">
        <div class="loading-progress-bar"></div>
      </div>
      <div class="loading-dots">
        <div class="loading-dot"></div>
        <div class="loading-dot"></div>
        <div class="loading-dot"></div>
      </div>
    </div>
  </div>

  <div class="sidebar" id="sidebar">
    <div class="logo">
      <img src="slatelogo.png" alt="SLATE Logo">
    </div>
    <div class="system-name">CORE II</div>
    <div class="sidebar-nav">
      <div class="nav-item">
        <a href="landpage.php" class="nav-link">
          <i class="bi bi-speedometer2"></i>
          Dashboard
        </a>
      </div>
      <div class="nav-item">
        <a href="service-provider.php" class="nav-link">
          <i class="bi bi-people"></i>
          Service Provider
        </a>
      </div>
      <div class="nav-item">
        <a href="service-network.php" class="nav-link active">
          <i class="bi bi-diagram-3"></i>
          Service Network & Route Planner
        </a>
      </div>
      <div class="nav-item">
        <a href="rate-tariff.php" class="nav-link admin-feature">
          <span class="peso-icon">₱</span>
          Rate & Tariff
        </a>
      </div>
      <div class="nav-item">
        <a href="sop-manager.php" class="nav-link admin-feature">
          <i class="bi bi-journal-text"></i>
          SOP Manager
        </a>
      </div>
      <div class="nav-item">
        <a href="schedules.php" class="nav-link">
          <i class="bi bi-calendar-week"></i>
          Schedules & Transit Timetable
        </a>
      </div>
    </div>
    <div class="sidebar-footer">
      <a href="#" class="nav-link" onclick="confirmLogout()">
        <i class="bi bi-box-arrow-right"></i>
        Logout
      </a>
    </div>
  </div>

  <div class="content" id="mainContent">
    <div class="header">
      <div class="hamburger" id="hamburger">☰</div>
      <div>
        <h1>Service Network & Route Planner <span class="system-title">| CORE II </span></h1>
      </div>
      <div class="header-controls">
        <a href="admin.php" class="btn btn-outline-primary btn-sm me-2">
          <i class="bi bi-shield-lock"></i>
          Admin
        </a>
        <div class="theme-toggle-container">
          <span class="theme-label">Dark Mode</span>
          <label class="theme-switch">
            <input type="checkbox" id="themeToggle">
            <span class="slider"></span>
          </label>
        </div>
      </div>
    </div>

    <div class="dashboard-cards">
      <div class="card">
        <h3>Total Routes</h3>
        <div class="stat-value" id="totalRoutes">0</div>
        <div class="stat-label">Active routes</div>
      </div>

      <div class="card">
        <h3>Service Points</h3>
        <div class="stat-value" id="servicePoints">0</div>
        <div class="stat-label">Network nodes</div>
      </div>

      <div class="card">
        <h3>Coverage Area</h3>
        <div class="stat-value" id="coverageArea">0 km²</div>
        <div class="stat-label">Service coverage</div>
      </div>

      <div class="card">
        <h3>Efficiency Score</h3>
        <div class="stat-value" id="efficiencyScore">0%</div>
        <div class="stat-label">Route optimization</div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Route Management</h3>
      <button class="btn btn-success" onclick="openAddRouteModal()">
        <i class="bi bi-plus-circle"></i> Add New Route
      </button>
    </div>
    

    <!-- Route Modal (popup for add/edit) -->
    <div class="modal fade" id="routeModal" tabindex="-1" aria-labelledby="routeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="routeModalLabel">Add New Route</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="routeForm">
              <input type="hidden" id="routeId">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="routeName" class="form-label">Route Name *</label>
                    <input type="text" class="form-control" id="routeName" name="routeName" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="routeType" class="form-label">Route Type *</nlabel>
                    <select class="form-select" id="routeType" name="routeType" required>
                      <option value="">Select Type</option>
                      <option value="Primary">Primary Route</option>
                      <option value="Secondary">Secondary Route</option>
                      <option value="Express">Express Route</option>
                      <option value="Local">Local Route</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="startPoint" class="form-label">Start Point *</label>
                    <input type="text" class="form-control" id="startPoint" name="startPoint" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="endPoint" class="form-label">End Point *</label>
                    <input type="text" class="form-control" id="endPoint" name="endPoint" required>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="distance" class="form-label">Distance (km) *</label>
                    <input type="number" class="form-control" id="distance" name="distance" step="0.1" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="estimatedTime" class="form-label">Estimated Time (minutes) *</label>
                    <input type="number" class="form-control" id="estimatedTime" name="estimatedTime" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label for="serviceFrequency" class="form-label">Service Frequency *</label>
                    <select class="form-select" id="serviceFrequency" name="serviceFrequency" required>
                      <option value="">Select Frequency</option>
                      <option value="Every 15 min">Every 15 minutes</option>
                      <option value="Every 30 min">Every 30 minutes</option>
                      <option value="Every hour">Every hour</option>
                      <option value="Every 2 hours">Every 2 hours</option>
                      <option value="Daily">Daily</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="routeStatus" class="form-label">Status *</label>
                    <select class="form-select" id="routeStatus" name="routeStatus" required>
                      <option value="Active">Active</option>
                      <option value="Inactive">Inactive</option>
                      <option value="Maintenance">Maintenance</option>
                      <option value="Planned">Planned</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="routeNotes" class="form-label">Route Notes</label>
                    <textarea class="form-control" id="routeNotes" name="routeNotes" rows="3"></textarea>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="saveRoute()">Save Route</button>
          </div>
        </div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-responsive">
        <table id="routesTable" class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Route Name</th>
              <th>Type</th>
              <th>Start Point</th>
              <th>End Point</th>
              <th>Distance</th>
              <th>Frequency</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="routesTableBody">
            <!-- Route data will be loaded here -->
          </tbody>
        </table>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0">Shipment Tracking</h3>
      <button class="btn btn-info" onclick="openTrackingModal()">
        <i class="bi bi-truck"></i> View All
      </button>
    </div>
    <div class="table-section">
      <div class="table-responsive">
        <table class="table table-hover" id="trackingTable">
          <thead>
            <tr>
              <th>Tracking ID</th>
              <th>Route</th>
              <th>Status</th>
              <th>ETA</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="trackingTableBody"></tbody>
        </table>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Service Points Network</h3>
      <button class="btn btn-success" onclick="openAddServicePointModal()">
        <i class="bi bi-plus-circle"></i> Add New Service Point
      </button>
    </div>
    

    <!-- Service Point Modal (popup for add/edit) -->
    <div class="modal fade" id="servicePointModal" tabindex="-1" aria-labelledby="servicePointModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="servicePointModalLabel">Add New Service Point</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="servicePointForm">
              <input type="hidden" id="servicePointId">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="pointName" class="form-label">Point Name *</label>
                    <input type="text" class="form-control" id="pointName" name="pointName" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="pointType" class="form-label">Point Type *</label>
                    <select class="form-select" id="pointType" name="pointType" required>
                      <option value="">Select Type</option>
                      <option value="Transport Hub">Transport Hub</option>
                      <option value="Terminal">Terminal</option>
                      <option value="Transfer Point">Transfer Point</option>
                      <option value="Station">Station</option>
                      <option value="Depot">Depot</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="pointLocation" class="form-label">Location *</label>
                    <input type="text" class="form-control" id="pointLocation" name="pointLocation" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="pointServices" class="form-label">Services *</label>
                    <input type="text" class="form-control" id="pointServices" name="pointServices" required>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="pointStatus" class="form-label">Status *</label>
                    <select class="form-select" id="pointStatus" name="pointStatus" required>
                      <option value="Active">Active</option>
                      <option value="Inactive">Inactive</option>
                      <option value="Maintenance">Maintenance</option>
                      <option value="Planned">Planned</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="pointNotes" class="form-label">Notes</label>
                    <textarea class="form-control" id="pointNotes" name="pointNotes" rows="3"></textarea>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="saveServicePoint()">Save Service Point</button>
          </div>
        </div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-responsive">
        <table id="servicePointsTable" class="table table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Point Name</th>
              <th>Type</th>
              <th>Location</th>
              <th>Services</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="servicePointsTableBody">
            <!-- Service points data will be loaded here -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Route Modal -->
  <div class="modal fade" id="routeModal" tabindex="-1" aria-labelledby="routeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="routeModalLabel">Add New Route</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="routeForm">
            <input type="hidden" id="routeId">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="routeName" class="form-label">Route Name *</label>
                  <input type="text" class="form-control" id="routeName" name="routeName" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="routeType" class="form-label">Route Type *</label>
                  <select class="form-select" id="routeType" name="routeType" required>
                    <option value="">Select Type</option>
                    <option value="Primary">Primary Route</option>
                    <option value="Secondary">Secondary Route</option>
                    <option value="Express">Express Route</option>
                    <option value="Local">Local Route</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="startPoint" class="form-label">Start Point *</label>
                  <input type="text" class="form-control" id="startPoint" name="startPoint" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="endPoint" class="form-label">End Point *</label>
                  <input type="text" class="form-control" id="endPoint" name="endPoint" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="distance" class="form-label">Distance (km) *</label>
                  <input type="number" class="form-control" id="distance" name="distance" step="0.1" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="estimatedTime" class="form-label">Estimated Time (minutes) *</label>
                  <input type="number" class="form-control" id="estimatedTime" name="estimatedTime" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="mb-3">
                  <label for="serviceFrequency" class="form-label">Service Frequency *</label>
                  <select class="form-select" id="serviceFrequency" name="serviceFrequency" required>
                    <option value="">Select Frequency</option>
                    <option value="Every 15 min">Every 15 minutes</option>
                    <option value="Every 30 min">Every 30 minutes</option>
                    <option value="Every hour">Every hour</option>
                    <option value="Every 2 hours">Every 2 hours</option>
                    <option value="Daily">Daily</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="routeStatus" class="form-label">Status *</label>
                  <select class="form-select" id="routeStatus" name="routeStatus" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Planned">Planned</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="routeNotes" class="form-label">Route Notes</label>
                  <textarea class="form-control" id="routeNotes" name="routeNotes" rows="3"></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="saveRoute()">Save Route</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Service Point Modal -->
  <div class="modal fade" id="servicePointModal" tabindex="-1" aria-labelledby="servicePointModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="servicePointModalLabel">Add New Service Point</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="servicePointForm">
            <input type="hidden" id="servicePointId">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="pointName" class="form-label">Point Name *</label>
                  <input type="text" class="form-control" id="pointName" name="pointName" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="pointType" class="form-label">Point Type *</label>
                  <select class="form-select" id="pointType" name="pointType" required>
                    <option value="">Select Type</option>
                    <option value="Transport Hub">Transport Hub</option>
                    <option value="Terminal">Terminal</option>
                    <option value="Transfer Point">Transfer Point</option>
                    <option value="Station">Station</option>
                    <option value="Depot">Depot</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="pointLocation" class="form-label">Location *</label>
                  <input type="text" class="form-control" id="pointLocation" name="pointLocation" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="pointServices" class="form-label">Services *</label>
                  <input type="text" class="form-control" id="pointServices" name="pointServices" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="pointStatus" class="form-label">Status *</label>
                  <select class="form-select" id="pointStatus" name="pointStatus" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Planned">Planned</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="pointNotes" class="form-label">Notes</label>
                  <textarea class="form-control" id="pointNotes" name="pointNotes" rows="3"></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Service Point</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Route Modal -->
  <div class="modal fade" id="viewRouteModal" tabindex="-1" aria-labelledby="viewRouteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewRouteModalLabel">Route Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>ID:</strong> <span id="viewRouteId"></span></p>
              <p><strong>Name:</strong> <span id="viewRouteName"></span></p>
              <p><strong>Type:</strong> <span id="viewRouteType"></span></p>
              <p><strong>Start Point:</strong> <span id="viewRouteStartPoint"></span></p>
            </div>
            <div class="col-md-6">
              <p><strong>End Point:</strong> <span id="viewRouteEndPoint"></span></p>
              <p><strong>Distance:</strong> <span id="viewRouteDistance"></span></p>
              <p><strong>Frequency:</strong> <span id="viewRouteFrequency"></span></p>
              <p><strong>Status:</strong> <span id="viewRouteStatus"></span></p>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <p><strong>Notes:</strong></p>
              <p id="viewRouteNotes"></p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Service Point Modal -->
  <div class="modal fade" id="viewServicePointModal" tabindex="-1" aria-labelledby="viewServicePointModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewServicePointModalLabel">Service Point Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>ID:</strong> <span id="viewPointId"></span></p>
              <p><strong>Name:</strong> <span id="viewPointName"></span></p>
              <p><strong>Type:</strong> <span id="viewPointType"></span></p>
              <p><strong>Location:</strong> <span id="viewPointLocation"></span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Services:</strong> <span id="viewPointServices"></span></p>
              <p><strong>Status:</strong> <span id="viewPointStatus"></span></p>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <p><strong>Notes:</strong></p>
              <p id="viewPointNotes"></p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Provider Modal -->
  <div class="modal fade" id="providerModal" tabindex="-1" aria-labelledby="providerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="providerModalLabel">Add New Provider</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="providerForm">
            <input type="hidden" id="providerId">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="providerName" class="form-label">Provider Name *</label>
                  <input type="text" class="form-control" id="providerName" name="providerName" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="providerType" class="form-label">Provider Type *</label>
                  <select class="form-select" id="providerType" name="providerType" required>
                    <option value="">Select Type</option>
                    <option value="Individual">Individual</option>
                    <option value="Company">Company</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="contactPerson" class="form-label">Contact Person *</label>
                  <input type="text" class="form-control" id="contactPerson" name="contactPerson" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="contactNumber" class="form-label">Contact Number *</label>
                  <input type="text" class="form-control" id="contactNumber" name="contactNumber" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="status" class="form-label">Status *</label>
                  <select class="form-select" id="status" name="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="mb-3">
                  <label for="notes" class="form-label">Notes</label>
                  <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="saveProvider()">Save Provider</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Provider Modal -->
  <div class="modal fade" id="viewProviderModal" tabindex="-1" aria-labelledby="viewProviderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewProviderModalLabel">Provider Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>ID:</strong> <span id="viewProviderId"></span></p>
              <p><strong>Name:</strong> <span id="viewProviderName"></span></p>
              <p><strong>Type:</strong> <span id="viewProviderType"></span></p>
              <p><strong>Contact Person:</strong> <span id="viewContactPerson"></span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Contact Number:</strong> <span id="viewContactNumber"></span></p>
              <p><strong>Email:</strong> <span id="viewEmail"></span></p>
              <p><strong>Status:</strong> <span id="viewStatus"></span></p>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <p><strong>Notes:</strong></p>
              <p id="viewNotes"></p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete <strong id="deleteItemName"></strong>?</p>
          <p class="text-danger">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Shipment Tracking Modal (center) -->
  <div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="trackingModalLabel">Shipment Tracking - All</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle" id="trackingModalTable">
              <thead>
                <tr>
                  <th>Tracking ID</th>
                  <th>Route</th>
                  <th>Status</th>
                  <th>Last Update</th>
                  <th>ETA</th>
                  <th>Current Location</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Shipment Details Modal (center) -->
  <div class="modal fade" id="trackingDetailsModal" tabindex="-1" aria-labelledby="trackingDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="trackingDetailsModalLabel">Shipment Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Tracking ID:</strong> <span id="td_id"></span></p>
              <p><strong>Route:</strong> <span id="td_route"></span></p>
              <p><strong>Status:</strong> <span id="td_status"></span></p>
            </div>
            <div class="col-md-6">
              <p><strong>Last Update:</strong> <span id="td_lastUpdate"></span></p>
              <p><strong>ETA:</strong> <span id="td_eta"></span></p>
              <p><strong>Current Location:</strong> <span id="td_location"></span></p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  
  <script>
    const ROUTES_API = 'api/routes.php';
    const POINTS_API = 'api/service-points.php';
    const SHIPMENTS_API = 'api/schedules.php';
    let routes = [];
    let servicePoints = [];
    let currentRouteId = null;
    let currentServicePointId = null;
    let isEditMode = false;
    let deleteType = '';

    // Initialize the application
    document.addEventListener('DOMContentLoaded', function() {
      initializeEventListeners();
      applyStoredTheme();
      initMap();
      fetchRoutes();
      fetchServicePoints();
      preloadShipments();
    });

    let leafletMap = null;
    function initMap() {
      if (leafletMap) return;
      const mapEl = document.getElementById('map');
      if (!mapEl) return;
      // Center on the Philippines
      leafletMap = L.map('map', { zoomControl: true }).setView([12.8797, 121.7740], 6);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(leafletMap);
    }

    async function fetchRoutes() {
      try {
        const res = await fetchWithLoading(ROUTES_API);
        const data = await res.json();
        routes = Array.isArray(data) ? data.map(dbToUiRoute) : [];
        loadRoutes();
        updateDashboardStats();
      } catch (e) {
        showNotification('Failed to load routes', 'danger');
      }
    }

    async function fetchServicePoints() {
      try {
        const res = await fetchWithLoading(POINTS_API);
        const data = await res.json();
        servicePoints = Array.isArray(data) ? data.map(dbToUiPoint) : [];
        loadServicePoints();
        updateDashboardStats();
      } catch (e) {
        showNotification('Failed to load service points', 'danger');
      }
    }

    function dbToUiRoute(row) {
      return {
        id: parseInt(row.id),
        name: row.name,
        type: row.type,
        startPoint: row.start_point,
        endPoint: row.end_point,
        distance: parseFloat(row.distance),
        frequency: row.frequency,
        status: row.status,
        estimatedTime: parseInt(row.estimated_time),
        notes: row.notes || ''
      };
    }

    function uiToDbRoute(r) {
      return {
        name: r.name,
        type: r.type,
        startPoint: r.startPoint,
        endPoint: r.endPoint,
        distance: r.distance,
        frequency: r.frequency,
        status: r.status,
        estimatedTime: r.estimatedTime,
        notes: r.notes || ''
      };
    }

    function dbToUiPoint(row) {
      return {
        id: parseInt(row.id),
        name: row.name,
        type: row.type,
        location: row.location,
        services: row.services,
        status: row.status,
        notes: row.notes || ''
      };
    }

    function uiToDbPoint(p) {
      return {
        name: p.name,
        type: p.type,
        location: p.location,
        services: p.services,
        status: p.status,
        notes: p.notes || ''
      };
    }

    function initializeEventListeners() {
      // Theme toggle
      document.getElementById('themeToggle').addEventListener('change', function() {
        document.body.classList.toggle('dark-mode', this.checked);
        localStorage.setItem('theme', this.checked ? 'dark' : 'light');
      });

      // Enhanced sidebar toggle with smooth animations
      document.getElementById('hamburger').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        // Add smooth transition class
        sidebar.classList.add('transitioning');
        setTimeout(() => {
          sidebar.classList.remove('transitioning');
        }, 300);
      });

      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const hamburger = document.getElementById('hamburger');
        
        if (window.innerWidth <= 992 && 
            !sidebar.contains(e.target) && 
            !hamburger.contains(e.target) &&
            !sidebar.classList.contains('collapsed')) {
          sidebar.classList.add('collapsed');
        }
      });

      // Active link management
      const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
      navLinks.forEach(link => {
        link.addEventListener('click', function() {
          navLinks.forEach(l => l.classList.remove('active'));
          this.classList.add('active');
        });
      });

      // Service Point form submit
      document.getElementById('servicePointForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveServicePoint();
      });
    }

    function applyStoredTheme() {
      const stored = localStorage.getItem('theme');
      const isDark = stored === 'dark';
      document.body.classList.toggle('dark-mode', isDark);
      const toggle = document.getElementById('themeToggle');
      if (toggle) toggle.checked = isDark;
    }

    function loadRoutes() {
      const tbody = document.getElementById('routesTableBody');
      tbody.innerHTML = '';

      routes.forEach(route => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${route.id}</td>
          <td>${route.name}</td>
          <td>${route.type}</td>
          <td>${route.startPoint}</td>
          <td>${route.endPoint}</td>
          <td>${route.distance} km</td>
          <td>${route.frequency}</td>
          <td><span class="badge ${getStatusBadgeClass(route.status)}">${route.status}</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn btn-sm btn-info" onclick="viewRoute(${route.id})" title="View Details">
                <i class="bi bi-eye"></i>
              </button>
              <button class="btn btn-sm btn-primary" onclick="editRoute(${route.id})" title="Edit Route">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-sm btn-warning" onclick="optimizeRoute(${route.id})" title="Optimize Route">
                <i class="bi bi-gear"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="deleteRoute(${route.id})" title="Delete Route">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        `;
        tbody.appendChild(row);
      });
    }

    function loadServicePoints() {
      const tbody = document.getElementById('servicePointsTableBody');
      tbody.innerHTML = '';

      servicePoints.forEach(point => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${point.id}</td>
          <td>${point.name}</td>
          <td>${point.type}</td>
          <td>${point.location}</td>
          <td>${point.services}</td>
          <td><span class="badge ${getStatusBadgeClass(point.status)}">${point.status}</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn btn-sm btn-info" onclick="viewServicePoint(${point.id})" title="View Details">
                <i class="bi bi-eye"></i>
              </button>
              <button class="btn btn-sm btn-primary" onclick="editServicePoint(${point.id})" title="Edit Service Point">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-sm btn-danger" onclick="deleteServicePoint(${point.id})" title="Delete Service Point">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        `;
        tbody.appendChild(row);
      });
    }

    function getStatusBadgeClass(status) {
      switch(status) {
        case 'Active': return 'bg-success';
        case 'Inactive': return 'bg-secondary';
        case 'Maintenance': return 'bg-warning text-dark';
        case 'Planned': return 'bg-info';
        default: return 'bg-secondary';
      }
    }

    function updateDashboardStats() {
      const totalRoutes = routes.length;
      const totalPoints = servicePoints.length;
      const coverageArea = routes.reduce((sum, r) => sum + (parseFloat(r.distance) || 0), 0).toFixed(1);
      const efficiencyScore = routes.length > 0 ? Math.round((routes.filter(r => r.status === 'Active').length / routes.length) * 100) : 0;

      document.getElementById('totalRoutes').textContent = totalRoutes;
      document.getElementById('servicePoints').textContent = totalPoints;
      document.getElementById('coverageArea').textContent = coverageArea + ' km²';
      document.getElementById('efficiencyScore').textContent = efficiencyScore + '%';
    }

    // Route functions
    function openAddRouteModal() {
      isEditMode = false;
      currentRouteId = null;
      const title = document.getElementById('routeModalLabel');
      if (title) title.textContent = 'Add New Route';
      document.getElementById('routeForm').reset();
      document.getElementById('routeId').value = '';
      const modal = new bootstrap.Modal(document.getElementById('routeModal'));
      modal.show();
    }

    function viewRoute(id) {
      const route = routes.find(r => r.id === id);
      if (!route) return;

      document.getElementById('viewRouteId').textContent = route.id;
      document.getElementById('viewRouteName').textContent = route.name;
      document.getElementById('viewRouteType').textContent = route.type;
      document.getElementById('viewRouteStartPoint').textContent = route.startPoint;
      document.getElementById('viewRouteEndPoint').textContent = route.endPoint;
      document.getElementById('viewRouteDistance').textContent = route.distance + ' km';
      document.getElementById('viewRouteFrequency').textContent = route.frequency;
      document.getElementById('viewRouteStatus').textContent = route.status;
      document.getElementById('viewRouteNotes').textContent = route.notes || 'No notes available';

      const viewModal = new bootstrap.Modal(document.getElementById('viewRouteModal'));
      viewModal.show();
    }

    function editRoute(id) {
      const route = routes.find(r => r.id === id);
      if (!route) return;

      isEditMode = true;
      currentRouteId = id;
      const title = document.getElementById('routeModalLabel');
      if (title) title.textContent = 'Edit Route';
      
      // Populate form fields
      document.getElementById('routeId').value = route.id;
      document.getElementById('routeName').value = route.name;
      document.getElementById('routeType').value = route.type;
      document.getElementById('startPoint').value = route.startPoint;
      document.getElementById('endPoint').value = route.endPoint;
      document.getElementById('distance').value = route.distance;
      document.getElementById('estimatedTime').value = route.estimatedTime;
      document.getElementById('serviceFrequency').value = route.frequency;
      document.getElementById('routeStatus').value = route.status;
      document.getElementById('routeNotes').value = route.notes || '';

      const modal = new bootstrap.Modal(document.getElementById('routeModal'));
      modal.show();
    }

    function optimizeRoute(id) {
      const route = routes.find(r => r.id === id);
      if (!route) return;

      showNotification(`Optimizing route: ${route.name}. Analyzing traffic patterns and calculating optimal path...`, 'info');
    }

    function deleteRoute(id) {
      const route = routes.find(r => r.id === id);
      if (!route) return;
      Swal.fire({
        title: `Delete route "${route.name}"?`,
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#dc3545'
      }).then(async (res) => {
        if (res.isConfirmed) {
          try {
            const r = await fetchWithLoading(`${ROUTES_API}?id=${id}`, { method: 'DELETE' });
            if (!r.ok) throw new Error();
            await fetchRoutes();
            updateDashboardStats();
            showNotification('Route deleted successfully!', 'success');
          } catch (e) {
            showNotification('Failed to delete', 'danger');
          }
        }
      });
    }

    async function saveRoute() {
      const form = document.getElementById('routeForm');
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      const formData = new FormData(form);
      const routeData = {
        name: formData.get('routeName'),
        type: formData.get('routeType'),
        startPoint: formData.get('startPoint'),
        endPoint: formData.get('endPoint'),
        distance: parseFloat(formData.get('distance')),
        frequency: formData.get('serviceFrequency'),
        status: formData.get('routeStatus'),
        estimatedTime: parseInt(formData.get('estimatedTime')),
        notes: formData.get('routeNotes')
      };

      try {
        if (isEditMode && currentRouteId) {
          const res = await fetchWithLoading(`${ROUTES_API}?id=${currentRouteId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(uiToDbRoute(routeData))
          });
          if (!res.ok) throw new Error();
          showNotification('Route updated successfully!', 'success');
        } else {
          const res = await fetchWithLoading(ROUTES_API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(uiToDbRoute(routeData))
          });
          if (!res.ok) throw new Error();
          showNotification('Route added successfully!', 'success');
        }
        await fetchRoutes();
        const modal = bootstrap.Modal.getInstance(document.getElementById('routeModal'));
        if (modal) modal.hide();
      } catch (e) {
        showNotification('Failed to save route', 'danger');
      }
    }

    // Service Point functions
    function openAddServicePointModal() {
      isEditMode = false;
      currentServicePointId = null;
      const title = document.getElementById('servicePointModalLabel');
      if (title) title.textContent = 'Add New Service Point';
      document.getElementById('servicePointForm').reset();
      document.getElementById('servicePointId').value = '';
      const modal = new bootstrap.Modal(document.getElementById('servicePointModal'));
      modal.show();
    }

    function viewServicePoint(id) {
      const point = servicePoints.find(p => p.id === id);
      if (!point) return;

      document.getElementById('viewPointId').textContent = point.id;
      document.getElementById('viewPointName').textContent = point.name;
      document.getElementById('viewPointType').textContent = point.type;
      document.getElementById('viewPointLocation').textContent = point.location;
      document.getElementById('viewPointServices').textContent = point.services;
      document.getElementById('viewPointStatus').textContent = point.status;
      document.getElementById('viewPointNotes').textContent = point.notes || 'No notes available';

      const viewModal = new bootstrap.Modal(document.getElementById('viewServicePointModal'));
      viewModal.show();
    }

    function editServicePoint(id) {
      const point = servicePoints.find(p => p.id === id);
      if (!point) return;

      isEditMode = true;
      currentServicePointId = id;
      const title = document.getElementById('servicePointModalLabel');
      if (title) title.textContent = 'Edit Service Point';
      
      // Populate form fields
      document.getElementById('servicePointId').value = point.id;
      document.getElementById('pointName').value = point.name;
      document.getElementById('pointType').value = point.type;
      document.getElementById('pointLocation').value = point.location;
      document.getElementById('pointServices').value = point.services;
      document.getElementById('pointStatus').value = point.status;
      document.getElementById('pointNotes').value = point.notes || '';

      const modal = new bootstrap.Modal(document.getElementById('servicePointModal'));
      modal.show();
    }

    function deleteServicePoint(id) {
      const point = servicePoints.find(p => p.id === id);
      if (!point) return;
      Swal.fire({
        title: `Delete service point "${point.name}"?`,
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#dc3545'
      }).then(async (res) => {
        if (res.isConfirmed) {
          try {
            const r = await fetchWithLoading(`${POINTS_API}?id=${id}`, { method: 'DELETE' });
            if (!r.ok) throw new Error();
            await fetchServicePoints();
            updateDashboardStats();
            showNotification('Service point deleted successfully!', 'success');
          } catch (e) {
            showNotification('Failed to delete', 'danger');
          }
        }
      });
    }

    async function saveServicePoint() {
      const form = document.getElementById('servicePointForm');
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      const formData = new FormData(form);
      const pointData = {
        name: formData.get('pointName'),
        type: formData.get('pointType'),
        location: formData.get('pointLocation'),
        services: formData.get('pointServices'),
        status: formData.get('pointStatus'),
        notes: formData.get('pointNotes')
      };

      try {
        if (isEditMode && currentServicePointId) {
          const res = await fetchWithLoading(`${POINTS_API}?id=${currentServicePointId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(uiToDbPoint(pointData))
          });
          if (!res.ok) throw new Error();
          showNotification('Service point updated successfully!', 'success');
        } else {
          const res = await fetchWithLoading(POINTS_API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(uiToDbPoint(pointData))
          });
          if (!res.ok) throw new Error();
          showNotification('Service point added successfully!', 'success');
        }
        await fetchServicePoints();
        const modal = bootstrap.Modal.getInstance(document.getElementById('servicePointModal'));
        if (modal) modal.hide();
      } catch (e) {
        showNotification('Failed to save service point', 'danger');
      }
    }

    // Inline form helpers no longer used (modals handle show/hide)

    // SweetAlert View dialogs
    function viewRoute(id) {
      const route = routes.find(r => r.id === id);
      if (!route) return;
      Swal.fire({
        title: 'Route Details',
        html: `
          <div class="text-start">
            <p><strong>ID:</strong> ${route.id}</p>
            <p><strong>Name:</strong> ${route.name}</p>
            <p><strong>Type:</strong> ${route.type}</p>
            <p><strong>Start Point:</strong> ${route.startPoint}</p>
            <p><strong>End Point:</strong> ${route.endPoint}</p>
            <p><strong>Distance:</strong> ${route.distance} km</p>
            <p><strong>Frequency:</strong> ${route.frequency}</p>
            <p><strong>Status:</strong> ${route.status}</p>
            <p><strong>Notes:</strong> ${route.notes || 'No notes available'}</p>
          </div>
        `,
        icon: 'info'
      });
    }

    function viewServicePoint(id) {
      const point = servicePoints.find(p => p.id === id);
      if (!point) return;
      Swal.fire({
        title: 'Service Point Details',
        html: `
          <div class="text-start">
            <p><strong>ID:</strong> ${point.id}</p>
            <p><strong>Name:</strong> ${point.name}</p>
            <p><strong>Type:</strong> ${point.type}</p>
            <p><strong>Location:</strong> ${point.location}</p>
            <p><strong>Services:</strong> ${point.services}</p>
            <p><strong>Status:</strong> ${point.status}</p>
            <p><strong>Notes:</strong> ${point.notes || 'No notes available'}</p>
          </div>
        `,
        icon: 'info'
      });
    }

    // Loading Utility Functions
    function showLoading(text = 'Loading...', subtext = 'Please wait') {
      const overlay = document.getElementById('loadingOverlay');
      const loadingText = document.getElementById('loadingText');
      const loadingSubtext = document.getElementById('loadingSubtext');
      if (loadingText) loadingText.textContent = text;
      if (loadingSubtext) loadingSubtext.textContent = subtext;
      overlay.classList.add('show');
    }
    function hideLoading() {
      const overlay = document.getElementById('loadingOverlay');
      overlay.classList.remove('show');
    }

    async function fetchWithLoading(url, options = {}) {
      try {
        showLoading('Processing...', 'Please wait');
        const res = await fetch(url, options);
        return res;
      } finally {
        hideLoading();
      }
    }

    function showNotification(message, type = 'info') {
      const bg = type === 'success' ? '#1cc88a' : type === 'danger' ? '#e74a3b' : type === 'warning' ? '#f6c23e' : '#36b9cc';
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2200,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer);
          toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
      });
      Toast.fire({
        icon: type === 'danger' ? 'error' : type,
        title: message,
        background: bg,
        color: '#fff'
      });
    }

    // Show loader on page load, hide after content is ready
    document.addEventListener('DOMContentLoaded', function() {
      showLoading('Loading Service Network...', 'Preparing service network and route planner');
      setTimeout(() => {
        hideLoading();
      }, 1200); // Adjust time as needed or hide after your data loads
    });

    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
      const loader = document.getElementById('loaderOverlay');
      loader.style.opacity = '0';
      setTimeout(() => loader.style.display = 'none', 400);
    });

    function confirmLogout() {
      Swal.fire({
        title: 'Are you sure you want to logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'logout.php';
        }
      });
    }

    // Shipment tracking
    async function preloadShipments() {
      try {
        const res = await fetch(SHIPMENTS_API);
        const data = await res.json();
        window.__shipmentsCache = normalizeShipments(data);
        renderTrackingTeaser(window.__shipmentsCache);
      } catch (e) {
        window.__shipmentsCache = [];
      }
    }

    function normalizeShipments(rows) {
      if (!Array.isArray(rows)) return [];
      return rows.map((r, idx) => ({
        id: r.tracking_id || r.id || `SHP-${1000 + idx}`,
        route: r.route || r.name || r.route_name || '-',
        status: r.status || 'In Transit',
        lastUpdate: r.updated_at || r.last_update || '-',
        eta: r.eta || r.arrival || r.estimated_arrival || '-',
        location: r.current_location || r.location || '-'
      }));
    }

    function renderTrackingTeaser(shipments) {
      const tbody = document.getElementById('trackingTableBody');
      if (!tbody) return;
      tbody.innerHTML = '';
      const items = shipments.slice(0, 5);
      items.forEach(s => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${s.id}</td>
          <td>${s.route}</td>
          <td><span class="badge ${getStatusBadgeClass(s.status)}">${s.status}</span></td>
          <td>${s.eta}</td>
          <td>
            <button class="btn btn-sm btn-info" onclick="openTrackingDetails('${s.id}')">
              <i class=\"bi bi-eye\"></i>
            </button>
          </td>
        `;
        tbody.appendChild(tr);
      });
      if (items.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td colspan="5" class="text-center text-muted py-4">No shipment records available.</td>`;
        tbody.appendChild(tr);
      }
    }

    function openTrackingModal() {
      const modal = new bootstrap.Modal(document.getElementById('trackingModal'));
      renderTrackingModalTable(window.__shipmentsCache || []);
      modal.show();
    }

    function renderTrackingModalTable(shipments) {
      const tbody = document.querySelector('#trackingModalTable tbody');
      if (!tbody) return;
      tbody.innerHTML = '';
      shipments.forEach(s => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${s.id}</td>
          <td>${s.route}</td>
          <td><span class="badge ${getStatusBadgeClass(s.status)}">${s.status}</span></td>
          <td>${s.lastUpdate}</td>
          <td>${s.eta}</td>
          <td>${s.location}</td>
          <td>
            <button class="btn btn-sm btn-info" onclick="openTrackingDetails('${s.id}')">
              <i class=\"bi bi-eye\"></i>
            </button>
          </td>
        `;
        tbody.appendChild(tr);
      });
      if (shipments.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td colspan="7" class="text-center text-muted py-4">No shipment records available.</td>`;
        tbody.appendChild(tr);
      }
    }

    function openTrackingDetails(id) {
      const s = (window.__shipmentsCache || []).find(x => String(x.id) === String(id));
      if (!s) return;
      const set = (i,v)=>{ const el = document.getElementById(i); if (el) el.textContent = v; };
      set('td_id', s.id);
      set('td_route', s.route);
      set('td_status', s.status);
      set('td_lastUpdate', s.lastUpdate);
      set('td_eta', s.eta);
      set('td_location', s.location);
      const modal = new bootstrap.Modal(document.getElementById('trackingDetailsModal'));
      modal.show();
    }
  </script>
</body>
</html>
