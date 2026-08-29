<style>
.prevent-select {
  -webkit-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
.stat-card {
  border-radius: 10px;
  padding: 20px;
  margin: 10px 0;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  transition: transform 0.3s;
}
.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}
.stat-number {
  font-size: 2.5rem;
  font-weight: bold;
  margin: 10px 0;
}
.stat-label {
  font-size: 0.9rem;
  color: #666;
  text-transform: uppercase;
}
.category-section {
  background: #f8f9fa;
  border-radius: 10px;
  padding: 20px;
  margin: 20px 0;
}
.progress-custom {
  height: 25px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: bold;
}
.chart-container {
  position: relative;
  height: 300px;
  margin: 20px 0;
}
.info-box {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px;
  border-radius: 10px;
  margin: 20px 0;
}
.table-custom {
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.table-custom thead {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
echo form_open('', array('id' => 'clustschool')); ?>

<input type="hidden" name="hidSchoolId" id="hidSchoolId" value="1">

<div class="row justify-content-center">
    <div class="card">
        <div class="card-body">
            <div class="col-lg-12">

    <?php
    for ($j = 0; $j < count($school); $j++) {
        $flag = 0;
        for ($k = 0; $k < count($part); $k++) {
            if ($school[$j]['school_code'] == $part[$k]['school_code']) {
                $entry = 'Yes';
                $flag = 1;
            }
        }
        if ($flag == 0)
            $entry = 'No';
        if ($school[$j]['is_finalize'] == 'Y')
            $finalize = 'Yes';
        else if ($school[$j]['is_finalize'] == 'N')
            $finalize = 'No';
        else
            $finalize = 'No';
        $status = (empty($school[$j]['is_finalize'])) ? 'N' : $school[$j]['is_finalize'];  ?>

        <br>

        <div class="row mt-3">
            <div class="col-12">
                <h3 class="text-primary prevent-select">Hi, <?php echo $school[$j]['school_name']; ?></h3>
            </div>
        </div>
        
        <div class="info-box mt-3">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="text-center prevent-select">Welcome to District Tamil Kalaivizha 2025 - 2026</h4>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4 text-center">
                    <strong>Sub District:</strong><br>
                    <?php echo isset($sub_district_name) ? $sub_district_name : 'N/A'; ?>
                </div>
                <div class="col-md-4 text-center">
                    <strong>Venue:</strong><br>
                    <?php echo isset($venue_details['venue']) ? $venue_details['venue'] : 'S N V H S Nankicity Kanjikuzhi'; ?>
                </div>
                <div class="col-md-4 text-center">
                    <strong>Date:</strong><br>
                    <?php 
                    if (isset($venue_details['start_date']) && isset($venue_details['end_date'])) {
                        echo date('d.m.y', strtotime($venue_details['start_date'])) . ' to ' . date('d.m.y', strtotime($venue_details['end_date']));
                    } else {
                        echo '26.11.24 to 30.11.24 [5 Days]';
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php if (isset($statistics)) { ?>
        
        <!-- Statistics Overview -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="stat-label">Total Students</div>
                    <div class="stat-number"><?php echo $statistics['total_students']; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <div class="stat-label">Boys</div>
                    <div class="stat-number"><?php echo $statistics['boys_count']; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                    <div class="stat-label">Girls</div>
                    <div class="stat-number"><?php echo $statistics['girls_count']; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                    <div class="stat-label">Result %</div>
                    <div class="stat-number"><?php echo $statistics['result_percentage']; ?>%</div>
                </div>
            </div>
        </div>

        <!-- Category-wise Analysis -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="category-section">
                    <h5 class="text-center mb-4">Category-wise Distribution</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><strong>LP (1-4)</strong></span>
                            <span><?php echo $statistics['lp_total']; ?> Students</span>
                        </div>
                        <div class="progress progress-custom mt-2">
                            <div class="progress-bar" style="width: <?php echo $statistics['total_students'] > 0 ? ($statistics['lp_boys']/$statistics['total_students']*100) : 0; ?>%; background: #667eea;">Boys: <?php echo $statistics['lp_boys']; ?></div>
                            <div class="progress-bar" style="width: <?php echo $statistics['total_students'] > 0 ? ($statistics['lp_girls']/$statistics['total_students']*100) : 0; ?>%; background: #f093fb;">Girls: <?php echo $statistics['lp_girls']; ?></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><strong>UP (5-7)</strong></span>
                            <span><?php echo $statistics['up_total']; ?> Students</span>
                        </div>
                        <div class="progress progress-custom mt-2">
                            <div class="progress-bar" style="width: <?php echo $statistics['total_students'] > 0 ? ($statistics['up_boys']/$statistics['total_students']*100) : 0; ?>%; background: #667eea;">Boys: <?php echo $statistics['up_boys']; ?></div>
                            <div class="progress-bar" style="width: <?php echo $statistics['total_students'] > 0 ? ($statistics['up_girls']/$statistics['total_students']*100) : 0; ?>%; background: #f093fb;">Girls: <?php echo $statistics['up_girls']; ?></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><strong>HS (8-10)</strong></span>
                            <span><?php echo $statistics['hs_total']; ?> Students</span>
                        </div>
                        <div class="progress progress-custom mt-2">
                            <div class="progress-bar" style="width: <?php echo $statistics['total_students'] > 0 ? ($statistics['hs_boys']/$statistics['total_students']*100) : 0; ?>%; background: #667eea;">Boys: <?php echo $statistics['hs_boys']; ?></div>
                            <div class="progress-bar" style="width: <?php echo $statistics['total_students'] > 0 ? ($statistics['hs_girls']/$statistics['total_students']*100) : 0; ?>%; background: #f093fb;">Girls: <?php echo $statistics['hs_girls']; ?></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><strong>HSS (11-12)</strong></span>
                            <span><?php echo $statistics['hss_total']; ?> Students</span>
                        </div>
                        <div class="progress progress-custom mt-2">
                            <div class="progress-bar" style="width: <?php echo $statistics['total_students'] > 0 ? ($statistics['hss_boys']/$statistics['total_students']*100) : 0; ?>%; background: #667eea;">Boys: <?php echo $statistics['hss_boys']; ?></div>
                            <div class="progress-bar" style="width: <?php echo $statistics['total_students'] > 0 ? ($statistics['hss_girls']/$statistics['total_students']*100) : 0; ?>%; background: #f093fb;">Girls: <?php echo $statistics['hss_girls']; ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="category-section">
                    <h5 class="text-center mb-4">Gender Distribution Chart</h5>
                    <div class="chart-container">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class-wise Detailed Table -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="category-section">
                    <h5 class="text-center mb-4">Class-wise Detailed Analysis</h5>
                    <table class="table table-custom table-striped">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Boys</th>
                                <th>Girls</th>
                                <th>Total</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (isset($statistics['class_wise']) && count($statistics['class_wise']) > 0) {
                                foreach ($statistics['class_wise'] as $class => $data) {
                                    $percentage = $statistics['total_students'] > 0 ? round(($data['total']/$statistics['total_students'])*100, 1) : 0;
                                    echo "<tr>";
                                    echo "<td><strong>Class $class</strong></td>";
                                    echo "<td>{$data['boys']}</td>";
                                    echo "<td>{$data['girls']}</td>";
                                    echo "<td><strong>{$data['total']}</strong></td>";
                                    echo "<td><span class='badge badge-primary' style='color: #000; background: #e3f2fd;'>$percentage%</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No data available</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot style="background: #f8f9fa; font-weight: bold;">
                            <tr>
                                <td>Total</td>
                                <td><?php echo $statistics['boys_count']; ?></td>
                                <td><?php echo $statistics['girls_count']; ?></td>
                                <td><?php echo $statistics['total_students']; ?></td>
                                <td>100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Result Analysis -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="category-section">
                    <h5 class="text-center mb-4">Performance Statistics</h5>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="stat-card" style="background: #e3f2fd;">
                                <div class="stat-label">Items Participated</div>
                                <div class="stat-number" style="color: #1976d2;"><?php echo $statistics['total_items_participated']; ?></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card" style="background: #fff3e0;">
                                <div class="stat-label">Prizes Won</div>
                                <div class="stat-number" style="color: #f57c00;"><?php echo $statistics['prizes_won']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="category-section">
                    <h5 class="text-center mb-4">Category-wise Chart</h5>
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <script>
        // Gender Distribution Chart
        var genderCtx = document.getElementById('genderChart').getContext('2d');
        var genderChart = new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Boys', 'Girls'],
                datasets: [{
                    data: [<?php echo $statistics['boys_count']; ?>, <?php echo $statistics['girls_count']; ?>],
                    backgroundColor: ['#667eea', '#f093fb'],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 14, weight: 'bold' },
                            padding: 20,
                            color: '#333'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' students';
                            }
                        }
                    }
                }
            }
        });

        // Category-wise Chart
        var categoryCtx = document.getElementById('categoryChart').getContext('2d');
        var categoryData = {
            labels: ['LP (1-4)', 'UP (5-7)', 'HS (8-10)', 'HSS (11-12)'],
            datasets: [{
                label: 'Boys',
                data: [<?php echo $statistics['lp_boys']; ?>, <?php echo $statistics['up_boys']; ?>, <?php echo $statistics['hs_boys']; ?>, <?php echo $statistics['hss_boys']; ?>],
                backgroundColor: '#667eea',
                borderWidth: 1,
                borderColor: '#fff'
            }, {
                label: 'Girls',
                data: [<?php echo $statistics['lp_girls']; ?>, <?php echo $statistics['up_girls']; ?>, <?php echo $statistics['hs_girls']; ?>, <?php echo $statistics['hss_girls']; ?>],
                backgroundColor: '#f093fb',
                borderWidth: 1,
                borderColor: '#fff'
            }]
        };
        
        var hasData = categoryData.datasets[0].data.some(v => v > 0) || categoryData.datasets[1].data.some(v => v > 0);
        
        if (hasData) {
            var categoryChart = new Chart(categoryCtx, {
                type: 'bar',
                data: categoryData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 14, weight: 'bold' },
                                padding: 15,
                                color: '#333'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' students';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#666'
                            },
                            grid: {
                                color: '#e0e0e0'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#666',
                                font: { weight: 'bold' }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        } else {
            categoryCtx.canvas.parentElement.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:300px;color:#999;font-size:18px;">No data available</div>';
        }
        </script>

        <?php } ?>

        <br>

        <!-- <button type="button" class="btn btn-outline-secondary" href="javascript:void(0)" onClick="javascript:goschooldet('<?php echo $school[$j]['school_code'] ?>')" style="width:120px; height:75px;">
                Registration
            </button> -->
<?php
    $flag = 0;
} ?> 
<?php echo form_close(); ?>

</div></div></div></div>
