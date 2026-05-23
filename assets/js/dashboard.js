const API_BASE = '../backend/consumo_api/api';
let chart = null;
let currentCoinId = 'bitcoin';
let currentDays = 7;
let allCoins = [];

const state = {
	coins: [],
	filtered: []
};

function money(value) {
	return new Intl.NumberFormat('en-US', {
		style: 'currency',
		currency: 'USD',
		maximumFractionDigits: value < 1 ? 6 : 2
	}).format(value || 0);
}

function number(value) {
	return new Intl.NumberFormat('en-US').format(value || 0);
}

function formatPercent(value) {
	const safeValue = Number(value || 0);
	return `${safeValue >= 0 ? '+' : ''}${safeValue.toFixed(2)}%`;
}

function setLastUpdate() {
	document.getElementById('lastUpdate').textContent = `Actualizado: ${new Date().toLocaleTimeString()}`;
}

function getPdfLibrary() {
	return window.jspdf && window.jspdf.jsPDF ? window.jspdf.jsPDF : null;
}

function getSelectedCoin() {
	return state.coins.find((coin) => coin.id === currentCoinId) || state.coins[0] || null;
}

function toggleSidebar() {
	document.body.classList.toggle('sidebar-collapsed');
	localStorage.setItem('emetian_sidebar_collapsed', document.body.classList.contains('sidebar-collapsed') ? '1' : '0');
}

function downloadPdfReport() {
	const PdfDocument = getPdfLibrary();
	if (!PdfDocument) {
		alert('No se pudo cargar la librería PDF.');
		return;
	}

	const pdf = new PdfDocument();
	const selectedCoin = getSelectedCoin();
	const totalMarketCap = state.filtered.reduce((sum, coin) => sum + Number(coin.market_cap || 0), 0);
	const avgChange = state.filtered.reduce((sum, coin) => sum + Number(coin.price_change_percentage_24h || 0), 0) / (state.filtered.length || 1);
	const topCoins = [...state.filtered].slice(0, 8);

	pdf.setFontSize(18);
	pdf.text('EmetianMetrics - Informe de criptomonedas', 14, 18);
	pdf.setFontSize(11);
	pdf.text(`Generado: ${new Date().toLocaleString()}`, 14, 26);
	pdf.text(`Moneda seleccionada: ${selectedCoin ? selectedCoin.name : 'N/A'}`, 14, 34);
	pdf.text(`Rango de días: ${currentDays}`, 14, 42);
	pdf.text(`Market cap total: ${money(totalMarketCap)}`, 14, 50);
	pdf.text(`Cambio promedio 24h: ${formatPercent(avgChange)}`, 14, 58);

	let y = 72;
	pdf.setFontSize(13);
	pdf.text('Top monedas visibles', 14, y);
	y += 8;
	pdf.setFontSize(10);
	for (const coin of topCoins) {
		if (y > 270) {
			pdf.addPage();
			y = 18;
		}
		pdf.text(`${coin.name} (${coin.symbol.toUpperCase()}) - ${money(coin.current_price)} - ${formatPercent(coin.price_change_percentage_24h)}`, 14, y);
		y += 7;
	}

	pdf.save(`emetianmetrics-reporte-${new Date().toISOString().slice(0, 10)}.pdf`);
}

function drawSparkline(canvasId, data) {
	const canvas = document.getElementById(canvasId);
	if (!canvas || !data || data.length < 2) return;

	const ctx = canvas.getContext('2d');
	const width = canvas.width;
	const height = canvas.height;
	ctx.clearRect(0, 0, width, height);

	const min = Math.min(...data);
	const max = Math.max(...data);
	const range = max - min || 1;

	ctx.beginPath();
	ctx.lineWidth = 2;
	ctx.strokeStyle = data[0] <= data[data.length - 1] ? '#1da56b' : '#e25555';

	data.forEach((price, index) => {
		const x = (index / (data.length - 1)) * width;
		const y = height - ((price - min) / range) * (height - 4) - 2;
		if (index === 0) {
			ctx.moveTo(x, y);
		} else {
			ctx.lineTo(x, y);
		}
	});

	ctx.stroke();
}

function renderTopCoins(coins) {
	const topFive = coins.slice(0, 5);
	const container = document.getElementById('topCoinsList');
	container.innerHTML = topFive.map((coin) => {
		const change = Number(coin.price_change_percentage_24h || 0);
		const changeClass = change >= 0 ? 'up' : 'down';
		return `
			<div class="coin-item" data-coin-id="${coin.id}" data-coin-name="${coin.name}" data-coin-symbol="${coin.symbol}">
				<div class="coin-name">
					<img src="${coin.image}" alt="${coin.name}" />
					<div>
						<strong>${coin.name}</strong>
						<span>${coin.symbol}</span>
					</div>
				</div>
				<div class="coin-price">
					<strong>${money(coin.current_price)}</strong>
					<small class="coin-change ${changeClass}">${formatPercent(change)}</small>
				</div>
			</div>
		`;
	}).join('');

	container.querySelectorAll('.coin-item').forEach((item) => {
		item.addEventListener('click', () => {
			selectCoin(item.dataset.coinId, item.dataset.coinName, item.dataset.coinSymbol);
		});
	});
}

function renderStats(coins) {
	const totalMarketCap = coins.reduce((sum, coin) => sum + Number(coin.market_cap || 0), 0);
	const avgChange = coins.reduce((sum, coin) => sum + Number(coin.price_change_percentage_24h || 0), 0) / (coins.length || 1);
	const topGainer = [...coins].sort((a, b) => Number(b.price_change_percentage_24h || 0) - Number(a.price_change_percentage_24h || 0))[0];
	const topLoser = [...coins].sort((a, b) => Number(a.price_change_percentage_24h || 0) - Number(b.price_change_percentage_24h || 0))[0];

	document.getElementById('totalMarketCap').textContent = money(totalMarketCap);
	document.getElementById('marketCapNote').textContent = `${coins.length} monedas rastreadas por la API`;
	document.getElementById('avgChange').textContent = formatPercent(avgChange);
	document.getElementById('avgChangeNote').textContent = avgChange >= 0 ? 'Mercado en tendencia positiva' : 'Mercado en tendencia negativa';
	document.getElementById('topGainer').textContent = topGainer ? `${topGainer.name} ${formatPercent(topGainer.price_change_percentage_24h)}` : '-';
	document.getElementById('topGainerNote').textContent = topLoser ? `Peor desempeño: ${topLoser.name}` : 'Mayor subida del día';
	document.getElementById('coinsCount').textContent = number(coins.length);
}

function renderTable(coins) {
	const tbody = document.getElementById('cryptoTableBody');
	tbody.innerHTML = coins.map((coin, index) => {
		const change = Number(coin.price_change_percentage_24h || 0);
		const changeClass = change >= 0 ? 'change-up' : 'change-down';
		return `
			<tr data-coin-id="${coin.id}" data-coin-name="${coin.name}" data-coin-symbol="${coin.symbol}">
				<td>${index + 1}</td>
				<td>
					<div class="td-coin">
						<img src="${coin.image}" alt="${coin.name}" />
						<div>
							<strong>${coin.name}</strong><br />
							<small>${coin.symbol.toUpperCase()}</small>
						</div>
					</div>
				</td>
				<td>${money(coin.current_price)}</td>
				<td class="${changeClass}">${formatPercent(change)}</td>
				<td>${money(coin.market_cap)}</td>
				<td><canvas id="sparkline_${coin.id}" class="sparkline" width="110" height="32"></canvas></td>
			</tr>
		`;
	}).join('');

	tbody.querySelectorAll('tr').forEach((row) => {
		row.addEventListener('click', () => {
			selectCoin(row.dataset.coinId, row.dataset.coinName, row.dataset.coinSymbol);
		});
	});

	setTimeout(() => {
		coins.forEach((coin) => {
			if (coin.sparkline_in_7d?.price) {
				drawSparkline(`sparkline_${coin.id}`, coin.sparkline_in_7d.price);
			}
		});
	}, 80);
}

function updateChart(prices) {
	const labels = prices.map((item) => new Date(item[0]).toLocaleDateString());
	const values = prices.map((item) => item[1]);
	const context = document.getElementById('priceChart').getContext('2d');

	if (chart) {
		chart.destroy();
	}

	chart = new Chart(context, {
		type: 'line',
		data: {
			labels,
			datasets: [{
				label: 'Precio USD',
				data: values,
				borderColor: '#1f7ae0',
				backgroundColor: 'rgba(31, 122, 224, 0.10)',
				borderWidth: 3,
				fill: true,
				tension: 0.35,
				pointRadius: 0,
				pointHoverRadius: 4
			}]
		},
		options: {
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: { display: false },
				tooltip: {
					callbacks: {
						label: (ctx) => money(ctx.raw)
					}
				}
			},
			scales: {
				x: { grid: { display: false } },
				y: {
					ticks: {
						callback: (value) => money(value)
					}
				}
			}
		}
	});
}

function loadChartData(coinId, days) {
	fetch(`${API_BASE}/chart-data.php?coin=${encodeURIComponent(coinId)}&days=${days}`)
		.then((response) => response.json())
		.then((data) => {
			if (!data || !data.prices || !data.prices.length) {
				document.getElementById('selectedCoinName').insertAdjacentHTML('afterend', '<small class="status-inline warning">Sin datos históricos</small>');
				return;
			}

			const warning = document.querySelector('.status-inline.warning');
			if (warning) warning.remove();
			updateChart(data.prices);
		})
		.catch(() => {
			document.getElementById('selectedCoinName').insertAdjacentHTML('afterend', '<small class="status-inline error">Error al cargar datos</small>');
		});
}

function selectCoin(id, name, symbol) {
	currentCoinId = id;
	document.getElementById('selectedCoinName').textContent = name;
	document.getElementById('selectedCoinSymbol').textContent = symbol.toUpperCase();
	currentDays = 7;
	document.querySelectorAll('.time-btn').forEach((button) => button.classList.remove('active'));
	document.querySelector('.time-btn[data-days="7"]').classList.add('active');
	loadChartData(currentCoinId, currentDays);
}

function applySearch(term) {
	const normalized = term.trim().toLowerCase();
	state.filtered = !normalized ? state.coins : state.coins.filter((coin) => {
		return coin.name.toLowerCase().includes(normalized) || coin.symbol.toLowerCase().includes(normalized);
	});
	renderStats(state.filtered);
	renderTopCoins(state.filtered);
	renderTable(state.filtered);
}

function loadCoins() {
	fetch(`${API_BASE}/top-coins.php`)
		.then((response) => response.json())
		.then((data) => {
			state.coins = Array.isArray(data) ? data : [];
			state.filtered = state.coins;
			renderStats(state.coins);
			renderTopCoins(state.coins);
			renderTable(state.coins);
			setLastUpdate();
			loadChartData(currentCoinId, currentDays);
		})
		.catch((error) => {
			console.error('Error al cargar monedas:', error);
		});
}

document.addEventListener('DOMContentLoaded', () => {
	const sidebarCollapsed = localStorage.getItem('emetian_sidebar_collapsed') === '1';
	if (sidebarCollapsed) {
		document.body.classList.add('sidebar-collapsed');
	}

	loadCoins();
	setInterval(setLastUpdate, 1000 * 60);

	document.getElementById('sidebarToggle').addEventListener('click', toggleSidebar);
	document.getElementById('downloadPdfBtn').addEventListener('click', downloadPdfReport);

	document.getElementById('timeRangeBtns').addEventListener('click', (event) => {
		const button = event.target.closest('.time-btn');
		if (!button) return;
		document.querySelectorAll('.time-btn').forEach((item) => item.classList.remove('active'));
		button.classList.add('active');
		currentDays = Number(button.dataset.days || 7);
		loadChartData(currentCoinId, currentDays);
	});

	document.getElementById('coinSearch').addEventListener('input', (event) => {
		applySearch(event.target.value);
	});
});
