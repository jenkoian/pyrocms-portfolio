<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package 		PyroCMS
 * @subpackage 		Latest portfolio items Widget
 * @author		Ian Jenkins
 *
 * Show Latest portfolio items in your site with a widget. Intended for use on cms pages
 *
 * Usage : on a CMS page add {widget_area('name_of_area')}
 * where 'name_of_area' is the name of the widget area you created in the admin control panel
 */
class Widget_Latest_items extends Widgets {

    /**
     * @var array
     */
    public $title = array(
        'en' => 'Latest portfolio items',
    );
    
    /**
     * @var array
     */
    public $description = array(
        'en' => 'Display latest portfolio items with a widget',
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
     * build form fields for the backend
     * MUST match the field name declared in the form.php file
     * @var array
     */
    public $fields = array(
        array(
            'field' => 'limit',
            'label' => 'Number of items',
        )
    );

    /**     
     * @param array $options
     * @return array
     */
    public function form($options) {
        !empty($options['limit']) OR $options['limit'] = 5;

        return array(
            'options' => $options
        );
    }

    /**     
     * @param array $options
     * @return array 
     */
    public function run($options) {
        // load the portfolio module's model
        class_exists('Portfolio_m') OR $this->load->model('portfolio/portfolio_m');

        // sets default number of items to be shown
        empty($options['limit']) AND $options['limit'] = 5;

        // retrieve the records using the portfolio module's model
        $portfolio_widget = $this->portfolio_m->limit($options['limit'])->get_many_by(array('status' => 'live'));

        // returns the variables to be used within the widget's view
        return array('portfolio_widget' => $portfolio_widget);
    }

}