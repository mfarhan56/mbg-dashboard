import json
import re

with open('data.txt', 'r', encoding='utf-8') as f:
    lines = f.readlines()

groups = []
students = []

current_class = ""
current_group_name = ""
current_group_id = ""

group_counter = 1
student_counter = 1

colors = ['#e11d48', '#2563eb', '#16a34a', '#d97706', '#9333ea', '#0ea5e9', '#ec4899']

for line in lines:
    line = line.strip()
    if not line:
        continue

    if line.startswith('Kelas'):
        # Kelas 10 TKJ 1 (36 siswa)
        m = re.match(r'Kelas\s+(.*?)\s*\(', line)
        if m:
            current_class = m.group(1).strip()
    elif line.startswith('Kelompok'):
        # Kelompok 1 (Ketua: ABDUL DHELFIN)
        m = re.match(r'Kelompok\s+(\d+)', line)
        if m:
            group_num = m.group(1)
            current_group_name = f'Kelompok {group_num} ({current_class})'
            current_group_id = f'G{group_counter}'
            color = colors[(group_counter - 1) % len(colors)]
            groups.append({
                'id': current_group_id,
                'name': current_group_name,
                'color': color
            })
            group_counter += 1
    elif re.match(r'^(\d+\.|•)\s+(.*)', line):
        # 1. ABDUL DHELFIN (Ketua)
        # • ALISA KHAIRANY (Ketua)
        m = re.match(r'^(\d+\.|•)\s+(.*)', line)
        if m:
            student_name = m.group(2).strip()
            # Remove (Ketua) if present
            student_name = re.sub(r'\s*\(Ketua\)', '', student_name)
            
            student_id = f'MBG{student_counter:03d}'
            students.append({
                'id': student_id,
                'name': student_name,
                'nis': '-',
                'class': current_class,
                'groupId': current_group_id,
                'status': False,
                'scanTime': None
            })
            student_counter += 1

print("const initialGroups = " + json.dumps(groups, indent=4) + ";\n")
print("const initialStudents = " + json.dumps(students, indent=4) + ";")
