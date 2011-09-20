<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Portfolio archive widget - based on blog archive widget
 * @package 		PyroCMS
 * @author		Ian Jenkins
 */
class Widget_Archive extends Widgets {

    public $title = array(
        'en' => 'Archive',
        'pt' => 'Arquivo do Portfolio'
    );
    public $description = array(
        'en' => 'Display a list of old months with links to items in those months',
        'pt' => 'Mostra uma lista navegação cronológica contendo o índice dos artigos publicados mensalmente'
    );
    public $author = 'Ian Jenkins';
    public $website = 'http://www.jenkins-web.co.uk/';
    public $version = '1.0';

    public function run($options) {
        $this->load->model('portfolio/portfolio_m');
        $this->lang->load('portfolio/portfolio');

        return array(
            'archive_months' => $this->portfolio_m->get_archive_months()
        );
    }

}