<?php
// Fichier minimal requis pour que le thème soit valide

get_header();
?>

    <main style="max-width:1200px;margin:2rem auto;padding:0 1.5rem;">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>">
                    <h1><?php the_title(); ?></h1>
                    <div>
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p>Aucun contenu trouvé.</p>
        <?php endif; ?>
    </main>

<?php
get_footer();
