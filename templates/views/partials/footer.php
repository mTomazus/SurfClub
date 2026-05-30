<div>
    <?php 
        if (isset($token)) {
            echo anchor('members/logout', '<i class="fa fa-sign-out"></i>');
        } else {
            echo anchor('members', '<i class="fa fa-user"></i>');
        }
    ?>
    <a href="tel:+37068602356" aria-label="call us on phone"><i class="fa fa-phone"></i></a>
    <button onclick="window.molasOpenChat && window.molasOpenChat()" aria-label="AI pokalbių asistentas" style="background:transparent;border:none;cursor:pointer;margin:auto;padding:0;display:flex;align-items:center;justify-content:center;">
        <svg width="35" height="35" viewBox="0 0 24 24" fill="none" style="filter:drop-shadow(2px 2px 2px black)">
            <path d="M12 2C6.48 2 2 5.58 2 10c0 2.24 1.12 4.26 2.94 5.66L4 22l4.73-2.73C9.77 19.73 10.86 20 12 20c5.52 0 10-3.58 10-8s-4.48-8-10-8z" fill="white"/>
            <circle cx="8" cy="10" r="1.2" fill="#2f78a8"/><circle cx="12" cy="10" r="1.2" fill="#2f78a8"/><circle cx="16" cy="10" r="1.2" fill="#2f78a8"/>
        </svg>
    </button>
    <a mx-get="enquiries/index" mx-build-modal='{
                "id": "contact-forma",
                "aria-label": "susisiekiteforma",
                "modalHeading": "Rašykite mums",
                "max-width": "460px"
            }' mx-target="this" mx-select="#contact-form"><i class="fa fa-envelope"></i></a>
    <a href="products/cart" aria-label="shopping cart"><i class="fa fa-shopping-basket"></i></a>
</div>