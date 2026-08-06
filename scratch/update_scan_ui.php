<?php
$html = file_get_contents('../MBG.html');

$uiOld = <<<EOT
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
EOT;

$uiNew = <<<EOT
                            <div class="row text-start bg-light p-3 rounded mb-3">
                                <div class="col-12 mb-3 border-bottom pb-2">
                                    <small class="text-muted d-block">Kelompok</small>
                                    <strong id="res-group">-</strong>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">Pengambilan</small>
                                    <div id="res-status-pickup">-</div>
                                </div>
                                <div class="col-6 mb-2 text-end">
                                    <small class="text-muted d-block">Pengembalian</small>
                                    <div id="res-status-return">-</div>
                                </div>
                            </div>
EOT;

$html = str_replace($uiOld, $uiNew, $html);

if (preg_match('/function onScanSuccess\(decodedText, decodedResult\).*?function onScanFailure\(error\)/s', $html, $matches)) {
    $newScanSuccess = <<<EOT
function onScanSuccess(decodedText, decodedResult) {
                html5QrcodeScanner.pause();
                
                const students = getStudents();
                const groups = getGroups();
                
                const group = groups.find(g => g.id === decodedText);
                const studentIndex = students.findIndex(s => s.id === decodedText);
                
                const alertBox = document.getElementById('scan-alert');

                if (group) {
                    const groupStudents = students.filter(s => s.groupId === group.id);
                    const unpicked = groupStudents.filter(s => !s.status);
                    const unreturned = groupStudents.filter(s => !s.statusReturn);
                    
                    document.getElementById('res-name').textContent = group.name;
                    document.getElementById('res-nis').textContent = "Ketua/Perwakilan";
                    document.getElementById('res-class').textContent = "Group Scan";
                    document.getElementById('res-group').textContent = group.name;
                    
                    const now = new Date();
                    const timeStr = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});

                    // Helper to update dual status
                    const updateDualStatus = () => {
                        const s_unpicked = students.filter(s => s.groupId === group.id && !s.status).length;
                        const s_unreturned = students.filter(s => s.groupId === group.id && !s.statusReturn).length;
                        const len = groupStudents.length;

                        let pickupBadge = '<span class="badge bg-secondary">Belum Ambil</span>';
                        if (s_unpicked === 0 && len > 0) pickupBadge = '<span class="badge bg-success">Sudah Ambil</span>';
                        else if (s_unpicked < len) pickupBadge = '<span class="badge bg-warning text-dark">Sebagian</span>';

                        let returnBadge = '<span class="badge bg-secondary">Belum Kembali</span>';
                        if (s_unreturned === 0 && len > 0) returnBadge = '<span class="badge bg-primary">Sudah Kembali</span>';
                        else if (s_unreturned < len) returnBadge = '<span class="badge bg-warning text-dark">Sebagian</span>';

                        document.getElementById('res-status-pickup').innerHTML = pickupBadge;
                        document.getElementById('res-status-return').innerHTML = returnBadge;
                    };

                    if (currentScanMode === 'pickup') {
                        if (unpicked.length > 0) {
                            students.forEach(s => {
                                if (s.groupId === group.id) {
                                    s.status = true;
                                    s.scanTime = s.scanTime || timeStr;
                                }
                            });
                            saveStudents(students);
                            renderDashboard();
                            
                            updateDualStatus();
                            alertBox.className = 'alert alert-success fw-bold';
                            alertBox.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>Berhasil. \${group.name} telah mengambil MBG.`;
                            alertBox.style.display = 'block';
                            Swal.fire({icon: 'success', title: 'Berhasil!', text: `\${group.name} telah mengambil MBG`, timer: 2000, showConfirmButton: false});
                        } else {
                            updateDualStatus();
                            alertBox.className = 'alert alert-danger fw-bold';
                            alertBox.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Kelompok ini sudah mengambil MBG sebelumnya.';
                            alertBox.style.display = 'block';
                            Swal.fire({icon: 'error', title: 'Peringatan!', text: `\${group.name} sudah mengambil MBG sebelumnya!`, timer: 2000, showConfirmButton: false});
                        }
                    } else if (currentScanMode === 'return') {
                        // Check if they have picked up
                        if (unpicked.length === groupStudents.length) {
                            // Nobody picked up yet
                            updateDualStatus();
                            alertBox.className = 'alert alert-danger fw-bold';
                            alertBox.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Belum mengambil MBG, tidak bisa mengembalikan.';
                            alertBox.style.display = 'block';
                            Swal.fire({icon: 'error', title: 'Peringatan!', text: `\${group.name} belum mengambil MBG!`, timer: 2000, showConfirmButton: false});
                        } else if (unreturned.length > 0) {
                            // Can return
                            students.forEach(s => {
                                if (s.groupId === group.id && s.status) {
                                    s.statusReturn = true;
                                    s.returnTime = s.returnTime || timeStr;
                                }
                            });
                            saveStudents(students);
                            renderDashboard();
                            
                            updateDualStatus();
                            alertBox.className = 'alert alert-success fw-bold';
                            alertBox.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>Berhasil. \${group.name} telah mengembalikan kotak makan.`;
                            alertBox.style.display = 'block';
                            Swal.fire({icon: 'success', title: 'Berhasil!', text: `\${group.name} telah mengembalikan MBG`, timer: 2000, showConfirmButton: false});
                        } else {
                            updateDualStatus();
                            alertBox.className = 'alert alert-danger fw-bold';
                            alertBox.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Kelompok ini sudah mengembalikan MBG sebelumnya.';
                            alertBox.style.display = 'block';
                            Swal.fire({icon: 'error', title: 'Peringatan!', text: `\${group.name} sudah mengembalikan MBG sebelumnya!`, timer: 2000, showConfirmButton: false});
                        }
                    }
                } else {
                    Swal.fire({icon: 'error', title: 'Tidak Ditemukan', text: 'QR Code tidak valid.', timer: 2000, showConfirmButton: false});
                }

                setTimeout(() => {
                    document.getElementById('scan-alert').style.display = 'none';
                    html5QrcodeScanner.resume();
                }, 3000);
            }

            function onScanFailure(error)
EOT;
    $html = str_replace($matches[0], $newScanSuccess, $html);
}

file_put_contents('../MBG.html', $html);
echo "Separated scan info successfully!\n";
