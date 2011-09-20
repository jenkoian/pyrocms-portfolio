<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Portfolio extends Public_Controller {

    public $limit = 5; // TODO: PS - Make me a settings option

    public function __construct() {
        parent::Public_Controller();
        $this->load->model('portfolio_m');
        $this->load->model('portfolio_clients_m');
        $this->load->model('portfolio_categories_m');
        $this->load->model('portfolio_portfolio_categories_m');
        $this->load->helper('text');
        $this->lang->load('portfolio');
    }

    // portfolio/page/x also routes here
    public function index() {
        $this->data->pagination = create_pagination('portfolio/page', $this->portfolio_m->count_by(array('status' => 'live')), NULL, 3);
        $this->data->portfolio = $this->portfolio_m->limit($this->data->pagination['limit'])->get_many_by(array('status' => 'live'));

        // Set meta description based on item titles
        $meta = $this->_items_metadata($this->data->portfolio);

        $this->template
                ->title($this->module_details['name'])
                ->set_breadcrumb(lang('portfolio_portfolio_title'))
                ->set_metadata('description', $meta['description'])
                ->set_metadata('keywords', $meta['keywords'])
                ->build('index', $this->data);
    }

    /**     
     * @param string $slug 
     */
    public function client($slug = '') {
        $slug OR redirect('portfolio');

        // Get client data
        $client = $this->portfolio_clients_m->get_by('slug', $slug) OR show_404();

        // Count total portfolio items and work out how many pages exist
        $pagination = create_pagination('portfolio/client/' . $slug, $this->portfolio_m->count_by(array(
                    'client' => $slug,
                    'status' => 'live'
                )), NULL, 4);

        // Get the current page of portfolio items
        $portfolio = $this->portfolio_m->limit($pagination['limit'])->get_many_by(array(
            'client' => $slug,
            'status' => 'live'
        ));

        // Set meta description based on item titles
        $meta = $this->_items_metadata($portfolio);

        // Build the page
        $this->template->title($this->module_details['name'], $client->title)
                ->set_metadata('description', $client->title . '. ' . $meta['description'])
                ->set_metadata('keywords', $client->title)
                ->set_breadcrumb(lang('portfolio_portfolio_title'), 'portfolio')
                ->set_breadcrumb($client->title)
                ->set('portfolio', $portfolio)
                ->set('client', $client)
                ->set('pagination', $pagination)
                ->build('client', $this->data);
    }

    /**     
     * @param string $slug 
     */
    public function category($slug = '') {
        $slug OR redirect('portfolio');

        // Get client data
        $category = $this->portfolio_categories_m->get_by('slug', $slug) OR show_404();

        // Count total portfolio items and work out how many pages exist
        $pagination = create_pagination('portfolio/category/' . $slug, $this->portfolio_m->count_by(array(
                    'category' => $slug,
                    'status' => 'live'
                )), NULL, 4);

        // Get portfolio items for the category
        $portfolioIds = $this->portfolio_portfolio_categories_m->getCategoryPortfolioIds($category->id);

        $portfolio = array();
        if (!empty($portfolioIds)) {
            $portfolio = $this->portfolio_m->limit($pagination['limit'])->get_many_by(array(
                'portfolioIds' => $portfolioIds,
                'status' => 'live'
                    ));
        }

        // Set meta description based on item titles
        $meta = $this->_items_metadata($portfolio);

        // Build the page
        $this->template->title($this->module_details['name'], $category->title)
                ->set_metadata('description', $category->title . '. ' . $meta['description'])
                ->set_metadata('keywords', $category->title)
                ->set_breadcrumb(lang('portfolio_portfolio_title'), 'portfolio')
                ->set_breadcrumb($category->title)
                ->set('portfolio', $portfolio)
                ->set('category', $category)
                ->set('pagination', $pagination)
                ->build('category', $this->data);
    }

    /**
     *
     * @param string $year
     * @param string $month 
     */
    public function archive($year = NULL, $month = '01') {
        $year OR $year = date('Y');
        $month_date = new DateTime($year . '-' . $month . '-01');
        $this->data->pagination = create_pagination('portfolio/archive/' . $year . '/' . $month, $this->portfolio_m->count_by(array('year' => $year, 'month' => $month)), NULL, 5);
        $this->data->portfolio = $this->portfolio_m->limit($this->data->pagination['limit'])->get_many_by(array('year' => $year, 'month' => $month));
        $this->data->month_year = format_date($month_date->format('U'), lang('portfolio_archive_date_format'));

        // Set meta description based on item titles
        $meta = $this->_items_metadata($this->data->portfolio);

        $this->template->title($this->data->month_year, $this->lang->line('portfolio_archive_title'), $this->lang->line('portfolio_portfolio_title'))
                ->set_metadata('description', $this->data->month_year . '. ' . $meta['description'])
                ->set_metadata('keywords', $this->data->month_year . ', ' . $meta['keywords'])
                ->set_breadcrumb($this->lang->line('portfolio_portfolio_title'), 'portfolio')
                ->set_breadcrumb($this->lang->line('portfolio_archive_title') . ': ' . format_date($month_date->format('U'), lang('portfolio_archive_date_format')))
                ->build('archive', $this->data);
    }

    /**     
     * @param string $slug 
     */
    public function view($slug = '') {
        if (!$slug or !$item = $this->portfolio_m->get_by('slug', $slug)) {
            redirect('portfolio');
        }

        if ($item->status != 'live' && !$this->ion_auth->is_admin()) {
            redirect('portfolio');
        }

        $item->author = $this->ion_auth->get_user($item->author_id);

        // If this item uses a client, grab it
        if ($item->client_id && ($client = $this->portfolio_clients_m->get($item->client_id))) {
            $item->client = $client;
        }

        // Set some defaults
        else {
            $item->client->id = 0;
            $item->client->slug = '';
            $item->client->title = '';
        }

        $item->categories = $this->portfolio_portfolio_categories_m->getPortfolioItemCategoryIds($item->id);

        $this->session->set_flashdata(array('referrer' => $this->uri->uri_string));

        $this->template->title($item->title, lang('portfolio_portfolio_title'))
                ->set_metadata('description', $item->intro)
                ->set_metadata('keywords', $item->client->title . ' ' . $item->title)
                ->append_metadata('<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js"></script>')
                ->append_metadata(js('fancybox/jquery.fancybox-1.3.4.js', 'portfolio'))
                ->append_metadata(js('portfolio.js', 'portfolio'))
                ->append_metadata(css('portfolio.css', 'portfolio'))
                ->append_metadata(css('fancybox.css', 'portfolio'))
                ->set_breadcrumb(lang('portfolio_portfolio_title'), 'portfolio');

        if ($item->client->id > 0) {
            $this->template->set_breadcrumb($item->client->title, 'portfolio/client/' . $item->client->slug);
        }

        $this->template
                ->set_breadcrumb($item->title)
                ->set('item', $item)
                ->build('view', $this->data);
    }

    /**
     *
     * @param array $items
     * @return array 
     */
    private function _items_metadata(&$items = array()) {
        $keywords = array();
        $description = array();

        // Loop through items and use titles for meta description
        if (!empty($items)) {
            foreach ($items as &$item) {
                if ($item->client_title) {
                    $keywords[$item->client_id] = $item->client_title . ', ' . $item->client_slug;
                }
                $description[] = $item->title;
            }
        }

        return array(
            'keywords' => implode(', ', $keywords),
            'description' => implode(', ', $description)
        );
    }

}