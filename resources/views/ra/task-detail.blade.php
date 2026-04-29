@extends('layouts.topbar')
@section('title', 'Detail Tugas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ra/room-checklist.css') }}">
@endpush

@section('content')

<div class="checklist-page">

    {{-- Back --}}
    <a href="{{ route('ra.rooms.index') }}" class="back-link"
       style="margin-bottom:14px; display:inline-flex">← Kembali ke Kamar</a>

    {{-- Header --}}
    <div class="room-header-card">
        <h2>Kamar {{ $task->room->room_number }}</h2>
        <p>{{ ucfirst($task->room->room_type) }} · {{ strtoupper(str_replace('_', ' ', $task->room->status)) }}</p>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:12px">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom:12px">⚠️ {{ session('error') }}</div>
    @endif

    {{-- ══════════════════════════════════════════
         KONDISI 1: PENDING / RETURNED — belum / perlu diulang
         ══════════════════════════════════════════ --}}
    @if($task->status === 'pending' && !$task->sop_viewed_at)

    {{-- SOP SCREEN --}}
    <div class="start-screen">
        <h3>SOP (Standard Operating Procedure)</h3>

        <ul>
            @foreach($task->checklists as $item)
                <li>{{ $item->name }}</li>
            @endforeach
        </ul>

        <form method="POST" action="{{ route('ra.tasks.sopDone', $task->id) }}">
            @csrf
            <button class="btn-start">✔ Saya Sudah Baca SOP</button>
        </form>
    </div>

@elseif(in_array($task->status, ['pending', 'returned_to_ra']))

        <div class="start-screen">
            @if($task->status === 'returned_to_ra')
            <h3>Perbaiki & Mulai Ulang</h3>
            <p>Supervisor mengembalikan tugas ini. Periksa catatan di bawah lalu mulai ulang.</p>
            @if($task->supervisor_note)
            <div style="background:#fde8e8;border-radius:10px;padding:12px 16px;margin:12px 0;font-size:13px;color:#c62828;text-align:left">
                <strong>Catatan Supervisor:</strong> {{ $task->supervisor_note }}
            </div>
            @endif
            @else
            <h3>Siap Mulai?</h3>
            <p>Tugas akan dimulai dan timer langsung berjalan.</p>
            @endif

            <div class="start-meta">
                <div class="start-meta-item">
                    <div class="start-meta-val">{{ $task->time_limit }}</div>
                    <div class="start-meta-key">Menit</div>
                </div>
                <div class="start-meta-item">
                    <div class="start-meta-val">
                        {{ $task->checklists->where('type', 'preparation')->count() +
                           $task->checklists->where('type', 'cleaning')->count() }}
                    </div>
                    <div class="start-meta-key">Item Checklist</div>
                </div>
                <div class="start-meta-item">
                    <div class="start-meta-val">{{ ucfirst($task->room->room_type) }}</div>
                    <div class="start-meta-key">Tipe Kamar</div>
                </div>
            </div>

            <form method="POST" action="{{ route('ra.tasks.start', $task->id) }}">
                @csrf
                <button type="submit" class="btn-start">▶ Mulai Tugas</button>
            </form>
        </div>

    {{-- ══════════════════════════════════════════
         KONDISI 2: IN PROGRESS — timer + checklist
         ══════════════════════════════════════════ --}}
    @else

        {{-- Timer Bar --}}
        <div class="timer-bar">
            <div>
                <div class="timer-label">⏱ Timer</div>
                <div class="timer-display" id="timerDisplay">--:--</div>
            </div>
            <div class="timer-track">
                <div class="timer-fill" id="timerFill" style="width:100%"></div>
            </div>
            <div>
                <div class="timer-overtime" id="timerOvertime">+00:00 melebihi</div>
            </div>
        </div>

        {{-- Checklist card --}}
        <div class="checklist-card">

            {{-- Tabs --}}
            <div class="cl-tabs" id="tabNav"></div>

            {{-- Section title --}}
            <div class="cl-section-title" id="sectionTitle"></div>

            {{-- Items --}}
            <div class="cl-grid" id="checklist"></div>

            {{-- Progress --}}
            <div class="cl-progress">
                <div class="cl-progress-meta">
                    <span id="progressLabel">Progress • 0/0</span>
                    <span class="cl-progress-pct" id="progressPct">0%</span>
                </div>
                <div class="cl-bar"><div class="cl-bar-fill" id="barFill"></div></div>
            </div>

            {{-- Nav buttons --}}
            <div class="cl-actions">
                <button class="btn btn-secondary" id="btnPrev" style="display:none">← Sebelumnya</button>
                <button class="btn btn-primary"   id="btnNext" disabled>Selanjutnya →</button>
            </div>

        </div>

        {{-- Submit --}}
        <form method="POST" action="{{ route('ra.tasks.submit', $task->id) }}"
              id="submitForm" style="display:none">
            @csrf
            <input type="hidden" name="checklists" id="checklistsInput">

            <button type="submit" class="btn-submit">✅ Submit ke Supervisor</button>
        </form>

    @endif

</div>

@endsection

@push('scripts')
<script>
// ── TIMER ────────────────────────────────────────────────────
@if($task->status === 'in_progress')
(function() {
    const startedAt  = new Date("{{ $task->started_at->toIso8601String() }}");
    const timeLimitS = {{ $task->time_limit }} * 60; // konversi ke detik
    const display    = document.getElementById('timerDisplay');
    const fill       = document.getElementById('timerFill');
    const overtime   = document.getElementById('timerOvertime');

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        const elapsed  = Math.floor((Date.now() - startedAt.getTime()) / 1000);
        const remain   = timeLimitS - elapsed;

        if (remain >= 0) {
            // Masih dalam batas waktu
            const m = Math.floor(remain / 60);
            const s = remain % 60;
            display.textContent = `${pad(m)}:${pad(s)}`;

            const pct = (remain / timeLimitS) * 100;
            fill.style.width = pct + '%';

            // Warning saat < 20%
            if (pct <= 20) {
                display.classList.add('warning');
                fill.classList.add('warning');
                display.classList.remove('danger');
                fill.classList.remove('danger');
            }
            // Danger saat < 5%
            if (pct <= 5) {
                display.classList.add('danger');
                fill.classList.add('danger');
                display.classList.remove('warning');
                fill.classList.remove('warning');
            }

            overtime.classList.remove('show');

        } else {
            // Overtime — timer merah, terus jalan
            const over = Math.abs(remain);
            const m    = Math.floor(over / 60);
            const s    = over % 60;
            display.textContent = `-${pad(m)}:${pad(s)}`;
            display.classList.add('danger');
            display.classList.remove('warning');
            fill.style.width = '0%';
            fill.classList.add('danger');
            overtime.textContent = `+${pad(m)}:${pad(s)} melebihi batas`;
            overtime.classList.add('show');
        }
    }

    tick();
    setInterval(tick, 1000);
})();
@endif

// ── CHECKLIST ────────────────────────────────────────────────
@if(in_array($task->status, ['in_progress', 'returned_to_ra']))
const data = {
    bedroom: {
        label: 'Bedroom',
        sections: {
            'BEDROOM': [
                'Headboard + Bed',
                'Pillow + Cushions',
                'Bed Cover',
                'Bed Skirting',
                'Bedside Table with Control Panel',
                'Bedside Table + Drawers',
                'Yellow Pages',
                'Telephone + Telephone Line',
                'Telephone Directory Label',
                'Notepad + Pen',
            ]
        }
    },
    livingroom: {
        label: 'Living Room',
        sections: {
            'LIVING ROOM': [
                'Window Frame + Glasses',
                'Sheer Curtain',
                'Night Curtain',
                'Sofa + Cushions (2)',
                'Coffe Table',
                'Ashtray + Matches + Magazines',
                'Bedside Table',
                'Lamp + Shade',
                'Puf + Cushion',
                'Mirror With Figure',
            ]
        }
    },
    bathroom1: {
        label: 'Bathroom 1',
        sections: {
            'BATHROOM — BAGIAN 1': [
                'Bathroom Door + Handle',
                'Bath Mat (1)',
                'Bath Tub',
                'Bath Tub Stopper',
                'Drainage',
                'Bath Tub Tap',
                'Shower Hose',
                'Safety Bar',
                'Soap Tray + Soap',
                'Vanity Counter',
                'Mineral Water (2)',
                'Tumbler Glasses (2)',
                'Wooden Tray',
                'Soap Dish',
                'Soap 25 gr',
                'Environment Tent Card',
                'Tissue Box + Metal Cover',
                'Tissue Paper',
                'Amenities Tray',
                'Conditioning Shampoo & Body Gel',
                'Body Lotion',
                'Bath Foam',
                'Shaving Kit for VIP',
                'Sewing Kit',
                'Comb',
                'Cotton Buds',
                'Shower Cap',
            ]
        }
    },
    bathroom2: {
        label: 'Bathroom 2',
        sections: {
            'BATHROOM — BAGIAN 2': [
                'Wash Basin',
                'Wash Basin Tap',
                'Wash Basin Stopper + Drainage',
                'Bottle Opener',
                'Hair Dryer',
                'Waste Bin',
                'Towel Hamper',
                'Door Hook (1)',
                'Towel Rack (2)',
                'Bath Towel (2)',
                'Hand Towel (2)',
                'Toilet',
                'Toilet Bowl + Flusher',
                'Tissue Roll Holder',
                'Tissue Roll (2)',
                'Fire Sign',
                'Wall Hook',
                'Telephone / Line',
                'Drainage',
                'Bathroom Walls',
                'Bathroom Floor',
            ],
            'TERRACES': []
        }
    },
    entrance: {
        label: 'Entrance',
        sections: {
            'ENTRANCE': [
                'Door / Frame / Handle',
                'Entrance Light / Room No.',
                'Privacy / Environment / Service Signs',
                'Living Room / Courtesy Light',
                'Master Control / Switch Control',
                'Emergency Exit Plan',
                'Peeping Hole',
                'Wall Wooden Box-fixed',
                'Wall Wooden Shelf-fixed',
            ]
        }
    },
    wardrobe: {
        label: 'Wardrobe',
        sections: {
            'WARDROBE': [
                'Wardrobe Door',
                'Wardrobe Shelf + Bar',
                'Wooden Hanger (10)',
                'Wooden Hanger + Bathrobe (2)',
                'Extra Pillow + Pillow Case (1)',
                'Laundry Basket + Bag',
                'Laundry Service List',
                'Slippers (for VIP)',
                'Shoe Rack',
            ]
        }
    },
    minibar: {
        label: 'Minibar',
        sections: {
            'MINIBAR COUNTER': [
                'Minibar Price List with Folder',
                'Refill Minibar (see MB Price list)',
                'Water Boiler + Tray',
                'Coofee / Tea Box',
                'Coffee (2)',
                'White Sugar (2)',
                'Sweetener (2)',
                'Brown Sugar (2)',
                'Mix Creamer (2)',
                'Tea (2)',
                'Coffee Mugs (2)',
                'Tea Spoons (2)',
                'Coffee Mugs Clothe',
                'Nuts / Cookies (2+2)',
                'Minibar Glass Shelf',
                'High Glass With Coster (2)',
                'Cocktail Napkins (4) + Bowl',
                'Refrigerator Cabinet',
                'Refrigerator',
            ],
            'LUGGAGE RACK': [
                'Drawers + Hangers',
            ]
        }
    },
    tvcabinet: {
        label: 'TV Cabinet',
        sections: {
            'TV CABINET': [
                'Cabinet with drawer + handle + Shelf',
                'Safety Box + Info.',
                'TV Cabinet + TV + Swivel',
                'TV Socket',
                'TV Channel List',
            ]
        }
    },
    writingdesk: {
        label: 'Writing Desk',
        sections: {
            'WRITING DESK': [
                'Writing Desk / Drawer / Handle / Yellow P.',
                'Chair + Waste Bin',
                'Mirrors',
                'Desk Lamp with Shade',
                'Magazine',
                'Directory of Service',
                'Stationery Folder',
                'The Patra Flyer',
                'Letterhead Small (2)',
                'Small Envelope (2)',
                'Postcard (2)',
                'Pencil (1)',
                'Drawer',
                'New Testament',
                'Qiblat Sign',
                'Fire Guide',
            ]
        }
    },
    toilet: {
        label: 'Toilet',
        sections: {
            'TOILET': [
                'Door + Handle + Stopper',
                'Tissue Roll Holder',
                'Shower Hose',
                'Toilet Bowl + Flusher',
                'Wash Basin + Mirror',
                'Wash Basin Tap',
                'Wash Basin Stopper + Drainage',
                'Soap Dish + Soap',
                'Towel Holder + Towel',
                'Waste Bin',
                'Mirrors',
            ]
        }
    },
    general: {
        label: 'General',
        sections: {
            'GENERAL': [
                'Ceilings',
                'Wooden Furnitures',
                'Wall + Mirrors',
                'Lamps / Lights / Switches',
                'Odour + Room Temperature',
                'Floor + Marble Skirting + Carpet',
                'AC Thermostat Panel',
                'AC Grill + Exhaust',
                'Smoke Detector + Water Sprinkler',
                'Plants Pots / Vase + Plant',
            ]
        }
    },
};

// UPDATED VERSION BASED ON YOUR OLD CODE (FIXED UX, NO LAG, NO SCROLL JUMP)

const tabOrder = Object.keys(data);
const checked  = {};
const unlocked = {};

tabOrder.forEach((key, i) => {
    checked[key] = {};
    Object.keys(data[key].sections).forEach(sec => {
        checked[key][sec] = data[key].sections[sec].map(() => false);
    });
    unlocked[key] = i === 0;
});

let activeIdx = 0;

function updateCheckAllState(checkAll, key) {
    const allValues = Object.values(checked[key]).flat();
    const total = allValues.length;
    const done  = allValues.filter(v => v).length;

    checkAll.checked = done === total;
    checkAll.indeterminate = done > 0 && done < total;
}

function totalOf(key) {
    return Object.values(data[key].sections).reduce((s, items) => s + items.length, 0);
}
function doneOf(key) {
    return Object.values(checked[key]).reduce((s, arr) => s + arr.filter(Boolean).length, 0);
}
function isTabComplete(key) {
    const t = totalOf(key);
    return t > 0 && doneOf(key) === t;
}

function renderTabs() {
    const nav = document.getElementById('tabNav');
    nav.innerHTML = '';

    tabOrder.forEach((key, i) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'cl-tab' +
            (i === activeIdx ? ' active' : '') +
            (isTabComplete(key) && i !== activeIdx ? ' done' : '');

        btn.textContent = isTabComplete(key) && i !== activeIdx
            ? data[key].label + ' ✓'
            : data[key].label;

        btn.disabled = !unlocked[key];

        btn.addEventListener('click', () => {
            if (!unlocked[key]) return;
            activeIdx = i;
            render();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        nav.appendChild(btn);
    });
}

function render() {
    const key      = tabOrder[activeIdx];
    const sections = data[key].sections;
    const firstSec = Object.keys(sections)[0];

    document.getElementById('sectionTitle').textContent = firstSec;

    const cl = document.getElementById('checklist');
    cl.innerHTML = '';

    // ✅ CHECK ALL
    const checkAllWrapper = document.createElement('div');
    checkAllWrapper.style.marginBottom = '10px';

    const checkAll = document.createElement('input');
    checkAll.type = 'checkbox';
    checkAll.id = 'checkAll';

    const label = document.createElement('label');
    label.textContent = ' Checklist Semua';
    label.prepend(checkAll);

    checkAllWrapper.appendChild(label);
    cl.appendChild(checkAllWrapper);
    updateCheckAllState(checkAll, key);

    checkAll.addEventListener('change', () => {
        Object.keys(checked[key]).forEach(sec => {
            checked[key][sec] = checked[key][sec].map(() => checkAll.checked);
        });

        document.querySelectorAll('#checklist input[type=checkbox]')
    .forEach(cb => {
        cb.checked = checkAll.checked;

        // 🔥 TAMBAHAN INI
        const parent = cb.closest('.cl-item');
        if (parent) {
            parent.classList.toggle('checked', checkAll.checked);
        }
    });
        updateProgress();
        updateButtons();

        updateCheckAllState(checkAll, key);
    });

    const multiSection = Object.keys(sections).filter(s => sections[s].length > 0).length > 1;

    Object.entries(sections).forEach(([secTitle, items]) => {
        if (items.length === 0) return;

        if (multiSection) {
            const div = document.createElement('div');
            div.className = 'cl-subsection';
            div.textContent = secTitle;
            cl.appendChild(div);
        }

        items.forEach((labelText, idx) => {
            const id = `${key}-${secTitle.replace(/\s/g,'-')}-${idx}`;

            // USE LABEL AS WRAPPER (NATIVE BEHAVIOR)
            const item = document.createElement('label');
            item.className = 'cl-item';

            const input = document.createElement('input');
            input.type = 'checkbox';
            input.id = id;
            input.checked = checked[key][secTitle][idx];

            const span = document.createElement('span');
            span.className = 'cl-item-label';
            span.textContent = labelText;

            input.addEventListener('change', () => {
                checked[key][secTitle][idx] = input.checked;
                item.classList.toggle('checked', input.checked);

                updateProgress();
                updateButtons();

                updateCheckAllState(checkAll, key);

                // only update tabs when complete
                if (isTabComplete(key)) {
                    renderTabs();
                }
            });

            item.appendChild(input);
            item.appendChild(span);

            if (input.checked) item.classList.add('checked');

            cl.appendChild(item);
        });
    });

    updateProgress();
    updateButtons();
    renderTabs();
}

function updateProgress() {
    const key   = tabOrder[activeIdx];
    const total = totalOf(key);
    const done  = doneOf(key);
    const pct   = total ? Math.round((done / total) * 100) : 0;

    document.getElementById('progressLabel').textContent =
        `${data[key].label} • ${done}/${total}`;
    document.getElementById('progressPct').textContent = pct + '%';
    document.getElementById('barFill').style.width = pct + '%';
}

function updateButtons() {
    const key    = tabOrder[activeIdx];
    const isLast = activeIdx === tabOrder.length - 1;
    const isDone = isTabComplete(key);

    const btnNext    = document.getElementById('btnNext');
    const btnPrev    = document.getElementById('btnPrev');
    const submitForm = document.getElementById('submitForm');

    btnPrev.style.display = activeIdx === 0 ? 'none' : '';
    btnNext.style.display = isLast ? 'none' : '';

    btnNext.disabled = !isDone;

    const nextLabel = tabOrder[activeIdx + 1] ? data[tabOrder[activeIdx + 1]].label : '';
    btnNext.textContent = `Lanjut ke ${nextLabel} →`;

    submitForm.style.display = isLast && isDone ? '' : 'none';
}

// NAVIGATION

document.getElementById('btnNext').addEventListener('click', () => {
    const nextKey = tabOrder[activeIdx + 1];
    if (!nextKey) return;

    unlocked[nextKey] = true;
    activeIdx++;

    render();
    window.scrollTo({ top: 0, behavior: 'smooth' });
});


document.getElementById('btnPrev').addEventListener('click', () => {
    if (activeIdx > 0) {
        activeIdx--;
        render();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

render();

document.getElementById('submitForm').addEventListener('submit', function () {
    document.getElementById('checklistsInput').value = JSON.stringify(checked);
});

@endif
</script>
@endpush