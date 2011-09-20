<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Portfolio Plugin
 *
 * Create lists of portfolio items
 *
 * @package		PyroCMS
 * @author		Ian Jenkins
 *
 */
class Plugin_Portfolio extends Plugin {

    /**
     * Portfolio List
     *
     * Creates a list of portfolio items
     *
     * Usage:
     * {pyro:portfolio:items limit="5"}
     * 	<h2>{pyro:title}</h2>
     * 	{pyro:body}
     * {/pyro:portfolio:items}
     *
     * @param	array
     * @return	array
     */
    function items($data = array()) {
        $limit = $this->attribute('limit', 10);
        $client = $this->attribute('client');
        $order_by = $this->attribute('order-by', 'created_on');
        $order_dir = $this->attribute('order-dir', 'ASC');

        if ($client) {
            $this->db->where($this->db->dbprefix('portfolio_clients').'.' . (is_numeric($client) ? 'id' : 'slug'), $client);
        }

        return $this->db
                        ->select($this->db->dbprefix('portfolio').'o.*, '.$this->db->dbprefix('portfolio_clients').'.title as client_name, '.$this->db->dbprefix('portfolio_clients').'.slug as client_slug')
                        ->where('status', 'live')
                        ->where('created_on <=', now())
                        ->join($this->db->dbprefix('portfolio_clients'), $this->db->dbprefix('portfolio').'.client_id = '.$this->db->dbprefix('portfolio_clients').'.id', 'LEFT')
                        ->order_by($this->db->dbprefix('portfolio').'.' . $order_by, $order_by)
                        ->limit($limit)
                        ->get($this->db->dbprefix('portfolio'))
                        ->result_array();
    }

}

/* End of file plugin.php */