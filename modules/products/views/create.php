<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Product Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);

        echo form_label('Name');
        echo form_input('name', $name, ["placeholder" => "Enter product name"]);

        echo form_label('Description');
        echo form_textarea('description', $description, ["placeholder" => "Enter full product description"]);
        
        echo form_label('Short description');
        echo form_textarea('short_desc', $short_desc, ["placeholder" => "Enter short product description"]);

        echo form_label('Price');
        echo form_input('price', $price, ["placeholder" => "Enter base price"]);
        
        echo form_label('Discount price');
        echo form_input('discount_price', $discount_price, ["placeholder" => "Enter discount price"]);

        echo form_label('In Stock');
        echo form_dropdown('in_stock', ['1' => 'Yes', '0' => 'No'], $in_stock);

        echo form_label('Status');
        echo form_dropdown('status', ['active' => 'Active', 'inactive' => 'Inactive'], $status);

        echo form_label('Categories');
        $name = 'categories[]';
        $options = ['3' => 'New', '1' => 'Surf', '2' => 'Beach', '4' => 'Best'];
        $selected_key = 'new';
        $attributes = ['required' => 'required','multiple' => 'true', 'class'  => 'multiple', 'aria-required' => 'true'];
        echo form_dropdown($name, $options, $selected_key, $attributes);

        echo '<hr><strong>Variants</strong><br>';
        echo form_label('Variant 1 (e.g., Size: M)');
        echo form_input('variants[]', '', ["placeholder" => "e.g., Size: M"]);

        echo form_label('Variant 2');
        echo form_input('variants[]', '', ["placeholder" => "e.g., Size: L"]);

        echo form_label('Variant 3');
        echo form_input('variants[]', '', ["placeholder" => "e.g., Size: XL"]);

        echo form_hidden('image', $image);

        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', ['class' => 'button alt']);
        echo form_close();
        ?>
    </div>
</div>

<style>
    .multiple {
        height:100px;
    }
</style>