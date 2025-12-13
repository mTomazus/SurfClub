<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --bg1:#0e1a3a;
    --bg2:#291d4d;
    --panel:#ffffff;
    --muted:#8b91a5;
    --text:#1f2333;
    --accent:#a7f19b;
    --primary:rgb(29, 26, 73)
    --accent-strong:#8de07e;
    --ring:rgba(76, 130, 251, .35);
    --shadow: 0 10px 30px rgba(0,0,0,.25);
    --radius:18px;
  }
  *{box-sizing:border-box}
  body{
    margin:0;
    background: radial-gradient(1200px 700px at 20% 10%, var(--bg2), var(--bg1));
    color: var(--text);
  }
  main {
    min-height: 100vh;
  }
  h2 {
    text-transform: uppercase;
    font-family: inherit;
    text-align: center;
    margin-bottom: 1.5rem;
    & strong {
        background: darkorchid;
        color: white;
        padding-inline: 0.5rem;
    }
  }
  hr {
    border: 0;
    height: 1px;
    background: var(--border);
    margin: 1rem 0 2em;
  }
  .auth-wrapper{
    min-height:100%;
    display:grid;
    place-items:center;
    padding:32px 16px;
  }
  .auth-card{
    width:min(980px, 100%);
    display:grid;
    grid-template-columns: 420px 1fr;
    overflow:hidden;
    background: transparent;
  }

  /* Left promo panel */
  .promo{
    position:relative;
    box-shadow: var(--shadow);
    border-radius: calc(var(--radius) + 2px);
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    padding:28px;
    background:url("<?= BASE_URL ?>competitions-users_module/images/tmbite.jpeg") center/cover no-repeat;
    color:#fff;
  }
  .promo-card{
    position:absolute; inset:0;
    background: rgba(255,255,255,.06);
    border-radius: var(--radius);
    margin:28px;
    backdrop-filter: blur(2px);
  }
  .promo-inner{
    position:relative;
    z-index:2;
    height:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:18px;
    text-align:center;
  }
  .avatar{
    display:grid; place-items:center;
    width:120px; height:120px; border-radius:50%;
    overflow:hidden; box-shadow: 0 8px 28px rgba(0,0,0,.35);
  }
  .avatar img{width:70px; height:70px; object-fit:cover}
  .promo h2{
    margin:0; font-weight:400; letter-spacing:.2px; color:#e7ebff;
    font-size:20px;
  }
  .promo h2 b{display:block; font-size:28px; color:#fff}
  .promo .dot{
    width:40px; height:40px; border-radius:50%;
    display:grid; place-items:center; margin-top:8px;
    background:#0f1633; color:#a1ff8b; box-shadow: inset 0 0 0 2px rgba(255,255,255,.08);
  }

  /* Right form panel */
  .panel{
    background: var(--panel);
    border-radius: calc(var(--radius) + 2px);
    box-shadow: var(--shadow);
    padding: 40px 46px 32px;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    z-index: 1;
  }
  .fld{
    box-shadow: var(--shadow);
    margin-bottom: 1rem;
    }
  .lbl{
    display:block; font-size:11px; letter-spacing:.6px; color:#8d93a6;
    text-transform:uppercase; margin: 0;padding: 1em 1em 0;
  }
  .txt{
    width:100%; padding: 0 0.6em 0.6em!important; border:none!important;
    outline:none; font-size:15px; color:#1f2333; background:#fff;
    transition:border-color .2s, box-shadow .2s;
  }
  .txt:focus {
    border-color:transparent; box-shadow:none;;
  }

  .row2{display:flex; gap:18px}
  .row2 > .fld{flex:1}

  .agree, .remember{
    display:flex; align-items:center; gap:10px; font-size:12px; color:#8a90a3;
    margin:0;
  }
  .agree input, .remember input{width:16px; height:16px}

  .btn{
    display: inline-block;
    padding: 14px 20px;
    border: none;
    height: 57px;
    width: 100%;
    box-shadow: var(--shadow);
    font-weight: 700;
    letter-spacing: .6px;
    cursor: pointer;
    transition: transform .04s ease, filter .2s ease;
    text-align: center;
  }
  .main {
    background: rgb(29, 26, 73);
  }
  .btn:active{transform: translateY(1px)}
  .btn:hover{filter:brightness(0.98)}
  .mute{color:#7e8499}
  .link{color:#0f63ff; text-decoration:none}

  .foot{
    margin-top:12px; font-size:13px; color:#7c8298;
  }
  .err{color:#d33; font-size:13px; margin:-6px 0 10px}

  /* Success tick style (decorative) */
  .ok{
    position:absolute; right:0; top:34px; transform:translateY(-50%);
    font-size:16px; color:#7bd67a;
  }
  .rel{position:relative}

  /* Responsive */
  @media (max-width: 920px){
    .auth-card{grid-template-columns: 1fr}
    .promo{display:none}
    .panel{border-radius: var(--radius)}
  }
</style>

<div class="auth-wrapper">
  <div class="auth-card">

    <!-- LEFT PROMO -->
    <aside class="promo">
      <div class="promo-card" aria-hidden="true"></div>
      <div class="promo-inner">
        <div class="avatar">
          <img src="competitions-users_module/images/logo-comp-white.png" alt="Logo">
        </div>
        <h2>So you're a <b>Superhero</b></h2>
        <div class="dot">➜</div>
      </div>
    </aside>

    <!-- RIGHT PANEL -->
    <section class="panel">
      <!-- ===== SIGNUP ===== -->
      <div id="signup" class="form-wrap" style="display:none;">
        <h2><strong>create</strong> your account</h2>
        <?php
          // Example attributes – tweak as you like
          $attr = ['id'=>'signupForm','autocomplete'=>'off'];
          echo form_open('competitions-users/submit_create_account', $attr);
        ?>
          <div class="row2">
            <div class="fld">
              <label class="lbl" for="name">Full Name</label>
              <div class="rel">
                <?php
                $name_attr = [
                  'class' => 'txt',
                  'id'    => 'name',
                  'maxlength' => '60',
                  'placeholder' => 'Vardas Pavardenis',
                  'required' => 'required'
                ];
                echo form_input('name', '', $name_attr); ?>
              </div>
            </div>
            <div class="fld">
              <label class="lbl" for="email">Email</label>
              <?php $email_attr = [
                'class' => 'txt',
                'id'    => 'email',
                'maxlength' => '120',
                'placeholder' => 'anyone@gmail.com',
                'required' => 'required'
              ];
              echo form_input('email', '', $email_attr); ?>
            </div>
          </div>

          <div class="row2">
            <div class="fld">
              <label class="lbl" for="password">Password</label>
              <div class="rel">
                <input class="txt" type="password" id="password" name="password" minlength="8" required>
              </div>
            </div>
            <div class="fld">
              <label class="lbl" for="repeat_password">Repeat Password</label>
              <div class="rel">
                <input class="txt" type="password" id="repeat_password" name="repeat_password" minlength="8" required>
              </div>
            </div>
          </div>

        <label class="agree">
            <input type="checkbox" name="agree" value="1" required>
            <span>By signing up I agree with <a class="link" href="<?= BASE_URL ?>terms">terms and conditions</a></span>
        </label>

        <button class="btn main" type="submit">SIGNUP</button>
        <div class="foot">We’ll <strong>never</strong> share your email with no one.</div>
        <hr>
        <a class="link btn" style="font-family: Arial;padding:1.21em;" href="#" data-target="login">LOGIN</a>
        <?= form_close(); ?>
      </div>

      <!-- ===== LOGIN ===== -->
      <div id="login" class="form-wrap">
        <h2 class="mb-1 text-center"><strong>login</strong> your account</h2>
        <?php
          $attr = ['id'=>'loginForm','autocomplete'=>'off'];
          echo form_open('competitions-users/submit_login', $attr); // e.g. modules/members/controllers/Members::submit_login()
          echo validation_errors('<div class="text-center err"><strong>', '</strong></div>');
        ?>
            <div class="fld">
                <label class="lbl" for="identity">Email</label>
                <input class="txt" type="email" id="identity" name="email" value="<?= out($email) ?>" required>
            </div>

            <div class="fld">
                <label class="lbl" for="login_password">Password</label>
                <input class="txt" type="password" id="login_password" name="password" required>
            </div>
            <label class="remember">
                <input type="checkbox" name="remember" value="1">
                <span>remember me</span>
            </label>
            <button class="btn main" type="submit">LOGIN</button>
            <div class="foot">
                <a class="link" href="competitions-users/forgot_password">Forgot password?</a>
            </div>
            <hr>
            <a class="link btn" style="font-family: Arial;padding:1.21em;" href="#" data-target="signup">CREATE ACCOUNT</a>
        <?= form_close(); ?>
      </div>
    </section>
  </div>
</div>

<script>
  // tiny tab switcher
  (function(){
    const switchTo = (name)=>{
      document.querySelectorAll('.tabs a').forEach(a=>{
        a.classList.toggle('active', a.dataset.target===name);
      });
      document.querySelectorAll('.form-wrap').forEach(w=>{
        w.style.display = (w.id===name)?'block':'none';
      });
    };
    document.querySelectorAll('[data-target]').forEach(el=>{
      el.addEventListener('click', e=>{
        e.preventDefault();
        switchTo(el.dataset.target);
      });
    });
  })();
</script>