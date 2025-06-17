<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['domain'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$domain = $_SESSION['domain'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>NexaVerse Dashboard</title>
  <!-- Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bai+Jamjuree:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* CSS Reset */
    *,
    *::before,
    *::after {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: #f8f9fa;
      color: #222;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    /* Fonts */
   * {
      font-family: 'Bai Jamjuree', sans-serif;
    }

    /* Layout */
    .app {
      display: grid;
      grid-template-columns: 80px 1fr;
      min-height: 100vh;
    }

    /* Large desktop: max width constrain and centered */
    @media (min-width: 1440px) {
      .app {
        max-width: 1400px;
        margin: 0 auto;
        grid-template-columns: 80px 1fr;
      }
    }

    /* Sidebar */
    .sidebar {
      background: #000000;
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 24px 0;
      position: sticky;
      top: 0;
      height: 100vh;
      width: 80px;
      align-items: center;
      gap: 32px;
      user-select: none;
      z-index: 10;
    }

    .sidebar .logo {
      writing-mode: vertical-rl;
      font-weight: 700;
      font-size: 20px;
      letter-spacing: -0.02em;
      transform: rotate(180deg);
      cursor: default;
      user-select: text;
    }

    .sidebar nav {
      flex: 1;
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 16px;
      align-items: center;
    }

    .sidebar nav button {
      background: none;
      border: none;
      color: white;
      cursor: pointer;
      width: 100%;
      text-align: center;
      padding: 10px 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      font-size: 12px;
      letter-spacing: 0.02em;
      gap: 6px;
      transition: background-color 0.2s ease;
    }

    .sidebar nav button:hover,
    .sidebar nav button:focus {
      background-color: rgba(255 255 255 / 0.1);
      outline: none;
      border-radius: 12px;
    }

    .sidebar nav button .material-icons {
      font-size: 24px;
      line-height: 1;
    }

    .sidebar .logout-btn {
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 12px;
      cursor: pointer;
      border-radius: 12px;
      margin-bottom: 16px;
      user-select: none;
    }

    .sidebar .logout-btn:hover,
    .sidebar .logout-btn:focus {
      background-color: rgba(255 255 255 / 0.1);
      outline: none;
    }

    /* Main content container */
    main {
      background: white;
      padding: 32px;
      display: flex;
      flex-direction: column;
      gap: 32px;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Header top bar */
    .header-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .header-bar h1 {
      font-weight: 700;
      font-size: 1.5rem;
      margin: 0;
      flex-grow: 1;
      color: #222222;
    }

    .search-bar {
      position: relative;
      flex-shrink: 0;
      width: 220px;
    }

    .search-bar input {
      width: 100%;
      padding: 8px 36px 8px 12px;
      border-radius: 12px;
      border: 1.5px solid #ddd;
      font-size: 0.9rem;
      transition: border-color 0.3s ease;
    }

    .search-bar input:focus {
      border-color: #ff931e;
      outline: none;
    }

    .search-bar .material-icons {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #666;
      pointer-events: none;
    }

    .user-profile {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      position: relative;
      user-select: none;
    }

    .user-profile img {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ff931e;
    }

    .user-profile span {
      font-weight: 600;
      color: #555;
      font-size: 0.9rem;
    }

    .user-profile .material-icons {
      color: #777;
      font-size: 20px;
    }

    /* Dashboard cards grid */
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 24px;
    }

    .card {
      border-radius: 16px;
      padding: 20px 24px;
      color: white;
      display: flex;
      flex-direction: column;
      gap: 12px;
      font-weight: 600;
      font-size: 0.95rem;
      user-select: none;
      min-height: 89px;
    }

    .card small {
      font-weight: 400;
      font-size: 0.8rem;
      opacity: 0.7;
      text-transform: uppercase;
    }

    .card .value {
      font-size: 1.3rem;
      font-weight: 700;
    }

    .card.current-mrr {
      background: #3F0D0D;
    }

    .card.current-customers {
      background: #7A4200;
    }

    .card.active-customers {
      background: #D0D3D4;
      color: #212121;
    }

    .card.churn-rate {
      background: #FF9F00;
    }

    /* Lower dashboard area grid */
    .lower-dashboard {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 24px;
    }

    @media (max-width: 1024px) {
      .lower-dashboard {
        grid-template-columns: 1fr;
      }
    }

    /* Left lower grid: Trend chart and Support Tickets */
    .trend-and-tickets {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
    }

    @media (max-width: 767px) {
      .trend-and-tickets {
        grid-template-columns: 1fr;
      }
    }

    /* Card container style */
    .card-white {
      background: white;
      border-radius: 16px;
      padding: 20px 24px;
      box-shadow: 0 4px 15px rgb(0 0 0 / 0.05);
      display: flex;
      flex-direction: column;
      gap: 16px;
      color: #212121;
    }

    .card-white h3 {
      margin: 0;
      font-weight: 700;
      font-size: 1rem;
      color: #222;
    }

    /* Trend chart container */
    .trend-chart {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .trend-chart .filters {
      font-size: 0.8rem;
      color: #666;
      display: flex;
      gap: 12px;
      user-select: none;
    }

    .trend-chart .filters button {
      border: none;
      background: none;
      padding: 4px 8px;
      border-radius: 10px;
      cursor: pointer;
      color: #666;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .trend-chart .filters button.active,
    .trend-chart .filters button:hover {
      background-color: #ff931e;
      color: white;
    }

    /* Bar chart styling */
    .bar-chart {
      display: flex;
      align-items: flex-end;
      gap: 8px;
      height: 120px;
      user-select: none;
    }

    .bar {
      flex-grow: 1;
      border-radius: 6px 6px 0 0;
      position: relative;
      cursor: default;
      transition: background-color 0.3s ease;
    }

    .bar span {
      position: absolute;
      width: 100%;
      bottom: 100%;
      text-align: center;
      font-size: 0.7rem;
      color: #444;
      margin-bottom: 4px;
    }

    /* Bar colors for each data type */
    .bar.sales {
      background-color: #ff931e;
    }

    .bar.expense {
      background-color: #000000cc;
    }

    .bar.profit {
      background-color: #7a4200;
    }

    /* Right lower grid: Sales and Transactions */
    .sales-transactions {
      display: grid;
      grid-template-rows: 1fr 1fr;
      gap: 24px;
    }

    /* Sales Pie chart container */
    .pie-chart-container {
      display: flex;
      flex-direction: column;
      gap: 12px;
      align-items: center;
      user-select: none;
      color: #333;
    }

    .pie-chart-container canvas {
      max-width: 150px;
      max-height: 150px;
    }

    .pie-chart-container .legend {
      display: flex;
      gap: 12px;
      font-size: 0.85rem;
      justify-content: center;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .legend-marker {
      width: 16px;
      height: 16px;
      border-radius: 4px;
    }

    .legend-sales {
      background-color: #ff931e;
    }

    .legend-refund {
      background-color: #7a4200;
    }

    .legend-canceled {
      background-color: #000000cc;
    }

    .sales-number {
      font-weight: 900;
      font-size: 2rem;
      margin: 0;
    }

    /* Transactions list container */
    .transactions-list {
      user-select: none;
    }

    .transaction-header {
      font-size: 1rem;
      font-weight: 700;
      color: #222;
      margin-bottom: 8px;
    }

    .transaction-items {
      max-height: 180px;
      overflow-y: auto;
      border-radius: 12px;
      background-color: #f1f1f1;
      padding: 12px 16px;
      font-size: 0.9rem;
    }

    .transaction-item {
      display: flex;
      justify-content: space-between;
      padding: 4px 0;
      border-bottom: 1px solid #ddd;
      gap: 8px;
      align-items: center;
    }

    .transaction-item:last-child {
      border-bottom: none;
    }

    .transaction-name {
      flex: 1;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
      color: #222;
    }

    .transaction-status {
      font-size: 0.85rem;
      padding: 2px 8px;
      border-radius: 12px;
      font-weight: 600;
      min-width: 80px;
      text-align: center;
      white-space: nowrap;
    }

    .status-paid {
      background: #7a4200;
      color: white;
    }

    .status-pending {
      background: #ff931e;
      color: white;
    }

    .status-failed {
      background: #000000cc;
      color: white;
    }

    .view-all-btn {
      margin-top: 10px;
      font-weight: 700;
      font-size: 0.9rem;
      background-color: #7a4200;
      color: white;
      border-radius: 8px;
      padding: 8px 12px;
      border: none;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
    }

    .view-all-btn:hover {
      background-color: #ff931e;
    }

    /* Support Tickets List */
    .support-tickets-filter {
      display: flex;
      gap: 16px;
      font-size: 0.85rem;
      font-weight: 600;
      color: #666;
      user-select: none;
    }

    .support-tickets-filter button {
      background: none;
      border: none;
      cursor: pointer;
      border-radius: 12px;
      padding: 6px 12px;
      color: #666;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .support-tickets-filter button.active,
    .support-tickets-filter button:hover {
      background-color: #ff931e;
      color: white;
    }

    .tickets-list {
      margin-top: 12px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      overflow-y: auto;
      max-height: 320px;
      font-size: 0.85rem;
      color: #333;
    }

    .ticket {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      align-items: center;
      border-radius: 12px;
      background-color: #f4f4f4;
      padding: 12px 16px;
    }

    .ticket-info {
      display: flex;
      gap: 12px;
      flex-grow: 1;
      min-width: 0;
      align-items: center;
    }

    .ticket-status-indicator {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .status-new {
      background-color: #ff931e;
    }

    .status-pending {
      background-color: #7a4200;
    }

    .status-closed {
      background-color: #000000cc;
    }

    .ticket-email {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      min-width: 0;
      font-weight: 600;
      cursor: default;
    }

    .ticket-desc {
      font-weight: 400;
      color: #555;
      font-size: 0.8rem;
      flex-shrink: 1;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 140px;
    }

    /* Customer Demographics Map */
    .customer-demographic {
      user-select: none;
    }

    .map-container {
      margin-top: 12px;
      width: 100%;
      height: 240px;
      border-radius: 16px;
      overflow: hidden;
      background-color: #eee;
      position: relative;
    }

    svg {
      display: block;
      width: 100%;
      height: 100%;
    }

    /* Responsive Adjustments */

    @media (max-width: 767px) {

      /* App switches to a single column with a header navigation hamburger + breadcrumb */
      .app {
        display: flex;
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        height: auto;
        padding: 12px 12px;
        flex-direction: row;
        justify-content: space-between;
      }

      .sidebar .logo {
        writing-mode: horizontal-tb;
        transform: none;
        font-size: 1.3rem;
        letter-spacing: 0;
      }

      .sidebar nav {
        flex-direction: row;
        width: auto;
        gap: 16px;
      }

      .sidebar nav button {
        font-size: 14px;
        flex-direction: row;
        gap: 8px;
        width: auto;
        text-align: left;
        padding: 8px 12px;
      }

      .sidebar nav button .material-icons {
        font-size: 20px;
      }

      .sidebar .logout-btn {
        display: none;
      }

      main {
        padding: 20px 16px 32px;
        min-height: auto;
      }

      .dashboard-cards {
        grid-template-columns: 1fr 1fr;
      }

      .header-bar {
        flex-wrap: nowrap;
        gap: 8px;
      }

      .search-bar {
        width: 1fr;
        flex-grow: 1;
      }

      .lower-dashboard {
        grid-template-columns: 1fr;
      }

      .trend-and-tickets {
        grid-template-columns: 1fr;
      }

      .sales-transactions {
        grid-template-rows: auto auto;
      }

      .transactions-list .transaction-items {
        max-height: 140px;
      }

      .tickets-list {
        max-height: 220px;
      }
    }
  </style>
</head>
<body>
  <div class="app" role="main">
    <aside class="sidebar" aria-label="Primary Navigation">
      <div class="logo" aria-label="NexaVerse Logo">NexaVerse</div>
      <nav aria-label="Main menu">
        <button aria-current="page" aria-label="Overview">
          <span class="material-icons" aria-hidden="true">dashboard</span>
          Overview
        </button>
        <button aria-label="Transactions">
          <span class="material-icons" aria-hidden="true">receipt_long</span>
          Transactions
        </button>
        <button aria-label="Customers">
          <span class="material-icons" aria-hidden="true">groups</span>
          Customers
        </button>
        <button aria-label="Reports">
          <span class="material-icons" aria-hidden="true">bar_chart</span>
          Reports
        </button>
        <button aria-label="Settings">
          <span class="material-icons" aria-hidden="true">settings</span>
          Settings
        </button>
        <button aria-label="Developer">
          <span class="material-icons" aria-hidden="true">code</span>
          Developer
        </button>
      </nav>
      <button class="logout-btn" aria-label="Log Out">
        <span class="material-icons" aria-hidden="true">logout</span>
        Log out
      </button>
    </aside>
    <main>
      <header class="header-bar" role="banner" aria-label="Dashboard Header">
        <h1>Dashboard</h1>
        <form class="search-bar" role="search" aria-label="Search dashboard">
          <input
            type="search"
            placeholder="Search transactions, customers, subscriptions"
            aria-label="Search transactions, customers, subscriptions" />
          <span class="material-icons">search</span>
        </form>
        <div class="user-profile" tabindex="0" aria-haspopup="true" aria-expanded="false" aria-label="User profile menu">
          <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/d8f12544-16f5-4ed8-bc14-0a88a445c8e5.png" alt="User avatar for AB" />
          <span>AB</span>
          <span class="material-icons">arrow_drop_down</span>
        </div>
      </header>

      <section class="dashboard-cards" aria-label="Key metrics">
        <article class="card current-mrr" tabindex="0" aria-labelledby="current-mrr-title current-mrr-value">
          <small id="current-mrr-title">Current MRR</small>
          <span class="value" id="current-mrr-value" aria-live="polite">$12.4k</span>
        </article>
        <article class="card current-customers" tabindex="0" aria-labelledby="current-customers-title current-customers-value">
          <small id="current-customers-title">Current Customers</small>
          <span class="value" id="current-customers-value" aria-live="polite">16,601</span>
        </article>
        <article class="card active-customers" tabindex="0" aria-labelledby="active-customers-title active-customers-value">
          <small id="active-customers-title">Active Customers</small>
          <span class="value" id="active-customers-value" aria-live="polite">33%</span>
        </article>
        <article class="card churn-rate" tabindex="0" aria-labelledby="churn-rate-title churn-rate-value">
          <small id="churn-rate-title">Churn Rate</small>
          <span class="value" id="churn-rate-value" aria-live="polite">2%</span>
        </article>
      </section>

      <section class="lower-dashboard" aria-label="Dashboard data and analytics">
        <div class="trend-and-tickets">
          <article class="card-white trend-chart" aria-label="Trend chart">
            <h3>Trend</h3>
            <div class="filters" role="group" aria-label="Trend filters">
              <button type="button" class="active" aria-pressed="true" data-filter="sales">Sales</button>
              <button type="button" aria-pressed="false" data-filter="expense">Expense</button>
              <button type="button" aria-pressed="false" data-filter="profit">Profit</button>
              <button type="button" aria-pressed="false" data-filter="year">This Year</button>
            </div>
            <div class="bar-chart" aria-live="polite" aria-label="Monthly sales, expense and profit bar chart" role="img" tabIndex="0">
              <div class="bar sales" style="height:65%;" aria-label="January sales $6k"><span>Jan</span></div>
              <div class="bar expense" style="height:40%;" aria-label="January expense $4k"><span>Jan</span></div>
              <div class="bar profit" style="height:20%;" aria-label="January profit $2k"><span>Jan</span></div>

              <div class="bar sales" style="height:70%;" aria-label="February sales $7k"><span>Feb</span></div>
              <div class="bar expense" style="height:45%;" aria-label="February expense $4.5k"><span>Feb</span></div>
              <div class="bar profit" style="height:20%;" aria-label="February profit $3k"><span>Feb</span></div>

              <div class="bar sales" style="height:80%;" aria-label="March sales $8k"><span>Mar</span></div>
              <div class="bar expense" style="height:55%;" aria-label="March expense $5.5k"><span>Mar</span></div>
              <div class="bar profit" style="height:30%;" aria-label="March profit $4k"><span>Mar</span></div>

              <div class="bar sales" style="height:78%;" aria-label="April sales $7.8k"><span>Apr</span></div>
              <div class="bar expense" style="height:50%;" aria-label="April expense $5k"><span>Apr</span></div>
              <div class="bar profit" style="height:28%;" aria-label="April profit $3.8k"><span>Apr</span></div>

              <div class="bar sales" style="height:90%;" aria-label="May sales $9k"><span>May</span></div>
              <div class="bar expense" style="height:60%;" aria-label="May expense $6k"><span>May</span></div>
              <div class="bar profit" style="height:35%;" aria-label="May profit $4.5k"><span>May</span></div>

              <div class="bar sales" style="height:85%;" aria-label="June sales $8.5k"><span>Jun</span></div>
              <div class="bar expense" style="height:55%;" aria-label="June expense $5.5k"><span>Jun</span></div>
              <div class="bar profit" style="height:33%;" aria-label="June profit $4k"><span>Jun</span></div>

              <div class="bar sales" style="height:88%;" aria-label="July sales $8.8k"><span>Jul</span></div>
              <div class="bar expense" style="height:59%;" aria-label="July expense $5.9k"><span>Jul</span></div>
              <div class="bar profit" style="height:36%;" aria-label="July profit $4.2k"><span>Jul</span></div>
            </div>
          </article>

          <article class="card-white" aria-label="Support Tickets">
            <h3>Support Tickets</h3>
            <div class="support-tickets-filter" role="group" aria-label="Filter support tickets">
              <button type="button" class="active" aria-pressed="true" data-ticketfilter="all">All</button>
              <button type="button" aria-pressed="false" data-ticketfilter="open">Open</button>
              <button type="button" aria-pressed="false" data-ticketfilter="pending">Pending</button>
              <button type="button" aria-pressed="false" data-ticketfilter="closed">Closed</button>
            </div>
            <div class="tickets-list" role="list" aria-label="List of support tickets">
              <div class="ticket" role="listitem">
                <div class="ticket-info">
                  <div class="ticket-status-indicator status-pending" aria-label="Pending ticket status"></div>
                  <div class="ticket-email">jane.smith@company.com</div>
                  <div class="ticket-desc">Login issue</div>
                </div>
              </div>
              <div class="ticket" role="listitem">
                <div class="ticket-info">
                  <div class="ticket-status-indicator status-closed" aria-label="Closed ticket status"></div>
                  <div class="ticket-email">client.support@bigcorp.com</div>
                  <div class="ticket-desc">Billing inquiry</div>
                </div>
              </div>
              <div class="ticket" role="listitem">
                <div class="ticket-info">
                  <div class="ticket-status-indicator status-open" aria-label="Open ticket status"></div>
                  <div class="ticket-email">helpdesk@startup.org</div>
                  <div class="ticket-desc">Product issue/defect</div>
                </div>
              </div>
              <div class="ticket" role="listitem">
                <div class="ticket-info">
                  <div class="ticket-status-indicator status-closed" aria-label="Closed ticket status"></div>
                  <div class="ticket-email">julia.white@themeforest.org</div>
                  <div class="ticket-desc">Feature request</div>
                </div>
              </div>
            </div>
          </article>
        </div>

        <div class="sales-transactions">
          <article class="card-white pie-chart-container" aria-label="Sales data">
            <h3>Sales</h3>
            <p class="sales-number" aria-live="polite" aria-atomic="true">342</p>
            <canvas id="salesPieChart" aria-label="Sales distribution pie chart" role="img"></canvas>
            <div class="legend" aria-hidden="true">
              <div class="legend-item">
                <div class="legend-marker legend-sales"></div><span>Sales</span>
              </div>
              <div class="legend-item">
                <div class="legend-marker legend-refund"></div><span>Refund</span>
              </div>
              <div class="legend-item">
                <div class="legend-marker legend-canceled"></div><span>Canceled</span>
              </div>
            </div>
          </article>

          <article class="card-white transactions-list" aria-label="Recent transactions">
            <h3 class="transaction-header">Transactions</h3>
            <div class="transaction-items" role="list" aria-live="polite">
              <div class="transaction-item" role="listitem">
                <div class="transaction-name">J. Stephane</div>
                <div class="transaction-status status-paid" aria-label="Paid">Paid</div>
              </div>
              <div class="transaction-item" role="listitem">
                <div class="transaction-name">D. Dudley</div>
                <div class="transaction-status status-failed" aria-label="Failed">Failed</div>
              </div>
              <div class="transaction-item" role="listitem">
                <div class="transaction-name">K. Newcomb</div>
                <div class="transaction-status status-paid" aria-label="Paid">Paid</div>
              </div>
              <div class="transaction-item" role="listitem">
                <div class="transaction-name">S. Anderson</div>
                <div class="transaction-status status-paid" aria-label="Paid">Paid</div>
              </div>
              <div class="transaction-item" role="listitem">
                <div class="transaction-name">L. Frank</div>
                <div class="transaction-status status-pending" aria-label="Pending">Pending</div>
              </div>
              <div class="transaction-item" role="listitem">
                <div class="transaction-name">M. Walsh</div>
                <div class="transaction-status status-paid" aria-label="Paid">Paid</div>
              </div>
            </div>
            <button class="view-all-btn" aria-label="View all transactions">View all transactions</button>
          </article>
        </div>
      </section>

      <section class="card-white customer-demographic" aria-label="Customer Demographic Map">
        <h3>Customer Demographic</h3>
        <div class="map-container" aria-hidden="true">
          <img
            src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/7f650609-c93f-4fdc-9c7c-edda98cbd7c7.png"
            alt="Map showing customer demographic with highlighted countries in orange color"
            width="600"
            height="240"
            style="width: 100%; height: auto; border-radius: 16px"
            onerror="this.style.display='none'" />
        </div>
      </section>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Initialize pie chart for sales distribution
    const ctx = document.getElementById('salesPieChart').getContext('2d');
    const pieChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Sales', 'Refund', 'Canceled'],
        datasets: [{
          label: 'Sales Distribution',
          data: [220, 75, 47],
          backgroundColor: ['#ff931e', '#7a4200', '#000000cc'],
          borderWidth: 0
        }]
      },
      options: {
        cutout: '65%',
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        animation: {
          animateRotate: true,
          duration: 1000
        }
      }
    });

    // Accessibility fixes for toggle buttons on trend filters and tickets filters
    function toggleButtonGroup(containerSelector) {
      const container = document.querySelector(containerSelector);
      if (!container) return;
      const buttons = [...container.querySelectorAll('button')];

      buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
          buttons.forEach((b) => {
            b.classList.remove('active');
            b.setAttribute('aria-pressed', 'false');
          });
          btn.classList.add('active');
          btn.setAttribute('aria-pressed', 'true');
          // You can hook filtering logic here if needed
        });
      });
    }

    toggleButtonGroup('.trend-chart .filters');
    toggleButtonGroup('.support-tickets-filter');
  </script>
=======
<body class="bg-success text-white d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="text-center">
        <script> alert("SELAMAT DATANG <?=($user)?>")</script>
        <h1 class="mb-4">✅ Login Berhasil!</h1>
        <p>Selamat datang, <strong><?= htmlspecialchars($user) ?></strong></p>
        <p>Anda berhasil login ke domain: <strong><?= htmlspecialchars($domain) ?></strong></p>
        <a href="logout.php" class="btn btn-light mt-3">Logout</a>
    </div>
</body>

</html>