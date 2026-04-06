<?php
/**
 * Plugin Name: RG Custom Footer & Newsletter
 * Description: Footer + Newsletter im urlaubsguru-Stil für robo-guru.de
 * Version: 1.2.0
 * Author: robo-guru.de
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ───── 1. BuddyBoss Default Footer ausblenden ───── */
add_action( 'wp_head', 'rg_hide_default_footer_css', 999 );
function rg_hide_default_footer_css() {
echo '<style id="rg-hide-bb-footer">
footer.footer-bottom.bb-footer,
footer.site-footer:not(.rg-site-footer),
.bb-footer:not(.rg-site-footer),
.site-footer-bottom,
.bb-footer-container,
.footer-widget-area,
#colophon:not(.rg-site-footer) {
    display: none !important;
}
/* Altes inline Newsletter im Footer-Bottom entfernen */
.rg-nl-footer { display: none !important; }
</style>';
}

/* ───── 2. Footer CSS ───── */
add_action( 'wp_head', 'rg_footer_styles', 1000 );
function rg_footer_styles() {
?>
<style id="rg-custom-footer-css">
/* ═══ NEWSLETTER SECTION ═══ */
.rg-newsletter-section {
    position: relative;
    background: linear-gradient(165deg, #F0FAFB 0%, #E0F7FA 50%, #F0FAFB 100%);
    border-top: 1px solid #E0E8ED;
    overflow: hidden;
    z-index: 1;
}
.rg-newsletter-section::before {
    content:''; position:absolute; top:-180px; right:-180px;
    width:500px; height:500px;
    background:radial-gradient(circle,rgba(0,188,212,.10) 0%,transparent 70%);
    pointer-events:none;
}
.rg-newsletter-inner {
    max-width:680px; margin:0 auto; padding:64px 24px;
    text-align:center; position:relative; z-index:1;
}
.rg-nl-badge {
    display:inline-flex; align-items:center; gap:8px;
    background:rgba(0,188,212,.12); border:1px solid rgba(0,188,212,.25);
    border-radius:100px; padding:7px 18px;
    font-size:13px; font-weight:600; color:#0097A7; margin-bottom:22px;
}
.rg-nl-badge svg { width:16px; height:16px; fill:currentColor; }
.rg-nl-heading {
    font-size:clamp(24px,4vw,36px); font-weight:700;
    line-height:1.25; margin-bottom:12px; color:#1A2332;
}
.rg-nl-heading span { color:#00ACC1; }
.rg-nl-subtext {
    font-size:15px; color:#5A6B7B; line-height:1.7;
    max-width:520px; margin:0 auto 32px;
}

/* ─── Newsletter Form (einfaches 2-Feld Layout) ─── */
.rg-nl-form {
    display:flex; gap:12px; max-width:520px; margin:0 auto 14px;
    flex-wrap: wrap;
}
.rg-nl-form .rg-nl-field {
    flex:1; min-width:140px;
}
.rg-nl-form input[type="text"],
.rg-nl-form input[type="email"] {
    width:100%;
    padding:14px 18px !important;
    border-radius:12px !important;
    border:2px solid #D5DEE5 !important;
    background:#FFFFFF !important;
    color:#1A2332 !important;
    font-size:15px !important;
    outline:none !important;
    transition:border-color .25s,box-shadow .25s !important;
    font-family:inherit !important;
    box-sizing:border-box !important;
    height:auto !important;
    line-height:1.5 !important;
    margin:0 !important;
    box-shadow:none !important;
}
.rg-nl-form input::placeholder { color:#94A3B8 !important; }
.rg-nl-form input:focus {
    border-color:#00BCD4 !important;
    box-shadow:0 0 0 3px rgba(0,188,212,.15) !important;
}
.rg-nl-form .rg-nl-submit {
    display:inline-flex; align-items:center; justify-content:center; gap:8px;
    padding:14px 28px; border:none; border-radius:12px;
    background:linear-gradient(135deg,#00BCD4,#0097A7);
    color:#FFFFFF; font-size:15px; font-weight:700;
    cursor:pointer; white-space:nowrap;
    transition:transform .2s,box-shadow .2s;
    font-family:inherit; min-width:140px;
    line-height:1.5;
}
.rg-nl-form .rg-nl-submit:hover {
    transform:translateY(-2px);
    box-shadow:0 6px 24px rgba(0,188,212,.3);
}
.rg-nl-form .rg-nl-submit svg { width:18px; height:18px; fill:currentColor; }
.rg-nl-privacy { font-size:12px; color:#7A8A9A; max-width:520px; margin:4px auto 0; }
.rg-nl-privacy a { color:#00ACC1; text-decoration:none; }
.rg-nl-privacy a:hover { text-decoration:underline; }
.rg-nl-success {
    display:none; padding:16px 24px; background:rgba(0,188,212,.08);
    border:1px solid rgba(0,188,212,.25); border-radius:12px;
    color:#0097A7; font-weight:600; font-size:15px;
    max-width:520px; margin:0 auto;
}

@media (max-width:560px) {
    .rg-nl-form { flex-direction:column; }
    .rg-nl-form .rg-nl-field { min-width:100%; }
    .rg-nl-form .rg-nl-submit { width:100%; }
}

/* ═══ FOOTER ═══ */
.rg-site-footer {
    background:#FFFFFF !important;
    border-top:1px solid #E5EBF0 !important;
    color:#1A2332 !important;
    position:relative; z-index:1;
}
.rg-footer-main {
    max-width:1200px; margin:0 auto;
    padding:52px 24px 40px;
    display:grid; grid-template-columns:1.4fr 1fr 1fr 1fr; gap:44px;
}
.rg-footer-brand { display:flex; flex-direction:column; gap:16px; }
.rg-footer-logo { display:flex; align-items:center; gap:12px; text-decoration:none; }
.rg-footer-logo-icon {
    width:44px; height:44px; border-radius:12px;
    background:linear-gradient(135deg,#00BCD4,#0097A7);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.rg-footer-logo-icon img { width:32px; height:32px; object-fit:contain; border-radius:6px; }
.rg-footer-logo-icon svg { width:24px; height:24px; fill:white; }
.rg-footer-logo-text { font-size:21px; font-weight:700; color:#1A2332; }
.rg-footer-logo-text span { color:#00ACC1; }
.rg-footer-brand-desc { font-size:14px; line-height:1.65; color:#5A6B7B; max-width:270px; }
.rg-footer-social { display:flex; gap:10px; margin-top:2px; }
.rg-footer-social a {
    width:38px; height:38px; border-radius:10px;
    background:#F3F6F9; border:1px solid #E5EBF0;
    display:flex; align-items:center; justify-content:center;
    color:#5A6B7B; text-decoration:none;
    transition:background .25s,color .25s,border-color .25s;
}
.rg-footer-social a:hover { background:rgba(0,188,212,.08); color:#00ACC1; border-color:rgba(0,188,212,.3); }
.rg-footer-social a svg { width:18px; height:18px; fill:currentColor; }

.rg-footer-col h4 {
    font-size:12px; font-weight:700; color:#1A2332;
    text-transform:uppercase; letter-spacing:1.5px; margin-bottom:20px;
}
.rg-footer-col ul { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:11px; }
.rg-footer-col ul li a {
    color:#5A6B7B; text-decoration:none; font-size:14px; font-weight:500;
    transition:color .2s,padding-left .2s; display:inline-block;
}
.rg-footer-col ul li a:hover { color:#00ACC1; padding-left:4px; }

.rg-footer-divider { max-width:1200px; margin:0 auto; border:none; border-top:1px solid #E5EBF0; }

.rg-footer-nl-bar {
    max-width:1200px; margin:0 auto; padding:24px;
    display:flex; align-items:center; justify-content:space-between; gap:20px;
}
.rg-footer-nl-text { font-size:17px; font-weight:700; color:#1A2332; }
.rg-footer-nl-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:10px 24px; border:2px solid #00BCD4; border-radius:10px;
    background:transparent; color:#00ACC1; font-size:14px; font-weight:700;
    text-decoration:none; transition:background .25s,color .25s; white-space:nowrap;
}
.rg-footer-nl-btn:hover { background:#00BCD4; color:#FFF; }
.rg-footer-nl-btn svg { width:16px; height:16px; fill:currentColor; }

.rg-footer-contact {
    max-width:1200px; margin:0 auto; padding:18px 24px 0;
    display:flex; align-items:center; gap:10px; font-size:14px;
    color:#5A6B7B; flex-wrap:wrap;
}
.rg-footer-contact svg { width:18px; height:18px; fill:#00ACC1; flex-shrink:0; }
.rg-footer-contact a { color:#1A2332; text-decoration:none; font-weight:600; }
.rg-footer-contact a:hover { color:#00ACC1; }
.rg-footer-contact-sep { margin:0 4px; opacity:.3; }

.rg-footer-bottom {
    max-width:1200px; margin:0 auto;
    padding:22px 24px 40px; /* Extra Padding unten für Cookie-Banner */
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:12px;
}
.rg-footer-copy { font-size:13px; color:#8899AA; }
.rg-footer-legal { display:flex; flex-wrap:wrap; gap:8px 20px; }
.rg-footer-legal a { font-size:13px; color:#8899AA; text-decoration:none; transition:color .2s; }
.rg-footer-legal a:hover { color:#00ACC1; }

@media (max-width:900px) {
    .rg-footer-main { grid-template-columns:1fr 1fr; gap:32px 24px; }
    .rg-footer-brand { grid-column:1/-1; }
}
@media (max-width:600px) {
    .rg-footer-main { grid-template-columns:1fr; gap:28px; padding:40px 20px 28px; }
    .rg-footer-nl-bar { flex-direction:column; text-align:center; padding:20px; }
    .rg-footer-bottom { flex-direction:column; text-align:center; gap:10px; padding-bottom:60px; }
    .rg-footer-legal { justify-content:center; }
    .rg-footer-contact { justify-content:center; text-align:center; }
}
</style>
<?php
}

/* ───── 3. Footer + Newsletter HTML ───── */
add_action( 'wp_footer', 'rg_render_custom_footer', 5 );
function rg_render_custom_footer() {
    $year = date('Y');
    $logo_id = get_theme_mod('custom_logo');
    $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'thumbnail' ) : '';
?>
<!-- RG NEWSLETTER -->
<section class="rg-newsletter-section" id="rg-newsletter">
<div class="rg-newsletter-inner">
    <div class="rg-nl-badge"><svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg> Kostenloser Newsletter</div>
    <h2 class="rg-nl-heading">Verpasse keine <span>Robotik-News</span> mehr.</h2>
    <p class="rg-nl-subtext">Melde dich jetzt an und erhalte regelmäßig Praxiswissen, Produktneuheiten und exklusive Insights rund um Reinigungsrobotik – direkt in dein Postfach.</p>

    <form class="rg-nl-form" id="rg-nl-form" action="javascript:void(0);" method="post">
        <div class="rg-nl-field">
            <input type="text" name="VORNAME" placeholder="Vorname" aria-label="Vorname">
        </div>
        <div class="rg-nl-field">
            <input type="email" name="EMAIL" placeholder="E-Mail-Adresse *" aria-label="E-Mail" required>
        </div>
        <button type="submit" class="rg-nl-submit">
            Anmelden
            <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
        </button>
    </form>
    <div class="rg-nl-success" id="rg-nl-success">✓ Vielen Dank! Bitte bestätige deine E-Mail-Adresse.</div>
    <p class="rg-nl-privacy">Die <a href="<?php echo esc_url( home_url('/datenschutzerklaerung/') ); ?>">Datenschutzhinweise</a> habe ich zur Kenntnis genommen.</p>
</div>
</section>

<!-- RG FOOTER -->
<footer class="rg-site-footer" id="rg-footer">
<div class="rg-footer-main">
    <div class="rg-footer-brand">
        <a href="<?php echo esc_url( home_url('/') ); ?>" class="rg-footer-logo">
            <div class="rg-footer-logo-icon">
                <?php if ( $logo_url ) : ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="robo-guru">
                <?php else : ?>
                    <svg viewBox="0 0 24 24"><path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1.07A7 7 0 0 1 14 22h-4a7 7 0 0 1-6.93-6H2a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2zm-2 12a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm4 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/></svg>
                <?php endif; ?>
            </div>
            <span class="rg-footer-logo-text">robo<span>-guru</span>.de</span>
        </a>
        <p class="rg-footer-brand-desc">Dein unabhängiger Experte für kommerzielle Reinigungsrobotik. Herstellerübergreifende Beratung für den DACH-Markt.</p>
        <div class="rg-footer-social">
            <a href="https://www.linkedin.com/in/sebastianhosbach" title="LinkedIn" target="_blank" rel="noopener"><svg viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg></a>
            <a href="https://www.youtube.com/@Sebastian-Hosbach" title="YouTube" target="_blank" rel="noopener"><svg viewBox="0 0 24 24"><path d="M10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83-.25.9-.83 1.48-1.73 1.73-.47.13-1.33.22-2.65.28-1.3.07-2.49.1-3.59.1L12 19c-4.19 0-6.8-.16-7.83-.44-.9-.25-1.48-.83-1.73-1.73-.13-.47-.22-1.1-.28-1.9-.07-.8-.1-1.49-.1-2.09L2 12c0-2.19.16-3.8.44-4.83.25-.9.83-1.48 1.73-1.73.47-.13 1.33-.22 2.65-.28 1.3-.07 2.49-.1 3.59-.1L12 5c4.19 0 6.8.16 7.83.44.9.25 1.48.83 1.73 1.73z"/></svg></a>
        </div>
    </div>
    <div class="rg-footer-col">
        <h4>Plattform</h4>
        <ul>
            <li><a href="<?php echo esc_url(home_url('/kaufberatung/')); ?>">Kaufberatung</a></li>
            <li><a href="<?php echo esc_url(home_url('/roi-rechner/')); ?>">ROI-Rechner</a></li>
            <li><a href="<?php echo esc_url(home_url('/robo-finder/')); ?>">Robo-Finder</a></li>
            <li><a href="<?php echo esc_url(home_url('/praxisberichte/')); ?>">Praxisberichte</a></li>
            <li><a href="<?php echo esc_url(home_url('/navigation-mapping/')); ?>">Navigation &amp; Mapping</a></li>
        </ul>
    </div>
    <div class="rg-footer-col">
        <h4>Wissenswertes</h4>
        <ul>
            <li><a href="<?php echo esc_url(home_url('/blog/')); ?>">Blog &amp; News</a></li>
            <li><a href="<?php echo esc_url(home_url('/uber-uns/')); ?>">Über uns</a></li>
            <li><a href="<?php echo esc_url(home_url('/kontakt/')); ?>">Kontakt &amp; Beratung</a></li>
            <li><a href="<?php echo esc_url(home_url('/community/')); ?>">Community &amp; Forum</a></li>
            <li><a href="<?php echo esc_url(home_url('/register/')); ?>">Kostenlos registrieren</a></li>
        </ul>
    </div>
    <div class="rg-footer-col">
        <h4>Roboter</h4>
        <ul>
            <li><a href="<?php echo esc_url(home_url('/roboter/')); ?>">Alle Roboter</a></li>
            <li><a href="<?php echo esc_url(home_url('/services/')); ?>">Tips &amp; Tricks</a></li>
            <li><a href="<?php echo esc_url(home_url('/roboter/?filter=reinigung')); ?>">Reinigungsroboter</a></li>
            <li><a href="<?php echo esc_url(home_url('/roboter/?filter=service')); ?>">Serviceroboter</a></li>
            <li><a href="<?php echo esc_url(home_url('/roboter/?filter=transport')); ?>">Transportroboter</a></li>
        </ul>
    </div>
</div>

<hr class="rg-footer-divider">
<div class="rg-footer-nl-bar">
    <span class="rg-footer-nl-text">Robotik-Wissen per Newsletter!</span>
    <a href="#rg-newsletter" class="rg-footer-nl-btn">Jetzt anmelden <svg viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg></a>
</div>
<hr class="rg-footer-divider">

<div class="rg-footer-contact">
    <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
    Kontakt: <a href="mailto:info@robo-guru.de">info@robo-guru.de</a>
    <span class="rg-footer-contact-sep">|</span>
    Wir beraten dich gerne – herstellerunabhängig &amp; kompetent.
</div>

<hr class="rg-footer-divider" style="margin-top:18px">
<div class="rg-footer-bottom">
    <span class="rg-footer-copy">&copy; <?php echo $year; ?> robo-guru.de</span>
    <div class="rg-footer-legal">
        <a href="<?php echo esc_url(home_url('/impressum/')); ?>">Impressum</a>
        <a href="<?php echo esc_url(home_url('/datenschutzerklaerung/')); ?>">Datenschutzerklärung</a>
        <a href="<?php echo esc_url(home_url('/uber-uns/')); ?>">Über uns</a>
    </div>
</div>
</footer>
<?php
}

/* ───── 4. Scripts: Smooth-Scroll + Brevo API Submit ───── */
add_action( 'wp_footer', 'rg_footer_scripts', 99 );
function rg_footer_scripts() {
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll
    document.querySelectorAll('a[href="#rg-newsletter"]').forEach(function(lnk) {
        lnk.addEventListener('click', function(e) {
            e.preventDefault();
            var t = document.getElementById('rg-newsletter');
            if (t) {
                t.scrollIntoView({ behavior:'smooth', block:'center' });
                setTimeout(function(){
                    var inp = t.querySelector('input[type="email"]');
                    if (inp) inp.focus();
                }, 600);
            }
        });
    });

    // Newsletter Submit → Brevo iframe (zuverlässig)
    var form = document.getElementById('rg-nl-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var email = form.querySelector('input[name="EMAIL"]').value;
            var name  = form.querySelector('input[name="VORNAME"]').value;
            if (!email) return;

            // Brevo Formular per verstecktem iframe absenden
            var iframeSrc = 'https://25927acd.sibforms.com/serve/MUIFAEgT4DpVeNTJcHNBLC9O6uwvGC6Nxr5KruRY3l1oW2AlFePZwco2XzeVm23rGWkJFdj0Q3Xqpbwe79kGMisGA3ZnSdLyUqdiLC2TPIh6j4Xt9GaHYO-2t9ycWDxBgrLIES1BzQ0BFHc-3evcuBXyBoLB88Oe3dDnLI59tqml2YIfnvra3cbQK-Hpc8DnIy5TIKsUFRQwCpgfuA==';

            // Formular-Daten direkt an Brevo senden
            var iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.name = 'rg-brevo-frame';
            document.body.appendChild(iframe);

            var brevoForm = document.createElement('form');
            brevoForm.method = 'POST';
            brevoForm.action = iframeSrc;
            brevoForm.target = 'rg-brevo-frame';
            brevoForm.style.display = 'none';

            var emailInput = document.createElement('input');
            emailInput.name = 'EMAIL'; emailInput.value = email;
            brevoForm.appendChild(emailInput);

            var nameInput = document.createElement('input');
            nameInput.name = 'VORNAME'; nameInput.value = name;
            brevoForm.appendChild(nameInput);

            document.body.appendChild(brevoForm);
            brevoForm.submit();

            // UI Update
            form.style.display = 'none';
            document.getElementById('rg-nl-success').style.display = 'block';
            var priv = form.parentElement.querySelector('.rg-nl-privacy');
            if (priv) priv.style.display = 'none';

            // Cleanup
            setTimeout(function(){
                document.body.removeChild(iframe);
                document.body.removeChild(brevoForm);
            }, 3000);
        });
    }
});
</script>
<?php
}
