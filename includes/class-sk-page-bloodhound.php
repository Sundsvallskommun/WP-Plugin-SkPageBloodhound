<?php
/**
 *
 * Builds a hierarchical list of pages.
 * The list is stored in a transient for seven days or updated on post save
 *
 * A searchable functionality of parent pages is added on post type = "page"
 * The bloodhound shows a full trail to the top level page (if page hit is a child)
 *
 *
 *
 *
 * @since 1.0.0
 */

class SK_Page_Bloodhound {

    const PAGE_TRANSIENT = "sk-bloodhound-page-list";

    private $m_list_markup = null;
	private $m_plugin_base_url = null;

    public function __construct() {
        global $pagenow;
        if (is_admin()) {

            add_action('wp_ajax_ajax_fetch_page_list', array($this, 'ajax_fetch_page_list'));

            if ( $pagenow === 'post.php' || $pagenow === 'post-new.php') {
                add_action('admin_enqueue_scripts', array($this, 'add_admin_assets'));
            }

			$this->m_plugin_base_url = dirname(plugin_dir_url(__FILE__));
        }

        add_action( 'save_post', array($this, 'update_page_list'), 10, 2);
    }


    /**
     *
     * Retrieves the page list, building it if necessary
     * Use this function as far as you can
     */
    public function get_page_list() {

        $this->m_list_markup = get_transient(SK_Page_Bloodhound::PAGE_TRANSIENT);
        if ($this->m_list_markup === false) {
            $this->build_page_list();
        }

        return $this->m_list_markup;
    }


    /**
     *
     * Updates the page list
     */
    public function update_page_list($post_id, $post) {

        if ($post->post_type === "page") {
            $this->build_page_list();
        }

    }


    /**
     *
     * Creates a hierarchical list based on WP pages
     * The result is stored in a transient
     *
     * @return String HTML markup representing the list
     */
    private function build_page_list() {

        require_once 'class-sk-bloodhound-page-walker.php';

        $defaults = array(
            'depth'        => 0,
            'show_date'    => '',
            'date_format'  => get_option( 'date_format' ),
            'child_of'     => 0,
            'exclude'      => '',
            'title_li'     => '',
            'echo'         => false,
            'authors'      => '',
            'sort_column'  => 'menu_order, post_title',
            'link_before'  => '',
            'link_after'   => '',
            'item_spacing' => 'preserve',
            'walker'       => new SK_Bloodhound_Page_Walker(),
        );

        $list = '<ul id="bloodhound-pages" class="level-0">';
        $list .= wp_list_pages($defaults);
        $list .= '</ul>';

        // Store for one week, save hook will trigger renewal anyway
        set_transient(SK_Page_Bloodhound::PAGE_TRANSIENT, $list, 7 * DAY_IN_SECONDS);

        $this->m_list_markup = $list;

    }

    public function add_admin_assets() {

        global $post;

        wp_enqueue_script( 'page-bloodhound-script',
            $this->m_plugin_base_url . '/admin/js/sk-page-bloodhound-admin.js',
            array('jquery'), false, true);


	    wp_localize_script(
	        'page-bloodhound-script', 'bloodhound_ajax_object',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'action' => 'ajax_fetch_page_list',
                'current_post' => $post->ID
            )
        );


        wp_enqueue_style( 'page-bloodhound-style',
            $this->m_plugin_base_url . '/admin/css/sk-page-bloodhound-admin.css');

    }

    /**
     *
     * Endpoint for retrieving the page list and parent page title (if any)
     */
    public function ajax_fetch_page_list() {

        try {

            $parent_id =  wp_get_post_parent_id($_POST['current_post']);
            $parent_post_title = '';
            if ($parent_id !== false) {

                $parent_post_title = get_the_title($parent_id);
            }
            $response['list'] = $this->get_page_list();
            $response['parent_title'] = $parent_post_title;

            wp_send_json( $response );
        }
        catch (\Exception $e) {

            error_log("Could not determine if page has parent. Error message: " . $e->getMessage());

            $response['list'] = "Ett fel uppstod: " . $e->getMessage();
            $response['parent_title'];
            wp_send_json( $response );
        }
    }

}
