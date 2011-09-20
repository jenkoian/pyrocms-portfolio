<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Categories model
 *
 * @package     PyroCMS
 * @subpackage	Portfolio Module
 * @category	Modules
 * @author	Ian Jenkins
 */
class Portfolio_portfolio_categories_m extends MY_Model {

    /**
     * Insert a new client into the database via ajax
     * @access public
     * @param array $input The data to insert
     * @return int
     */
    public function insert_ajax($input = array()) {
        return parent::insert($input);
    }

    /**
     * Return an array of categories id's from a portfolio id
     * 
     * @param int $portfolioId
     * @return array 
     */
    public function getPortfolioItemCategoryIds($portfolioId) {
        $ppCats = parent::get_many_by('portfolio_id', $portfolioId);

        $catIds = array();
        foreach ($ppCats as $ppCat) {
            $catIds[] = $ppCat->category_id;
        }

        return $catIds;
    }

    /**
     *
     * @param int $categoryId
     * @return array 
     */
    public function getCategoryPortfolioIds($categoryId) {
        $catPortfolioIds = parent::get_many_by('category_id', $categoryId);

        $portfolioIds = array();
        foreach ($catPortfolioIds as $catPortfolioId) {
            $portfolioIds[] = $catPortfolioId->portfolio_id;
        }

        return $portfolioIds;
    }

}