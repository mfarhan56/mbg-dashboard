<?php
$html = file_get_contents('../MBG.html');

$jsInitOld = "const saveStudents = (data) => localStorage.setItem('mbg_students_v4', JSON.stringify(data));";
$jsInitNew = "const saveStudents = (data) => localStorage.setItem('mbg_students_v4', JSON.stringify(data));\n            let currentScanMode = 'pickup';\n\n            window.setScanMode = (mode) => {\n                currentScanMode = mode;\n                document.getElementById('scan-title').textContent = mode === 'pickup' ? 'Scan QR Code (Pengambilan)' : 'Scan QR Code (Pengembalian)';\n                if (html5QrcodeScanner) {\n                    html5QrcodeScanner.clear();\n                    document.getElementById('btn-start-scan').style.display = 'inline-block';\n                }\n            };";
$html = str_replace($jsInitOld, $jsInitNew, $html);

$renderDashOld = <<<EOT
            function renderDashboard() {
                const students = getStudents();
                const groups = getGroups();
                
                const total = students.length;
                const pickedUp = students.filter(s => s.status).length;
                const notPickedUp = total - pickedUp;

                document.getElementById('stat-total').textContent = total;
                document.getElementById('stat-picked').textContent = pickedUp;
                document.getElementById('stat-notpicked').textContent = notPickedUp;
                document.getElementById('stat-groups').textContent = groups.length;
            }
EOT;

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

                groups.forEach(group => {
                    const groupStudents = students.filter(s => s.groupId === group.id);
                    const unpicked = groupStudents.filter(s => !s.status);
                    if (unpicked.length === 0 && groupStudents.length > 0) {
                        groupsPicked++;
                    } else {
                        groupsNotPicked++;
                    }
                });

                if(document.getElementById('stat-total')) document.getElementById('stat-total').textContent = total;
                if(document.getElementById('stat-picked')) document.getElementById('stat-picked').textContent = pickedUp;
                if(document.getElementById('stat-notpicked')) document.getElementById('stat-notpicked').textContent = notPickedUp;
                if(document.getElementById('stat-returned')) document.getElementById('stat-returned').textContent = returnedCount;
                if(document.getElementById('stat-groups')) document.getElementById('stat-groups').textContent = groups.length;
                if(document.getElementById('stat-groups-picked')) document.getElementById('stat-groups-picked').textContent = groupsPicked;
                if(document.getElementById('stat-groups-notpicked')) document.getElementById('stat-groups-notpicked').textContent = groupsNotPicked;
            }
EOT;
$html = str_replace($renderDashOld, $renderDashNew, $html);

// Now we update onScanSuccess logic
// I'll grab everything between function onScanSuccess(decodedText, decodedResult) { and function onScanFailure(error) {
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
                            
                            document.getElementById('res-status').innerHTML = '<span class="badge bg-success">Berhasil (Semua)</span>';
                            alertBox.className = 'alert alert-success fw-bold';
                            alertBox.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>Berhasil. \${group.name} telah mengambil MBG.`;
                            alertBox.style.display = 'block';
                            Swal.fire({icon: 'success', title: 'Berhasil!', text: `\${group.name} telah mengambil MBG`, timer: 2000, showConfirmButton: false});
                        } else {
                            document.getElementById('res-status').innerHTML = '<span class="badge bg-success">Sudah Mengambil Semua</span>';
                            alertBox.className = 'alert alert-danger fw-bold';
                            alertBox.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Kelompok ini sudah mengambil MBG sebelumnya.';
                            alertBox.style.display = 'block';
                            Swal.fire({icon: 'error', title: 'Peringatan!', text: `\${group.name} sudah mengambil MBG sebelumnya!`, timer: 2000, showConfirmButton: false});
                        }
                    } else if (currentScanMode === 'return') {
                        // Check if they have picked up
                        if (unpicked.length === groupStudents.length) {
                            // Nobody picked up yet
                            document.getElementById('res-status').innerHTML = '<span class="badge bg-secondary">Belum Mengambil</span>';
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
                            
                            document.getElementById('res-status').innerHTML = '<span class="badge bg-primary">Berhasil Dikembalikan</span>';
                            alertBox.className = 'alert alert-success fw-bold';
                            alertBox.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>Berhasil. \${group.name} telah mengembalikan kotak makan.`;
                            alertBox.style.display = 'block';
                            Swal.fire({icon: 'success', title: 'Berhasil!', text: `\${group.name} telah mengembalikan MBG`, timer: 2000, showConfirmButton: false});
                        } else {
                            document.getElementById('res-status').innerHTML = '<span class="badge bg-primary">Sudah Dikembalikan</span>';
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

// Now replace renderStudents table header and rows to include return status
$renderStudentsOld = <<<EOT
            function renderStudents() {
                const students = getStudents();
                const groups = getGroups();
                const tbody = document.getElementById('students-table-body');
                tbody.innerHTML = '';
                
                students.forEach((s, idx) => {
                    const group = groups.find(g => g.id === s.groupId);
                    tbody.innerHTML += `
                        <tr>
                            <td>\${idx + 1}</td>
                            <td>\${s.id}</td>
                            <td>\${s.name}</td>
                            <td>\${s.nis}</td>
                            <td>\${s.class}</td>
                            <td>\${group ? group.name : '-'}</td>
                            <td>\${s.status ? '<span class="badge bg-success">Sudah Mengambil</span>' : '<span class="badge bg-secondary">Belum Mengambil</span>'}</td>
                        </tr>
                    `;
                });
            }
EOT;

$renderStudentsNew = <<<EOT
            function renderStudents() {
                const students = getStudents();
                const groups = getGroups();
                const tbody = document.getElementById('students-table-body');
                tbody.innerHTML = '';
                
                students.forEach((s, idx) => {
                    const group = groups.find(g => g.id === s.groupId);
                    let statusAmbil = s.status ? '<span class="badge bg-success">Sudah Ambil</span>' : '<span class="badge bg-secondary">Belum Ambil</span>';
                    let statusKembali = s.statusReturn ? '<span class="badge bg-primary">Sudah Kembali</span>' : '<span class="badge bg-secondary">Belum Kembali</span>';
                    
                    tbody.innerHTML += `
                        <tr>
                            <td>\${idx + 1}</td>
                            <td>\${s.id}</td>
                            <td>\${s.name}</td>
                            <td>\${s.nis}</td>
                            <td>\${s.class}</td>
                            <td>\${group ? group.name : '-'}</td>
                            <td>\${statusAmbil}</td>
                            <td>\${statusKembali}</td>
                        </tr>
                    `;
                });
            }
EOT;
$html = str_replace($renderStudentsOld, $renderStudentsNew, $html);

// We must also update the HTML table header for students to add "Status Kembali"
$studentTableOld = <<<EOT
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>NIS</th>
                                        <th>Kelas</th>
                                        <th>Kelompok</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
EOT;

$studentTableNew = <<<EOT
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>NIS</th>
                                        <th>Kelas</th>
                                        <th>Kelompok</th>
                                        <th>Status Ambil</th>
                                        <th>Status Kembali</th>
                                    </tr>
                                </thead>
EOT;
$html = str_replace($studentTableOld, $studentTableNew, $html);

// What about the student table in section? Let's check view_file. It was <thead class="table-light"> ... 
$studentTableLightOld = <<<EOT
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>NIS</th>
                                        <th>Kelas</th>
                                        <th>Kelompok</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
EOT;
$studentTableLightNew = <<<EOT
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>ID</th>
                                        <th>Nama</th>
                                        <th>NIS</th>
                                        <th>Kelas</th>
                                        <th>Kelompok</th>
                                        <th>Status Ambil</th>
                                        <th>Status Kembali</th>
                                    </tr>
                                </thead>
EOT;
$html = str_replace($studentTableLightOld, $studentTableLightNew, $html);

// Now update viewGroupDetail to show return status
$viewGroupDetailOld = <<<EOT
            window.viewGroupDetail = (groupId) => {
                const groups = getGroups();
                const students = getStudents();
                
                const group = groups.find(g => g.id === groupId);
                const groupStudents = students.filter(s => s.groupId === groupId);
                
                let tbody = '';
                groupStudents.forEach((s, idx) => {
                    const time = s.scanTime ? s.scanTime : '-';
                    const badge = s.status ? '<span class="badge bg-success">Sudah</span>' : '<span class="badge bg-secondary">Belum</span>';
                    
                    tbody += `
                        <tr>
                            <td>\${idx + 1}</td>
                            <td>\${s.name}</td>
                            <td>\${s.nis}</td>
                            <td>\${badge}</td>
                            <td>\${time}</td>
                        </tr>
                    `;
                });
EOT;

$viewGroupDetailNew = <<<EOT
            window.viewGroupDetail = (groupId) => {
                const groups = getGroups();
                const students = getStudents();
                
                const group = groups.find(g => g.id === groupId);
                const groupStudents = students.filter(s => s.groupId === groupId);
                
                let tbody = '';
                groupStudents.forEach((s, idx) => {
                    const time = s.scanTime ? s.scanTime : '-';
                    const timeReturn = s.returnTime ? s.returnTime : '-';
                    const badge = s.status ? '<span class="badge bg-success">Sudah Ambil</span>' : '<span class="badge bg-secondary">Belum Ambil</span>';
                    const badgeReturn = s.statusReturn ? '<span class="badge bg-primary">Sudah Kembali</span>' : '<span class="badge bg-secondary">Belum Kembali</span>';
                    
                    tbody += `
                        <tr>
                            <td>\${idx + 1}</td>
                            <td>\${s.name}</td>
                            <td>\${badge} (\${time})</td>
                            <td>\${badgeReturn} (\${timeReturn})</td>
                        </tr>
                    `;
                });
EOT;
$html = str_replace($viewGroupDetailOld, $viewGroupDetailNew, $html);

// Update table header in sweetalert
$swalOld = <<<EOT
                Swal.fire({
                    title: `\${group.name}`,
                    html: `
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-sm text-start align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>NIS</th>
                                        <th>Status</th>
                                        <th>Jam Ambil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    \${tbody}
                                </tbody>
                            </table>
                        </div>
                    `,
                    width: '800px',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: group.color
                });
EOT;

$swalNew = <<<EOT
                Swal.fire({
                    title: `\${group.name}`,
                    html: `
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-sm text-start align-middle" style="font-size: 0.9rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Status Pengambilan</th>
                                        <th>Status Pengembalian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    \${tbody}
                                </tbody>
                            </table>
                        </div>
                    `,
                    width: '800px',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: group.color || '#3b82f6'
                });
EOT;
$html = str_replace($swalOld, $swalNew, $html);

file_put_contents('../MBG.html', $html);
echo "JS Logic and Modals updated successfully.\n";
