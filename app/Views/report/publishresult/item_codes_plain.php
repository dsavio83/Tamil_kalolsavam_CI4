<style>
	<?php
	$printCategories = !empty($hide_lp) ? [2 => 'UP', 3 => 'HS', 4 => 'HSS'] : [1 => 'LP', 2 => 'UP', 3 => 'HS', 4 => 'HSS'];
	$printItemsByCategory = array_fill_keys(array_keys($printCategories), []);
	foreach (($items ?? []) as $printItem) {
		$printFestId = (int) ($printItem['fest_id'] ?? 0);
		if (isset($printItemsByCategory[$printFestId])) {
			$printItemsByCategory[$printFestId][] = $printItem;
		}
	}
	?>
	.badge-code {
		font-family: 'Inter', monospace;
		font-weight: 700;
		font-size: 0.86rem;
		background: #eff6ff;
		color: #1d4ed8;
		border: 1px solid #bfdbfe;
		border-radius: 6px;
		padding: 0.25rem 0.55rem;
		display: inline-block;
	}

	#itemsTable {
		font-size: 14px;
		font-family: 'TAU-Pallai', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	}

	#itemsTable td {
		vertical-align: middle;
	}

	.items-print-table {
		display: none;
	}

	.cat-btn {
		font-size: 0.82rem;
		font-weight: 600;
	}

	@media print {
		@page {
			size: portrait;
			margin: 25mm 9mm 9mm;
		}

		body {
			background: #fff !important;
			color: #000 !important;
		}

		#global-preloader,
		#sidebarBackdrop,
		#sidebar,
		#header,
		#debug-bar,
		#debugbar_loader,
		.header,
		.header-nav,
		.sidebar,
		.footer,
		#footer,
		.pagetitle nav,
		.pagetitle>div:last-child,
		.item-codes-toolbar {
			display: none !important;
		}

		#main {
			margin: 0 !important;
			padding: 0 !important;
		}

		.pagetitle {
			display: none !important;
		}

		.section,
		.card,
		.card-body,
		.table-responsive {
			display: block !important;
			width: 100% !important;
			margin: 0 !important;
			padding: 0 !important;
			border: 0 !important;
			box-shadow: none !important;
			overflow: visible !important;
		}

		#itemsTable {
			display: none !important;
		}

		.items-print-table {
			display: table !important;
			width: 100% !important;
			table-layout: fixed !important;
			border-collapse: collapse !important;
			font: 7pt Arial, sans-serif !important;
			color: #000 !important;
		}

		.items-print-table thead {
			display: table-header-group;
		}

		.items-print-table {
			break-inside: auto;
			page-break-inside: auto;
		}

		.items-print-table.print-page-break {
			break-before: page;
			page-break-before: always;
		}

		.items-print-table tr {
			break-inside: avoid;
			page-break-inside: avoid;
		}

		.items-print-table caption {
			caption-side: top;
			text-align: center;
			font-weight: bold;
			padding: 0 0 3mm;
			color: #000 !important;
		}

		.items-print-table caption span {
			display: block;
		}

		.items-print-table .print-report-title {
			font-size: 14pt;
			margin: 0 0 1mm;
		}

		.items-print-table .print-category-title {
			font-size: 12pt;
		}

		.items-print-table th,
		.items-print-table td {
			padding: 7px 4px !important;
			line-height: 1.4 !important;
			border: 1px solid #555 !important;
			color: #000 !important;
			background: #fff !important;
			overflow-wrap: anywhere;
		}

		.items-print-table th:nth-child(1),
		.items-print-table td:nth-child(1) {
			width: 4% !important;
		}

		.items-print-table th:nth-child(2),
		.items-print-table td:nth-child(2) {
			width: 8% !important;
		}

		.items-print-table th:nth-child(3),
		.items-print-table td:nth-child(3) {
			width: 22% !important;
		}

		.items-print-table th:nth-child(4),
		.items-print-table td:nth-child(4) {
			width: 22% !important;
			white-space: normal !important;
			overflow-wrap: anywhere !important;
			word-break: break-word !important;
		}

		.items-print-table th:nth-child(5),
		.items-print-table td:nth-child(5) {
			width: 8% !important;
		}

		.items-print-table th:nth-child(6),
		.items-print-table td:nth-child(6) {
			width: 8% !important;
		}

		.items-print-table th:nth-child(7),
		.items-print-table td:nth-child(7) {
			width: 11% !important;
		}

		.items-print-table th:nth-child(8),
		.items-print-table td:nth-child(8) {
			width: 8% !important;
		}

		.items-print-table th:nth-child(9),
		.items-print-table td:nth-child(9) {
			width: 9% !important;
		}

		.items-print-table th {
			background: #e8edf5 !important;
			font-weight: bold !important;
		}

		.items-print-table .print-participation-note td {
			padding: 7px 9px !important;
			font-size: 8.5pt !important;
			line-height: 1.5 !important;
			background: #f7f9fc !important;
		}

		.items-print-table .print-participation-note-title {
			display: block;
			margin: 0 0 2px;
			font-weight: bold;
			font-size: 9pt;
		}

		.items-print-table .print-participation-note ul {
			margin: 0;
			padding-left: 18px;
		}

		.items-print-table .print-participation-note li {
			padding: 1px 0;
		}
	}
</style>

<!-- Standard Admin Pagetitle & Breadcrumb -->
<div class="pagetitle d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
	<div>
		<h1 class="mb-1"><?php echo esc($pdf_title ?? 'Item Codes Directory'); ?></h1>
		<nav>
			<ol class="breadcrumb mb-0">
				<li class="breadcrumb-item"><a href="<?php echo base_url('welcome'); ?>">Home</a></li>
				<li class="breadcrumb-item">Downloads</li>
				<li class="breadcrumb-item active">Item Codes</li>
			</ol>
		</nav>
	</div>
	<div class="d-flex align-items-center gap-2">
		<?php if (!empty($sub_district_name)): ?>
			<span
				class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-bold">
				<i class="bi bi-geo-alt me-1"></i> <?php echo esc($sub_district_name); ?> Sub-District
			</span>
		<?php elseif (!empty($district_name)): ?>
			<span
				class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-bold">
				<i class="bi bi-geo-alt me-1"></i> <?php echo esc($district_name); ?> District
			</span>
		<?php endif; ?>
		<span class="badge bg-secondary px-3 py-2 rounded-pill fw-bold" id="itemCountBadge">
			<span id="statTotal"><?php echo count($items ?? []); ?></span> Items
		</span>
	</div>
</div><!-- End Page Title -->

<!-- Main Content Area: Direct Clean Table View (No Standalone Header, No District/Sub-District/School Filters) -->
<section class="section">
	<div class="card shadow-sm border-0">
		<div class="card-body pt-3">

			<!-- TOOLBAR: Live Search & Category Tabs Only (No District, Sub-District, School dropdowns) -->
			<div
				class="item-codes-toolbar d-flex justify-content-between align-items-center flex-wrap gap-2 pb-3 mb-3 border-bottom">

				<!-- Category Filter Pills -->
				<div class="d-flex align-items-center gap-1 flex-wrap" id="categoryPillContainer">
					<span class="small fw-bold text-secondary me-2">Category:</span>
					<button type="button" class="btn btn-sm btn-primary rounded-pill px-3 cat-btn active"
						onclick="setCategoryFilter('', this)">All</button>
					<?php if (empty($hide_lp)): ?><button type="button"
							class="btn btn-sm btn-outline-secondary rounded-pill px-3 cat-btn" onclick="setCategoryFilter('1', this)">LP
							Kalaivizha</button><?php endif; ?>
					<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 cat-btn"
						onclick="setCategoryFilter('2', this)">UP Kalaivizha</button>
					<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 cat-btn"
						onclick="setCategoryFilter('3', this)">HS Kalaivizha</button>
					<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 cat-btn"
						onclick="setCategoryFilter('4', this)">HSS Kalaivizha</button>
				</div>

				<!-- Search Box & Print Button -->
				<div class="d-flex align-items-center gap-2">
					<div class="input-group input-group-sm" style="min-width: 220px;">
						<span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
						<input type="text" id="searchBox" class="form-control shadow-none" placeholder="Search code or name..."
							value="<?php echo esc($search_query ?? ''); ?>" oninput="onSearchInput()">
						<button class="btn btn-outline-secondary" type="button" onclick="clearSearch()" title="Clear">
							<i class="bi bi-x"></i>
						</button>
					</div>
					<button type="button" onclick="window.print()"
						class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-inline-flex align-items-center">
						<i class="bi bi-printer me-1"></i>
						<span>Print</span>
					</button>
				</div>

			</div>

			<!-- DIRECT SIMPLE TABLE VIEW (Category column removed; Pinnani column added after Max Reg.) -->
			<div class="table-responsive">
				<table class="table table-hover table-bordered table-striped align-middle" id="itemsTable">
					<thead class="table-light">
						<tr>
							<th style="width: 50px;" class="text-center">#</th>
							<th style="width: 100px;" class="text-center">Item Code</th>
							<th>Competition Item Name</th>
							<th style="width: 170px;">Section / Vibhagam</th>
							<th style="width: 100px;" class="text-center">Type</th>
							<th style="width: 110px;" class="text-center">Stage</th>
							<th style="width: 110px;" class="text-center">Time Limit</th>
							<th style="width: 95px;" class="text-center">Max Reg.</th>
							<th style="width: 95px;" class="text-center">Pinnani</th>
							<th style="width: 110px;" class="text-center">Indicators</th>
						</tr>
					</thead>
					<tbody id="itemsTableBody">
						<?php if (!empty($items)):
							foreach (array_values($items) as $itemIndex => $item):
								$isGroup = (($item['item_type'] ?? '') === 'G');
								$isOffStage = (($item['is_off_stage'] ?? '') === 'Y');
								?>
								<tr>
									<td class="text-center text-muted fw-bold"><?php echo $itemIndex + 1; ?></td>
									<td class="text-center">
										<span class="badge-code"><?php echo esc($item['item_code']); ?></span>
									</td>
									<td>
										<div class="fw-bold text-dark"><?php echo esc($item['item_name']); ?></div>
									</td>
									<td>
										<span class="badge bg-light text-secondary border">
											<?php echo esc($item['vibhagam_name'] ?: 'General'); ?>
										</span>
									</td>
									<td class="text-center">
										<?php if ($isGroup): ?>
											<span
												class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1">
												<i class="bi bi-people me-1"></i> Group
											</span>
										<?php else: ?>
											<span
												class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1">
												<i class="bi bi-person me-1"></i> Single
											</span>
										<?php endif; ?>
									</td>
									<td class="text-center">
										<?php if ($isOffStage): ?>
											<span
												class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 rounded-pill px-2 py-1">
												Off Stage
											</span>
										<?php else: ?>
											<span
												class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2 py-1">
												On Stage
											</span>
										<?php endif; ?>
									</td>
									<td class="text-center fw-semibold text-secondary">
										<?php echo !empty($item['max_time']) ? esc($item['max_time'] . ' ' . $item['time_type']) : '-'; ?>
									</td>
									<td class="text-center fw-bold text-dark">
										<?php echo (int) ($item['max_participants'] ?? 1); ?>
									</td>
									<td class="text-center fw-bold text-secondary">
										<?php echo (int) ($item['max_pinnani'] ?? 0); ?>
									</td>
									<td class="text-center">
										<button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1"
											style="font-size: 11.5px; font-weight: 500;"
											onclick="showIndicatorsByIndex(<?php echo (int) $itemIndex; ?>)"
											title="மதிப்பீட்டுக் குறிப்புகளைக் காண்க">
											<i class="bi bi-card-checklist me-1"></i>View
										</button>
									</td>
								</tr>
							<?php endforeach; else: ?>
							<tr>
								<td colspan="10" class="text-center py-5 text-muted">
									<i class="bi bi-folder2-open fs-3 text-secondary mb-2 d-block"></i>
									<div class="fw-bold">No competition items found.</div>
									<small class="text-muted">Try clearing search filters.</small>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>

				<?php foreach ($printCategories as $categoryId => $categoryName): ?>
					<table class="items-print-table <?php echo $categoryId === 1 ? '' : 'print-page-break'; ?>">
						<caption>
							<span class="print-report-title"><?php echo esc($pdf_title ?? 'Item Codes Kalaivizha'); ?></span>
							<span class="print-category-title"><?php echo esc($categoryName); ?> Tamil Kalaivizha</span>
						</caption>
						<thead>
							<tr>
								<th align="center">#</th>
								<th align="center">Item Code</th>
								<th>Competition Item Name</th>
								<th>Section / Vibhagam</th>
								<th align="center">Type</th>
								<th align="center">Stage</th>
								<th align="center">Time Limit</th>
								<th align="center">Max Reg.</th>
								<th align="center">Pinnani</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($printItemsByCategory[$categoryId])): ?>
								<?php foreach ($printItemsByCategory[$categoryId] as $rowNumber => $item): ?>
									<tr>
										<td align="center"><?php echo $rowNumber + 1; ?></td>
										<td align="center"><?php echo esc($item['item_code'] ?? ''); ?></td>
										<td><?php echo esc($item['item_name'] ?? ''); ?></td>
										<td><?php echo esc(($item['vibhagam_name'] ?? '') ?: 'General'); ?></td>
										<td align="center"><?php echo (($item['item_type'] ?? '') === 'G') ? 'Group' : 'Single'; ?></td>
										<td align="center"><?php echo (($item['is_off_stage'] ?? '') === 'Y') ? 'Off Stage' : 'On Stage'; ?></td>
										<td align="center">
											<?php echo !empty($item['max_time']) ? esc($item['max_time'] . ' ' . ($item['time_type'] ?? '')) : '-'; ?>
										</td>
										<td align="center"><?php echo (int) ($item['max_participants'] ?? 1); ?></td>
										<td align="center"><?php echo (int) ($item['max_pinnani'] ?? 0); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="9" align="center">No competition items found.</td>
								</tr>
							<?php endif; ?>
							<tr class="print-participation-note">
								<td colspan="9">
									<span class="print-participation-note-title">குறிப்புகள்:</span>
									<ul>
										<li>ஒரு மாணவர் அதிகபட்சமாக 3 தனிநபர் போட்டிகளிலும், 2 குழுப் போட்டிகளிலும் பங்கேற்கலாம்.</li>
										<?php if ($categoryId === 1): ?>
											<li>திருக்குறள் ஒப்புவித்தல் போட்டியில் 3, 4ஆம் வகுப்பு மாணவர்கள் மட்டுமே பங்கேற்க வேண்டும்.</li>
											<?php
											$show_action_song_note = false;
											if (isset($selected_sub_district)) {
												$show_action_song_note = ((int)$selected_sub_district === 218);
											} elseif (!empty($sub_district_name)) {
												$show_action_song_note = (mb_stripos($sub_district_name, 'Mannarkkad') !== false || mb_stripos($sub_district_name, 'mannarkad') !== false);
											}
											if ($show_action_song_note && !empty($items)) {
												foreach ($items as $it) {
													if ((int)($it['item_code'] ?? 0) === 166) {
														$show_action_song_note = true;
														break;
													}
												}
											}
											?>
											<?php if ($show_action_song_note): ?>
											<li>Action Song (166) போட்டி இனத்திற்கு 1, 2ஆம் வகுப்பு மாணவர்கள் மட்டும் பங்கேற்க வேண்டும்.</li>
											<?php endif; ?>
										<?php endif; ?>
									</ul>
								</td>
							</tr>
						</tbody>
					</table>
				<?php endforeach; ?>
			</div>

		</div>
	</div>
</section>

<!-- GRADING INDICATORS / VALUE POINTS MODAL -->
<div class="modal fade" id="gradingIndicatorsModal" tabindex="-1" aria-labelledby="gradingIndicatorsModalLabel"
	aria-hidden="true" style="z-index: 10055;">
	<div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
		<div class="modal-content shadow-lg border-0 rounded-4">
			<div class="modal-header border-bottom bg-light px-4 py-3">
				<div class="d-flex align-items-center gap-2">
					<div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
						style="width: 38px; height: 38px;">
						<i class="bi bi-card-checklist fs-5"></i>
					</div>
					<div>
						<h5 class="modal-title fw-bold text-dark mb-0" id="gradingIndicatorsModalLabel">Grading Indicators</h5>
						<small class="text-muted" id="giModalSubTitle"></small>
					</div>
				</div>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4">
				<div class="d-flex flex-wrap align-items-center gap-2 mb-3" id="giMetaBadges"></div>

				<div class="card border rounded-3 mb-3 overflow-hidden shadow-2xs">
					<div
						class="card-header bg-light py-2 px-3 fw-semibold small text-secondary d-flex justify-content-between align-items-center">
						<span><i class="bi bi-table me-1.5 text-primary"></i>&nbsp; மதிப்பீட்டுக் குறிப்புகள் / Value Points</span>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-bordered mb-0 align-middle" id="giDynamicTable"
								style="font-family: 'TAU-Pallai', sans-serif; font-size: 14px;">
							</table>
						</div>
					</div>
				</div>

				<div class="card border rounded-3 mb-1 shadow-2xs" id="giImageCard" style="display: none;">
					<div
						class="card-header bg-light py-2 px-3 fw-semibold small text-secondary d-flex justify-content-between align-items-center">
						<span><i class="bi bi-image me-1.5 text-success"></i>&nbsp; அட்டவணைப் படம் (Table Image)</span>
						<a id="giImageDownloadBtn" href="#" target="_blank"
							class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5" style="font-size: 11.5px;">
							<i class="bi bi-box-arrow-up-right me-1"></i>படத்தைத் திற / அச்சிடு
						</a>
					</div>
					<div class="card-body p-3 text-center bg-light-subtle">
						<img id="giImagePreview" src="" alt="Value Points Table" class="img-fluid border rounded bg-white shadow-sm"
							style="max-height: 280px;" />
					</div>
				</div>
			</div>
			<div class="modal-footer border-top bg-light px-4 py-2.5">
				<button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal"
					style="font-size: 13.5px;">மூடுக</button>
			</div>
		</div>
	</div>
</div>

<!-- INTERACTIVE DYNAMIC SCRIPT -->
<script>
	const itemCodesData = <?php echo json_encode(array_values($items ?? []), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	let currentItems = itemCodesData;
	let selectedCategory = '<?php echo esc($selected_fest_id ?? ''); ?>';
	const itemCodesDistrict = '<?php echo (int) ($selected_district ?? 0); ?>';
	const itemCodesSubDistrict = '<?php echo (int) ($selected_sub_district ?? 0); ?>';
	let searchTimeout = null;

	function setCategoryFilter(catId, btn) {
		selectedCategory = catId;
		const buttons = document.querySelectorAll('#categoryPillContainer .cat-btn');
		buttons.forEach(b => {
			b.classList.remove('btn-primary', 'active');
			b.classList.add('btn-outline-secondary');
		});
		if (btn) {
			btn.classList.remove('btn-outline-secondary');
			btn.classList.add('btn-primary', 'active');
		}
		triggerFilter();
	}

	function onSearchInput() {
		if (searchTimeout) clearTimeout(searchTimeout);
		searchTimeout = setTimeout(() => {
			triggerFilter();
		}, 250);
	}

	function clearSearch() {
		document.getElementById('searchBox').value = '';
		triggerFilter();
	}

	async function triggerFilter() {
		const search = document.getElementById('searchBox').value.trim();
		const params = new URLSearchParams({
			ajax: '1',
			category: selectedCategory,
			search: search,
			district: itemCodesDistrict,
			sub_district: itemCodesSubDistrict
		});

		try {
			const response = await fetch('<?php echo base_url('downloads/item_codes'); ?>?' + params.toString());
			const data = await response.json();
			if (data.status === 'success') {
				renderItemsTable(data.items);
				const total = data.items ? data.items.length : 0;
				const statTotalEl = document.getElementById('statTotal');
				if (statTotalEl) statTotalEl.innerText = total;
			}
		} catch (err) {
			console.error('Filter error:', err);
		}
	}

	function renderItemsTable(items) {
		const tbody = document.getElementById('itemsTableBody');
		if (!tbody) return;

		currentItems = items || [];

		if (!items || items.length === 0) {
			tbody.innerHTML = `
						<tr>
								<td colspan="10" class="text-center py-5 text-muted">
										<i class="bi bi-folder2-open fs-3 text-secondary mb-2 d-block"></i>
										<div class="fw-bold">No competition items found.</div>
										<small class="text-muted">Try clearing search filters.</small>
								</td>
						</tr>
				`;
			return;
		}

		let html = '';
		items.forEach((item, index) => {
			const isGroup = (item.item_type === 'G');
			const isOffStage = (item.is_off_stage === 'Y');
			const timeText = item.max_time ? `${item.max_time} ${item.time_type || ''}` : '-';

			html += `
						<tr>
								<td class="text-center text-muted fw-bold">${index + 1}</td>
								<td class="text-center">
										<span class="badge-code">${escapeHtml(item.item_code)}</span>
								</td>
								<td>
										<div class="fw-bold text-dark">${escapeHtml(item.item_name)}</div>
								</td>
								<td>
										<span class="badge bg-light text-secondary border">
												${escapeHtml(item.vibhagam_name || 'General')}
										</span>
								</td>
								<td class="text-center">
										${isGroup ? '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1"><i class="bi bi-people me-1"></i> Group</span>' : '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1"><i class="bi bi-person me-1"></i> Single</span>'}
								</td>
								<td class="text-center">
										${isOffStage ? '<span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 rounded-pill px-2 py-1">Off Stage</span>' : '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2 py-1">On Stage</span>'}
								</td>
								<td class="text-center fw-semibold text-secondary">${escapeHtml(timeText)}</td>
								<td class="text-center fw-bold text-dark">${item.max_participants || 1}</td>
								<td class="text-center fw-bold text-secondary">${item.max_pinnani || 0}</td>
								<td class="text-center">
										<button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" style="font-size: 11.5px; font-weight: 500;" onclick="showIndicatorsByIndex(${index})" title="மதிப்பீட்டுக் குறிப்புகளைக் காண்க">
												<i class="bi bi-card-checklist me-1"></i>View
										</button>
								</td>
						</tr>
				`;
		});
		tbody.innerHTML = html;
	}

	function showIndicatorsByIndex(index) {
		const item = (currentItems || [])[index];
		if (item) showGradingIndicators(item);
	}

	function showGradingIndicators(item) {
		if (!item) return;

		document.getElementById('gradingIndicatorsModalLabel').innerText = 'Grading Indicators: #' + item.item_code + ' - ' + item.item_name;
		document.getElementById('giModalSubTitle').innerText = (item.fest_name || '') + (item.vibhagam_name ? ' • ' + item.vibhagam_name : '');

		const metaContainer = document.getElementById('giMetaBadges');
		if (metaContainer) {
			metaContainer.innerHTML = `
						<span class="badge bg-light text-dark border px-2.5 py-1">குறியீடு: <strong>${escapeHtml(item.item_code)}</strong></span>
						<span class="badge bg-light text-dark border px-2.5 py-1">வகை: <strong>${item.item_type === 'G' ? 'குழுப் போட்டி (Group)' : 'தனியாள் போட்டி (Single)'}</strong></span>
						<span class="badge bg-light text-dark border px-2.5 py-1">நேரம்: <strong>${escapeHtml(String(item.max_time || 5))} ${item.time_type === 'H' ? 'மணி' : 'நிமிடம்'}</strong></span>
						<span class="badge bg-light text-dark border px-2.5 py-1">பங்கேற்பாளர்: <strong>${item.max_participants || 1}</strong></span>
				`;
		}

		const table = document.getElementById('giDynamicTable');
		if (table) {
			table.innerHTML = '';
			const vpText = item.value_points || '';
			const lines = vpText.split(/\r?\n/).map(l => l.trim()).filter(l => l !== '');
			const titleLines = [];
			const rows = [];

			lines.forEach(line => {
				const m = line.match(/^(\d+)\s*[\)\.]\s*(?:[\-\–]\s*)?(.*)$/);
				if (m) {
					const num = m[1];
					const rest = m[2].trim();
					const rm = rest.match(/^(.*?)\s*[\-\–]\s*([^\-\–]+)$/);
					if (rm) {
						rows.push({ no: num, desc: rm[1].trim(), marks: rm[2].trim() });
					} else {
						rows.push({ no: num, desc: rest, marks: '' });
					}
				} else {
					titleLines.push(line);
				}
			});

			const titleHtml = titleLines.length > 0
				? `<tr class="table-primary text-center fw-bold" style="background-color: #e0e7ff;"><td colspan="3" class="py-2.5 text-primary-emphasis" style="font-size: 15px;">${titleLines.map(escapeHtml).join('<br>')}</td></tr>`
				: '';

			const headHtml = `
						<tr class="table-light text-center fw-bold text-secondary" style="font-size: 13px;">
								<th style="width: 55px;">எண்</th>
								<th style="width: 55%; text-align: left; padding-left: 15px;">மதிப்பீட்டுக் குறிப்பு / Indicator</th>
								<th style="width: 220px;">மதிப்பெண் / Points</th>
						</tr>
				`;

			let bodyHtml = '';
			if (rows.length > 0) {
				rows.forEach(r => {
					bodyHtml += `
										<tr>
												<td class="text-center fw-semibold text-secondary">${escapeHtml(r.no)}</td>
												<td class="ps-3">${escapeHtml(r.desc)}</td>
												<td class="text-center fw-semibold text-primary">${escapeHtml(r.marks)}</td>
										</tr>
								`;
				});
			} else if (vpText.trim() !== '') {
				bodyHtml = `<tr><td colspan="3" class="text-center text-muted py-3" style="white-space: pre-line;">${escapeHtml(vpText)}</td></tr>`;
			} else {
				bodyHtml = `<tr><td colspan="3" class="text-center text-muted py-3">மதிப்பீட்டுக் குறிப்புகள் பதிவு செய்யப்படவில்லை.</td></tr>`;
			}

			table.innerHTML = `<thead>${titleHtml}${headHtml}</thead><tbody>${bodyHtml}</tbody>`;
		}

		const imgCard = document.getElementById('giImageCard');
		const imgEl = document.getElementById('giImagePreview');
		const imgDownload = document.getElementById('giImageDownloadBtn');
		if (imgCard && imgEl) {
			const imgSrc = '<?php echo base_url("value_points/"); ?>' + item.item_code + '.png?v=' + Date.now();
			imgCard.style.display = 'none';
			imgEl.src = imgSrc;
			if (imgDownload) imgDownload.href = imgSrc;
			imgEl.onerror = function () { imgCard.style.display = 'none'; };
			imgEl.onload = function () { imgCard.style.display = 'block'; };
		}

		const modalEl = document.getElementById('gradingIndicatorsModal');
		const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
		modal.show();
	}

	function escapeHtml(text) {
		if (!text) return '';
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}
</script>
