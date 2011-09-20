<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rss extends Public_Controller {

    public function __construct() {
        parent::Public_Controller();
        $this->load->model('portfolio_m');
        $this->load->helper('xml');
        $this->load->helper('date');
        $this->lang->load('portfolio');
    }

    /**
     * Default action
     */
    public function index() {
        $items = $this->pyrocache->model('portfolio_m', 'get_many_by', array(array(
                'status' => 'live',
                'limit' => $this->settings->item('rss_feed_items'))
                ), $this->settings->item('rss_cache'));

        $this->_build_feed($items);
        $this->data->rss->feed_name .= $this->lang->line('portfolio_rss_name_suffix');
        $this->output->set_header('Content-Type: application/rss+xml');
        $this->load->view('rss', $this->data);
    }

    /**
     *
     * @param string $slug 
     */
    function client($slug = '') {
        $this->load->model('clients/clients_m');

        if (!$client = $this->clients_m->get_by('slug', $slug)) {
            redirect('portfolio/rss/index');
        }

        $items = $this->pyrocache->model('portfolio_m', 'get_many_by', array(array(
                'status' => 'live',
                'client' => $slug,
                'limit' => $this->settings->item('rss_feed_items'))
                ), $this->settings->item('rss_cache'));

        $this->_build_feed($items);
        $this->data->rss->feed_name .= ' ' . $client->title . $this->lang->line('portfolio_rss_client_suffix');
        $this->output->set_header('Content-Type: application/rss+xml');
        $this->load->view('rss', $this->data);
    }

    /**
     *
     * @param array $items 
     */
    function _build_feed($items = array()) {
        $this->data->rss->encoding = $this->config->item('charset');
        $this->data->rss->feed_name = $this->settings->item('site_name');
        $this->data->rss->feed_url = base_url();
        $this->data->rss->page_description = sprintf($this->lang->line('portfolio_rss_items_title'), $this->settings->item('site_name'));
        $this->data->rss->page_language = 'en-gb';
        $this->data->rss->creator_email = $this->settings->item('contact_email');

        if (!empty($items)) {
            foreach ($items as $row) {
                //$row->created_on = human_to_unix($row->created_on);
                $row->link = site_url('portfolio/' . date('Y/m', $row->created_on) . '/' . $row->slug);
                $row->created_on = standard_date('DATE_RSS', $row->created_on);

                $item = array(
                    //'author' => $row->author,
                    'title' => xml_convert($row->title),
                    'link' => $row->link,
                    'guid' => $row->link,
                    'description' => $row->intro,
                    'date' => $row->created_on
                );
                $this->data->rss->items[] = (object) $item;
            }
        }
    }

}

?>
