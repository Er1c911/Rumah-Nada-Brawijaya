// --- 1. KONFIGURASI FIREBASE ---
const firebaseConfig = {
    apiKey: "AIzaSyD6qDgPe7s1DD_F5zXOiFU-nKwATsuVaFA",
    authDomain: "rumah-nada-brawijaya.firebaseapp.com",
    databaseURL: "https://rumah-nada-brawijaya-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "rumah-nada-brawijaya",
    storageBucket: "rumah-nada-brawijaya.firebasestorage.app",
    messagingSenderId: "593614745117",
    appId: "1:593614745117:web:2ff5e80d23b905c26f20f4",
    measurementId: "G-VQRJTBNW8K"
};

// Inisialisasi Firebase
firebase.initializeApp(firebaseConfig);
const db = firebase.database();

// --- 2. VARIABEL GLOBAL ---
const ADMIN_NUMBER = "6282143857754";
const ADMIN_CRED = { user: "Divisi RT", pass: "rtkeras" };

let currentDate = new Date();
let schedules = [];
let pendingRequests = [];
let isAdmin = sessionStorage.getItem('is_admin') === 'true';

// --- 3. MONITOR DATA REAL-TIME (SINKRONISASI) ---
db.ref('schedules').on('value', (snapshot) => {
    const data = snapshot.val();
    schedules = data ? Object.values(data) : [];
    renderTable(); 
});

db.ref('pendingRequests').on('value', (snapshot) => {
    const data = snapshot.val();
    pendingRequests = data ? Object.values(data) : [];
    if(isAdmin) updateNotificationPanel();
});

// --- 4. LOGIKA JALANNYA PROGRAM ---
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
    if(document.getElementById('prev-week')) {
        document.getElementById('prev-week').onclick = () => { currentDate.setDate(currentDate.getDate() - 7); renderTable(); };
    }
    if(document.getElementById('next-week')) {
        document.getElementById('next-week').onclick = () => { currentDate.setDate(currentDate.getDate() + 7); renderTable(); };
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
        head.innerHTML += `<th ${isToday}>${d.toLocaleDateString('id-ID', {weekday:'long'})}<br><small>${d.getDate()}</small></th>`;
    }

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
                            db.ref('schedules').orderByChild('id').equalTo(found.id).once('value', snapshot => {
                                snapshot.forEach(child => child.ref.remove());
                            });
                        }
                    };
                }
            } else if(isAdmin && typeof IS_ADMIN_FILE !== 'undefined') {
                cell.style.cursor = 'cell';
                cell.onclick = () => {
                    const manualName = prompt("Nama Penyewa Manual:");
                    if(manualName) {
                        const nextHour = (h + 1).toString().padStart(2, '0') + ".00";
                        const newSch = { id: Date.now(), name: manualName, date, start: hourStr, end: nextHour, type: 'terisi' };
                        db.ref('schedules').push(newSch);
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
    const hStart = parseInt(start); const hEnd = parseInt(end);
    if(hEnd <= hStart) return alert("Jam selesai tidak valid!");

    const newReq = { id: Date.now(), name: `${name} (${phone})`, date, start, end, phone, type: 'terisi' };
    
    // Simpan ke Firebase
    db.ref('pendingRequests').push(newReq);
    
    window.open(`https://wa.me/${ADMIN_NUMBER}?text=*PENGAJUAN BOOKING*%0ANama: ${name}%0ATgl: ${date}%0AJam: ${start}-${end}`, '_blank');
    alert("Berhasil diajukan ke admin!");
    document.getElementById('booking-modal').style.display = 'none';
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
    const overlay = document.getElementById('login-overlay');
    const content = document.getElementById('admin-content');
    if(overlay) overlay.style.display = 'none';
    if(content) content.style.display = 'block';
}

function updateNotificationPanel() {
    const list = document.getElementById('request-list');
    const count = document.getElementById('req-count');
    if(!list) return;
    count.innerText = pendingRequests.length;
    list.innerHTML = '';
    pendingRequests.forEach((req) => {
        const item = document.createElement('div');
        item.className = 'request-item';
        item.innerHTML = `
            <div class="request-info"><strong>${req.name}</strong><br><small>${req.date} | ${req.start}-${req.end}</small></div>
            <div class="request-actions">
                <button class="btn btn-green btn-sm" onclick="approveReq(${req.id})">Terima</button>
                <button class="btn btn-red btn-sm" onclick="rejectReq(${req.id})">Tolak</button>
            </div>`;
        list.appendChild(item);
    });
}

window.approveReq = function(id) {
    const req = pendingRequests.find(r => r.id === id);
    if(req) {
        db.ref('schedules').push(req);
        db.ref('pendingRequests').orderByChild('id').equalTo(id).once('value', snapshot => {
            snapshot.forEach(child => child.ref.remove());
        });
    }
};

window.rejectReq = function(id) {
    const req = pendingRequests.find(r => r.id === id);
    if(req) {
        db.ref('pendingRequests').orderByChild('id').equalTo(id).once('value', snapshot => {
            snapshot.forEach(child => child.ref.remove());
        });
    }
};

function handleSetClose() {
    const d = document.getElementById('close-date').value;
    if(!d) return;
    const closeSch = { id: Date.now(), name: "STUDIO TUTUP", date: d, start: "09.00", end: "24.00", type: 'tutup' };
    db.ref('schedules').push(closeSch);
    alert("Studio berhasil ditutup untuk tanggal tersebut.");
}

function showModal(id) { document.getElementById(id).style.display = 'block'; }
