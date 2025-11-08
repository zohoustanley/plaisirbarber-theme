<?php
/**
 * Template de la page d'accueil PlaisirBarber
 */

get_header();

// On récupère les réglages du plugin s'il est actif
if (function_exists('pbcore_get_settings')) {
    $pb = pbcore_get_settings();
} else {
    $pb = [
        'hero_title'    => "Où tradition rencontre style moderne",
        'hero_subtitle' => "Barber shop dédié à ceux qui veulent une coupe nette, une barbe soignée et une expérience premium.",
        'hero_cta_label'=> "Prendre rendez-vous",
        'hero_video_url'=> "",
        'address'       => "12 rue du Barber, 78370 Plaisir",
        'phone'         => "01 23 45 67 89",
        'email'         => "contact@plaisirbarber.fr",
        'hours'         => "Mardi – Samedi : 10h–20h",
        'instagram_url' => "https://www.instagram.com",
        'tiktok_url'    => "https://www.tiktok.com",
        'map_iframe'    => "",
    ];
}
?>

<!-- HERO VIDEO -->
<section class="hero" id="top">
    <video class="hero__video" autoplay muted loop playsinline>
        <?php if (!empty($pb['hero_video_url'])) : ?>
            <source src="<?php echo esc_url($pb['hero_video_url']); ?>" type="video/mp4">
        <?php else : ?>
            <source src="<?php echo esc_url(get_template_directory_uri() . '/assets/video/barber.mp4'); ?>" type="video/mp4">
        <?php endif; ?>
    </video>

    <div class="hero__overlay">
        <div class="container">
            <div class="hero-grid">
                <div>
                    <h1 class="hero-title">
                        <?php echo nl2br(esc_html($pb['hero_title'])); ?>
                    </h1>
                    <p class="hero-subtitle">
                        <?php echo esc_html($pb['hero_subtitle']); ?>
                    </p>
                    <div style="display:flex;flex-wrap:wrap;gap:1rem;">
                        <a href="/prise-de-rendez-vous" class="btn btn--primary">
                            <?php echo esc_html($pb['hero_cta_label']); ?>
                        </a>
                        <a href="#tarifs" class="btn btn--ghost">
                            Voir les tarifs
                        </a>
                    </div>
                </div>

                <div>
                    <div class="location-card" style="backdrop-filter:blur(10px);background:rgba(15,23,42,.85);">
                        <h2 class="section__title" style="font-size:1.2rem;margin-bottom:1rem;">Infos rapides</h2>
                        <p style="margin:.25rem 0;font-size:.9rem;">📍 <?php echo esc_html($pb['address']); ?></p>
                        <p style="margin:.25rem 0;font-size:.9rem;">📞 <?php echo esc_html($pb['phone']); ?></p>
                        <p style="margin:.75rem 0 1.25rem;font-size:.9rem;">🕒 <?php echo esc_html($pb['hours']); ?></p>
                        <a href="#localisation" class="btn btn--light" style="width:100%;justify-content:center;">
                            Voir comment venir
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TARIFS -->
<section class="section section--soft" id="tarifs">
    <div class="container">
        <!-- Bloc texte centré -->
        <div class="tarifs-header">
            <h2 class="section__title">NOS SERVICES</h2>
            <p>
                Certains services sont marqués « dès … » ou « à partir … » en fonction du travail demandé.
            </p>
        </div>

        <div class="tarifs-groups">
            <?php
            // On récupère toutes les catégories de prestations du plugin Barbershop
            if (taxonomy_exists('bs_prestation_category') && post_type_exists('bs_prestation')) {

                $categories = get_terms([
                    'taxonomy'   => 'bs_prestation_category',
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                ]);

                if (!empty($categories) && !is_wp_error($categories)) :
                    foreach ($categories as $cat) :

                        $prestations = new WP_Query([
                            'post_type'      => 'bs_prestation',
                            'posts_per_page' => -1,
                            'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
                            'order'          => 'ASC',
                            'tax_query'      => [
                                [
                                    'taxonomy' => 'bs_prestation_category',
                                    'field'    => 'term_id',
                                    'terms'    => $cat->term_id,
                                ],
                            ],
                        ]);

                        if ($prestations->have_posts()) :
                            ?>
                            <div class="price-group">
                                <h3 class="price-group__title">
                                    <?php echo esc_html($cat->name); ?>
                                </h3>
                                <?php if (!empty($cat->description)) : ?>
                                    <p class="price-group__desc">
                                        <?php echo esc_html($cat->description); ?>
                                    </p>
                                <?php endif; ?>

                                <table class="prices-table">
                                    <?php while ($prestations->have_posts()) : $prestations->the_post(); ?>
                                        <?php
                                        $pid   = get_the_ID();
                                        $price = get_post_meta($pid, '_barbershop_prestation_price', true);
                                        ?>
                                        <tr>
                                            <td><?php the_title(); ?></td>
                                            <td style="text-align:right;">
                                                <?php
                                                if ($price !== '') {
                                                    echo esc_html($price);
                                                } else {
                                                    echo '<span style="opacity:.6;">Sur devis</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </table>
                            </div>
                            <?php
                            wp_reset_postdata();
                        endif;

                    endforeach;
                else :
                    ?>
                    <p>Ajoutez des “Prestations” et des “Catégories de prestations” dans l’admin pour afficher les tarifs.</p>
                <?php
                endif;

            } else {
                ?>
                <p>Le plugin Barbershop n’est pas actif ou les types de contenu ne sont pas disponibles.</p>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<!-- SOCIAL + GALERIE -->
<section class="section section--dark" id="galerie">
    <div class="container">
        <h2 class="section__title centered-header">Nos réalisations</h2>

        <div class="social-gallery-grid">
            <!-- Colonne galerie -->
            <div>
                <?php
                $works = new WP_Query([
                    'post_type'      => 'pb_work',
                    'posts_per_page' => 9,
                    'orderby'        => ['menu_order' => 'ASC', 'date' => 'DESC'],
                ]);
                ?>

                <?php if ($works->have_posts()) : ?>
                    <div class="gallery-carousel" data-gallery-carousel>
                        <div class="gallery-track">
                            <?php
                            $index = 0;
                            while ($works->have_posts()) :
                                $works->the_post();
                                $img = get_the_post_thumbnail_url(get_the_ID(), 'large');
                                if (!$img) {
                                    $img = get_template_directory_uri() . '/assets/images/cut-placeholder.jpg';
                                }
                                ?>
                                <article class="gallery-item<?php echo $index === 0 ? ' is-active' : ''; ?>">
                                    <img
                                            src="<?php echo esc_url($img); ?>"
                                            alt="<?php the_title_attribute(); ?>"
                                            loading="lazy"
                                            data-full="<?php echo esc_url($img); ?>"
                                    >
                                    <div class="gallery-card__label">
                                        <?php the_title(); ?>
                                    </div>
                                </article>
                                <?php
                                $index++;
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </div>

                        <!-- Flèches -->
                        <button class="gallery-arrow gallery-arrow--prev" type="button" aria-label="Photo précédente">‹</button>
                        <button class="gallery-arrow gallery-arrow--next" type="button" aria-label="Photo suivante">›</button>

                        <!-- Petits points -->
                        <div class="gallery-controls">
                            <?php for ($i = 0; $i < $index; $i++) : ?>
                                <button
                                        type="button"
                                        class="gallery-dot<?php echo $i === 0 ? ' is-active' : ''; ?>"
                                        data-index="<?php echo esc_attr($i); ?>"
                                        aria-label="Aller à la photo <?php echo esc_attr($i + 1); ?>"
                                ></button>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <p style="color:#9ca3af;">Ajoutez quelques “Réalisations” dans l’admin pour alimenter la galerie.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<div class="lightbox" id="galerie-lightbox" aria-hidden="true">
    <div class="lightbox__backdrop"></div>
    <figure class="lightbox__content">
        <button class="lightbox__close" type="button" aria-label="Fermer la photo">×</button>
        <img src="" alt="" class="lightbox__img">
    </figure>
</div>


<!-- ÉQUIPE -->
<section class="section section--light" id="equipe">
    <div class="container">
        <div class="centered-header">
            <h2 class="section__title">Notre équipe</h2>
            <p style="margin-bottom:2rem;font-size:.95rem;color:#4b5563; text-align: center;">
                Une équipe passionnée, formée aux dernières tendances comme aux techniques classiques.
            </p>
        </div>

        <?php
        // On récupère les utilisateurs avec les rôles "collaborateur" et "manager"
        $barbers = get_users([
            'role__in' => ['collaborateur', 'manager'],
            'orderby'  => 'display_name',
            'order'    => 'ASC',
        ]);
        ?>

        <?php if (!empty($barbers)) : ?>
            <div class="team-grid">
                <?php foreach ($barbers as $user) : ?>
                    <?php
                    $user_id = $user->ID;

                    // Photo : avatar WP ou placeholder
                    $avatar_url = get_avatar_url($user_id, ['size' => 400]);
                    if (!$avatar_url || strpos($avatar_url, 'gravatar.com') !== false) {
                        // Si tu as un placeholder spécifique pour les barbiers
                        $avatar_url = get_template_directory_uri() . '/assets/images/barber-placeholder.png';
                    }

                    // Rôle "lisible" : meta personnalisée ou rôle WP
                    $custom_role = get_user_meta($user_id, 'barbershop_role', true); // ex: "Barbier", "Manager"
                    if ($custom_role) {
                        $role_label = $custom_role;
                    } else {
                        $roles = (array) $user->roles;
                        if (in_array('manager', $roles, true)) {
                            $role_label = 'Manager';
                        } elseif (in_array('collaborateur', $roles, true)) {
                            $role_label = 'Collaborateur';
                        } else {
                            $role_label = '';
                        }
                    }

                    // Bio : champ "Biographie" du profil utilisateur
                    $bio = get_user_meta($user_id, 'description', true);
                    ?>
                    <article class="team-card">
                        <img
                                src="<?php echo esc_url($avatar_url); ?>"
                                alt="<?php echo esc_attr($user->first_name); ?>"
                                style="border-radius:1rem;margin-bottom:1rem;height:220px;object-fit:cover;width:100%;"
                        >
                        <h3 style="margin:0 0 .25rem;"><?php echo esc_html($user->first_name); ?></h3>

                        <form method="get"
                              action="/prise-de-rendez-vous"
                              class="bs-booking-staff-form-inline">
                            <input type="hidden" name="bs_step" value="1">
                            <input type="hidden" name="bs_staff" value="<?php echo esc_attr($user->ID); ?>">
                            <button type="submit" class="btn btn--primary">
                                Prendre RDV
                            </button>
                        </form>

                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>Ajoutez des utilisateurs avec les rôles “Collaborateur” ou “Manager” pour présenter l’équipe.</p>
        <?php endif; ?>
    </div>
</section>


<!-- LOCALISATION / CONTACT -->
<section class="section section--dark" id="localisation">
    <div class="container">
        <div class="location-grid">

            <!-- Colonne réseaux sociaux -->
            <aside class="social-card">
                <h2 class="section__title barbercolor" style="font-size:1.3rem;">Suivez-nous</h2>
                <p style="color:#9ca3af;margin-bottom:1.5rem;">
                    Avant / après, nouveaux styles, promos et live sur nos réseaux.
                </p>
                <div style="display:flex; gap:2rem; font-size:.95rem; margin-bottom:1.5rem;">
                    <?php if (!empty($pb['instagram_url'])) : ?>
                        <a href="<?php echo esc_url($pb['instagram_url']); ?>" target="_blank" rel="noreferrer">
                            📸 Instagram<br>
                            <span style="color:#9ca3af;font-size:.85rem;">@plaisirbarber</span>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($pb['tiktok_url'])) : ?>
                        <a href="<?php echo esc_url($pb['tiktok_url']); ?>" target="_blank" rel="noreferrer">
                            🎥 TikTok<br>
                            <span style="color:#9ca3af;font-size:.85rem;">@plaisirbarber</span>
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo esc_url($pb['tiktok_url']); ?>" target="_blank" rel="noreferrer">
                        🎥 WhatsApp<br>
                        <span style="color:#9ca3af;font-size:.85rem;">@plaisirbarber</span>
                    </a>
                </div>
                <div style="display:flex;flex-direction:column;gap:.75rem;font-size:.95rem;">
                    <p style="margin:.25rem 0;font-size:.9rem;"><span class="barbercolor">Adresse: </span> <?php echo esc_html($pb['address']); ?></p>
                    <p style="margin:.25rem 0;font-size:.9rem;"><span class="barbercolor">Téléphone: </span>
                        <a href="tel:<?php echo esc_attr(preg_replace('/\D+/', '', $pb['phone'])); ?>">
                            <?php echo esc_html($pb['phone']); ?>
                        </a>
                    </p>
                    <p style="margin:.25rem 0;font-size:.9rem;"><span class="barbercolor">E-mail: </span>
                        <a href="mailto:<?php echo esc_attr($pb['email']); ?>">
                            <?php echo esc_html($pb['email']); ?>
                        </a>
                    </p>
                </div>
            </aside>
            <div>
                <div class="location-card">
                    <h3 style="margin-top:0;margin-bottom:.75rem;">Nous trouver</h3>
                    <div style="width:100%;height:260px;border-radius:1rem;overflow:hidden;margin-bottom:1rem;">
                        <?php
                        if (!empty($pb['map_iframe'])) {
                            // On affiche exactement le code iframe fourni dans l'admin
                            echo $pb['map_iframe']; // déjà sécurisé par wp_kses_post côté plugin
                        } else {
                            ?>
                            <iframe
                                    src="https://maps.google.com/maps?q=Paris&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                    width="100%"
                                    height="260"
                                    style="border:0;"
                                    loading="lazy">
                            </iframe>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
