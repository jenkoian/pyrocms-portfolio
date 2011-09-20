<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Portfolio_m extends MY_Model {

    /**
     * @var string
     */
    protected $_table = 'portfolio';

    /**
     * @return array
     */
    public function get_all() {
        $this->db->select($this->db->dbprefix('portfolio') . '.*, ' . $this->db->dbprefix('portfolio_clients') . '.title AS client_title, ' . $this->db->dbprefix('portfolio_clients') . '.slug AS client_slug, f.filename, f.extension');
        $this->db->join($this->db->dbprefix('portfolio_clients'), $this->db->dbprefix('portfolio') . '.client_id = ' . $this->db->dbprefix('portfolio_clients') . '.id', 'left');
        $this->db->join('files f', 'f.id = ' . $this->db->dbprefix('portfolio') . '.thumbnail_id', 'left');

        $this->db->order_by('created_on', 'DESC');

        return $this->db->get($this->db->dbprefix('portfolio'))->result();
    }

    /**     
     * @param int $id
     * @return array 
     */
    public function get($id) {
        $this->db->where(array('id' => $id));
        return $this->db->get($this->db->dbprefix('portfolio'))->row();
    }

    /**
     *
     * @param array $params
     * @return array 
     */
    public function get_many_by($params = array()) {
        $this->load->helper('date');

        if (!empty($params['client'])) {
            if (is_numeric($params['client']))
                $this->db->where($this->db->dbprefix('portfolio_clients') . '.id', $params['client']);
            else
                $this->db->where($this->db->dbprefix('portfolio_clients') . '.slug', $params['client']);
        }

        if (!empty($params['portfolioIds']) && is_array($params['portfolioIds'])) {
            $this->db->where_in($this->db->dbprefix('portfolio') . '.id', $params['portfolioIds']);
        }

        if (!empty($params['month'])) {
            $this->db->where('MONTH(FROM_UNIXTIME(created_on))', $params['month']);
        }

        if (!empty($params['year'])) {
            $this->db->where('YEAR(FROM_UNIXTIME(created_on))', $params['year']);
        }

        // Is a status set?
        if (!empty($params['status'])) {
            // If it's all, then show whatever the status
            if ($params['status'] != 'all') {
                // Otherwise, show only the specific status
                $this->db->where('status', $params['status']);
            }
        }

        // Nothing mentioned, show live only (general frontend stuff)
        else {
            $this->db->where('status', 'live');
        }

        // By default, dont show future items
        if (!isset($params['show_future']) || (isset($params['show_future']) && $params['show_future'] == FALSE)) {
            $this->db->where('created_on <=', now());
        }

        // Limit the results based on 1 number or 2 (2nd is offset)
        if (isset($params['limit']) && is_array($params['limit']))
            $this->db->limit($params['limit'][0], $params['limit'][1]);
        elseif (isset($params['limit']))
            $this->db->limit($params['limit']);

        return $this->get_all();
    }

    /**
     *
     * @param array $params
     * @return int 
     */
    public function count_by($params = array()) {
        $this->db->join($this->db->dbprefix('portfolio_clients'), $this->db->dbprefix('portfolio') . '.client_id = ' . $this->db->dbprefix('portfolio_clients') . '.id', 'left');

        if (!empty($params['client'])) {
            if (is_numeric($params['client']))
                $this->db->where($this->db->dbprefix('portfolio_clients') . '.id', $params['client']);
            else
                $this->db->where($this->db->dbprefix('portfolio_clients') . '.slug', $params['client']);
        }

        if (!empty($params['month'])) {
            $this->db->where('MONTH(FROM_UNIXTIME(created_on))', $params['month']);
        }

        if (!empty($params['year'])) {
            $this->db->where('YEAR(FROM_UNIXTIME(created_on))', $params['year']);
        }

        // Is a status set?
        if (!empty($params['status'])) {
            // If it's all, then show whatever the status
            if ($params['status'] != 'all') {
                // Otherwise, show only the specific status
                $this->db->where('status', $params['status']);
            }
        }

        // Nothing mentioned, show live only (general frontend stuff)
        else {
            $this->db->where('status', 'live');
        }

        return $this->db->count_all_results($this->db->dbprefix('portfolio'));
    }

    /**
     *
     * @param int $id
     * @param array $input
     * @return int 
     */
    public function update($id, $input) {
        $input['updated_on'] = now();

        return parent::update($id, $input);
    }

    /**
     *
     * @param int $id
     * @return int 
     */
    public function publish($id = 0) {
        return parent::update($id, array('status' => 'live'));
    }

    // -- Archive ---------------------------------------------

    /**
     * @return array 
     */
    public function get_archive_months() {
        $this->db->select('UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(t1.created_on), "%Y-%m-02")) AS `date`', FALSE);
        $this->db->distinct();
        $this->db->select('(SELECT count(id) FROM ' . $this->db->dbprefix('portfolio') . ' t2
							WHERE MONTH(FROM_UNIXTIME(t1.created_on)) = MONTH(FROM_UNIXTIME(t2.created_on))
								AND YEAR(FROM_UNIXTIME(t1.created_on)) = YEAR(FROM_UNIXTIME(t2.created_on))
								AND status = "live"
								AND created_on <= ' . now() . '
						   ) as item_count');

        $this->db->where('status', 'live');
        $this->db->where('created_on <=', now());
        $this->db->having('item_count >', 0);
        $this->db->order_by('t1.created_on DESC');
        $query = $this->db->get($this->db->dbprefix('portfolio') . ' t1');

        return $query->result();
    }
    
    /**
     * @todo DIRTY frontend functions. Move to views
     * @param array $params
     * @return string 
     */
    public function get_portfolio_fragment($params = array()) {
        $this->load->helper('date');

        $this->db->where('status', 'live');
        $this->db->where('created_on <=', now());

        $string = '';
        $this->db->order_by('created_on', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get($this->db->dbprefix('portfolio'));
        if ($query->num_rows() > 0) {
            $this->load->helper('text');
            foreach ($query->result() as $portfolio) {
                $string .= '<p>' . anchor('portfolio/' . date('Y/m') . '/' . $portfolio->slug, $portfolio->title) . '<br />' . strip_tags($portfolio->intro) . '</p>';
            }
        }
        return $string;
    }

    /**
     *
     * @param string $field
     * @param string $value
     * @param int $id
     * @return boolean 
     */
    public function check_exists($field, $value = '', $id = 0) {
        if (is_array($field)) {
            $params = $field;
            $id = $value;
        } else {
            $params[$field] = $value;
        }
        $params['id !='] = (int) $id;

        return parent::count_by($params) == 0;
    }

    /**
     * Searches portfolio items based on supplied data array
     * @param $data array
     * @return array
     */
    public function search($data = array()) {
        if (array_key_exists('client_id', $data)) {
            $this->db->where('client_id', $data['client_id']);
        }

        if (array_key_exists('status', $data)) {
            $this->db->where('status', $data['status']);
        }

        if (array_key_exists('keywords', $data)) {
            $matches = array();
            if (strstr($data['keywords'], '%')) {
                preg_match_all('/%.*?%/i', $data['keywords'], $matches);
            }

            if (!empty($matches[0])) {
                foreach ($matches[0] as $match) {
                    $phrases[] = str_replace('%', '', $match);
                }
            } else {
                $temp_phrases = explode(' ', $data['keywords']);
                foreach ($temp_phrases as $phrase) {
                    $phrases[] = str_replace('%', '', $phrase);
                }
            }

            $counter = 0;
            foreach ($phrases as $phrase) {
                if ($counter == 0) {
                    $this->db->like($this->db->dbprefix('portfolio') . '.title', $phrase);
                } else {
                    $this->db->or_like($this->db->dbprefix('portfolio') . '.title', $phrase);
                }

                $this->db->or_like($this->db->dbprefix('portfolio') . '.body', $phrase);
                $this->db->or_like($this->db->dbprefix('portfolio') . '.intro', $phrase);
                $counter++;
            }
        }
        return $this->get_all();
    }

}