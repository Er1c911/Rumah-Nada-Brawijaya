// --- KONFIGURASI UTAMA ---
const ADMIN_NUMBER = "6282143857754";
const ADMIN_CRED = { 
    user: "Divisi RT", 
    pass: "rtkeras" 
};

let currentDate = new Date();
let schedules = JSON.parse(localStorage.getItem('rn_brawijaya_db')) || [];
let pendingRequests = JSON.parse(localStorage.getItem('rn_pending_requests')) || [];
let isAdmin = sessionStorage.getItem('is_admin') === 'true';

document.addEventListener('DOMContentLoaded', () => {
    if(document.getElementById('book-start')) populateTimeOptions();

    if(typeof IS_ADMIN_FILE !== 'undefined') {
        if(isAdmin) {
            showAdminUI();
        } else {
            const overlay = document.getElementById('login-overlay');
            if(overlay) overlay.style.display = 'flex';
        }
    }

    renderTable();
    setupEvents();
});

function populateTimeOptions() {
    const startSelect = document.getElementById('book-start');
    const endSelect = document.getElementById('book-end');
    if(!startSelect || !endSelect) return;
    for(let h = 9; h <= 23; h++) {
        const hour = h.toString().padStart(2, '0') + ".00";
        startSelect.options.add(new Option(hour, hour));
        endSelect.options.add(new Option(hour, hour));
    }
}

function setupEvents() {
    document.getElementById('prev-week').onclick = () => { currentDate.setDate(currentDate.getDate() - 7); renderTable(); };
    document.getElementById('next-week').onclick = () => { currentDate.setDate(currentDate.getDate() + 7); renderTable(); };
    
    const themeBtn = document.getElementById('theme-toggle');
    if(themeBtn) {
        themeBtn.onclick = () => {
            const h = document.documentElement;
            const isDark = h.getAttribute('data-theme') === 'dark';
            h.setAttribute('data-theme', isDark ? 'light' : 'dark');
        };
    }

    if(document.getElementById('submit-login')) document.getElementById('submit-login').onclick = handleLogin;
    if(document.getElementById('logout-btn')) document.getElementById('logout-btn').onclick = () => { sessionStorage.clear(); location.href = 'index.html'; };
    if(document.getElementById('submit-booking')) document.getElementById('submit-booking').onclick = handleBooking;
    if(document.getElementById('set-close-btn')) document.getElementById('set-close-btn').onclick = handleSetClose;
    if(document.getElementById('open-booking-main')) document.getElementById('open-booking-main').onclick = () => showModal('booking-modal');
    
    document.querySelectorAll('.close-modal').forEach(b => b.onclick = () => b.closest('.modal').style.display='none');
}

function renderTable() {
    const head = document.getElementById('table-head');
    const body = document.getElementById('table-body');
    const weekLabel = document.getElementById('week-label');
    if(!head || !body) return;

    head.innerHTML = '<th>Jam</th>';
    body.innerHTML = '';

    let start = new Date(currentDate);
    const day = start.getDay();
    const diff = start.getDate() - day + (day === 0 ? -6 : 1);
    start.setDate(diff);

    const weekDates = [];
    const todayStr = new Date().toISOString().split('T')[0];

    for(let i=0; i<7; i++) {
        let d = new Date(start);
        d.setDate(start.getDate() + i);
        const iso = d.toISOString().split('T')[0];
        weekDates.push(iso);

        const isToday = iso === todayStr ? 'class="today-highlight"' : '';
        // UPDATE: Baris di bawah ini hanya menampilkan d.getDate() (Tanggal saja)
        head.innerHTML += `<th ${isToday}>${d.toLocaleDateString('id-ID', {weekday:'long'})}<br><small>${d.getDate()}</small></th>`;
    }

    const optionsMonth = { month: 'long' };
    const monthName = start.toLocaleDateString('id-ID', optionsMonth);
    const startDay = start.getDate();
    const endDay = new Date(weekDates[6]).getDate();
    const endMonth = new Date(weekDates[6]).toLocaleDateString('id-ID', optionsMonth);
    const monthRange = monthName === endMonth ? monthName : `${monthName} - ${endMonth}`;
    
    if(weekLabel) weekLabel.innerText = `Jadwal Mingguan : Bulan ${monthRange}, (${startDay} - ${endDay})`;

    for(let h=9; h<=23; h++) {
        const row = document.createElement('tr');
        const hourStr = h.toString().padStart(2, '0') + ".00";
        row.innerHTML = `<td style="font-weight:bold; color:var(--accent)">${hourStr}</td>`;

        weekDates.forEach(date => {
            const cell = document.createElement('td');
            if(date === todayStr) cell.classList.add('today-column');

            const found = schedules.find(s => s.date === date && h >= parseInt(s.start) && h < parseInt(s.end));
            
            if(found) {
                cell.innerHTML = `<div class="status-cell ${found.type === 'tutup' ? 'status-tutup' : 'status-terisi'}">${found.name}</div>`;
                if(isAdmin && typeof IS_ADMIN_FILE !== 'undefined') {
                    cell.style.cursor = 'pointer';
                    cell.onclick = () => {
                        if(confirm(`Hapus jadwal: ${found.name}?`)) {
                            schedules = schedules.filter(x => x.id !== found.id);
                            save();
                        }
                    };
                }
            } else if(isAdmin && typeof IS_ADMIN_FILE !== 'undefined') {
                cell.style.cursor = 'cell';
                cell.onclick = () => {
                    const manualName = prompt("Nama Penyewa Manual:");
                    if(manualName) {
                        const nextHour = (h + 1).toString().padStart(2, '0') + ".00";
                        schedules.push({ id: Date.now(), name: manualName, date, start: hourStr, end: nextHour, type: 'terisi' });
                        save();
                    }
                };
            }
            row.appendChild(cell);
        });
        body.appendChild(row);
    }
}

function handleBooking() {
    const name = document.getElementById('book-name').value;
    const phone = document.getElementById('book-phone').value;
    const date = document.getElementById('book-date').value;
    const start = document.getElementById('book-start').value;
    const end = document.getElementById('book-end').value;

    if(!name || !phone || !date) return alert("Mohon lengkapi data!");

    const hStart = parseInt(start);
    const hEnd = parseInt(end);
    if(hEnd <= hStart) return alert("Jam selesai tidak valid!");

    // Validasi Maksimal 3 Jam
    if((hEnd - hStart) > 3) return alert("Maaf, maksimal sewa studio adalah 3 jam!");

    const isConflict = schedules.some(s => {
        if (s.date !== date) return false;
        return (hStart < parseInt(s.end) && hEnd > parseInt(s.start));
    });

    if (isConflict) return alert("Jam tersebut sudah terisi!");

    const newReq = { id: Date.now(), name: `${name} (${phone})`, date, start, end, phone, type: 'terisi' };
    pendingRequests.push(newReq);
    localStorage.setItem('rn_pending_requests', JSON.stringify(pendingRequests));
    
    window.open(`https://wa.me/${ADMIN_NUMBER}?text=*PENGAJUAN BOOKING*%0ANama: ${name}%0ATgl: ${date}%0AJam: ${start}-${end}`, '_blank');
    
    alert("Berhasil diajukan! Menunggu konfirmasi admin.");
    if (confirm("Ingin menambahkan jadwal ini ke Google Calendar Anda?")) {
        window.open(generateCalendarLink(name, date, start, end), '_blank');
    }
    location.reload();
}

function handleLogin() {
    const u = document.getElementById('admin-user').value;
    const p = document.getElementById('admin-password').value;
    if(u === ADMIN_CRED.user && p === ADMIN_CRED.pass) {
        sessionStorage.setItem('is_admin', 'true');
        location.reload();
    } else alert("Login Gagal!");
}

function showAdminUI() {
    document.getElementById('login-overlay').style.display = 'none';
    document.getElementById('admin-content').style.display = 'block';
    updateNotificationPanel();
}

function updateNotificationPanel() {
    const list = document.getElementById('request-list');
    const count = document.getElementById('req-count');
    if(!list) return;
    count.innerText = pendingRequests.length;
    list.innerHTML = '';
    pendingRequests.forEach((req, index) => {
        const item = document.createElement('div');
        item.className = 'request-item';
        item.innerHTML = `
            <div class="request-info"><strong>${req.name}</strong><br><small>${req.date} | ${req.start}-${req.end}</small></div>
            <div class="request-actions">
                <button class="btn btn-green btn-sm" onclick="approveReq(${index})">Terima</button>
                <button class="btn btn-red btn-sm" onclick="rejectReq(${index})">Tolak</button>
            </div>`;
        list.appendChild(item);
    });
}

function approveReq(index) {
    schedules.push(pendingRequests[index]);
    pendingRequests.splice(index, 1);
    saveRequests(); save();
}

function rejectReq(index) {
    const req = pendingRequests[index];
    const reason = prompt("Alasan Penolakan:", "Jadwal penuh");
    if(reason) {
        const msg = `Mohon Maaf Untuk Booking Atas Nama : ${req.name.split(' (')[0]}, Tgl: ${req.date}, Jam: ${req.start}-${req.end}. Tidak dapat digunakan karena ${reason}`;
        pendingRequests.splice(index, 1);
        saveRequests();
        window.open(`https://wa.me/${req.phone}?text=${encodeURIComponent(msg)}`, '_blank');
    }
}

function handleSetClose() {
    const d = document.getElementById('close-date').value;
    if(!d) return;
    schedules.push({ id: Date.now(), name: "STUDIO TUTUP", date: d, start: "09.00", end: "24.00", type: 'tutup' });
    save();
}

function save() { localStorage.setItem('rn_brawijaya_db', JSON.stringify(schedules)); renderTable(); }
function saveRequests() { localStorage.setItem('rn_pending_requests', JSON.stringify(pendingRequests)); updateNotificationPanel(); }
function showModal(id) { document.getElementById(id).style.display = 'block'; }

function generateCalendarLink(name, date, start, end) {
    const dateFormatted = date.replace(/-/g, "");
    const startTime = start.replace(".", "") + "00";
    const endTime = end.replace(".", "") + "00";
    return `https://www.google.com/calendar/render?action=TEMPLATE&text=StudioSession_${name}&dates=${dateFormatted}T${startTime}00/${dateFormatted}T${endTime}00&details=Booking_Studio_RUmah_Nada&location=Malang&sf=true&output=xml`;
}
