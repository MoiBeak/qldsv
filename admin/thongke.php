<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="sidebar">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="logo.png" alt="Logo">
                </span>
                <div class="text header-text">
                    <span class="name">Admin</span>
                    <span class="profession">Web QLDSV</span>
                </div>
            </div>
        </header>
        <div class="menu-bar">
            <div class="menu">
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="admin.php">
                            <i class="bx bx-home-alt icon"></i>
                            <span class="text nav-text">Trang chủ</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="thongtinsv.php">
                            <i class="bx bx-search icon"></i>
                            <span class="text nav-text">Search</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="thongke.php">
                            <i class="bx bx-bar-chart-alt-2 icon"></i>
                            <span class="text nav-text">Thống kê</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="quanlydiem.php">
                            <i class="bx bx-book icon"></i>
                            <span class="text nav-text">Quản lý điểm</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="bottom-content">
                <li class="nav-link">
                    <a href="../model/logout.php">
                        <i class="bx bx-log-out icon"></i>
                        <span class="text nav-text">Đăng Xuất</span>
                    </a>
                </li>
            </div>
        </div>
    </nav>
    <section class="home">
        <div class="chart-container">
            <h1>Biểu đồ Điểm (Pie Chart)</h1>
            <canvas id="myPieChart"></canvas>
            <div class="chart-legend" id="chartLegend"></div>
        </div>
    </section>


    <!-- JavaScript -->
    <script>
        // Fetch dữ liệu từ fetch_data.php
        fetch('fetch_data.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                const ctx = document.getElementById('myPieChart').getContext('2d');
                const labels = data.map(item => item.grade);
                const percentages = data.map(item => item.percentage);
                const colors = ['#4caf50', '#2196f3', '#ffc107', '#f44336']; // Màu sắc

                // Tạo biểu đồ
                const myPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Tỷ lệ (%)',
                            data: percentages,
                            backgroundColor: colors
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true, // Duy trì tỷ lệ biểu đồ
                        plugins: {
                            legend: {
                                display: false // Ẩn chú thích mặc định
                            }
                        }
                    }
                });

                // Thêm chú thích dưới biểu đồ
                const legendContainer = document.getElementById('chartLegend');
                labels.forEach((label, index) => {
                    const legendItem = document.createElement('div');
                    legendItem.innerHTML = `
                        <span style="display: inline-block; width: 12px; height: 12px; background-color: ${colors[index]}; margin-right: 10px;"></span>
                        ${label}: ${percentages[index]}%
                    `;
                    legendContainer.appendChild(legendItem);
                });
            });
    </script>
</body>
</html>
