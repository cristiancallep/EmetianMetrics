<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoDashboard - Las mejores criptomonedas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .coin-card { cursor: pointer; transition: transform 0.2s; }
        .coin-card:hover { transform: translateY(-5px); }
        .price-up { color: #00b894; }
        .price-down { color: #ff7675; }
        .chart-container { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .sidebar-card { background: #f8f9fa; border-radius: 10px; padding: 15px; margin-bottom: 20px; }
        .trending-badge { font-size: 0.7rem; background: #e74c3c; color: white; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand h1 mb-0">
                <i class="bi bi-currency-bitcoin"></i> CryptoDashboard
            </span>
            <span class="text-white-50" id="lastUpdate"></span>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <!-- Sidebar izquierdo: Top 5 monedas -->
            <div class="col-md-3">
                <div class="sidebar-card">
                    <h5><i class="bi bi-trophy-fill text-warning"></i> Top 5 del momento</h5>
                    <div id="topCoinsList" class="list-group list-group-flush"></div>
                </div>
                
                <div class="sidebar-card">
                    <h5><i class="bi bi-newspaper"></i> Últimas noticias</h5>
                    <div id="newsList"></div>
                </div>
            </div>

            <!-- Main content: Gráfico y detalles -->
            <div class="col-md-9">
                <div class="chart-container mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3 id="selectedCoinName" class="mb-0">Bitcoin</h3>
                            <small id="selectedCoinSymbol" class="text-muted">BTC</small>
                        </div>
                        <div class="btn-group" id="timeRangeBtns">
                            <button class="btn btn-sm btn-outline-primary active" data-days="7">7D</button>
                            <button class="btn btn-sm btn-outline-primary" data-days="14">14D</button>
                            <button class="btn btn-sm btn-outline-primary" data-days="30">30D</button>
                            <button class="btn btn-sm btn-outline-primary" data-days="90">90D</button>
                        </div>
                    </div>
                    <canvas id="priceChart" height="300"></canvas>
                </div>

                <!-- Tabla completa de monedas -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Todas las criptomonedas</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="cryptoTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Moneda</th>
                                        <th>Precio</th>
                                        <th>24h %</th>
                                        <th>Market Cap</th>
                                        <th>Gráfico</th>
                                    </tr>
                                </thead>
                                <tbody id="cryptoTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    let chart = null;
    let currentCoinId = 'bitcoin';
    let currentDays = 7;

    // Mapeo de nombres a IDs de CoinGecko
const coinIds = {
    'bitcoin': 'bitcoin',
    'ethereum': 'ethereum',
    'binancecoin': 'binancecoin', 
    'ripple': 'ripple',
    'cardano': 'cardano',
    'solana': 'solana',
    'dogecoin': 'dogecoin',
    'polkadot': 'polkadot',
    'polygon': 'matic-network',
    'litecoin': 'litecoin',
    'chainlink': 'chainlink',
    'uniswap': 'uniswap',
    'avalanche': 'avalanche-2',
    'tron': 'tron',
    'stellar': 'stellar'
};

    // Cargar datos iniciales
    $(document).ready(function() {
        loadTopCoins();
        loadAllCoins();
        loadChartData(currentCoinId, currentDays);
        
        $('#timeRangeBtns button').click(function() {
            $('#timeRangeBtns button').removeClass('active');
            $(this).addClass('active');
            currentDays = $(this).data('days');
            loadChartData(currentCoinId, currentDays);
        });
    });

    // Cargar top 5 monedas para sidebar
    function loadTopCoins() {
        $.get('api/top-coins.php', function(data) {
            const top5 = data.slice(0, 5);
            let html = '';
            top5.forEach(coin => {
                const changeClass = coin.price_change_percentage_24h >= 0 ? 'price-up' : 'price-down';
                const changeIcon = coin.price_change_percentage_24h >= 0 ? 'arrow-up' : 'arrow-down';
                html += `
                    <a href="#" class="list-group-item list-group-item-action" onclick="selectCoin('${coin.id}', '${coin.name}', '${coin.symbol}')">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <img src="${coin.image}" width="24" height="24" class="me-2">
                                <strong>${coin.name}</strong>
                                <small class="text-muted">${coin.symbol.toUpperCase()}</small>
                            </div>
                            <div class="text-end">
                                <div>$${coin.current_price.toLocaleString()}</div>
                                <small class="${changeClass}">
                                    <i class="bi bi-${changeIcon}"></i> ${Math.abs(coin.price_change_percentage_24h).toFixed(2)}%
                                </small>
                            </div>
                        </div>
                    </a>
                `;
            });
            $('#topCoinsList').html(html);
            
            // Actualizar timestamp
            $('#lastUpdate').text('Actualizado: ' + new Date().toLocaleTimeString());
        });
    }

    // Cargar tabla completa
    function loadAllCoins() {
        $.get('api/top-coins.php', function(data) {
            let html = '';
            data.forEach((coin, index) => {
                const changeClass = coin.price_change_percentage_24h >= 0 ? 'price-up' : 'price-down';
                const changeBg = coin.price_change_percentage_24h >= 0 ? 'success' : 'danger';
                html += `
                    <tr onclick="selectCoin('${coin.id}', '${coin.name}', '${coin.symbol}')" style="cursor: pointer">
                        <td>${index + 1}</td>
                        <td>
                            <img src="${coin.image}" width="24" height="24" class="me-2">
                            ${coin.name}
                            <small class="text-muted">${coin.symbol.toUpperCase()}</small>
                        </td>
                        <td>$${coin.current_price.toLocaleString()}</td>
                        <td class="${changeClass}">
                            ${coin.price_change_percentage_24h?.toFixed(2) || 0}%
                        </td>
                        <td>$${coin.market_cap.toLocaleString()}</td>
                        <td>
                            <canvas id="sparkline_${coin.id}" width="100" height="30"></canvas>
                        </td>
                    </tr>
                `;
            });
            $('#cryptoTableBody').html(html);
            
            // Dibujar sparklines después de que el DOM esté listo
            setTimeout(() => {
                data.forEach(coin => {
                    if (coin.sparkline_in_7d?.price) {
                        drawSparkline(`sparkline_${coin.id}`, coin.sparkline_in_7d.price);
                    }
                });
            }, 100);
        });

        
console.log('Monedas cargadas:', data.map(c => ({ id: c.id, name: c.name, symbol: c.symbol })));
    }

    // Cargar datos del gráfico principal 
function loadChartData(coinId, days) {
    // Mostrar loading en el gráfico
    const ctx = document.getElementById('priceChart').getContext('2d');
    if (chart) {
        chart.data.datasets[0].data = [];
        chart.update();
    }
    
    $.get(`api/chart-data.php?coin=${coinId}&days=${days}`, function(data) {
        // Verificar si hay datos válidos
        if (!data || !data.prices || data.prices.length === 0) {
            console.warn(`No hay datos para ${coinId}`);
            // Mostrar mensaje amigable en el gráfico
            if (chart) {
                chart.data.datasets[0].data = [];
                chart.update();
            }
            // Mostrar notificación al usuario
            $('#selectedCoinName').parent().append('<small class="text-warning ms-2">⚠️ Sin datos históricos</small>');
            setTimeout(() => {
                $('.text-warning').remove();
            }, 3000);
            return;
        }
        
        // Limpiar mensajes de error anteriores
        $('.text-warning').remove();
        
        const prices = data.prices.map(p => ({ x: new Date(p[0]), y: p[1] }));
        const labels = prices.map(p => p.x.toLocaleDateString());
        const values = prices.map(p => p.y);
        
        if (chart) {
            chart.destroy();
        }
        
        const newCtx = document.getElementById('priceChart').getContext('2d');
        chart = new Chart(newCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Precio USD',
                    data: values,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `$${ctx.raw.toLocaleString()}`
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => '$' + value.toLocaleString()
                        }
                    }
                }
            }
        });
    }).fail(function(error) {
        console.error('Error al cargar gráfico:', error);
        // Mostrar mensaje de error amigable
        $('#selectedCoinName').parent().append('<small class="text-danger ms-2">❌ Error al cargar datos</small>');
        setTimeout(() => {
            $('.text-danger').remove();
        }, 3000);
    });
}

    // Seleccionar moneda para ver gráfico (con ID correcto)
function selectCoin(id, name, symbol) {
    // Algunos IDs necesitan mapeo especial
    const idMap = {
        'binancecoin': 'binancecoin',
        'avalanche-2': 'avalanche-2',
        'matic-network': 'matic-network',
        'usd-coin': 'usd-coin',
        'binance-usd': 'binance-usd'
    };
    
    // Usar el ID que viene de la API
    currentCoinId = id;
    $('#selectedCoinName').text(name);
    $('#selectedCoinSymbol').text(symbol.toUpperCase());
    
    // Resetear a 7 días cuando cambias de moneda
    currentDays = 7;
    $('#timeRangeBtns button').removeClass('active');
    $('#timeRangeBtns button[data-days="7"]').addClass('active');
    
    loadChartData(currentCoinId, currentDays);
}

    // Dibujar sparkline mini
    function drawSparkline(canvasId, data) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        
        ctx.clearRect(0, 0, width, height);
        
        const min = Math.min(...data);
        const max = Math.max(...data);
        const range = max - min;
        
        ctx.beginPath();
        ctx.strokeStyle = data[0] < data[data.length-1] ? '#00b894' : '#ff7675';
        ctx.lineWidth = 1.5;
        
        data.forEach((price, i) => {
            const x = (i / (data.length - 1)) * width;
            const y = height - ((price - min) / range) * height;
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        ctx.stroke();
    }
    </script>
</body>
</html>