<?php $cart_count = !empty($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
<div>
    <?php
        if (isset($token)) {
            echo anchor('members/logout', '<i class="fa fa-sign-out"></i>');
        } else {
            echo anchor('members', '<i class="fa fa-user"></i>');
        }
    ?>
    <a href="tel:+37068602356" aria-label="call us on phone"><i class="fa fa-phone"></i></a>
    <a href="t.me/molas_surf_bot" aria-label="message telega"><i class="fa fa-telegram"></i></a>
    <a mx-get="enquiries/index" mx-build-modal='{
                "id": "contact-forma",
                "aria-label": "susisiekite-forma",
                "modalHeading": "Rašykite mums",
                "max-width": "460px"
            }' mx-target="this" mx-select="#contact-form"><i class="fa fa-envelope"></i></a>
    <a onclick="openCartDrawer()" href="javascript:void(0);" aria-label="shopping cart"><i class="fa fa-shopping-basket"></i><?php if ($cart_count): ?><span class="cart-count"><?= $cart_count ?></span><?php endif; ?>
</a>
</div>
