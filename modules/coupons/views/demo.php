<h1>Trongate MX Tutorial</h1>

<div id="page-upper"></div>

<form class="container-xxs" mx-post="<?= BASE_URL ?>coupons/submit_new_coupon" 
                            mx-target="#page-upper"
                            mx-on-success="#result">
    <label for="">Coupon Type</label>
    <input type="text" name="coupon_type" placeholder="Enter Coupon type here..." autocomplete="off">
    <label for="">Coupon Price</label>
    <input type="text" name="price" placeholder="Enter Coupon price here..." autocomplete="off">
    <label for="">Phone Number</label>
    <input type="text" name="phone" placeholder="Enter phone number here..." autocomplete="off">
    <label for="">Your name</label>
    <input type="text" name="name" placeholder="Enter Name here..." autocomplete="off">
    <label for="">Date Formed</label>
    <input type="text" name="date_formed" placeholder="Enter date coupon was formed here..." autocomplete="off">
    <button type="submit">Add Coupon</button>
</form>

<div id="result" mx-get="<?= BASE_URL ?>coupons/manage" 
                mx-select="table"
                mx-trigger="load"
                class="mt-3"
                style="background-color:white;"><span class="blink">Loading...</span></div>