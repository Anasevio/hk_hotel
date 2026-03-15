@extends('layouts.ra')
@section('title', 'Detail Tugas')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ra/room-checklist.css') }}">
@endpush

@section('content')

<a href="{{ route('ra.rooms.index') }}" class="back-link">← Kembali ke Kamar</a>

<div class="card" role="application">
    <h1>Kamar {{ $task->room->room_number }}</h1>
    <p>{{ ucfirst($task->room->room_type) }} · {{ strtoupper(str_replace('_',' ', $task->room->status)) }}</p>

    <div class="tabs" role="tablist" id="tabNav"></div>

    <div class="section-title" id="sectionTitle"></div>
    <div class="checklist" id="checklist"></div>

    <div class="progress-row">
        <div class="progress-meta">
            <span id="progressLabel">Progress • 0/0</span>
            <span class="progress-pct" id="progressPct">0%</span>
        </div>
        <div class="bar"><div class="bar-fill" id="barFill"></div></div>
    </div>

    <div class="actions">
        <button class="btn btn-secondary" id="btnPrev">← Sebelumnya</button>
        <button class="btn btn-primary"   id="btnNext" disabled>Selanjutnya →</button>
    </div>
</div>

<form method="POST" action="{{ route('ra.tasks.submit', $task->id) }}" id="submitForm" style="display:none">
    @csrf
    <button type="submit" class="btn-submit">✅ Submit ke Supervisor</button>
</form>

@endsection

@push('scripts')
<script>
const data = {
    // ── FORM 1 ──────────────────────────────────────────────────
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
                '~ Telephone + Telephone Line',
                '~ Telephone Directory Label',
                '~ Notepad + Pen',
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
                '~ Bath Tub Stopper',
                '~ Drainage',
                '~ Bath Tub Tap',
                '~ Shower Hose',
                '~ Safety Bar',
                'Soap Tray + Soap',
                'Vanity Counter',
                '~ Mineral Water (2)',
                '~ Tumbler Glasses (2)',
                '~ Wooden Tray',
                '~ Soap Dish',
                '~ Soap 25 gr',
                '~ Environment Tent Card',
                '~ Tissue Box + Metal Cover',
                '~ Tissue Paper',
                'Amenities Tray',
                '~ Conditioning Shampoo & Body Gel',
                '~ Body Lotion',
                '~ Bath Foam',
                '~ Shaving Kit for VIP',
                '~ Sewing Kit',
                '~ Comb',
                '~ Cotton Buds',
                '~ Shower Cap',
            ]
        }
    },
    bathroom2: {
        label: 'Bathroom 2',
        sections: {
            'BATHROOM — BAGIAN 2': [
                'Wash Basin',
                '~ Wash Basin Tap',
                '~ Wash Basin Stopper + Drainage',
                'Bottle Opener',
                'Hair Dryer',
                'Waste Bin',
                'Towel Hamper',
                'Door Hook (1)',
                'Towel Rack (2)',
                '~ Bath Towel (2)',
                '~ Hand Towel (2)',
                'Toilet',
                '~ Toilet Bowl + Flusher',
                '~ Tissue Roll Holder',
                '~ Tissue Roll (2)',
                'Fire Sign',
                'Wall Hook',
                '~ Telephone / Line',
                '~ Drainage',
                'Bathroom Walls',
                'Bathroom Floor',
            ],
            'TERRACES': []
        }
    },
    // ── FORM 2 (UJIKOM) ─────────────────────────────────────────
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
                '~ Coffee (2)',
                '~ White Sugar (2)',
                '~ Sweetener (2)',
                '~ Brown Sugar (2)',
                '~ Mix Creamer (2)',
                '~ Tea (2)',
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
                '~ Letterhead Small (2)',
                '~ Small Envelope (2)',
                '~ Postcard (2)',
                '~ Pencil (1)',
                'Drawer',
                '~ New Testament',
                '~ Qiblat Sign',
                '~ Fire Guide',
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

// ── helpers ────────────────────────────────────────────────────
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

// ── render tabs ────────────────────────────────────────────────
function renderTabs() {
    const nav = document.getElementById('tabNav');
    nav.innerHTML = '';
    tabOrder.forEach((key, i) => {
        const btn = document.createElement('button');
        btn.className = 'tab' +
            (i === activeIdx ? ' active' : '') +
            (isTabComplete(key) && i !== activeIdx ? ' done' : '');
        btn.setAttribute('role', 'tab');
        btn.setAttribute('aria-selected', i === activeIdx);
        btn.textContent = (i + 1) + ' • ' + data[key].label;

        if (!unlocked[key]) btn.disabled = true;
        btn.addEventListener('click', () => {
            if (!unlocked[key]) return;
            activeIdx = i;
            render();
        });
        nav.appendChild(btn);
    });
}

// ── render checklist ───────────────────────────────────────────
function render() {
    const key      = tabOrder[activeIdx];
    const tabData  = data[key];
    const sections = tabData.sections;
    const firstSec = Object.keys(sections)[0];

    document.getElementById('sectionTitle').textContent = firstSec;

    const cl = document.getElementById('checklist');
    cl.innerHTML = '';

    Object.entries(sections).forEach(([secTitle, items]) => {
        if (items.length === 0) return;

        // Sub-section header (kalau lebih dari 1 section dalam tab)
        if (Object.keys(sections).filter(s => data[key].sections[s].length > 0).length > 1) {
            const div = document.createElement('div');
            div.className = 'subsection-header';
            div.textContent = secTitle;
            cl.appendChild(div);
        }

        items.forEach((label, idx) => {
            const id   = `${key}-${secTitle.replace(/\s/g,'-')}-${idx}`;
            const item = document.createElement('div');
            item.className = 'item' + (checked[key][secTitle][idx] ? ' checked' : '');

            const input    = document.createElement('input');
            input.type     = 'checkbox';
            input.id       = id;
            input.checked  = checked[key][secTitle][idx];

            input.addEventListener('change', () => {
                checked[key][secTitle][idx] = input.checked;
                item.classList.toggle('checked', input.checked);
                updateProgress();
                updateButtons();
                renderTabs();
            });

            const labelEl   = document.createElement('label');
            labelEl.htmlFor = id;
            labelEl.textContent = label;

            item.appendChild(input);
            item.appendChild(labelEl);

            item.addEventListener('click', ev => {
                if (ev.target !== input) {
                    input.checked = !input.checked;
                    input.dispatchEvent(new Event('change'));
                }
            });

            cl.appendChild(item);
        });
    });

    updateProgress();
    updateButtons();
    renderTabs();
}

// ── progress ───────────────────────────────────────────────────
function updateProgress() {
    const key   = tabOrder[activeIdx];
    const total = totalOf(key);
    const done  = doneOf(key);
    const pct   = total ? Math.round((done / total) * 100) : 0;
    document.getElementById('progressLabel').textContent =
        `Progress ${data[key].label} • ${done}/${total}`;
    document.getElementById('progressPct').textContent = pct + '%';
    document.getElementById('barFill').style.width = pct + '%';
}

// ── buttons ────────────────────────────────────────────────────
function updateButtons() {
    const key    = tabOrder[activeIdx];
    const isLast = activeIdx === tabOrder.length - 1;
    const isDone = isTabComplete(key);

    const btnNext    = document.getElementById('btnNext');
    const btnPrev    = document.getElementById('btnPrev');
    const submitForm = document.getElementById('submitForm');

    btnPrev.style.display = activeIdx === 0 ? 'none' : '';
    btnNext.style.display = isLast ? 'none' : '';

    btnNext.disabled      = !isDone;
    btnNext.style.opacity = isDone ? '1' : '0.55';
    btnNext.style.cursor  = isDone ? '' : 'not-allowed';

    const nextLabel = tabOrder[activeIdx + 1] ? data[tabOrder[activeIdx + 1]].label : '';
    btnNext.textContent = `Lanjut ke ${nextLabel} →`;

    submitForm.style.display = isLast && isDone ? '' : 'none';
}

// ── nav ────────────────────────────────────────────────────────
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

// ── init ───────────────────────────────────────────────────────
render();
</script>
@endpush