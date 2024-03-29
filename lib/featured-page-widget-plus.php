<?php

add_action( 'widgets_init', 'uga_load_widgets', 99 );

function uga_load_widgets() {

	unregister_widget( 'Genesis_Featured_Page' );
	register_widget( 'Genesis_Featured_Page_Plus' );

}

class Genesis_Featured_Page_Plus extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 *
	 * @since 0.1.8
	 */
	public function __construct() {

		$this->defaults = array(
			'title'           => '',
			'page_id'         => '',
			'show_image'      => 0,
			'show_caption'    => 0,
			'image_alignment' => '',
			'image_size'      => '',
			'show_title'      => 0,
			'show_content'    => 0,
			'content_limit'   => '',
			'more_text'       => '',
		);

		$widget_ops = array(
			'classname'   => 'featured-content featuredpage',
			'description' => __( 'Displays featured page with thumbnails', 'genesis' ),
		);

		$control_ops = array(
			'id_base' => 'featured-page',
			'width'   => 200,
			'height'  => 250,
		);

		parent::__construct( 'featured-page', __( 'Genesis - Featured Page Plus', 'genesis' ), $widget_ops, $control_ops );

	}

	/**
	 * Echo the widget content.
	 *
	 * @since 0.1.8
	 *
	 * @global WP_Query $wp_query Query object.
	 * @global int      $more
	 *
	 * @param array $args     Display arguments including `before_title`, `after_title`,
	 *                        `before_widget`, and `after_widget`.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {

		global $wp_query;

		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $args['before_widget'];

		// Set up the author bio.
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
		}

		$wp_query = new WP_Query( array( 'page_id' => $instance['page_id'] ) );

		if ( have_posts() ) : while ( have_posts() ) : the_post();

			genesis_markup( array(
				'open'    => '<article %s>',
				'context' => 'entry',
				'params'  => array(
					'is_widget' => true,
				),
			) );

			$image = genesis_get_image( array(
				'format'  => 'html',
				'size'    => $instance['image_size'],
				'context' => 'featured-page-widget',
				'attr'    => genesis_parse_attr( 'entry-image-widget', array ( 'alt' => get_the_title() ) ),
			) );
			
			if ( $image && $instance['show_image'] ) {
				$role = empty( $instance['show_title'] ) ? '' : 'aria-hidden="true"';
				$classes = esc_attr( $instance['image_alignment'] );
				$caption = '';
				if ( $instance['show_caption'] ) {
					$img_id = get_post_thumbnail_id( $instance['page_id'] );
					$caption_text = get_post_field( 'post_excerpt', $img_id );
					if ( !empty( $caption_text ) ) {
						$caption = '<figcaption class="wp-caption-text">' . $caption_text . '</figcaption>';
						$classes .= ' wp-caption';
						if ( '2' == $instance['show_caption'] )
							$classes .= ' overlay';
					}
				}
					
				printf( '<figure class="%s"><a href="%s" %s>%s</a> %s</figure>', $classes, get_permalink(), $role, wp_make_content_images_responsive( $image ), $caption );
			}

			if ( ! empty( $instance['show_title'] ) ) {

				$title = get_the_title() ? get_the_title() : __( '(no title)', 'genesis' );

				/**
				 * Filter the featured page widget title.
				 *
				 * @since  2.2.0
				 *
				 * @param string $title    Featured page title.
				 * @param array  $instance {
				 *     Widget settings for this instance.
				 *
				 *     @type string $title           Widget title.
				 *     @type int    $page_id         ID of the featured page.
				 *     @type bool   $show_image      True if featured image should be shown, false
				 *                                   otherwise.
				 *     @type string $image_alignment Image alignment: `alignnone`, `alignleft`,
				 *                                   `aligncenter` or `alignright`.
				 *     @type string $image_size      Name of the image size.
				 *     @type bool   $show_title      True if featured page title should be shown,
				 *                                   false otherwise.
				 *     @type int   $show_content     0 if featured page content should not be shown,
				 *                                   1 if page content should be show; 2 if excerpt should be shown.
				 *     @type int    $content_limit   Amount of content to show, in characters.
				 *     @type int    $more_text       Text to use for More link.
				 * }
				 * @param array  $args     {
				 *     Widget display arguments.
				 *
				 *     @type string $before_widget Markup or content to display before the widget.
				 *     @type string $before_title  Markup or content to display before the widget title.
				 *     @type string $after_title   Markup or content to display after the widget title.
				 *     @type string $after_widget  Markup or content to display after the widget.
				 * }
				 */
				$title = apply_filters( 'genesis_featured_page_title', $title, $instance, $args );
				$heading = genesis_a11y( 'headings' ) ? 'h4' : 'h2';

				$entry_title = genesis_markup( array(
					'open'    => "<{$heading} %s>",
					'close'   => "</{$heading}>",
					'context' => 'entry-title',
					'content' => sprintf( '<a href="%s">%s</a>', get_permalink(), $title ),
					'params'  => array(
						'is_widget' => true,
						'wrap'      => $heading,
					),
					'echo'    => false,
				) );

				genesis_markup( array(
					'open'    => "<header %s>",
					'close'   => "</header>",
					'context' => 'entry-header',
					'content' => $entry_title,
					'params'  => array(
						'is_widget' => true,
					),
				) );

			}

			if ( ! empty( $instance['show_content'] ) ) {

				genesis_markup( array(
					'open'    => '<div %s>',
					'context' => 'entry-content',
					'params'  => array(
						'is_widget' => true,
					),
				) );

				if ( '2' == $instance['show_content'] ) {
					the_excerpt();
				}
				elseif ( empty( $instance['content_limit'] ) ) {

					global $more;

					$orig_more = $more;
					$more = 0;

					the_content( genesis_a11y_more_link( $instance['more_text'] ) );

					$more = $orig_more;

				} else {
					the_content_limit( (int) $instance['content_limit'], genesis_a11y_more_link( esc_html( $instance['more_text'] ) ) );
				}

				genesis_markup( array(
					'close'   => '</div>',
					'context' => 'entry-content',
					'params'  => array(
						'is_widget' => true,
					),
				) );

			}

			genesis_markup( array(
				'close'   => '</article>',
				'context' => 'entry',
				'params'  => array(
					'is_widget' => true,
				),
			) );

			endwhile;
		endif;

		// Restore original query.
		wp_reset_query();

		echo $args['after_widget'];

	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that `$new_instance` is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @since 0.1.8
	 *
	 * @param array $new_instance New settings for this instance as input by the user via `form()`.
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {

		$new_instance['title']     = strip_tags( $new_instance['title'] );
		$new_instance['more_text'] = strip_tags( $new_instance['more_text'] );
		return $new_instance;

	}

	/**
	 * Echo the settings update form.
	 *
	 * @since 0.1.8
	 *
	 * @param array $instance Current settings.
	 * @return void
	 */
	public function form( $instance ) {

		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'page_id' ); ?>"><?php _e( 'Page', 'genesis' ); ?>:</label>
			<?php
			wp_dropdown_pages( array(
				'name'     => esc_attr( $this->get_field_name( 'page_id' ) ),
				'id'       => $this->get_field_id( 'page_id' ),
				'exclude'  => get_option( 'page_for_posts' ),
				'selected' => $instance['page_id'],
			) );
			?>
		</p>

		<hr class="div" />

		<p>
			<input id="<?php echo $this->get_field_id( 'show_image' ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_image' ) ); ?>" value="1"<?php checked( $instance['show_image'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_image' ) ); ?>"><?php _e( 'Show Featured Image', 'genesis' ); ?></label>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_caption' ) ); ?>"><?php _e( 'Show Image Caption', 'uga-online' ); ?>:</label><br />
			<select id="<?php echo esc_attr( $this->get_field_id( 'show_caption' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_caption' ) ); ?>">
				<option value="0">- <?php _e( 'None', 'genesis' ); ?> -</option>
				<option value="1" <?php selected( '1', $instance['show_caption'] ); ?>><?php _e( 'Below Image', 'uga-online' ); ?></option>
				<option value="2" <?php selected( '2', $instance['show_caption'] ); ?>><?php _e( 'Overlay', 'uga-online' ); ?></option>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>"><?php _e( 'Image Size', 'genesis' ); ?>:</label><br />
			<select id="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>" class="genesis-image-size-selector" name="<?php echo esc_attr( $this->get_field_name( 'image_size' ) ); ?>">
				<?php
				$sizes = genesis_get_image_sizes();
				foreach ( (array) $sizes as $name => $size ) {
					echo '<option value="' . esc_attr( $name ) . '" ' . selected( $name, $instance['image_size'], false ) . '>' . esc_html( $name ) . ' (' . absint( $size['width'] ) . 'x' . absint( $size['height'] ) . ')</option>';
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_alignment' ) ); ?>"><?php _e( 'Image Alignment', 'genesis' ); ?>:</label><br />
			<select id="<?php echo esc_attr( $this->get_field_id( 'image_alignment' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_alignment' ) ); ?>">
				<option value="alignnone">- <?php _e( 'None', 'genesis' ); ?> -</option>
				<option value="alignleft" <?php selected( 'alignleft', $instance['image_alignment'] ); ?>><?php _e( 'Left', 'genesis' ); ?></option>
				<option value="alignright" <?php selected( 'alignright', $instance['image_alignment'] ); ?>><?php _e( 'Right', 'genesis' ); ?></option>
				<option value="aligncenter" <?php selected( 'aligncenter', $instance['image_alignment'] ); ?>><?php _e( 'Center', 'genesis' ); ?></option>
			</select>
		</p>

		<hr class="div" />

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_title' ) ); ?>" value="1"<?php checked( $instance['show_title'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>"><?php _e( 'Show Page Title', 'genesis' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>"><?php _e( 'Show Page Content', 'uga-online' ); ?>:</label><br />
			<select id="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_content' ) ); ?>">
				<option value="0">- <?php _e( 'None', 'genesis' ); ?> -</option>
				<option value="1" <?php selected( '1', $instance['show_content'] ); ?>><?php _e( 'Limited Content', 'uga-online' ); ?></option>
				<option value="2" <?php selected( '2', $instance['show_content'] ); ?>><?php _e( 'Excerpt', 'uga-online' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'content_limit' ) ); ?>"><?php _e( 'Content Character Limit', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'content_limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content_limit' ) ); ?>" value="<?php echo esc_attr( $instance['content_limit'] ); ?>" size="3" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'more_text' ) ); ?>"><?php _e( 'More Text', 'genesis' ); ?>:</label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'more_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'more_text' ) ); ?>" value="<?php echo esc_attr( $instance['more_text'] ); ?>" />
		</p>
		<?php

	}

}