<?php
$html = file_get_contents('../MBG.html');

$dashboardHtmlOld = <<<EOT
                </div>
            </section>

            <!-- SCANNER SECTION -->
EOT;

$dashboardHtmlNew = <<<EOT
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-top: 3px solid var(--danger-color) !important;">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Daftar Kelompok Belum Mengambil MBG</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4">No</th>
                                                <th>Nama Kelompok</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="unpicked-groups-list">
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">Memuat data...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

            <!-- SCANNER SECTION -->
EOT;

$html = str_replace($dashboardHtmlOld, $dashboardHtmlNew, $html);

// Now update renderDashboard()
if (preg_match('/function renderDashboard\(\).*?if\(document\.getElementById\(\'stat-groups-notpicked\'\)\) document\.getElementById\(\'stat-groups-notpicked\'\)\.textContent = groupsNotPicked;\s*\}/s', $html, $matches)) {

    $renderDashOld = $matches[0];
    
    // We want to insert logic inside renderDashboard to populate unpicked-groups-list
    $renderDashNew = <<<EOT
function renderDashboard() {
                const students = getStudents();
                const groups = getGroups();
                
                const total = students.length;
                const pickedUp = students.filter(s => s.status).length;
                const notPickedUp = total - pickedUp;
                const returnedCount = students.filter(s => s.statusReturn).length;

                let groupsPicked = 0;
                let groupsNotPicked = 0;
                let unpickedHtml = '';
                let no = 1;

                groups.forEach(group => {
                    const groupStudents = students.filter(s => s.groupId === group.id);
                    const unpicked = groupStudents.filter(s => !s.status);
                    if (unpicked.length === 0 && groupStudents.length > 0) {
                        groupsPicked++;
                    } else {
                        groupsNotPicked++;
                        
                        let badge = '<span class="badge bg-danger">Belum Sama Sekali</span>';
                        if(unpicked.length < groupStudents.length) {
                            badge = '<span class="badge bg-warning text-dark">Baru Sebagian</span>';
                        }
                        
                        unpickedHtml += `
                            <tr>
                                <td class="px-4">\${no++}</td>
                                <td class="fw-bold">\${group.name}</td>
                                <td>\${badge}</td>
                            </tr>
                        `;
                    }
                });

                if(groupsNotPicked === 0) {
                    unpickedHtml = `<tr><td colspan="3" class="text-center py-4 text-success fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Semua kelompok sudah mengambil MBG!</td></tr>`;
                }

                if(document.getElementById('stat-total')) document.getElementById('stat-total').textContent = total;
                if(document.getElementById('stat-picked')) document.getElementById('stat-picked').textContent = pickedUp;
                if(document.getElementById('stat-notpicked')) document.getElementById('stat-notpicked').textContent = notPickedUp;
                if(document.getElementById('stat-returned')) document.getElementById('stat-returned').textContent = returnedCount;
                if(document.getElementById('stat-groups')) document.getElementById('stat-groups').textContent = groups.length;
                if(document.getElementById('stat-groups-picked')) document.getElementById('stat-groups-picked').textContent = groupsPicked;
                if(document.getElementById('stat-groups-notpicked')) document.getElementById('stat-groups-notpicked').textContent = groupsNotPicked;
                if(document.getElementById('unpicked-groups-list')) document.getElementById('unpicked-groups-list').innerHTML = unpickedHtml;
            }
EOT;

    $html = str_replace($renderDashOld, $renderDashNew, $html);
}

file_put_contents('../MBG.html', $html);
echo "Added unpicked groups list to dashboard!\n";
