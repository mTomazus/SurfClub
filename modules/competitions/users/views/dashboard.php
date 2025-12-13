  <?php $user_comps = Modules::run("competitions-users/_get_user_comps") ?>
  <div class="container grid cards">
    <!-- ===== Search Competitions / Organisers ===== -->
    <section class="card pad span-12" aria-labelledby="search-title">
      <div class="section-head">
        <h3 id="search-title">Search Competitions or Organisers</h3>
      </div>
      <div class="controls">
        <label class="field">
          <input type="search" id="searchCompetition" placeholder="Type competition or organiser name…" oninput="searchCompetitions()" />
        </label>
        <button class="btn" onclick="searchCompetitions()">Search</button>
      </div>
      <div id="searchResults" class="list" style="margin-top:10px; display:none"></div>
    </section>

    <!-- ===== Context / Controls ===== -->
    <section class="card pad span-12" aria-labelledby="ctx-title">
      <div class="section-head">
      <h3 id="ctx-title">My Competitions</h3>
        <div class="chips">
          <span class="chip status-open"><span class="dot" style="background:var(--chip-open)"></span>Open</span>
          <span class="chip status-running"><span class="dot" style="background:var(--chip-running)"></span>Running</span>
          <span class="chip status-scheduled"><span class="dot" style="background:var(--chip-scheduled)"></span>Scheduled</span>
          <span class="chip status-finished"><span class="dot" style="background:var(--chip-finished)"></span>Finished</span>
        </div>
      </div>
      <div class="controls">
        <label class="field" title="Select competition">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0118 0v6"/><path d="M21 18a3 3 0 01-3 3H6a3 3 0 01-3-3"/></svg>
          <select id="competitionSelect">
            <?php foreach ($user_comps as $comp) { ?>
              <option value="<?= $comp['id'] ?>"><?= out($comp['name']) ?> <?= out($comp['year']) ?> — status: <?= out($comp['status']) ?></option>
            <?php } ?>
          </select>
        </label>

        <label class="field" title="Your division">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 007.4 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06A1.65 1.65 0 003 15.4a1.65 1.65 0 00-1.51-1H1a2 2 0 110-4h.09A1.65 1.65 0 003 7.4a1.65 1.65 0 00-.33-1.82l-.06-.06A2 2 0 115.44 2.7l.06.06A1.65 1.65 0 007.32 3a1.65 1.65 0 001-1.51V1a2 2 0 114 0v.09A1.65 1.65 0 0013 3.6a1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09A1.65 1.65 0 0019.4 15z"/></svg>
          <select id="divisionSelect">
            <?php foreach ($user_comps as $comp) { ?>
              <option value="<?= $comp['id'] ?>"><?= out($comp['gender_age']) ?></option>
            <?php } ?>
          </select>
        </label>

        <span class="chip jersey" id="jerseyChip" title="Assigned jersey" style="background: var(--jersey-blue);">Jersey: BLUE</span>
      </div>
    </section>

    <!-- ===== Next Heat ===== -->
    <section class="card pad span-7" aria-labelledby="next-heat-title">
      <div class="section-head">
        <h3 id="next-heat-title">Your Next Heat</h3>
        <span class="chip status-scheduled"><span class="dot" style="background:var(--chip-scheduled)"></span><span id="heatStatus">Scheduled</span></span>
      </div>
      <div class="next-heat">
        <div class="countdown">
          <span id="countdown">—:—:—</span>
          <span class="pill" id="countdownPill">Starts in</span>
        </div>
        <div class="meta">
          <span>Heat <strong>#12</strong> · Round 1</span>
          <span>Division: <strong>Men Longboard</strong></span>
          <span>Location: <strong>2nd Jetty, Melnrage</strong></span>
          <span>Call time: <strong id="callTime">11:20</strong></span>
          <span>Start: <strong id="startTimeLabel">11:30</strong></span>
          <span>Duration: <strong>20 min</strong></span>
        </div>
        <div class="chips" aria-label="Opponents">
          <span class="chip jersey" style="background:var(--jersey-blue);">You — BLUE</span>
          <span class="chip jersey" style="background:var(--jersey-red);">Opponent — RED</span>
          <span class="chip jersey" style="background:var(--jersey-white); border:1px solid #bbb;">Opponent — WHITE</span>
          <span class="chip jersey" style="background:var(--jersey-green);">Opponent — GREEN</span>
        </div>
        <div class="controls">
          <button class="btn" onclick="checkIn()">Check-in</button>
          <button class="btn" onclick="alert('Rules opened (wire to /docs/rulebook.pdf)')">View rules</button>
          <button class="btn" onclick="alert('Heats map (wire to map link)')">Heats map</button>
        </div>
      </div>
    </section>

    <!-- ===== Scores ===== -->
    <section class="card pad span-5" aria-labelledby="scores-title">
      <div class="section-head">
        <h3 id="scores-title">My Scores</h3>
        <span class="subtle">Best two waves count</span>
      </div>
      <div class="table-wrap">
        <table class="table" aria-describedby="scores-title">
          <thead>
            <tr>
              <th>Heat</th>
              <th>Wave</th>
              <th>J1</th>
              <th>J2</th>
              <th>J3</th>
              <th>J4</th>
              <th>J5</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#5</td>
              <td>1</td>
              <td>4.5</td><td>4.7</td><td>4.8</td><td>4.6</td><td>4.9</td>
              <td>23.5</td>
            </tr>
            <tr>
              <td>#5</td>
              <td>2</td>
              <td>6.2</td><td>6.0</td><td>6.1</td><td>6.3</td><td>6.2</td>
              <td>30.8</td>
            </tr>
            <tr>
              <td>#5</td>
              <td>3</td>
              <td>1.5</td><td>1.8</td><td>1.7</td><td>1.9</td><td>1.6</td>
              <td>8.5</td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="7" class="right">Best 2 waves</th>
              <th>54.3</th>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="controls" style="margin-top:10px">
        <button class="btn" onclick="alert('Open detailed heat results…')">View heat details</button>
        <button class="btn" onclick="alert('Open rankings…')">Event rankings</button>
      </div>
    </section>

    <!-- ===== Schedule ===== -->
    <section class="card pad span-7" aria-labelledby="schedule-title">
      <div class="section-head">
        <h3 id="schedule-title">Upcoming Heats</h3>
        <div class="controls">
          <label class="field"><input type="search" id="scheduleSearch" placeholder="Filter by heat/division…" oninput="filterSchedule()" /></label>
          <button class="btn" onclick="exportSchedule()">Export .ICS</button>
        </div>
      </div>
      <div class="table-wrap">
        <table class="table" aria-describedby="schedule-title" id="scheduleTable">
          <thead>
            <tr>
              <th>Time</th>
              <th>Heat</th>
              <th>Division</th>
              <th>Jersey</th>
              <th>Status</th>
              <th class="right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>11:30</td>
              <td>#12</td>
              <td>Men Longboard</td>
              <td><span class="chip jersey" style="background:var(--jersey-blue);">BLUE</span></td>
              <td><span class="badge">Scheduled</span></td>
              <td class="right"><button class="btn" onclick="addCalendar('Heat 12', '2025-08-30T08:30:00Z')">Add to calendar</button></td>
            </tr>
            <tr>
              <td>14:10</td>
              <td>#28</td>
              <td>Men Longboard — R2</td>
              <td><span class="chip jersey" style="background:var(--jersey-blue);">BLUE</span></td>
              <td><span class="badge">TBC</span></td>
              <td class="right"><button class="btn" disabled>Pending</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- ===== Documents ===== -->
    <section class="card pad span-5" aria-labelledby="docs-title">
      <div class="section-head">
        <h3 id="docs-title">Documents</h3>
        <span class="subtle">Rulebook, waivers & heat draw</span>
      </div>
      <div class="doc-grid">
        <article class="doc">
          <div>
            <strong>Rulebook (PDF)</strong>
            <div class="subtle">Updated Aug 2025</div>
          </div>
          <div class="controls">
            <button class="btn" onclick="alert('Download rulebook…')">Download</button>
          </div>
        </article>
        <article class="doc">
          <div>
            <strong>Heat Draw (HTML)</strong>
            <div class="subtle">Men Longboard · Rev 2</div>
          </div>
          <div class="controls">
            <button class="btn" onclick="alert('Open heat draw…')">Open</button>
          </div>
        </article>
        <article class="doc">
          <div>
            <strong>Liability Waiver</strong>
            <div class="subtle">Signed · 2025‑08‑25</div>
          </div>
          <div class="controls">
            <button class="btn" onclick="alert('View waiver…')">View</button>
          </div>
        </article>
      </div>
    </section>

    <!-- ===== Registrations ===== -->
    <section class="card pad span-12" aria-labelledby="regs-title">
      <div class="section-head">
        <h3 id="regs-title">My Registrations</h3>
        <span class="subtle">Manage entries & payments</span>
      </div>
      <div  id="registrations" mx-get="competitions-users" mx-select="#registrations" mx-target="#registrations" mx-trigger="activate" class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>Competition</th>
              <th>Division</th>
              <th>Status</th>
              <th>Payment</th>
              <th class="right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($user_comps as $comp) { 
              if (!empty($comp['name'])) { ?>
              <tr>
                <td><?= out($comp['name']) ?><?= out($comp['year']) ?></td>
                <td><?= out($comp['gender_age']) ?></td>
                <td><span class="badge"><?= out($comp['participation_status']) ?></span></td>
                <td><span class="badge" style="background: color-mix(in oklab, var(--ok), transparent 80%); border-color: color-mix(in oklab, var(--ok), transparent 50%);">Free</span></td>
                <td class="right" style="gap: 1rem;display: flex;justify-content: end;">
                  <?php
                    if ($comp['participation_status'] === 'pending') {
                      if ($comp['entry_type'] === 'free entry') {
                        // free entry, pending approval
                        echo '<button class="btn" onclick="alert(\'Entry pending approval…\')" >Pending</button>';
                      } else {
                        // paid entry, pending payment
                        echo '<button class="btn" onclick="alert(\'Pay now — wire to EveryPay\')">Pay now</button>';
                      } 
                    } else if ($comp['participation_status'] === 'paid') {
                        // paid entry, not confirmed
                        echo '<button class="btn" onclick="alert(\'Open receipt…\')">Receipt</button>';
                    } else if ($comp['entry_type'] === 'entry fee') {
                      // other status (e.g. withdrawn)
                      echo '<button class="btn" onclick="alert(\'Open receipt…\')">Receipt</button>';
                    }
                  ?>
                  <button class="btn danger" mx-get="competitions-users/withdraw/<?= out($comp['record_id']) ?>" mx-build-modal="modalWithdraw">Withdraw</button>
                </td>
              </tr>
            <?php } } ?>

            <tr>
              <td>Baltic Juniors 2025</td>
              <td>Open</td>
              <td><span class="badge">Pending</span></td>
              <td><span class="badge">Unpaid</span></td>
              <td class="right">
                <button class="btn" onclick="alert('Pay now — wire to EveryPay')">Pay now</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

  </div>

  <script>
    // Quick selector
    const $ = sel => document.querySelector(sel);

    let __t;
    function searchCompetitions() {
      clearTimeout(__t);
      __t = setTimeout(async () => {
        const q = $('#searchCompetition').value.trim();
        const box = document.getElementById('searchResults');

        if (!q || q.length < 2) { box.style.display = 'none'; box.innerHTML = ''; return; }

        try {
          const res = await fetch(`/banglente_v25/competitions-users/search?q=${encodeURIComponent(q)}`, {
            headers: { 'Accept': 'application/json' }
          });

          const text = await res.text();
          if (!res.ok) { console.error('Search failed', res.status, text); throw new Error('Network error'); }

          let data;
          try { data = JSON.parse(text); } catch(e){ console.error('Bad JSON', text); throw e; }

          if (!Array.isArray(data) || data.length === 0) {
            box.style.display = 'block';
            box.innerHTML = '<div class="subtle">No results.</div>';
            return;
          }

          box.style.display = 'grid';
          box.style.gap = '8px';
          box.innerHTML = data.map(x => `
            <div class="card pad" style="padding:12px">
              <div style="display:flex; align-items:center; justify-content: space-between; gap:10px;">
                <div style="text-align: left;margin-left: 2rem;">
                  <strong>${escapeHtml(String((x.title).toUpperCase()))} ${escapeHtml(x.year || '')}</strong>
                  <div class="subtle">• ${x.type === 'competition' ? 'Competition' : 'Organiser'} •</div>
                  <div class="subtle">${escapeHtml(x.organiser || x.country)} </div>
                </div>
                <div>
                  ${x.entry_type ? `<span class="badge chip status-${escapeHtml(String(x.entry_type))}">${escapeHtml(String(x.entry_type).toUpperCase())}</span>` : ''}
                  <span class="badge chip status-${escapeHtml(String(x.status || ''))}">${escapeHtml(String(x.status || '').toUpperCase())}</span>
                  ${buttonsFor(x)}
                </div>
              </div>
            </div>
          `).join('');

        } catch (e) {
          box.style.display = 'block';
          box.innerHTML = `<div class="subtle">Search failed. Please try again.</div>`;
          console.error(e);
        }
      }, 250);
    }

    function escapeHtml(s='') {
      return String(s).replace(/[&<>"']/g, m => ({
        '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
      }[m]));
    }

    function buttonsFor(x) {
      const id = encodeURIComponent(String(x.id || ''));
      if (x.type !== 'competition') {
        return `<button class="btn view" style="margin-left:8px" mx-get="competitions-users/organizer/${id}" mx-build-modal="organzer-info-modal">View</button>`;
      }

      const status = String(x.status || '').toLowerCase(); // normalize
      switch (status) {
        case 'running':
          return `
            <button class="btn running" style="margin-left:8px" onclick="goLive('${id}')">Live</button>
          `;
        case 'finished':
          return `
            <button class="btn finished" style="margin-left:8px" onclick="openCompetition('${id}')">Results</button>
          `;
        default: // open/scheduled/other
          return `<button class="btn" style="margin-left:8px" mx-get="competitions-users/competition/${id}" mx-build-modal="competitionModal">Action</button>`;
      }
    }

    // optional: endpoints these buttons go to
    function goLive(id)       { if (!id) return; location.href = `competitions-heats/show_heats_draw/${id}`; }
    function viewResults(id)  { if (!id) return; location.href = `competitions-heats/show_heats_draw/${id}`; }
    function openCompetition(id){ if (!id) return; location.href = `competitions-heats/show_heats_draw/${id}`; }
    function openOrganiser(id){ if (!id) return; location.href = `organisers/view/${id}`; }

    // ===== Countdown to next heat =====
    const startAt = new Date(Date.now() + 60 * 60 * 1000); // +1h
    const callAt  = new Date(startAt.getTime() - 10 * 60 * 1000); // -10 min
    document.getElementById('startTimeLabel').textContent = startAt.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
    document.getElementById('callTime').textContent = callAt.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});

    let checkedIn = false;

    function tickCountdown() {
      const now = new Date();
      const ms = startAt - now;
      const cd = $('#countdown');
      const pill = $('#countdownPill');
      const status = $('#heatStatus');
      if (ms <= 0) {
        cd.textContent = 'LIVE NOW';
        pill.textContent = checkedIn ? 'Checked-in' : 'Starts';
        status.textContent = 'Running';
        status.parentElement.className = 'chip status-running';
        return; // stop updating after start
      }
      const s = Math.floor(ms / 1000);
      const h = String(Math.floor(s / 3600)).padStart(2, '0');
      const m = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
      const sec = String(s % 60).padStart(2, '0');
      cd.textContent = `${h}:${m}:${sec}`;
      pill.textContent = now >= callAt ? 'Check-in open' : 'Starts in';
      if (now >= callAt) {
        status.textContent = 'Check-in';
        status.parentElement.className = 'chip status-open';
      }
    }

    const timer = setInterval(tickCountdown, 1000); tickCountdown();

    // ===== Actions =====
    function checkIn() {
      checkedIn = true;
      alert('Checked-in! (POST /heats/checkin)');
      $('#countdownPill').textContent = 'Checked-in';
    }

    function copyJoinCode(code) {
      navigator.clipboard.writeText(code).then(() => {
        alert('Your join code copied: ' + code);
      });
    }

    function joinCompetition(ev) {
      ev.preventDefault();
      const code = $('#joinCode').value.trim();
      if (!code) return alert('Enter a code');
      alert('Joining with code: ' + code + ' (POST /competitions/join)');
    }

    function addCalendar(title, iso) {
      const dtStart = new Date(iso);
      const dtEnd = new Date(dtStart.getTime() + 20*60*1000);
      const fmt = d => d.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
      const ics = [
        'BEGIN:VCALENDAR','VERSION:2.0','PRODID:-//Surf Club LT//Participant//EN','BEGIN:VEVENT',
        'UID:'+crypto.randomUUID(),
        'DTSTAMP:'+fmt(new Date()),
        'DTSTART:'+fmt(dtStart),
        'DTEND:'+fmt(dtEnd),
        'SUMMARY:'+title,
        'LOCATION:2nd Jetty, Melnrage',
        'END:VEVENT','END:VCALENDAR' 
      ].join('\r\n');
      const blob = new Blob([ics], {type:'text/calendar'});
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url; a.download = `${title.replace(/\s+/g,'_')}.ics`; a.click();
      URL.revokeObjectURL(url);
    }

    function exportSchedule() { addCalendar('Heat 12', new Date(Date.now()+3600*1000).toISOString()); }

    // ===== Schedule filter =====
    function filterSchedule() {
      const q = $('#scheduleSearch').value.toLowerCase();
      const rows = document.querySelectorAll('#scheduleTable tbody tr');
      rows.forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    }
  </script>
