<?php if ($post->loop == 0):?>
	<div class="column-item item-top">
        <div class="post-inner">
            <?php if (has_post_thumbnail() && '' !== get_the_post_thumbnail()) : ?>
            <div class="post-thumbnail">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail(); ?>
                </a>
            </div>
            <?php endif;?>

            <div class="content">
                <div class="entry-header">
                    <div class="entry-meta">
                        <?php
                        poco_categories_link();
                        poco_post_meta();
                        ?>
                    </div>
                    <?php
                    the_title(sprintf('<h3 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h3>');
                    ?>
                </div>
                <p><?php echo  wp_trim_words(esc_html(get_the_excerpt()), 30); ?></p>
            </div>
        </div>
	</div>
<?php else:?>

<div class="column-item post-style-2">
    <div class="post-inner">

		<?php if (has_post_thumbnail() && '' !== get_the_post_thumbnail()) : ?>
			<div class="post-thumbnail">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail('poco-post-grid-2'); ?>
				</a>
			</div>
		<?php endif;?>

		<div class="content">
			<div class="entry-header">
                <?php
                the_title(sprintf('<h3 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h3>');
                ?>
				<div class="entry-meta">
					<?php
					// Posted on.
					$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

					if (get_the_time('U') !== get_the_modified_time('U')) {
						$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
					}

					$time_string = sprintf(
						$time_string,
						esc_attr(get_the_date('c')),
						esc_html(get_the_date()),
						esc_attr(get_the_modified_date('c')),
						esc_html(get_the_modified_date())
					);

					echo '<span class="posted-on">' . sprintf('<a href="%1$s" rel="bookmark">%2$s</a>', esc_url(get_permalink()), $time_string) . '</span>';
					?>
				</div>

			</div>
		</div>
    </div>
</div>
<?php endif;?>


