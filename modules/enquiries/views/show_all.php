<div id="title" style="display:none"><h1>Enquiries</h1></div>

<div id="stat-panel">
    <a class="stat-card">
        <span class="stat-label">Module</span>
        <span class="stat-count" style="font-size:1.1rem">Enquiries</span>
    </a>
</div>

<div id="enquiries-show-all" class="container">
    
    <div id="table" class="enquiries-list">
        <h2 class="mt-1 mb-1">Here you can view all enquiries.</h2>
        <?php if (isset($enquiries) && !empty($enquiries)): ?>
            <div>
                <?php foreach ($enquiries as $enquiry): ?>
                    <p><?php out($enquiry['name']); ?></p>
                    <?= out($enquiry['email']); ?>
                    <?= out($enquiry['phone']); ?>
                    <h3><?= out($enquiry['message']); ?></h3>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="xl mt-2 mb-2 blink">No enquiries found.</p>
        <?php endif; ?>
    </div>

</div>