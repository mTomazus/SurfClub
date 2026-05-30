<div id="title" style="display:none"><h1><?= out($headline) ?></h1></div>

<div id="stat-panel">
    <a class="stat-card">
        <span class="stat-label">Products</span>
        <span class="stat-count"><?= $pagination_data['total_rows'] ?></span>
    </a>
</div>

<div id="products-container">
<?php
flashdata();
echo '<p>'.anchor('products/create', 'Create New Product Record', array("class" => "button"));
echo ' '.anchor('products/orders', 'View Orders', array("class" => "button alt"));
if(strtolower(ENV) === 'dev') {
    echo anchor('api/explorer/products', 'API Explorer', array("class" => "button alt"));
}
echo '</p>';
echo Pagination::display($pagination_data);
if (count($rows)>0) { ?>
    <table id="results-tbl">
        <thead>
            <tr>
                <th colspan="7">
                    <div>
                        <div><?php
                        echo form_open('products/manage/1/', array("method" => "get"));
                        echo form_search('searchphrase', '', array("placeholder" => "Search records..."));
                        echo form_submit('submit', 'Search', array("class" => "alt"));
                        echo form_close();
                        ?></div>
                        <div>Records Per Page: <?php
                        $dropdown_attr['onchange'] = 'setPerPage()';
                        echo form_dropdown('per_page', $per_page_options, $selected_per_page, $dropdown_attr); 
                        ?></div>
                    </div>                    
                </th>
            </tr>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Categories</th>
                <th>Variants</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $attr['class'] = 'button alt';
            foreach($rows as $row) { 
                // Fetch categories and variants here if available
                $categories = isset($row->categories) ? implode(', ', $row->categories) : '—';
                $variants = isset($row->variants) ? implode(', ', $row->variants) : '—';
            ?>
            <tr>
                <td><?= out($row->name) ?></td>
                <td><?= out($row->price) ?></td>
                <td><?= out($row->in_stock) ?></td>
                <td><?= out($row->status) ?></td>
                <td><?= out($categories) ?></td>
                <td><?= out($variants) ?></td>
                <td><?= anchor('products/show/'.$row->id, 'View', $attr) ?></td>        
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php 
    if(count($rows)>9) {
        unset($pagination_data['include_showing_statement']);
        echo Pagination::display($pagination_data);
    }
}
?></div><?php
?>