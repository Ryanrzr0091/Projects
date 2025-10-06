// Project Neptune - Dashboard + Bulk Reporting

const readout   = document.getElementById("readout");
const indicator = document.getElementById("csg-status");
const label     = document.getElementById("csg-label");

const goText     = ok => ok ? "GO" : "NO-GO";
const colorClass = c  => (c || "green").toLowerCase();

async function fetchSummary() {
  const res = await fetch("Data/api/csg_summary.php", { cache: "no-store" });
  if (!res.ok) throw new Error(`summary API error (${res.status})`);
  return res.json();
}

function parseNum(inputEl) {
  if (!inputEl) return null;
  const raw = String(inputEl.value ?? "").trim();
  if (raw === "") return null;             
  const n = Number.parseInt(raw, 10);
  return Number.isNaN(n) ? null : n;       
}

function cellClassFor(v) {
  if (v == null || Number.isNaN(v)) return "";
  if (v <= 50) return "cell-red";
  if (v <= 78) return "cell-amber";
  return "";
}

function limitingAssets(assets) {
  const reds = assets.filter(a => a.color === "RED");
  if (reds.length) return reds;
  return assets.filter(a => a.color === "AMBER");
}

function renderSummary(d) {
  // Update indicator
  indicator.setAttribute("data-status", d.csg.color);
  label.textContent = `CSG STATUS: ${goText(d.csg.go)} (${d.csg.color})`;

  const limiters = limitingAssets(d.assets);
  const limiterText = limiters.slice(0, 6)
    .map(a => `${a.name} (${a.hull || "N/A"}) [${a.color}]`)
    .join(", ");

  let html = `
    <div class="kv"><b>Summary:</b> ${d.csg.total} assets,
      ${d.csg.red_count} RED, ${d.csg.amber_count} AMBER
    </div>
    <div class="kv"><b>Limiting assets:</b> ${limiterText || "None"}</div>
    <form id="report-form">
      <div class="asset-table-wrapper">
        <table class="asset-table" id="asset-table">
          <tr>
            <th>Kind</th><th>Name/Hull</th><th>Status</th>
            <th>Fuel%</th><th>Crew%</th><th>Ammunition%</th>
            <th>Weapons GO</th><th>Comms GO</th><th>Remarks</th>
          </tr>
  `;

  for (const a of d.assets) {
    const remarks = a.remarks ? a.remarks.replace(/"/g, '&quot;') : "";
    const cFuel = cellClassFor(a.fuel);
    const cCrew = cellClassFor(a.crew);
    const cAmmo = cellClassFor(a.ammunition);

    html += `
      <tr data-id="${a.id}" class="row-clickable">
        <td>${a.kind}</td>
        <td>${a.name} (${a.hull || "N/A"})</td>
        <td><span class="pill ${colorClass(a.color)}">${a.color}</span></td>
        <td class="${cFuel}"><input type="number" step="1" min="0" max="100" name="fuel-${a.id}" value="${a.fuel ?? ''}"></td>
        <td class="${cCrew}"><input type="number" step="1" min="0" max="100" name="crew-${a.id}" value="${a.crew ?? ''}"></td>
        <td class="${cAmmo}"><input type="number" step="1" min="0" max="100" name="ammo-${a.id}" value="${a.ammunition ?? ''}"></td>
        <td><input type="checkbox" name="weapons-${a.id}" ${a.weapons_go ? 'checked' : ''}></td>
        <td><input type="checkbox" name="comms-${a.id}"   ${a.comms_go   ? 'checked' : ''}></td>
        <td><input type="text"   name="remarks-${a.id}" value="${remarks}"></td>
      </tr>
    `;
  }

  html += `
        </table>
      </div>
      <button type="submit">Submit CSG Report</button>
    </form>
  `;

  readout.innerHTML = html;

  // Row selection (inspect behavior)
  const tbl = document.getElementById("asset-table");
  tbl.addEventListener("click", (e) => {
    let tr = e.target.closest("tr[data-id]");
    if (!tr) return;
    // toggle selection
    if (tr.classList.contains("row-selected")) tr.classList.remove("row-selected");
    else {
      // single-select aesthetic
      tbl.querySelectorAll("tr.row-selected").forEach(x => x.classList.remove("row-selected"));
      tr.classList.add("row-selected");
    }
  });

  document.getElementById("report-form").addEventListener("submit", async (e) => {
    e.preventDefault();
    try {
      await submitReport(d.assets);
      await refresh(); // pull fresh colors/values
    } catch (err) {
      alert("Report failed: " + (err?.message || err));
    }
  });
}

async function submitReport(assets) {
  const form = document.getElementById("report-form");
  const rows = assets.map(a => ({
    id: a.id,
    fuel:        parseNum(form[`fuel-${a.id}`]),
    crew:        parseNum(form[`crew-${a.id}`]),
    ammunition:  parseNum(form[`ammo-${a.id}`]),
    weapons_go:  form[`weapons-${a.id}`]?.checked ? 1 : 0,
    comms_go:    form[`comms-${a.id}`]?.checked   ? 1 : 0,
    remarks:     (form[`remarks-${a.id}`]?.value || "").trim()
  }));

  const res = await fetch("Data/api/report_submit.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ assets: rows })
  });

  let j = null;
  try { j = await res.json(); } catch (_) {}
  if (!res.ok || !j || j.ok !== true) {
    throw new Error(j?.error || `HTTP ${res.status}`);
  }
}

async function refresh() {
  const data = await fetchSummary();
  renderSummary(data);
}

refresh();
setInterval(refresh, 60000);