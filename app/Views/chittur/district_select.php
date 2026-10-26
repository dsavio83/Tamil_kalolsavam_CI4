<?php
// No direct access
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!doctype html>
<html lang="ta">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Select District - Tamil Kalaivizha Item Codes</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
      --accent-gradient: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }

    body {
      font-family: 'Inter', 'TAU-Pallai', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans Tamil', sans-serif;
      background-color: #f8fafc;
      color: #1e293b;
      min-height: 100vh;
    }

    .header-banner {
      background: var(--primary-gradient);
      color: white;
      border-radius: 1.25rem;
      box-shadow: 0 10px 25px -5px rgba(30, 60, 114, 0.25);
    }

    .stat-card {
      border: 1px solid #e2e8f0;
      border-radius: 1rem;
      background: #ffffff;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08);
      border-color: #cbd5e1;
    }

    .filter-btn.active {
      background: #0f172a !important;
      color: #ffffff !important;
      border-color: #0f172a !important;
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
    }

    .card {
      border: none;
      border-radius: 1rem;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    @media print {
      body { background: #fff !important; color: #000 !important; padding: 0 !important; }
      .no-print { display: none !important; }
      .header-banner { background: none !important; color: #000 !important; box-shadow: none !important; border: 1px solid #ccc; padding: 1.5rem !important; }
      .header-banner h2 { color: #000 !important; font-size: 1.5rem !important; }
    }
  </style>
</head>
<body class="py-4">

<div class="container-fluid px-lg-5 px-3">

  <!-- Header Banner -->
  <div class="header-banner p-4 p-md-5 mb-4 position-relative overflow-hidden">
    <div class="row align-items-center">
      <div class="col-lg-8 col-md-7">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-20 text-white small fw-medium mb-3">
          <i class="bi bi-trophy-fill text-warning"></i>
          <span>Kerala School Kalolsavam 2025-26</span>
        </div>
        <h2 class="fw-bold text-white mb-2">Tamil Kalaivizha Item Codes</h2>
        <p class="text-white-50 mb-0 fs-6">Select District and Sub-District</p>
      </div>
      <div class="col-lg-4 col-md-5 text-md-end mt-3 mt-md-0 no-print">
        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
          <button onclick="window.print()" class="btn btn-light rounded-pill px-3 py-2 fw-shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-printer-fill text-primary"></i> Print
          </button>
          <a href="/welcome" class="btn btn-outline-light rounded-pill px-3 py-2 fw-shadow-sm d-inline-flex align-items-center gap-2">
            <i class="bi bi-house-door-fill"></i> Home
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- District Selection Card -->
  <div class="card mb-4 no-print">
    <div class="card-header bg-white pb-3">
      <h4 class="card-title fw-bold mb-0">Select District</h4>
    </div>
    <div class="card-body p-4">
      <div class="row g-3">
        <div class="col-12">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-geo-fill text-primary"></i></span>
            <select id="districtSelect" class="form-select border-start-0 border-end-0" onchange="loadTaluks(this.value)">
              <option value="">-- Select District --</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row g-3 mt-3">
        <div class="col-12">
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-building-fill text-secondary"></i></span>
            <select id="talukSelect" class="form-select border-start-0 border-end-0" onchange="loadItemCodes(this.value)" disabled>
              <option value="">-- Select Sub-District --</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Items Table Card -->
  <div class="item-table-container">
    <div class="table-responsive">
      <table class="table custom-table align-middle mb-0" id="itemCodesTable">
        <thead>
          <tr>
            <th class="text-center" style="width: 50px;">#</th>
            <th class="text-center" style="width: 100px;">Item Code</th>
            <th class="text-center" style="width: 90px;">Section</th>
            <th>Item Category</th>
            <th>Item Name</th>
            <th class="text-center" style="width: 100px;">Gender</th>
            <th class="text-center" style="width: 110px;">Item Type</th>
            <th class="text-center" style="width: 110px;">Max Time</th>
            <th class="text-center" style="width: 120px;">Participants</th>
            <th class="text-center" style="width: 100px;">Pinnani</th>
            <th class="text-center" style="width: 110px;">Stage Mode</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          <!-- Items will be loaded here -->
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
async function loadDistricts() {
  const response = await fetch('/chittur/getDistricts');
  const districts = await response.json();
  
  const select = document.getElementById('districtSelect');
  select.innerHTML = '<option value="">-- Select District --</option>';
  
  districts.forEach(district => {
    const option = document.createElement('option');
    option.value = district.code;
    option.textContent = district.name;
    select.appendChild(option);
  });
}

async function loadTaluks(districtCode) {
  if (!districtCode) {
    document.getElementById('talukSelect').disabled = true;
    document.getElementById('talukSelect').innerHTML = '<option value="">-- Select Sub-District --</option>';
    document.getElementById('itemCodesTable').innerHTML = '<tbody id="tableBody"></tbody>';
    return;
  }
  
  const response = await fetch(`/chittur/getTaluks?districtCode=${districtCode}`);
  const taluks = await response.json();
  
  const select = document.getElementById('talukSelect');
  select.disabled = false;
  select.innerHTML = '<option value="">-- Select Sub-District --</option>';
  
  taluks.forEach(taluk => {
    const option = document.createElement('option');
    option.value = taluk.code;
    option.textContent = taluk.name;
    select.appendChild(option);
  });
  
  // Clear table
  document.getElementById('tableBody').innerHTML = '';
}

async function loadItemCodes(talukCode) {
  if (!talukCode) {
    document.getElementById('itemCodesTable').innerHTML = '<tbody id="tableBody"></tbody>';
    return;
  }
  
  const response = await fetch(`/chittur/getItemCodes?talukCode=${talukCode}`);
  const items = await response.json();
  
  const tbody = document.getElementById('tableBody');
  tbody.innerHTML = '';
  
  // Section mapping: vibhagam_id 1,2 = LP; 3,4 = UP; 5,6 = HS; 7,8 = HSS
  const sectionNames = {
    1: 'LP', 2: 'LP', 3: 'UP', 4: 'UP', 5: 'HS', 6: 'HS', 7: 'HSS', 8: 'HSS'
  };
  
  const typeNames = { S: 'Single (S)', G: 'Group (G)' };
  
  items.forEach((item, index) => {
    const section = sectionNames[item.section] || 'Unknown';
    const type = typeNames[item.type] || 'Unknown';
    const offStage = item.is_off_stage === 'Y' ? 'Off Stage' : 'On Stage';
    const common = item.is_common === 'Y' ? 'Common' : 'Specific';
    
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="text-center text-muted fw-semibold">${index + 1}</td>
      <td class="text-center"><span class="item-code-badge">${item.item_code}</span></td>
      <td class="text-center"><span class="badge badge-fest-${section.toLowerCase()} rounded-pill px-2 py-1">${section}</span></td>
      <td class="text-secondary small fw-medium">${type} ${common}</td>
      <td class="fw-bold text-dark">${item.item_name}</td>
      <td class="text-center"><span class="badge bg-light text-secondary border rounded-pill px-2">${common}</span></td>
      <td class="text-center"><span class="badge badge-${item.type === 'S' ? 'single' : 'group'} rounded-pill px-2 py-1"><i class="bi bi-${item.type === 'S' ? 'person-fill' : 'people-fill'} me-1"></i>${type}</span></td>
      <td class="text-center"><span class="badge bg-light text-dark border rounded-pill px-2"><i class="bi bi-clock me-1 text-primary"></i>${item.max_time} Mins</span></td>
      <td class="text-center fw-semibold text-dark">1</td>
      <td class="text-center text-muted">0</td>
      <td class="text-center"><span class="badge badge-${offStage.toLowerCase().replace(' ', '-') } rounded-pill px-2 py-1"><i class="bi bi-${offStage === 'On Stage' ? 'display' : 'pencil-square'} me-1"></i>${offStage}</span></td>
    `;
    tbody.appendChild(tr);
  });
}

// Initial load
document.addEventListener('DOMContentLoaded', loadDistricts);
</script>

</body>
</html>