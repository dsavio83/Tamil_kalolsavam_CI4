<?php if (count($details) > 0) { ?>
    <div class="table-container">
        <h4 class="text-center mb-4">
            <i class="fas fa-trophy text-warning me-2 animate__animated animate__bounceIn"></i>
            Competition Results - <?php echo $details[0]['fest_name']; ?>
        </h4>

        <!-- Mobile Card Layout -->
        <div class="mobile-cards">
            <div class="row">
                <?php
                $index = 1;
                foreach ($details as $value) {
                    if ($value['is_publish'] != 'N') {
                        ?>
                        <div class="col-12 mb-4">
                            <div class="card result-card animate__animated animate__fadeInUp position-relative">
                                <?php
                                $rank = $value['rank'];
                                if ($rank == 1) {
                                    echo '<div class="rank-badge rank-1">1</div>';
                                } elseif ($rank == 2) {
                                    echo '<div class="rank-badge rank-2">2</div>';
                                } elseif ($rank == 3) {
                                    echo '<div class="rank-badge rank-3">3</div>';
                                }
                                ?>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3 text-center">
                                            <div class="index-number"><?php echo $index; ?></div>
                                            <?php
                                            $reg = $value['admn_no'];
                                            if (@$Photo['pic'][$reg]) {
                                                @$pic_path = @$Photo['pic'][$reg];
                                                echo "<img src='" . @$pic_path . "' class='img-fluid rounded-circle result-photo' alt='Photo'>";
                                            } else {
                                                @$pic_path = base_url() . "photos/img_user_nophoto.png";
                                                echo "<img src='" . @$pic_path . "' class='img-fluid rounded-circle result-photo' alt='No photo'>";
                                            }
                                            ?>
                                        </div>
                                        <div class="col-9">
                                            <h5 class="card-title"><?php echo $value['participant_name']; ?> <span class="badge bg-info"><?php echo $value['class']; ?></span></h5>
                                            <p class="card-text">
                                                <strong>Reg. No & Code No:</strong> <?php echo $value['participant_id']; ?> & <?php echo $value['code_no']; ?><br>
                                                <strong>School Name:</strong> <?php echo $value['school_name']; ?><br>
                                                <strong>Grade:</strong>
                                                <?php
                                                $grade = $value['grade'];
                                                if ($grade == 'A') echo '<span class="badge bg-success">Grade A</span>';
                                                elseif ($grade == 'B') echo '<span class="badge bg-warning">Grade B</span>';
                                                elseif ($grade == 'C') echo '<span class="badge bg-primary">Grade C</span>';
                                                else echo '<span class="badge bg-secondary">' . $grade . '</span>';
                                                ?><br>
                                                <strong>Point:</strong> <span class="text-success"><?php echo $value['point']; ?> pts</span>
                                                <?php if ($rank == 1) { ?>
                                                    <div class="eligible-message">
                                                        <h6>Eligible for Higher Level</h6>
                                                    </div>
                                                <?php } ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $index++;
                    }
                }
                ?>
            </div>
        </div>

        <!-- Desktop Table Layout -->
        <div class="desktop-table">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Reg. No</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Code No.</th>
                            <th>School Name</th>
                            <th>Grade</th>
                            <th>Point</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $index = 1;
                        foreach ($details as $value) {
                            if ($value['is_publish'] != 'N') {
                                ?>
                                <tr>
                                    <td><?php echo $index; ?></td>
                                    <td class="text-center">
                                        <?php
                                        $reg = $value['admn_no'];
                                        if (@$Photo['pic'][$reg]) {
                                            @$pic_path = @$Photo['pic'][$reg];
                                            echo "<img src='" . @$pic_path . "' width='60' height='60' class='img-thumbnail rounded-circle'>";
                                        } else {
                                            @$pic_path = base_url() . "photos/img_user_nophoto.png";
                                            echo "<img src='" . @$pic_path . "' width='60' height='60' class='img-thumbnail rounded-circle'>";
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $value['participant_id']; ?></td>
                                    <td>
                                        <?php echo $value['participant_name']; ?>
                                        <?php if ($value['rank'] == 1) { ?>
                                            <div class="eligible-text">Eligible for Higher Level</div>
                                        <?php } ?>
                                    </td>
                                    <td><span class="badge bg-info"><?php echo $value['class']; ?></span></td>
                                    <td><?php echo $value['code_no']; ?></td>
                                    <td><?php echo $value['school_name']; ?></td>
                                    <td>
                                        <?php
                                        $grade = $value['grade'];
                                        if ($grade == 'A') echo '<span class="badge bg-success">Grade A</span>';
                                        elseif ($grade == 'B') echo '<span class="badge bg-warning">Grade B</span>';
                                        elseif ($grade == 'C') echo '<span class="badge bg-primary">Grade C</span>';
                                        else echo '<span class="badge bg-secondary">' . $grade . '</span>';
                                        ?>
                                    </td>
                                    <td><strong class="text-success"><?php echo $value['point']; ?> pts</strong></td>
                                </tr>
                                <?php
                                $index++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="no-results">
        <i class="fas fa-clock text-muted"></i>
        <h4>No Results Found</h4>
        <p class="text-muted">No results available for the selected item.</p>
    </div>
<?php } ?>
