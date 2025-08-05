<h1><?= out($headline) ?></h1>
<?php
flashdata();
echo '<p>'.anchor('lessons-schedules/create', 'Create New Lesson Schedule Record', array("class" => "button"));
if(strtolower(ENV) === 'dev') {
    echo anchor('api/explorer/lessons-schedules', 'API Explorer', array("class" => "button alt"));
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
                        echo form_open('lessons-schedules/manage/' . $row->id . '/', array("method" => "get"));
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
                <th>Pamoka</th>
                <th>Data</th>
                <th>Pradžia</th>
                <th>Available</th>
                <th>Reserved</th>
                <th style="width: 20px;">Action</th>            
            </tr>
        </thead>
        <tbody>
            <?php 
            $attr['class'] = 'button alt';
            foreach($rows as $row) { ?>
            <tr>
                <td><?= out($row->name) ?></td>
                <td><?= date('l jS F Y',  strtotime($row->date)) ?></td>
                <td><?= out($row->start_time) ?></td>
                <td><?= out($row->available_places) ?></td>
                <td><?= out($row->reserved_places) ?></td>
                <td><?= anchor('lessons-schedules/show/'.$row->id, 'View', $attr) ?></td>        
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