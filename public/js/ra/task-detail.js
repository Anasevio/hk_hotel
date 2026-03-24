const tabOrder = Object.keys(data);
const checked = {};
const unlocked = {};

tabOrder.forEach((key, i) => {
    checked[key] = {};
    Object.keys(data[key].sections).forEach((sec) => {
        checked[key][sec] = data[key].sections[sec].map(() => false);
    });
    unlocked[key] = i === 0;
});

let activeIdx = 0;

function totalOf(key) {
    return Object.values(data[key].sections).reduce(
        (s, items) => s + items.length,
        0,
    );
}
function doneOf(key) {
    return Object.values(checked[key]).reduce(
        (s, arr) => s + arr.filter(Boolean).length,
        0,
    );
}
function isTabComplete(key) {
    const t = totalOf(key);
    return t > 0 && doneOf(key) === t;
}

function renderTabs() {
    const nav = document.getElementById("tabNav");
    nav.innerHTML = "";
    tabOrder.forEach((key, i) => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className =
            "cl-tab" +
            (i === activeIdx ? " active" : "") +
            (isTabComplete(key) && i !== activeIdx ? " done" : "");
        btn.setAttribute("role", "tab");
        btn.setAttribute("aria-selected", i === activeIdx);
        btn.textContent =
            isTabComplete(key) && i !== activeIdx
                ? data[key].label + " ✓"
                : data[key].label;
        btn.disabled = !unlocked[key];
        btn.addEventListener("click", () => {
            if (!unlocked[key]) return;
            activeIdx = i;
            render();
        });
        nav.appendChild(btn);
    });

    const activeBtn = nav.querySelector(".cl-tab.active");
}

function render() {
    const key = tabOrder[activeIdx];
    const sections = data[key].sections;
    const firstSec = Object.keys(sections)[0];

    document.getElementById("sectionTitle").textContent = firstSec;

    const cl = document.getElementById("checklist");
    cl.innerHTML = "";

    const multiSection =
        Object.keys(sections).filter((s) => sections[s].length > 0).length > 1;

    Object.entries(sections).forEach(([secTitle, items]) => {
        if (items.length === 0) return;

        if (multiSection) {
            const div = document.createElement("div");
            div.className = "cl-subsection";
            div.textContent = secTitle;
            cl.appendChild(div);
        }

        items.forEach((label, idx) => {
            const id = `${key}-${secTitle.replace(/\s/g, "-")}-${idx}`;
            const item = document.createElement("div");
            item.className =
                "cl-item" + (checked[key][secTitle][idx] ? " checked" : "");

            const input = document.createElement("input");
            input.type = "checkbox";
            input.id = id;
            input.checked = checked[key][secTitle][idx];

            input.addEventListener("change", () => {
                checked[key][secTitle][idx] = input.checked;
                item.classList.toggle("checked", input.checked);
                updateProgress();
                updateButtons();
                if (isTabComplete(key)) {
                    renderTabs();
                }
            });

            const labelEl = document.createElement("label");
            labelEl.htmlFor = id;
            labelEl.className = "cl-item-label";
            labelEl.textContent = label;

            item.appendChild(input);
            item.appendChild(labelEl);

            labelEl.addEventListener("click", () => {
                input.checked = !input.checked;
                input.dispatchEvent(new Event("change"));
            });

            cl.appendChild(item);
        });
    });

    updateProgress();
    updateButtons();
    renderTabs();
}

function updateProgress() {
    const key = tabOrder[activeIdx];
    const total = totalOf(key);
    const done = doneOf(key);
    const pct = total ? Math.round((done / total) * 100) : 0;
    document.getElementById("progressLabel").textContent =
        `${data[key].label} • ${done}/${total}`;
    document.getElementById("progressPct").textContent = pct + "%";
    document.getElementById("barFill").style.width = pct + "%";
}

function updateButtons() {
    const key = tabOrder[activeIdx];
    const isLast = activeIdx === tabOrder.length - 1;
    const isDone = isTabComplete(key);

    const btnNext = document.getElementById("btnNext");
    const btnPrev = document.getElementById("btnPrev");
    const submitForm = document.getElementById("submitForm");

    btnPrev.style.display = activeIdx === 0 ? "none" : "";
    btnNext.style.display = isLast ? "none" : "";

    btnNext.disabled = !isDone;
    btnNext.style.opacity = isDone ? "1" : "0.5";
    btnNext.style.cursor = isDone ? "" : "not-allowed";

    const nextLabel = tabOrder[activeIdx + 1]
        ? data[tabOrder[activeIdx + 1]].label
        : "";
    btnNext.textContent = `Lanjut ke ${nextLabel} →`;

    submitForm.style.display = isLast && isDone ? "" : "none";
}

document.getElementById("btnNext").addEventListener("click", () => {
    const nextKey = tabOrder[activeIdx + 1];
    if (!nextKey) return;
    unlocked[nextKey] = true;
    activeIdx++;
    render();
    window.scrollTo({ top: 0, behavior: "smooth" });
});

document.getElementById("btnPrev").addEventListener("click", () => {
    if (activeIdx > 0) {
        activeIdx--;
        render();
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
});

render();
