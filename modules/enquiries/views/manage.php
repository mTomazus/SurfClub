<h1><?= $headline ?></h1>
<?php
flashdata();
echo '<p>'.anchor('enquiries/create', 'Create New Enquiry Record', array("class" => "button")).'</p>'; 
echo Pagination::display($pagination_data);
if (count($rows)>0) { ?>
    <table id="results-tbl">
        <thead>
            <tr>
                <th colspan="4">
                    <div>
                        <div><?php
                        echo form_open('enquiries/manage/1/', array("method" => "get"));
                        echo form_input('searchphrase', '', array("placeholder" => "Search records..."));
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
                <th>Email Address</th>
                <th>Date Sent</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $attr['class'] = 'button alt';
            foreach($rows as $row) { 

                if ($row->opened == 'no') {
                    $row_icon = '<i class="fa fa-envelope"></i> ';
                } else {
                    $row_icon  = '<i class="fa fa-envelope-open"></i> ';
                }


            ?>
            <tr>
                <td><?= $row_icon.' '.$row->name ?></td>
                <td><?= $row->email_address ?></td>
                <td><?= date('l jS F Y \a\t H:i',  $row->date_created) ?></td>
                <td><?= anchor('enquiries/show/'.$row->id, 'View', $attr) ?></td>        
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

<style>
    #results-tbl .fa-envelope {
        color: #d2b207;
    }

    #results-tbl .fa-envelope-open {
        color: #b9b9b9;
    }
</style>