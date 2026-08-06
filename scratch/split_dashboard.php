<?php
$html = file_get_contents('../MBG.html');

$dashboardHtmlOldRegex = '/<section id="dashboard" class="section-page active">.*?<\/section>/s';

$dashboardHtmlNew = <<<EOT
<section id="dashboard" class="section-page active">
                <div class="row">
                    <!-- KOLOM PENGAMBILAN -->
                    <div class="col-lg-6 mb-4">
                        <h5 class="mb-3 fw-bold text-success"><i class="bi bi-box-arrow-up-right me-2"></i>Dashboard Pengambilan</h5>
                        <div class="row mb-3">
                            <div class="col-sm-6 mb-3">
                                <div class="stat-card p-3">
                                    <div class="stat-icon primary"><i class="bi bi-people"></i></div>
                                    <div class="stat-details">
                                        <h3 id="stat-total" style="font-size: 1.2rem;">0</h3>
                                        <p style="font-size: 0.8rem;">Total Peserta</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="stat-card p-3" style="border: 2px solid var(--success-color);">
                                    <div class="stat-icon success"><i class="bi bi-check-circle"></i></div>
                                    <div class="stat-details">
                                        <h3 id="stat-picked" style="font-size: 1.2rem;">0</h3>
                                        <p style="font-size: 0.8rem;">Peserta Sdh Ambil</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="stat-card p-3">
                                    <div class="stat-icon warning"><i class="bi bi-collection"></i></div>
                                    <div class="stat-details">
                                        <h3 id="stat-groups" style="font-size: 1.2rem;">0</h3>
                                        <p style="font-size: 0.8rem;">Total Kelompok</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="stat-card p-3" style="border: 2px solid var(--success-color);">
                                    <div class="stat-icon success"><i class="bi bi-check2-all"></i></div>
                                    <div class="stat-details">
                                        <h3 id="stat-groups-picked" style="font-size: 1.2rem;">0</h3>
                                        <p style="font-size: 0.8rem;">Klp Sdh Ambil</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm" style="border-top: 3px solid var(--danger-color) !important;">
                            <div class="card-header bg-white py-2">
                                <h6 class="mb-0 fw-bold text-danger" style="font-size: 0.9rem;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Kelompok Belum Ambil</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th class="px-3">Kelompok</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="unpicked-groups-list">
                                            <tr><td colspan="2" class="text-center py-3 text-muted">Memuat...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KOLOM PENGEMBALIAN -->
                    <div class="col-lg-6 mb-4">
                        <h5 class="mb-3 fw-bold text-primary"><i class="bi bi-box-arrow-in-down-left me-2"></i>Dashboard Pengembalian</h5>
                        <div class="row mb-3">
                            <div class="col-sm-6 mb-3">
                                <div class="stat-card p-3" style="border: 2px solid var(--primary-color);">
                                    <div class="stat-icon primary" style="background-color: rgba(37, 99, 235, 0.1); color: #2563eb;"><i class="bi bi-check-circle"></i></div>
                                    <div class="stat-details">
                                        <h3 id="stat-returned" style="font-size: 1.2rem;">0</h3>
                                        <p style="font-size: 0.8rem;">Peserta Sdh Kembali</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="stat-card p-3" style="border: 2px solid var(--danger-color);">
                                    <div class="stat-icon danger"><i class="bi bi-x-circle"></i></div>
                                    <div class="stat-details">
                                        <h3 id="stat-notreturned" style="font-size: 1.2rem;">0</h3>
                                        <p style="font-size: 0.8rem;">Peserta Blm Kembali</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="stat-card p-3" style="border: 2px solid var(--primary-color);">
                                    <div class="stat-icon primary" style="background-color: rgba(37, 99, 235, 0.1); color: #2563eb;"><i class="bi bi-check2-all"></i></div>
                                    <div class="stat-details">
                                        <h3 id="stat-groups-returned" style="font-size: 1.2rem;">0</h3>
                                        <p style="font-size: 0.8rem;">Klp Sdh Kembali</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="stat-card p-3" style="border: 2px solid var(--danger-color);">
                                    <div class="stat-icon danger"><i class="bi bi-exclamation-circle"></i></div>
                                    <div class="stat-details">
                                        <h3 id="stat-groups-notreturned" style="font-size: 1.2rem;">0</h3>
                                        <p style="font-size: 0.8rem;">Klp Blm Kembali</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm" style="border-top: 3px solid var(--danger-color) !important;">
                            <div class="card-header bg-white py-2">
                                <h6 class="mb-0 fw-bold text-danger" style="font-size: 0.9rem;"><i class="bi bi-exclamation-triangle-fill me-2"></i>Kelompok Belum Kembali</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th class="px-3">Kelompok</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="unreturned-groups-list">
                                            <tr><td colspan="2" class="text-center py-3 text-muted">Memuat...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
EOT;

$html = preg_replace($dashboardHtmlOldRegex, $dashboardHtmlNew, $html);

// Now update renderDashboard JS
$renderDashRegex = '/function renderDashboard\(\).*?if\(document\.getElementById\(\'unpicked-groups-list\'\)\) document\.getElementById\(\'unpicked-groups-list\'\)\.innerHTML = unpickedHtml;\s*\}/s';

$renderDashNewJS = <<<EOT
function renderDashboard() {
                const students = getStudents();
                const groups = getGroups();
                
                const total = students.length;
                const pickedUp = students.filter(s => s.status).length;
                const returnedCount = students.filter(s => s.statusReturn).length;
                const notReturnedCount = pickedUp - returnedCount; // Yang belum kembali adalah yg sudah ambil tapi belum kembali

                let groupsPicked = 0;
                
                let groupsReturned = 0;
                let groupsNotReturned = 0;
                
                let unpickedHtml = '';
                let unreturnedHtml = '';

                groups.forEach(group => {
                    const groupStudents = students.filter(s => s.groupId === group.id);
                    const len = groupStudents.length;
                    if(len === 0) return;

                    const s_picked = groupStudents.filter(s => s.status).length;
                    const s_returned = groupStudents.filter(s => s.statusReturn).length;

                    // PENGAMBILAN
                    if (s_picked === len) {
                        groupsPicked++;
                    } else {
                        let badge = '<span class="badge bg-danger">Belum Sama Sekali</span>';
                        if(s_picked > 0) badge = '<span class="badge bg-warning text-dark">Baru Sebagian</span>';
                        
                        unpickedHtml += `
                            <tr>
                                <td class="px-3 fw-bold">\${group.name}</td>
                                <td>\${badge}</td>
                            </tr>
                        `;
                    }

                    // PENGEMBALIAN (Hanya cek yg sudah ambil)
                    if (s_picked > 0) {
                        if (s_returned === s_picked) {
                            groupsReturned++;
                        } else {
                            groupsNotReturned++;
                            let badgeR = '<span class="badge bg-danger">Belum Sama Sekali</span>';
                            if(s_returned > 0) badgeR = '<span class="badge bg-warning text-dark">Baru Sebagian</span>';
                            
                            unreturnedHtml += `
                                <tr>
                                    <td class="px-3 fw-bold">\${group.name}</td>
                                    <td>\${badgeR}</td>
                                </tr>
                            `;
                        }
                    }
                });

                if(unpickedHtml === '') unpickedHtml = `<tr><td colspan="2" class="text-center py-3 text-success fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Semua kelompok sudah mengambil MBG!</td></tr>`;
                if(unreturnedHtml === '') unreturnedHtml = `<tr><td colspan="2" class="text-center py-3 text-success fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Semua kelompok sudah mengembalikan!</td></tr>`;

                const setTxt = (id, val) => { const el = document.getElementById(id); if(el) el.textContent = val; };
                const setHtml = (id, val) => { const el = document.getElementById(id); if(el) el.innerHTML = val; };

                setTxt('stat-total', total);
                setTxt('stat-picked', pickedUp);
                setTxt('stat-groups', groups.length);
                setTxt('stat-groups-picked', groupsPicked);
                setHtml('unpicked-groups-list', unpickedHtml);

                setTxt('stat-returned', returnedCount);
                setTxt('stat-notreturned', notReturnedCount);
                setTxt('stat-groups-returned', groupsReturned);
                setTxt('stat-groups-notreturned', groupsNotReturned);
                setHtml('unreturned-groups-list', unreturnedHtml);
            }
EOT;

$html = preg_replace($renderDashRegex, $renderDashNewJS, $html);

file_put_contents('../MBG.html', $html);
echo "Split dashboard successfully!\n";
