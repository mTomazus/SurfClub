<div id="title" style="display:none"><h1>Dashboard</h1></div>

<div id="dashboard">

    <h2>Overview</h2>

    <div id="stat-cards" class="table-responsive">
        <a class="stat-card" mx-get="camps" mx-target="#form-container" mx-select="#nav-table" mx-select-oob="#title:#top-title">
            <span class="stat-label">Camps</span>
            <span class="stat-count"><?= $stats['camps'] ?></span>
        </a>
        <a class="stat-card" mx-get="coupons" mx-target="#form-container" mx-select="#coupons" mx-select-oob="#title:#top-title">
            <span class="stat-label">Coupons</span>
            <span class="stat-count"><?= $stats['coupons'] ?></span>
        </a>
        <a class="stat-card" mx-get="enquiries/show_all" mx-target="#form-container" mx-select="#table" mx-select-oob="#title:#top-title">
            <span class="stat-label">Enquiries</span>
            <span class="stat-count"><?= $stats['enquiries'] ?></span>
        </a>
        <a class="stat-card" mx-get="events" mx-target="#form-container" mx-select="#event-container" mx-select-oob="#title:#top-title">
            <span class="stat-label">Events</span>
            <span class="stat-count"><?= $stats['events'] ?></span>
        </a>
        <a class="stat-card" mx-get="products/manage" mx-target="#form-container" mx-select="main" mx-select-oob="#title:#top-title">
            <span class="stat-label">Products</span>
            <span class="stat-count"><?= $stats['products'] ?></span>
        </a>
        <a class="stat-card" mx-get="news/manage" mx-target="#form-container" mx-select="main" mx-select-oob="#title:#top-title">
            <span class="stat-label">News</span>
            <span class="stat-count"><?= $stats['news'] ?></span>
        </a>

    </div>

    <div id="form-container">
        <h2>Recent Camp Registrations</h2>

        <?php if (!empty($recent_camps)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Session</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_camps as $reg): ?>
                    <tr>
                        <td><?= out($reg->name) ?><br><small><?= out($reg->email) ?></small></td>
                        <td><?= out($reg->pamaina) ?></td>
                        <td><?= ucfirst(out($reg->status)) ?></td>
                        <td><?= date('Y-m-d', strtotime($reg->date_created)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p>No registrations yet.</p>
        <?php endif; ?>
    </div>

</div>

<style>
    #dashboard {
        padding: 1rem 2rem;
        color: floralwhite;
    }
    #stat-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 1.2rem 1rem;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 8px;
        color: floralwhite;
        text-decoration: none;
        font-family: Silom, monospace;
        cursor: pointer;
        transition: background 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        background: rgba(255, 255, 255, 0.28);
        box-shadow: 0 0 12px rgba(135, 206, 235, 0.5);
        color: floralwhite;
    }
    .stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        opacity: 0.85;
    }
    .stat-count {
        font-size: 2.2rem;
        font-weight: bold;
        line-height: 1;
    }
    #dashboard .table-responsive {
        background: transparent;
    }
</style>
