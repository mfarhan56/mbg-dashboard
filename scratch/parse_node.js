const fs = require('fs');

const lines = fs.readFileSync('data.txt', 'utf8').split('\n');

const groups = [];
const students = [];

let current_class = "";
let current_group_name = "";
let current_group_id = "";

let group_counter = 1;
let student_counter = 1;

const colors = ['#e11d48', '#2563eb', '#16a34a', '#d97706', '#9333ea', '#0ea5e9', '#ec4899'];

for (let line of lines) {
    line = line.trim();
    if (!line) continue;

    if (line.startsWith('Kelas')) {
        const m = line.match(/Kelas\s+(.*?)\s*\(/);
        if (m) {
            current_class = m[1].trim();
        }
    } else if (line.startsWith('Kelompok')) {
        const m = line.match(/Kelompok\s+(\d+)/);
        if (m) {
            const group_num = m[1];
            current_group_name = `Kelompok ${group_num} (${current_class})`;
            current_group_id = `G${group_counter}`;
            const color = colors[(group_counter - 1) % colors.length];
            groups.push({
                id: current_group_id,
                name: current_group_name,
                color: color
            });
            group_counter++;
        }
    } else if (/^(\d+\.|•)\s+(.*)/.test(line)) {
        const m = line.match(/^(\d+\.|•)\s+(.*)/);
        if (m) {
            let student_name = m[2].trim();
            student_name = student_name.replace(/\s*\(Ketua\)/g, '');
            
            const student_id = `MBG${String(student_counter).padStart(3, '0')}`;
            students.push({
                id: student_id,
                name: student_name,
                nis: '-',
                class: current_class,
                groupId: current_group_id,
                status: false,
                scanTime: null
            });
            student_counter++;
        }
    }
}

fs.writeFileSync('output.js', `const initialGroups = ${JSON.stringify(groups, null, 4)};\n\nconst initialStudents = ${JSON.stringify(students, null, 4)};`);
console.log("Done");
