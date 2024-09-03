<h1><?= out($headline) ?></h1>
<?php
flashdata();
echo '<p>'.anchor('coupons/create', 'Create New Coupon Record', array("class" => "button"));
if(strtolower(ENV) === 'dev') {
    echo anchor('api/explorer/coupons', 'API Explorer', array("class" => "button alt"));
}
echo '</p>';
echo Pagination::display($pagination_data);
if (count($rows)>0) { ?>
    <table id="results-tbl">
        <thead>
            <?php
            if(from_trongate_mx() === false) { ?>
            <tr>
                <th colspan="6">
                    <div>
                        <div><?php
                        echo form_open('coupons/manage/1/', array("method" => "get"));
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
            <?php
            }
            ?>
            <tr>
                <th>Coupon Type</th>
                <th>Price</th>
                <th>Phone</th>
                <th>Name</th>
                <th>Date Formed</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $attr['class'] = 'button alt';
            foreach($rows as $row) { ?>
            <tr>
                <td><?= out($row->coupon_type) ?></td>
                <td><?= out($row->price) ?></td>
                <td><?= out($row->phone) ?></td>
                <td><?= out($row->name) ?></td>
                <td><?= date('l jS F Y',  strtotime($row->date_formed)) ?></td>
                <td><?php
                if(from_trongate_mx() === false) {
                    echo anchor('coupons/show/'.$row->id, 'View', $attr);
                } else {
                    ?>

                    <button class="danger" mx-get="<?= BASE_URL ?>coupons/submit_delete_coupon/<?= $row->id ?>"
                                            mx-on-success="#result"><i class="fa fa-trash"></i></button>
                    
                    <button class="" mx-get="<?= BASE_URL ?>coupons/edit_coupon/<?= $row->id ?>"
                                    mx-target="#page-upper"
                                    mx-build-modal='{
                                        "id": "edit-coupon-modal",
                                        "modalHeading": "Edit Coupon",
                                        "showCloseButton": "true"
                                    }'
                                    mx-select=".edit-container"><i class="fa fa-pencil"></i></button>

                    <?php
                }
                 ?></td>      
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
?>