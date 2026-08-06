<?php
$html = file_get_contents('../MBG.html');

$printCardsOld = <<<EOT
            function renderPrintCards() {
                const students = getStudents();
                const groups = getGroups();
                const container = document.getElementById('cards-container');
                container.innerHTML = '';

                students.forEach((s, idx) => {
                    const group = groups.find(g => g.id === s.groupId);
                    const color = group ? group.color : '#10b981';
                    
                    const cardId = `qr-code-\${s.id}`;
                    
                    container.innerHTML += `
                        <div class="id-card-wrapper" style="border-top: 5px solid \${color}">
                            <div class="id-card-header" style="background-color: \${color}">
                                <h5 class="mb-0">KARTU PESERTA MBG</h5>
                                <small>Sekolah Hebat</small>
                            </div>
                            <div class="id-card-body">
                                <h4 class="fw-bold text-dark">\${s.name}</h4>
                                <p class="mb-1 text-muted">NIS: \${s.nis} | Kelas: \${s.class}</p>
                                <span class="badge" style="background-color: \${color}">\${group ? group.name : ''}</span>
                                <div id="\${cardId}" class="id-card-qr"></div>
                                <h5 class="mt-2 fw-bold">\${s.id}</h5>
                            </div>
                            <div class="id-card-footer">
                                No. Peserta: \${String(idx + 1).padStart(3, '0')}
                            </div>
                        </div>
                    `;
                });

                // Generate QRs
                setTimeout(() => {
                    students.forEach(s => {
                        new QRCode(document.getElementById(`qr-code-\${s.id}`), {
                            text: s.id,
                            width: 120,
                            height: 120,
                            colorDark : "#000000",
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.H
                        });
                    });
                }, 100);
            }
EOT;

$printCardsNew = <<<EOT
            function renderPrintCards() {
                const students = getStudents();
                const groups = getGroups();
                const container = document.getElementById('cards-container');
                container.innerHTML = '';

                groups.forEach((group, idx) => {
                    const groupStudents = students.filter(s => s.groupId === group.id);
                    const color = group.color || '#10b981';
                    const cardId = `qr-code-\${group.id}`;
                    
                    let membersHtml = groupStudents.map((s, i) => `
                        <div style="font-size: 0.85rem; padding: 4px 0; border-bottom: 1px solid #eee; text-align: left;">
                            \${i + 1}. \${s.name} \${i === 0 ? '(Ketua)' : ''}
                        </div>
                    `).join('');
                    
                    container.innerHTML += `
                        <div class="id-card-wrapper" style="width: 500px; height: auto; min-height: 250px; display: flex; flex-direction: row; border-top: none; border-left: 5px solid \${color};">
                            <!-- Kiri: Info Group & QR -->
                            <div style="width: 220px; display: flex; flex-direction: column; border-right: 1px dashed #ddd; background: #fff;">
                                <div class="id-card-header" style="background-color: \${color}; padding: 15px 10px;">
                                    <h6 class="mb-0 text-white fw-bold">KARTU KELOMPOK</h6>
                                    <small class="text-white" style="font-size: 0.75rem; opacity: 0.9;">Sistem Pengambilan MBG</small>
                                </div>
                                <div class="id-card-body" style="flex: 1; padding: 15px;">
                                    <h6 class="fw-bold text-dark mb-3">\${group.name}</h6>
                                    <div id="\${cardId}" class="id-card-qr" style="width: 120px; height: 120px; margin: 0 auto;"></div>
                                    <small class="text-muted d-block mt-3 fw-bold">ID: \${group.id}</small>
                                </div>
                            </div>
                            <!-- Kanan: Anggota -->
                            <div style="flex: 1; padding: 20px; background: #fafafa; display: flex; flex-direction: column;">
                                <h6 class="fw-bold mb-3" style="color: \${color}; text-align: left; border-bottom: 2px solid \${color}; padding-bottom: 8px;">Daftar Anggota Kelompok</h6>
                                <div style="flex: 1; text-align: left;">
                                    \${membersHtml}
                                </div>
                                <div style="margin-top: 15px; font-size: 0.75rem; color: #888; text-align: left; font-style: italic;">
                                    * Scan QR untuk menandai seluruh anggota sudah mengambil MBG
                                </div>
                            </div>
                        </div>
                    `;
                });

                // Generate QRs
                setTimeout(() => {
                    groups.forEach(group => {
                        new QRCode(document.getElementById(`qr-code-\${group.id}`), {
                            text: group.id,
                            width: 120,
                            height: 120,
                            colorDark : "#000000",
                            colorLight : "#ffffff",
                            correctLevel : QRCode.CorrectLevel.H
                        });
                    });
                }, 100);
            }
EOT;

$scanSuccessOld = <<<EOT
            function onScanSuccess(decodedText, decodedResult) {
                // Pause scanner momentarily
                html5QrcodeScanner.pause();
                
                const students = getStudents();
                const studentIndex = students.findIndex(s => s.id === decodedText);
                
                if (studentIndex !== -1) {
                    const student = students[studentIndex];
                    const groups = getGroups();
                    const group = groups.find(g => g.id === student.groupId);

                    // Update result UI
                    document.getElementById('res-name').textContent = student.name;
                    document.getElementById('res-nis').textContent = student.nis;
                    document.getElementById('res-class').textContent = student.class;
                    document.getElementById('res-group').textContent = group ? group.name : '-';
                    
                    const alertBox = document.getElementById('scan-alert');
                    
                    if (!student.status) {
                        // Not picked up yet
                        student.status = true;
                        const now = new Date();
                        student.scanTime = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                        
                        students[studentIndex] = student;
                        saveStudents(students);
                        renderDashboard(); // update stats
                        
                        document.getElementById('res-status').innerHTML = '<span class="badge bg-success">Sudah Mengambil</span>';
                        
                        alertBox.className = 'alert alert-success fw-bold';
                        alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Berhasil. Peserta telah mengambil MBG.';
                        alertBox.style.display = 'block';

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: `\${student.name} telah mengambil MBG`,
                            timer: 1500,
                            showConfirmButton: false
                        });

                    } else {
                        // Already picked up
                        document.getElementById('res-status').innerHTML = '<span class="badge bg-success">Sudah Mengambil</span>';
                        
                        alertBox.className = 'alert alert-danger fw-bold';
                        alertBox.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Peserta sudah mengambil MBG sebelumnya.';
                        alertBox.style.display = 'block';

                        Swal.fire({
                            icon: 'error',
                            title: 'Peringatan!',
                            text: `Peserta atas nama \${student.name} sudah mengambil MBG sebelumnya!`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Ditemukan',
                        text: 'QR Code tidak valid atau peserta tidak terdaftar.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }

                // Resume scanning after 2 seconds
                setTimeout(() => {
                    document.getElementById('scan-alert').style.display = 'none';
                    // document.getElementById('res-name').textContent = '-';
                    html5QrcodeScanner.resume();
                }, 3000);
            }
EOT;

$scanSuccessNew = <<<EOT
            function onScanSuccess(decodedText, decodedResult) {
                // Pause scanner momentarily
                html5QrcodeScanner.pause();
                
                const students = getStudents();
                const groups = getGroups();
                
                // 1. Check if it's a group QR
                const group = groups.find(g => g.id === decodedText);
                
                // 2. Check if it's a student QR
                const studentIndex = students.findIndex(s => s.id === decodedText);
                
                const alertBox = document.getElementById('scan-alert');

                if (group) {
                    const groupStudents = students.filter(s => s.groupId === group.id);
                    const unpicked = groupStudents.filter(s => !s.status);
                    
                    document.getElementById('res-name').textContent = group.name;
                    document.getElementById('res-nis').textContent = "Ketua/Perwakilan";
                    document.getElementById('res-class').textContent = "Group Scan";
                    document.getElementById('res-group').textContent = group.name;
                    
                    if (unpicked.length > 0) {
                        const now = new Date();
                        const timeStr = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                        
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
                        alertBox.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>Berhasil. \${group.name} telah mengambil MBG (\${groupStudents.length} porsi).`;
                        alertBox.style.display = 'block';

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: `\${group.name} telah mengambil MBG (\${groupStudents.length} porsi)`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        document.getElementById('res-status').innerHTML = '<span class="badge bg-success">Sudah Mengambil Semua</span>';
                        alertBox.className = 'alert alert-danger fw-bold';
                        alertBox.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Kelompok ini sudah mengambil MBG sebelumnya.';
                        alertBox.style.display = 'block';

                        Swal.fire({
                            icon: 'error',
                            title: 'Peringatan!',
                            text: `\${group.name} sudah mengambil MBG sebelumnya!`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else if (studentIndex !== -1) {
                    const student = students[studentIndex];
                    const studentGroup = groups.find(g => g.id === student.groupId);

                    document.getElementById('res-name').textContent = student.name;
                    document.getElementById('res-nis').textContent = student.nis;
                    document.getElementById('res-class').textContent = student.class;
                    document.getElementById('res-group').textContent = studentGroup ? studentGroup.name : '-';
                    
                    if (!student.status) {
                        student.status = true;
                        student.scanTime = new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                        
                        students[studentIndex] = student;
                        saveStudents(students);
                        renderDashboard();
                        
                        document.getElementById('res-status').innerHTML = '<span class="badge bg-success">Sudah Mengambil</span>';
                        alertBox.className = 'alert alert-success fw-bold';
                        alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Berhasil. Peserta telah mengambil MBG.';
                        alertBox.style.display = 'block';

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: `\${student.name} telah mengambil MBG`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        document.getElementById('res-status').innerHTML = '<span class="badge bg-success">Sudah Mengambil</span>';
                        alertBox.className = 'alert alert-danger fw-bold';
                        alertBox.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Peserta sudah mengambil MBG sebelumnya.';
                        alertBox.style.display = 'block';

                        Swal.fire({
                            icon: 'error',
                            title: 'Peringatan!',
                            text: `Peserta atas nama \${student.name} sudah mengambil MBG sebelumnya!`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Ditemukan',
                        text: 'QR Code tidak valid atau tidak terdaftar.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }

                // Resume scanning after 2 seconds
                setTimeout(() => {
                    document.getElementById('scan-alert').style.display = 'none';
                    html5QrcodeScanner.resume();
                }, 3000);
            }
EOT;

$html = str_replace($printCardsOld, $printCardsNew, $html, $count1);
$html = str_replace($scanSuccessOld, $scanSuccessNew, $html, $count2);

file_put_contents('../MBG.html', $html);
echo "Replaced renderPrintCards: $count1\n";
echo "Replaced onScanSuccess: $count2\n";
