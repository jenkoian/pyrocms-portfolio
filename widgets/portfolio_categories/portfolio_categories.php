<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package 		PyroCMS
 * @subpackage 		Categories Menu Widget
 * @author		Ian Jenkins
 * 
 * Show a list of portfolio clients
 */
class Widget_Portfolio_categories extends Widgets {

    /**
     * @var array
     */
    public $title = array(
        'en' => 'Portfolio categories',
    );
    
    /**     
     * @var array
     */
    public $description = array(
        'en' => 'Show a list of portfolio categories',
    );
    
    /**
     * @var string
     */
    public $author = 'Ian Jenkins';
    
    /**
     * @var string
     */    
    public $version = '1.0';
    
    /**
     * @var string
     */    
    public $website = 'http://www.jenkins-web.co.uk';

    /**
     *
     * @return array
     */
    public function run() {
        $this->load->model('portfolio/portfolio_categories_m');

        $categories = $this->portfolio_categories_m->order_by('title')->get_all();

        return array('categories' => $categories);
    }

}