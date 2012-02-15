<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * @package  	PyroCMS
 * @subpackage  Categories
 * @category  	Module
 */
class Admin extends Admin_Controller {
    const PORTFOLIO_IMAGE_FOLDER_SLUG = 'portfolio';

    /**
     * The id of post
     * @access protected
     * @var int
     */
    protected $id = 0;

    /**
     * Array that contains the validation rules
     * @access protected
     * @var array
     */
    protected $validation_rules = array(
        array(
            'field' => 'title',
            'label' => 'lang:portfolio_title_label',
            'rules' => 'trim|htmlspecialchars|required|max_length[100]|callback__check_title'
        ),
        array(
            'field' => 'slug',
            'label' => 'lang:portfolio_slug_label',
            'rules' => 'trim|required|alpha_dot_dash|max_length[100]|callback__check_slug'
        ),
        array(
            'field' => 'client_id',
            'label' => 'lang:portfolio_client_label',
            'rules' => 'trim|numeric'
        ),
        array(
            'field' => 'thumbnail_id',
            'label' => 'lang:portfolio_thumbnail_label',
            'rules' => 'trim'
        ),
        array(
            'field' => 'intro',
            'label' => 'lang:portfolio_intro_label',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'body',
            'label' => 'lang:portfolio_content_label',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'status',
            'label' => 'lang:portfolio_status_label',
            'rules' => 'trim|alpha'
        ),
        array(
            'field' => 'created_on',
            'label' => 'lang:portfolio_date_label',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'created_on_hour',
            'label' => 'lang:portfolio_created_hour',
            'rules' => 'trim|numeric|required'
        ),
        array(
            'field' => 'created_on_minute',
            'label' => 'lang:portfolio_created_minute',
            'rules' => 'trim|numeric|required'
        ),
    );

    /**
     * The constructor
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $this->load->model('portfolio_m');
        $this->load->model('portfolio_clients_m');
        $this->load->model('portfolio_categories_m');
        $this->load->model('portfolio_portfolio_categories_m');
        $this->load->model('portfolio_images_m');
        $this->lang->load('portfolio');
        $this->lang->load('clients');
        $this->lang->load('categories');

        // Date ranges for select boxes
        $this->data->hours = array_combine($hours = range(0, 23), $hours);
        $this->data->minutes = array_combine($minutes = range(0, 59), $minutes);

        $this->data->clients = array();
        if ($clients = $this->portfolio_clients_m->order_by('title')->get_all()) {
            foreach ($clients as $client) {
                $this->data->clients[$client->id] = $client->title;
            }
        }

        $this->data->portfolio_images = $this->portfolio_images_m->getImagesByFolderSlug(self::PORTFOLIO_IMAGE_FOLDER_SLUG);

        $this->data->categories = array();
        if ($categories = $this->portfolio_categories_m->order_by('title')->get_all()) {
            foreach ($categories as $category) {
                $this->data->categories[$category->id] = $category->title;
            }
        }

        $this->template
                ->append_metadata(css('portfolio.css', 'portfolio'))
                ->set_partial('shortcuts', 'admin/partials/shortcuts');
    }

    /**
     * Show all created portfolio items
     * @access public
     * @return void
     */
    public function index() {
        //set the base/default where clause
        $base_where = array('show_future' => TRUE, 'status' => 'all');

        //add post values to base_where if f_module is posted
        $base_where = $this->input->post('f_client') ? $base_where + array('client' => $this->input->post('f_client')) : $base_where;

        $base_where['status'] = $this->input->post('f_status') ? $this->input->post('f_status') : $base_where['status'];

        $base_where = $this->input->post('f_keywords') ? $base_where + array('keywords' => $this->input->post('f_keywords')) : $base_where;

        // Create pagination links
        $total_rows = $this->portfolio_m->count_by($base_where);
        $pagination = create_pagination('admin/portfolio/index', $total_rows);

        // Using this data, get the relevant results               		
        $portfolio = $this->portfolio_m->limit($pagination['limit'])->get_many_by($base_where);

        foreach ($portfolio as &$item) {
            $item->author = $this->ion_auth->get_user($item->author_id);
        }

        //do we need to unset the layout because the request is ajax?
        $this->input->is_ajax_request() ? $this->template->set_layout(FALSE) : '';

        $this->template
                ->title($this->module_details['name'])
                ->set_partial('filters', 'admin/partials/filters')
                ->append_metadata(js('admin/filter.js'))
                ->set('pagination', $pagination)
                ->set('portfolio', $portfolio)
                ->build('admin/index', $this->data);
    }

    /**
     * Create new post
     * @access public
     * @return void
     */
    public function create() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules($this->validation_rules);

        if ($this->input->post('created_on')) {
            $created_on = strtotime(sprintf('%s %s:%s', $this->input->post('created_on'), $this->input->post('created_on_hour'), $this->input->post('created_on_minute')));
        } else {
            $created_on = now();
        }

        if ($this->form_validation->run()) {
            // They are trying to put this live
            if ($this->input->post('status') == 'live') {
                role_or_die('portfolio', 'put_live');
            }

            $id = $this->portfolio_m->insert(array(
                'title' => $this->input->post('title'),
                'slug' => $this->input->post('slug'),
                'client_id' => $this->input->post('client_id'),
                'intro' => $this->input->post('intro'),
                'body' => $this->input->post('body'),
                'status' => $this->input->post('status'),
                'created_on' => $created_on,
                'author_id' => $this->user->id,
                'thumbnail_id' => $this->input->post('thumbnail_id')
                    ));

            if ($id) {
                // Insert any portfolio portfolio categories
                if ($this->input->post('category_id')) {
                    $this->portfolio_portfolio_categories_m->delete_by('portfolio_id', $id);
                    foreach ($this->input->post('category_id') as $category_id) {
                        $this->portfolio_portfolio_categories_m->insert(array(
                            'portfolio_id' => $id,
                            'category_id' => $category_id
                        ));
                    }
                }

                $this->pyrocache->delete_all('portfolio_m');
                $this->session->set_flashdata('success', sprintf($this->lang->line('portfolio_item_add_success'), $this->input->post('title')));
            } else {
                $this->session->set_flashdata('error', $this->lang->line('portfolio_item_add_error'));
            }

            // Redirect back to the form or main page
            $this->input->post('btnAction') == 'save_exit' ? redirect('admin/portfolio') : redirect('admin/portfolio/edit/' . $id);
        } else {
            // Go through all the known fields and get the post values
            foreach ($this->validation_rules as $key => $field) {
                $item->$field['field'] = set_value($field['field']);
            }
            $item->created_on = $created_on;
        }

        $this->template
                ->title($this->module_details['name'], lang('portfolio_create_title'))
                ->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
                ->append_metadata(js('portfolio_form.js', 'portfolio'))
                ->set('item', $item)
                ->build('admin/form');
    }

    /**
     * Edit portfolio item
     * @access public
     * @param int $id the ID of the portfolio item to edit
     * @return void
     */
    public function edit($id = 0) {
        $id OR redirect('admin/portfolio');

        $this->load->library('form_validation');

        $this->form_validation->set_rules($this->validation_rules);

        $item = $this->portfolio_m->get($id);
        $item->author = $this->ion_auth->get_user($item->author_id);

        $item->categories = $this->portfolio_portfolio_categories_m->getPortfolioItemCategoryIds($id);

        // If we have a useful date, use it
        if ($this->input->post('created_on')) {
            $created_on = strtotime(sprintf('%s %s:%s', $this->input->post('created_on'), $this->input->post('created_on_hour'), $this->input->post('created_on_minute')));
        } else {
            $created_on = $item->created_on;
        }

        $this->id = $item->id;

        if ($this->form_validation->run()) {
            // They are trying to put this live
            if ($item->status != 'live' and $this->input->post('status') == 'live') {
                role_or_die('portfolio', 'put_live');
            }

            $author_id = empty($post->author) ? $this->user->id : $post->author_id;

            $result = $this->portfolio_m->update($id, array(
                'title' => $this->input->post('title'),
                'slug' => $this->input->post('slug'),
                'client_id' => $this->input->post('client_id'),
                'intro' => $this->input->post('intro'),
                'body' => $this->input->post('body'),
                'status' => $this->input->post('status'),
                'created_on' => $created_on,
                'author_id' => $author_id,
                'thumbnail_id' => $this->input->post('thumbnail_id'),
                    ));

            if ($result) {

                // Insert any portfolio portfolio categories
                if ($this->input->post('category_id')) {
                    $this->portfolio_portfolio_categories_m->delete_by('portfolio_id', $id);
                    foreach ($this->input->post('category_id') as $category_id) {
                        $this->portfolio_portfolio_categories_m->insert(array(
                            'portfolio_id' => $id,
                            'category_id' => $category_id
                        ));
                    }
                }
                $this->session->set_flashdata(array('success' => sprintf($this->lang->line('portfolio_edit_success'), $this->input->post('title'))));

                // The twitter module is here, and enabled!
//				if ($this->settings->item('twitter_blog') == 1 && ($item->status != 'live' && $this->input->post('status') == 'live'))
//				{
//					$url = shorten_url('portfolio/'.$date[2].'/'.str_pad($date[1], 2, '0', STR_PAD_LEFT).'/'.url_title($this->input->post('title')));
//					$this->load->model('twitter/twitter_m');
//					if ( ! $this->twitter_m->update(sprintf($this->lang->line('portfolio_twitter_posted'), $this->input->post('title'), $url)))
//					{
//						$this->session->set_flashdata('error', lang('portfoliotwitter_error') . ": " . $this->twitter->last_error['error']);
//					}
//				}
            } else {
                $this->session->set_flashdata(array('error' => $this->lang->line('portfolio_edit_error')));
            }

            // Redirect back to the form or main page
            $this->input->post('btnAction') == 'save_exit' ? redirect('admin/portfolio') : redirect('admin/portfolio/edit/' . $id);
        }

        // Go through all the known fields and get the post values
        foreach (array_keys($this->validation_rules) as $field) {
            if (isset($_POST[$field])) {
                $item->$field = $this->form_validation->$field;
            }
        }

        $item->created_on = $created_on;

        // Load WYSIWYG editor
        $this->template
                ->title($this->module_details['name'], sprintf(lang('portfolio_edit_title'), $item->title))
                ->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
                ->append_metadata(js('portfolio_form.js', 'portfolio'))
                ->set('item', $item)
                ->build('admin/form');
    }

    /**
     * Preview portfolio item
     * @access public
     * @param int $id the ID of the portfolio item to preview
     * @return void
     */
    public function preview($id = 0) {
        $item = $this->portfolio_m->get($id);

        $this->template
                ->set_layout('modal', 'admin')
                ->set('item', $item)
                ->build('admin/preview');
    }

    /**
     * Helper method to determine what to do with selected items from form post
     * @access public
     * @return void
     */
    public function action() {
        switch ($this->input->post('btnAction')) {
            case 'publish':
                role_or_die('portfolio', 'put_live');
                $this->publish();
                break;

            case 'delete':
                role_or_die('portfolio', 'delete_live');
                $this->delete();
                break;

            default:
                redirect('admin/portfolio');
                break;
        }
    }

    /**
     * Publish portfolio item
     * @access public
     * @param int $id the ID of the portfolio item to make public
     * @return void
     */
    public function publish($id = 0) {
        role_or_die('portfolio', 'put_live');

        // Publish one
        $ids = ($id) ? array($id) : $this->input->post('action_to');

        if (!empty($ids)) {
            // Go through the array of slugs to publish
            $item_titles = array();
            foreach ($ids as $id) {
                // Get the current page so we can grab the id too
                if ($item = $this->portfolio_m->get($id)) {
                    $this->portfolio_m->publish($id);

                    // Wipe cache for this model, the content has changed
                    $this->pyrocache->delete('portfolio_m');
                    $item_titles[] = $item->title;
                }
            }
        }

        // Some posts have been published
        if (!empty($item_titles)) {
            // Only publishing one post
            if (count($item_titles) == 1) {
                $this->session->set_flashdata('success', sprintf($this->lang->line('portfolio_publish_success'), $item_titles[0]));
            }
            // Publishing multiple posts
            else {
                $this->session->set_flashdata('success', sprintf($this->lang->line('portfolio_mass_publish_success'), implode('", "', $item_titles)));
            }
        }
        // For some reason, none of them were published
        else {
            $this->session->set_flashdata('notice', $this->lang->line('portfolio_publish_error'));
        }

        redirect('admin/portfolio');
    }

    /**
     * Delete portfolio item
     * @access public
     * @param int $id the ID of the portfolio item to delete
     * @return void
     */
    public function delete($id = 0) {
        // Delete one
        $ids = ($id) ? array($id) : $this->input->post('action_to');

        // Go through the array of slugs to delete
        if (!empty($ids)) {
            $item_titles = array();
            foreach ($ids as $id) {
                // Get the current page so we can grab the id too
                if ($item = $this->portfolio_m->get($id)) {
                    $this->portfolio_m->delete($id);

                    // Delete any portfolio portfolio categories
                    $this->portfolio_portfolio_categories_m->delete_by('portfolio_id', $id);

                    // Wipe cache for this model, the content has changed
                    $this->pyrocache->delete('portfolio_m');
                    $item_titles[] = $item->title;
                }
            }
        }

        // Some pages have been deleted
        if (!empty($item_titles)) {
            // Only deleting one page
            if (count($item_titles) == 1) {
                $this->session->set_flashdata('success', sprintf($this->lang->line('portfolio_delete_success'), $item_titles[0]));
            }
            // Deleting multiple pages
            else {
                $this->session->set_flashdata('success', sprintf($this->lang->line('portfolio_mass_delete_success'), implode('", "', $item_titles)));
            }
        }
        // For some reason, none of them were deleted
        else {
            $this->session->set_flashdata('notice', lang('portfolio_delete_error'));
        }

        redirect('admin/portfolio');
    }

    /**
     * Callback method that checks the title of an item
     * @access public
     * @param string title The Title to check
     * @return bool
     */
    public function _check_title($title = '') {
        if (!$this->portfolio_m->check_exists('title', $title, $this->id)) {
            $this->form_validation->set_message('_check_title', sprintf(lang('portfolio_already_exist_error'), lang('portfolio_title_label')));
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Callback method that checks the slug of an item
     * @access public
     * @param string slug The Slug to check
     * @return bool
     */
    public function _check_slug($slug = '') {
        if (!$this->portfolio_m->check_exists('slug', $slug, $this->id)) {
            $this->form_validation->set_message('_check_slug', sprintf(lang('portfolio_already_exist_error'), lang('portfolio_slug_label')));
            return FALSE;
        }

        return TRUE;
    }

    /**
     * method to fetch filtered results for portfolio list
     * @access public
     * @return void
     */
    public function ajax_filter() {
        $client = $this->input->post('f_client');
        $status = $this->input->post('f_status');
        $keywords = $this->input->post('f_keywords');

        $post_data = array();

        if ($status == 'live' OR $status == 'draft') {
            $post_data['status'] = $status;
        }

        if ($client != 0) {
            $post_data['client_id'] = $client;
        }

        //keywords, lets explode them out if they exist
        if ($keywords) {
            $post_data['keywords'] = $keywords;
        }
        $results = $this->portfolio_m->search($post_data);

        //set the layout to false and load the view
        $this->template
                ->set_layout(FALSE)
                ->set('portfolio', $results)
                ->build('admin/index');
    }

}
