<section class="footer-links">
    <ul>
        <li>
            <a class="copyright secondary">Copyright © 2024 banglente.com</a>
        </li>
        <li class="dot-devider">
            <span aria-hidden="true" role="presentation">.</span>
        </li>
        <li>
            <?php 
                $name = 'kalba';
                $options = ['lt' => 'Lietuvių', 'en' => 'English', 'ru' => 'Русский', 'ch' => '汉语'];
                echo form_dropdown($name, $options);
            ?>
        </li>
        <li class="dot-devider"><span aria-hidden="true" role="presentation">.</span></li>
        <li><a href="salygos" class="secondary">Naudojimo sąlygos</a></li>
        <li class="dot-devider"><span aria-hidden="true" role="presentation">.</span></li>
        <li><a href="privatumas" class="secondary">Privatumo politika</a></li>
        <li class="dot-devider"><span aria-hidden="true" role="presentation">.</span></li>
        <li><a href="slapukai" class="secondary">Slapukų politika</a></li>
    </ul>
</section>

<style>
    .footer-links {
        color:white;
        right: 0;
        bottom: 0;
        width: 100%;
        color: white;
        text-align: center;
        z-index: 2;
        a {
            color: white;
            text-decoration: initial;
        }
    }
    .dot-devider {
        width:15%!important;
    }
    .footer-links ul {
        list-style-type: none;
        margin: 0;
        padding: 0.5rem;
        display: flex;
        justify-content: space-evenly;
    }
</style>