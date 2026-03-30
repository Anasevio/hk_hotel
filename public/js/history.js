const tasks = [
    { date: "5 Feb 2026", room: "Kamar 01", checklist: 12, status: "ok" },
    { date: "5 Feb 2026", room: "Kamar 02", checklist: 12, status: "late" },
    { date: "5 Feb 2026", room: "Kamar 03", checklist: 12, status: "ok" },
    { date: "5 Feb 2026", room: "Kamar 04", checklist: 12, status: "late" },
    { date: "5 Feb 2026", room: "Kamar 05", checklist: 12, status: "ok" },
    { date: "4 Feb 2026", room: "Kamar 06", checklist: 12, status: "late" },
];

const table = document.getElementById("taskTable");

tasks.forEach(task => {
    const row = document.createElement("tr");

    row.innerHTML = `
        <td>${task.date}</td>
        <td>${task.room}</td>
        <td>● ${task.checklist}</td>
        <td class="status ${task.status}">
            ${task.status === "ok"
                ? "● Selesai Tepat Waktu"
                : "○ Selesai, Tidak Tepat Waktu"}
        </td>
    `;

    table.appendChild(row);
});