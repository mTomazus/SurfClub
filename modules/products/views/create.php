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
        $attributes = ['required' => 'required', 'multiple' => 'true', 'class' => 'multiple', 'aria-required' => 'true'];
        echo form_dropdown('categories[]', $category_options, $selected_categories, $attributes);

        echo '<hr><strong>Variants</strong><br>';
        echo '<div id="variants-container">';
        echo form_label('Variant 1 (e.g., Size: M)');
        echo form_input('variants[]', '', ["placeholder" => "e.g., Size: M"]);
        echo form_label('Variant 2');
        echo form_input('variants[]', '', ["placeholder" => "e.g., Size: L"]);
        echo form_label('Variant 3');
        echo form_input('variants[]', '', ["placeholder" => "e.g., Size: XL"]);
        echo '</div>';
        echo '<button type="button" onclick="addVariant()" style="margin:0.5rem 0 1rem">+ Add variant</button>';
        echo '<script>
function addVariant() {
    var c = document.getElementById("variants-container");
    var n = Math.floor(c.children.length / 2) + 1;
    var lbl = document.createElement("label");
    lbl.textContent = "Variant " + n;
    var inp = document.createElement("input");
    inp.type = "text";
    inp.name = "variants[]";
    inp.placeholder = "e.g., Size: M";
    inp.className = "trongate-input";
    c.appendChild(lbl);
    c.appendChild(inp);
}
</script>';

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