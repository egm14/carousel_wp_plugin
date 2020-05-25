<?php
		/*
		Plugin Name: Carousel - MobileOneContainers
		Description: Display a carousel list of containers product 
		Author: Edwin Marte | Ntono Digital
		Author URI: http://ntono.com
		Version: 1.0
		*/

	/*---------------- Adding new metabox Image to post type ---------------------*/
	//Meta Boxes
   function listing_image_add_metabox () {
		add_meta_box( 'listingimagediv', __( 'Image Containers Loop', 'text-domain' ), 'listing_image_metabox', 'product', 'side', 'low');
	}

	add_action( 'add_meta_boxes', 'listing_image_add_metabox' );

	function listing_image_metabox ( $post, $page ) {
	global $content_width, $_wp_additional_image_sizes;
	$image_id = get_post_meta( $post->ID, '_listing_image_id', true );
	$old_content_width = $content_width;
	$content_width = 254;
	if ( $image_id && get_post( $image_id ) ) {
		if ( ! isset( $_wp_additional_image_sizes['post-thumbnails'] ) ) {
			$thumbnail_html = wp_get_attachment_image( $image_id, array( $content_width, $content_width ) );
		} else {
			$thumbnail_html = wp_get_attachment_image( $image_id, 'post-thumbnails' );
		}
		if ( ! empty( $thumbnail_html ) ) {
			$content = $thumbnail_html;
			$content .= '<p class="hide-if-no-js"><a href="javascript:;" id="remove_listing_image_button" >' . esc_html__( 'Remove container image', 'text-domain' ) . '</a></p>';
			$content .= '<input type="hidden" id="upload_listing_image" name="_listing_cover_image" value="' . esc_attr( $image_id ) . '" />';
		}
		$content_width = $old_content_width;
	} else {
				$content = '<img src="" style="width:' . esc_attr( $content_width ) . 'px;height:auto;border:0;display:none;" />';
				$content .= '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Container image PNG', 'text-domain' ) . '" href="javascript:;" id="upload_listing_image_button" id="set-listing-image" data-uploader_title="' . esc_attr__( 'Choose an image', 'text-domain' ) . '" data-uploader_button_text="' . esc_attr__( 'Container image PNG', 'text-domain' ) . '">' . esc_html__( 'Container image PNG', 'text-domain' ) . '</a></p>';
				$content .= '<input type="hidden" id="upload_listing_image" name="_listing_cover_image" value="" />';
			}
			echo $content;
		}
		
		function listing_image_save ( $post_id ) {
			if( isset( $_POST['_listing_cover_image'] ) ) {
				$image_id = (int) $_POST['_listing_cover_image'];
				update_post_meta( $post_id, '_listing_image_id', $image_id );
			}
		}
		add_action( 'save_post', 'listing_image_save', 10, 1 );

		function listing_image_get_meta ($value) {
			global $post;

			$image_id = get_post_meta ($post->ID, $value, true);

				if (!empty ($image_id)) {
				return is_array ($image_id) ? stripslashes_deep ($image_id) : stripslashes (wp_kses_decode_entities ($image_id));
				} else {
				return false;
				}
		}


	/*---------------- Add new widget and sidebar ---------------------*/
	// Register and load the widget
	function wpb_carousel_widget() {
	    register_widget( 'wpb_widget' );
	}
	add_action( 'widgets_init', 'wpb_carousel_widget' );
	 
	// Creating the widget 
	class wpb_widget extends WP_Widget {
	 
		function __construct() {
		parent::__construct(
		 
			// Base ID of your widget
			'wpb_widget', 
			 
			// Widget name will appear in UI
			__('Carousel containers Product', 'wpb_widget_domain'), 
			 
			// Widget description
			array( 'description' => __( 'Add this widget at Carousel Sidebar area to use on the header or other website place.', 'wpb_widget_domain' ), ) 
		);

		//ADding script
		wp_enqueue_script('Carousel-mobileOneContainers-swiper.min', plugin_dir_url( __FILE__ ) . '/assets/js/swiper.min.js',array('jquery'),'',true);
		wp_enqueue_script('Carousel-mobileOneContainers-controller', plugin_dir_url( __FILE__ ) . '/assets/js/controller.js',array('jquery'),'',true);

		//Adding style
		wp_enqueue_style('Carousel-mobileOneContainers-swiper.min', plugin_dir_url( __FILE__ ) . '/assets/css/swiper.min.css',false, '4.5.0', 'all');
		wp_enqueue_style('Carousel-mobileOneContainers-carouselStyle', plugin_dir_url( __FILE__ ) . '/assets/css/carouselStyle.css',false, '4.5.0', 'all');
	}
	 
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		 
		// before and after widget arguments are defined by themes
		//echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];
		 
		// This is where you run the code and display the output
		//echo __( 'Hello, World!', 'wpb_widget_domain' );
		echo $args['after_widget'];
		//echo "Esto es una prueba";

		//Adding content to present on the sidebar - Carousel
		$args = array( 'post_type' => 'product', 'posts_per_page' => -1 , 'orderby' => 'date',
    'order' => 'ASC');
		$loop = new WP_Query( $args );
		echo' <!-- Swiper -->
		  <div id="carousel-containers" class="swiper-container swiper-container-initialized swiper-container-horizontal">
		    <div class="swiper-wrapper">';
 				while ( $loop->have_posts() ) : $loop->the_post();
				//while ( $loop->have_posts() ) : $loop->the_post();
			      echo '<div class="swiper-slide">';
			      		echo '<div class="entry-content">
			      		<div class="title-carousel">';
			      		echo '<a class="menu-container-title" href="'.get_post_permalink().'">'.get_the_title().'</a>'.
			      		'</div>';
			      		//var_dump($post_id);
			      		//$campos_container = get_post_custom($post_id);
			            $containerImage = wp_get_attachment_image_src(listing_image_get_meta('_listing_image_id'), 'Menu-container-150x90');
			                echo '<!--Post Image --><div class="contain-carousel">';
			            if(isset($containerImage[0])) {
			                 echo '<a class="menu-container-image" href="'.get_post_permalink().'"><img data-src="'.$containerImage[0].'" alt="" class="swiper-lazy" ><div class="swiper-lazy-preloader"></div></a>'.'<br />';   
			            } else{
			            	 echo '<a class="menu-container-image" href="'.get_post_permalink().'"><img data-src="'.get_stylesheet_directory_uri().'/assets/media/contianer-cover-moc-gray.png" alt="" class="swiper-lazy" ><div class="swiper-lazy-preloader"></div></a>'.'<br />';
			            }
			            echo '</div><!-- /Post Image -->';
					 // the_content();
					 echo '</div>';
			      echo '</div>';
		      endwhile;
			 wp_reset_postdata();
		    echo '</div>
		    <!-- Add Pagination -->
		    <div class="swiper-pagination"></div>
		  </div>';
	}
	         
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
		$title = $instance[ 'title' ];
	}
	else {
		$title = __( 'New title', 'wpb_widget_domain' );
	}
	// Widget admin form
	?>
	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		<span>Carousel container present on the header banners.</span>
	</p>
	<?php 
	}
	     
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	return $instance;
	}
	} // Class wpb_widget ends here

	//Creating new sibebar widget 
	if (!function_exists( 'carousel_sidebar' ) ) {
	// Register Sidebars

	add_action( 'widgets_init', 'others_sidebar' );

	function others_sidebar() {
		register_sidebar( array(
			'name'          => __( 'Carousel-Sidebard', 'moc' ),
			'id'            => 'Carousel-Sidebard',
			'description'   => __( 'This sitebard was create to use the carousel widget', 'moc' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
			)
		);
	}
	}

?>