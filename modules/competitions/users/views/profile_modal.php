    <?php $user_info = Modules::run("competitions-users/_get_user_info") ?>
    <!-- ===== Profile ===== -->
    <section class="card pad span-12" aria-labelledby="profile-title">
        <div class="section-head">
            <h3 id="profile-title">Profile</h3>
            <span class="subtle">Keep your contact info up to date</span>
        </div>
        <div id="response"></div>

        <?php
        $form_attr = [
            'mx-post' => 'competitions-users/submit_update_profile',
            'mx-target' => '#response',
            'mx-close-on-success' => 'true',
            'class' => 'grid',
            'style' => 'grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:12px"'
            ];
        echo form_open('#', $form_attr);
        ?>
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:12px">
            <label class="field"><span class="subtle">Name</span><input name="name" value="<?= out($user_info[0]['name'])?>" placeholder="Vardas Pavardenis" /></label>
            <label class="field"><span class="subtle">Email</span><input name="email" value="<?= out($user_info[0]['email']) ?>" /></label>
            <label class="field"><span class="subtle">Phone</span><input name="phone" value="<?= out($user_info[0]['phone']) ?>" /></label>
            <label class="field"><span class="subtle">DOB</span><input name="dob"value="<?= out($user_info[0]['dob']) ?>" placeholder="1999-09-09" /></label>
            <label class="field"><span class="subtle">Gender</span><input name="gender" value="<?= out($user_info[0]['gender']) ?>" placeholder="Male" /></label>
            <label class="field"><span class="subtle">Club</span><input name="club_name" value="<?= out($user_info[0]['club_name']) ?>" placeholder="Molas Surf Club" /></label>
        </div>
        <div class="controls" style="margin-top:12px">
            <button class="btn" type="submit" name="submit" value="Save">Save</button>
            <button class="btn" onclick="alert('Change password…')">Change password</button>
        </div>
        <?= form_close() ?>
    </section>