<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Module_Portfolio extends Module {

    /**     
     * @var string
     */
    public $version = '1.0';

    /**     
     * @return array
     */
    public function info() {
        return array(
            'name' => array(
                'en' => 'Portfolio',
            ),
            'description' => array(
                'en' => 'Manage your portfolio.',
            ),
            'frontend' => TRUE,
            'backend' => TRUE,
            'skip_xss' => TRUE,
            'menu' => 'content',
            'roles' => array(
                'put_live', 'edit_live', 'delete_live'
            )
        );
    }

    /**
     * @todo Don't use separate client, have hierarchial categories.
     * @return boolean
     */
    public function install() {
        $this->dbforge->drop_table('portfolio_clients');
        $this->dbforge->drop_table('portfolio_categories');
        $this->dbforge->drop_table('portfolio');
        $this->dbforge->drop_table('portfolio_portfolio_categories');

        $portfolio_clients = "
            CREATE TABLE " . $this->db->dbprefix('portfolio_clients') . " (
              `id` int(11) NOT NULL auto_increment,
              `slug` varchar(20) collate utf8_unicode_ci NOT NULL default '',
              `title` varchar(20) collate utf8_unicode_ci NOT NULL default '',
              PRIMARY KEY  (`id`),
              UNIQUE KEY `slug - unique` (`slug`),
              UNIQUE KEY `title - unique` (`title`),
              KEY `slug - normal` (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Portfolio Clients.';
        ";

        $portfolio_categories = "
            CREATE TABLE " . $this->db->dbprefix('portfolio_categories') . " (
              `id` int(11) NOT NULL auto_increment,
              `slug` varchar(20) collate utf8_unicode_ci NOT NULL default '',
              `title` varchar(20) collate utf8_unicode_ci NOT NULL default '',
              PRIMARY KEY  (`id`),
              UNIQUE KEY `slug - unique` (`slug`),
              UNIQUE KEY `title - unique` (`title`),
              KEY `slug - normal` (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Portfolio Categories.';
        ";

        $portfolio = "
            CREATE TABLE " . $this->db->dbprefix('portfolio') . " (
              `id` int(11) NOT NULL auto_increment,
              `title` varchar(100) collate utf8_unicode_ci NOT NULL default '',
              `slug` varchar(100) collate utf8_unicode_ci NOT NULL default '',
              `client_id` int(11) NOT NULL,
              `thumbnail_id` int(11) DEFAULT NULL,
              `intro` text collate utf8_unicode_ci NOT NULL,
              `body` text collate utf8_unicode_ci NOT NULL,
              `author_id` int(11) NOT NULL default '0',                            
              `created_on` int(11) NOT NULL,
              `updated_on` int(11) NOT NULL default 0,                          
              `status` enum('draft','live') collate utf8_unicode_ci NOT NULL default 'draft',
              PRIMARY KEY  (`id`),
              UNIQUE KEY `title` (`title`),
              KEY `client_id - normal` (`client_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Portfolio items.';
        ";

        $portfolio_portfolio_categories = "
            CREATE TABLE " . $this->db->dbprefix('portfolio_portfolio_categories') . " (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `portfolio_id` int(11) NOT NULL,
              `category_id` int(11) NOT NULL,
              `order` int(11) DEFAULT '0',
              PRIMARY KEY (`id`),
                          KEY `portfolio_id` (`portfolio_id`),        
                          KEY `category_id` (`category_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ";

        if ($this->db->query($portfolio_categories) &&
                $this->db->query($portfolio) &&
                $this->db->query($portfolio_clients) &&
                $this->db->query($portfolio_portfolio_categories)) {
            return TRUE;
        }
    }

    /**
     *
     * @return boolean
     */
    public function uninstall() {
        if ($this->dbforge->drop_table($this->db->dbprefix('portfolio')) &&
                $this->dbforge->drop_table($this->db->dbprefix('portfolio_categories')) &&
                $this->dbforge->drop_table($this->db->dbprefix('portfolio_clients')) &&
                $this->dbforge->drop_table($this->db->dbprefix('portfolio_portfolio_categories'))) {
            return TRUE;
        }
    }

    /**
     * @todo this
     * @param string $old_version
     * @return boolean
     */
    public function upgrade($old_version) {
        // Your Upgrade Logic
        return TRUE;
    }

    /**
     * @todo this
     * @return boolean
     */
    public function help() {
        /**
         * Either return a string containing help info
         * return "Some help info";
         *
         * Or add a language/help_lang.php file and
         * return TRUE;
         *
         * help_lang.php contents
         * $lang['help_body'] = "Some help info";
         */
        return TRUE;
    }

}

/* End of file details.php */
