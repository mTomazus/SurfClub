<h1><?= $headline ?></h1>
<?php
flashdata();
echo '<p>'.anchor('news/create', 'Create New News Record', array("class" => "button")).'</p>';

if ($allow_mass_delete == true) {
    include('mass_delete_modal.php');
}

echo Pagination::display($pagination_data);
if (count($rows)>0) { ?>
    <table id="results-tbl">
        <thead>
            <tr>
                <th colspan="3">
                    <div>
                        <div><?php
                        echo form_open('news/manage/1/', array("method" => "get"));
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
                <th>Date And Time</th>
                <th>Article Headline</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $attr['class'] = 'button alt';
            foreach($rows as $row) { 

                $published_icon = ($row->published == 'yes' ? '<i class="fa fa-circle"></i>' : '<i class="fa fa-circle unpublished"></i>');

                $picture_icon = ($row->picture !== '' ? '<i class="fa fa-photo"></i>' : '');
            ?>
            <tr>
                <td><?= date('jS M Y H:i',  strtotime($row->date_and_time)) ?></td>
                <td><?= $published_icon.' '.$picture_icon.$row->article_headline ?></td>
                <td><?= anchor('news/show/'.$row->id, 'View', $attr) ?></td>        
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php 
    if(count($rows)>9) {
        echo '<p>';
        unset($pagination_data['include_showing_statement']);
        echo Pagination::display($pagination_data);
        echo '</p>';
    }
}
?>
<style>
#results-tbl .fa-circle,
#results-tbl .fa-circle-o,
#results-tbl .fa-photo {
    margin-right: 4px;
}

#results-tbl .fa-circle {
    color: green;
}

#results-tbl .unpublished {
    color: red;
}
</style>