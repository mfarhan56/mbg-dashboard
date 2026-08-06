const fs = require('fs');

let group = { id: 'G1', name: 'Group 1' };
let students = [
    { id: '1', groupId: 'G1', status: false, statusReturn: false },
    { id: '2', groupId: 'G1', status: false, statusReturn: false }
];

let groupStudents = students.filter(s => s.groupId === group.id);
let unpicked = groupStudents.filter(s => !s.status);
let unreturned = groupStudents.filter(s => !s.statusReturn);

let currentScanMode = 'pickup';

if (currentScanMode === 'pickup') {
    if (unpicked.length > 0) {
        students.forEach(s => {
            if (s.groupId === group.id) {
                s.status = true;
                s.scanTime = '12:00';
            }
        });
        
        // updateDualStatus
        const s_unpicked = students.filter(s => s.groupId === group.id && !s.status).length;
        const s_unreturned = students.filter(s => s.groupId === group.id && !s.statusReturn).length;
        const len = groupStudents.length;

        let pickupBadge = 'Belum Ambil';
        if (s_unpicked === 0 && len > 0) pickupBadge = 'Sudah Ambil';
        else if (s_unpicked < len) pickupBadge = 'Sebagian Ambil';

        let returnBadge = 'Belum Kembali';
        if (s_unreturned === 0 && len > 0) returnBadge = 'Sudah Kembali';
        else if (s_unreturned < len) returnBadge = 'Sebagian Kembali';

        console.log({ pickupBadge, returnBadge });
    }
}
