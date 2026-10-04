<?php
require 'header.php';

// ดึงข้อมูลสรุปยอดขายแยกตามสินค้า (เรียงตามจำนวนที่ขายได้มากที่สุด 10 อันดับแรก)
$st = $pdo->prepare("
    SELECT 
        product_name, 
        SUM(qty) AS total_qty, 
        SUM(price * qty) AS total_sales 
    FROM order_items 
    GROUP BY product_name 
    ORDER BY total_qty DESC 
    LIMIT 10
");
$st->execute();
$sales_data = $st->fetchAll();

// เตรียม Array สำหรับส่งค่าให้ Chart.js (JavaScript)
$labels = [];
$quantities = [];
$sales_totals = [];

foreach ($sales_data as $row) {
    $labels[] = $row['product_name'];
    $quantities[] = (int)$row['total_qty'];
    $sales_totals[] = (float)$row['total_sales'];
}
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-bar-chart-line-fill"></i> รายงานสรุปยอดขายสินค้าขายดี</h2>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> กลับหน้าหลัก</a>
    </div>

    <!-- ส่วนแสดงกราฟแท่ง -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold text-secondary mb-3">กราฟเปรียบเทียบยอดขาย (จำนวนชิ้น)</h5>
            <div style="position: relative; height: 350px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- ส่วนตารางสรุปข้อมูล -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <h5 class="fw-bold text-secondary mb-3">ตารางสรุปอันดับสินค้าขายดี</h5>
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 80px;">อันดับ</th>
                            <th>ชื่อสินค้า</th>
                            <th class="text-center">จำนวนที่ขายได้ (ชิ้น)</th>
                            <th class="text-end">ยอดขายรวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sales_data)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">ยังไม่มีข้อมูลการสั่งซื้อ</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sales_data as $index => $row): ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= $index + 1 ?></td>
                                    <td><?= e($row['product_name']) ?></td>
                                    <td class="text-center fw-bold text-primary"><?= number_format($row['total_qty']) ?></td>
                                    <td class="text-end fw-bold text-success"><?= baht($row['total_sales']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- โหลด Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // แปลงค่า PHP Array เป็น JavaScript Array
    const labels = <?= json_encode($labels) ?>;
    const quantities = <?= json_encode($quantities) ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'จำนวนที่ขายได้ (ชิ้น)',
                data: quantities,
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
});
</script>

<?php require 'footer.php'; ?>