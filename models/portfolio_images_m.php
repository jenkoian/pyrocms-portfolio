<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Portfolio_images_m extends MY_Model {

    /**
     * @var string 
     */
    protected $_table = 'files';

    /**
     *
     * @param string $slug
     * @return array 
     */
    public function getImagesByFolderSlug($slug) {

        $this->db->select('files.*');
        $this->db->join('file_folders ff', "files.folder_id = ff.id AND ff.slug = '".$slug."'", 'inner');            

        $this->db->order_by('date_added', 'DESC');

        return $this->db->get('files')->result();            
    }
}