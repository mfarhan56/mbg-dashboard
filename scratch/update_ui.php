<?php
$html = file_get_contents('../MBG.html');

$sidebarOld = <<<EOT
        <ul class="sidebar-menu">
            <li><a href="#dashboard" class="active"><i class="bi bi-grid-1x2-fill"></i> Dashboard & Scan</a></li>
            <li><a href="#groups"><i class="bi bi-people-fill"></i> Data Kelompok</a></li>
            <li><a href="#students"><i class="bi bi-person-lines-fill"></i> Data Peserta</a></li>
            <li><a href="#print"><i class="bi bi-printer-fill"></i> Cetak Kartu</a></li>
            <hr class="mx-3 my-2 text-muted">
            <li><a href="#" onclick="resetData()"><i class="bi bi-arrow-counterclockwise text-danger"></i> <span class="text-danger">Reset Data</span></a></li>
        </ul>
EOT;
$sidebarNew = <<<EOT
        <ul class="sidebar-menu">
            <li><a href="#dashboard" class="active"><i class="bi bi-grid-1x2-fill"></i> Dashboard Overview</a></li>
            <li><a href="#scan" onclick="setScanMode('pickup')"><i class="bi bi-box-arrow-up-right"></i> Scan Pengambilan</a></li>
            <li><a href="#scan" onclick="setScanMode('return')"><i class="bi bi-box-arrow-in-down-left"></i> Scan Pengembalian</a></li>
            <li><a href="#groups"><i class="bi bi-people-fill"></i> Data Kelompok</a></li>
            <li><a href="#students"><i class="bi bi-person-lines-fill"></i> Data Peserta</a></li>
            <li><a href="#print"><i class="bi bi-printer-fill"></i> Cetak Kartu</a></li>
            <hr class="mx-3 my-2 text-muted">
            <li><a href="#" onclick="resetData()"><i class="bi bi-arrow-counterclockwise text-danger"></i> <span class="text-danger">Reset Data</span></a></li>
        </ul>
EOT;
$html = str_replace($sidebarOld, $sidebarNew, $html);

// Remove the old scanner area from dashboard
$dashboardFullOld = <<<EOT
            <!-- DASHBOARD SECTION -->
            <section id="dashboard" class="section-page active">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon primary"><i class="bi bi-people"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-total">0</h3>
                                <p>Total Peserta</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon success"><i class="bi bi-check-circle"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-picked">0</h3>
                                <p>Sudah Mengambil</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon danger"><i class="bi bi-x-circle"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-notpicked">0</h3>
                                <p>Belum Mengambil</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon warning"><i class="bi bi-collection"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-groups">0</h3>
                                <p>Total Kelompok</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Scanner Area -->
                    <div class="col-md-6 mb-4">
                        <div class="scanner-container h-100 d-flex flex-column justify-content-center align-items-center">
                            <h5 class="mb-4 fw-bold">Scan QR Code Peserta</h5>
                            <button id="btn-start-scan" class="btn btn-success btn-lg px-4 rounded-pill shadow-sm mb-3" onclick="startScan()">
                                <i class="bi bi-camera me-2"></i> Mulai Scan
                            </button>
                            <div id="reader"></div>
                        </div>
                    </div>

                    <!-- Result Area -->
                    <div class="col-md-6 mb-4">
                        <div class="result-card text-center">
                            <h5 class="mb-4 fw-bold">Informasi Peserta</h5>
                            
                            <div id="scan-alert" class="alert alert-success fw-bold" style="display: none;">
                                <!-- Alert content -->
                            </div>

                            <img src="https://ui-avatars.com/api/?name=P&background=f3f4f6&color=a1a1aa&size=100" class="avatar" alt="Avatar">
                            
                            <h4 id="res-name" class="fw-bold">-</h4>
                            <p class="text-muted mb-4"><span id="res-nis">-</span> | <span id="res-class">-</span></p>
                            
                            <div class="row text-start bg-light p-3 rounded mb-3">
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">Kelompok</small>
                                    <strong id="res-group">-</strong>
                                </div>
                                <div class="col-6 mb-2 text-end">
                                    <small class="text-muted d-block">Status</small>
                                    <div id="res-status">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
EOT;

$dashboardFullNew = <<<EOT
            <!-- DASHBOARD SECTION -->
            <section id="dashboard" class="section-page active">
                <h5 class="mb-3 fw-bold text-muted">Statistik Peserta</h5>
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon primary"><i class="bi bi-people"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-total">0</h3>
                                <p>Total Peserta</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon success"><i class="bi bi-box-arrow-up-right"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-picked">0</h3>
                                <p>Peserta Sudah Ambil</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon danger"><i class="bi bi-x-circle"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-notpicked">0</h3>
                                <p>Peserta Belum Ambil</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon primary" style="background-color: rgba(37, 99, 235, 0.1); color: #2563eb;"><i class="bi bi-box-arrow-in-down-left"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-returned">0</h3>
                                <p>Peserta Sdh Kembali</p>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3 fw-bold text-muted">Statistik Kelompok</h5>
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon warning"><i class="bi bi-collection"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-groups">0</h3>
                                <p>Total Kelompok</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card" style="border: 2px solid var(--success-color);">
                            <div class="stat-icon success"><i class="bi bi-check2-all"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-groups-picked">0</h3>
                                <p>Klp Sudah Ambil</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card" style="border: 2px solid var(--danger-color);">
                            <div class="stat-icon danger"><i class="bi bi-exclamation-circle"></i></div>
                            <div class="stat-details">
                                <h3 id="stat-groups-notpicked">0</h3>
                                <p>Klp Belum Ambil</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SCANNER SECTION -->
            <section id="scan" class="section-page">
                <div class="row">
                    <!-- Scanner Area -->
                    <div class="col-md-6 mb-4">
                        <div class="scanner-container h-100 d-flex flex-column justify-content-center align-items-center">
                            <h5 class="mb-4 fw-bold" id="scan-title">Scan QR Code (Pengambilan)</h5>
                            <button id="btn-start-scan" class="btn btn-success btn-lg px-4 rounded-pill shadow-sm mb-3" onclick="startScan()">
                                <i class="bi bi-camera me-2"></i> Mulai Scan
                            </button>
                            <div id="reader"></div>
                        </div>
                    </div>

                    <!-- Result Area -->
                    <div class="col-md-6 mb-4">
                        <div class="result-card text-center">
                            <h5 class="mb-4 fw-bold">Informasi Scan</h5>
                            
                            <div id="scan-alert" class="alert alert-success fw-bold" style="display: none;">
                                <!-- Alert content -->
                            </div>

                            <img src="https://ui-avatars.com/api/?name=P&background=f3f4f6&color=a1a1aa&size=100" class="avatar" alt="Avatar">
                            
                            <h4 id="res-name" class="fw-bold">-</h4>
                            <p class="text-muted mb-4"><span id="res-nis">-</span> | <span id="res-class">-</span></p>
                            
                            <div class="row text-start bg-light p-3 rounded mb-3">
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">Kelompok</small>
                                    <strong id="res-group">-</strong>
                                </div>
                                <div class="col-6 mb-2 text-end">
                                    <small class="text-muted d-block">Status</small>
                                    <div id="res-status">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
EOT;
$html = str_replace($dashboardFullOld, $dashboardFullNew, $html);

file_put_contents('../MBG.html', $html);
echo "HTML Layout updated.\n";
