<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * @package  	PyroCMS
 * @subpackage  Categories
 * @category  	Module
 * @author  	Phil Sturgeon - PyroCMS Dev Team
 */
class Admin_Clients extends Admin_Controller {

    /**
     * Array that contains the validation rules
     * @access protected
     * @var array
     */
    protected $validation_rules;

    /**
     * The constructor
     * @access public
     * @return void
     */
    public function __construct() {
        parent::Admin_Controller();
        $this->load->model('portfolio_clients_m');
        $this->lang->load('clients');
        $this->lang->load('categories');
        $this->lang->load('portfolio');

        $this->template->set_partial('shortcuts', 'admin/partials/shortcuts');

        // Set the validation rules
        $this->validation_rules = array(
            array(
                'field' => 'title',
                'label' => lang('clients.title_label'),
                'rules' => 'trim|required|max_length[20]|callback__check_title'
            ),
        );

        // Load the validation library along with the rules
        $this->load->library('form_validation');
        $this->form_validation->set_rules($this->validation_rules);
    }

    /**
     * Index method, lists all clients
     * @access public
     * @return void
     */
    public function index() {
        $this->pyrocache->delete_all('modules_m');
        // Create pagination links
        $total_rows = $this->portfolio_clients_m->count_all();
        $pagination = create_pagination('admin/portfolio/clients/index', $total_rows);

        // Using this data, get the relevant results
        $clients = $this->portfolio_clients_m->order_by('title')->limit($pagination['limit'])->get_all();

        $this->template
                ->title($this->module_details['name'], lang('client_list_title'))
                ->set('clients', $clients)
                ->set('pagination', $pagination)
                ->build('admin/clients/index', $this->data);
    }

    /**
     * Create method, creates a new client
     * @access public
     * @return void
     */
    public function create() {

        // Validate the data
        if ($this->form_validation->run()) {
            $this->portfolio_clients_m->insert($_POST) ? $this->session->set_flashdata('success', sprintf(lang('client_add_success'), $this->input->post('title'))) : $this->session->set_flashdata(array('error' => lang('client_add_error')));

            redirect('admin/portfolio/clients');
        }

        // Loop through each validation rule
        foreach ($this->validation_rules as $rule) {
            $client->{$rule['field']} = set_value($rule['field']);
        }

        // Render the view	
        $this->data->client = & $client;
        $this->template->title($this->module_details['name'], lang('client_create_title'))
                ->build('admin/clients/form', $this->data);
    }

    /**
     * Edit method, edits an existing client
     * @access public
     * @param int id The ID of the client to edit 
     * @return void
     */
    public function edit($id = 0) {
        // Get the category
        $client = $this->portfolio_clients_m->get($id);

        // ID specified?
        $client or redirect('admin/portfolio/clients/index');

        // Validate the results
        if ($this->form_validation->run()) {
            $this->portfolio_categories_m->update($id, $_POST) ? $this->session->set_flashdata('success', sprintf(lang('client_edit_success'), $this->input->post('title'))) : $this->session->set_flashdata(array('error' => lang('client_edit_error')));

            redirect('admin/portfolio/clients/index');
        }

        // Loop through each rule
        foreach ($this->validation_rules as $rule) {
            if ($this->input->post($rule['field']) !== FALSE) {
                $category->{$rule['field']} = $this->input->post($rule['field']);
            }
        }

        // Render the view
        $this->data->client = & $client;
        $this->template->title($this->module_details['name'], sprintf(lang('client_edit_title'), $client->title))
                ->build('admin/clients/form', $this->data);
    }

    /**
     * Delete method, deletes an existing client (obvious isn't it?)
     * @access public
     * @param int id The ID of the client to edit 
     * @return void
     */
    public function delete($id = 0) {
        $id_array = (!empty($id)) ? array($id) : $this->input->post('action_to');

        // Delete multiple
        if (!empty($id_array)) {
            $deleted = 0;
            $to_delete = 0;
            foreach ($id_array as $id) {
                if ($this->portfolio_clients_m->delete($id)) {
                    $deleted++;
                } else {
                    $this->session->set_flashdata('error', sprintf($this->lang->line('client_mass_delete_error'), $id));
                }
                $to_delete++;
            }

            if ($deleted > 0) {
                $this->session->set_flashdata('success', sprintf($this->lang->line('client_mass_delete_success'), $deleted, $to_delete));
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('client_no_select_error'));
        }

        redirect('admin/portfolio/clients/index');
    }

    /**
     * Callback method that checks the title of the client
     * @access public
     * @param string title The title to check
     * @return bool
     */
    public function _check_title($title = '') {
        if ($this->portfolio_clients_m->check_title($title)) {
            $this->form_validation->set_message('_check_title', sprintf($this->lang->line('client_already_exist_error'), $title));
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Create method, creates a new client via ajax
     * @access public
     * @return void
     */
    public function create_ajax() {
        // Loop through each validation rule
        foreach ($this->validation_rules as $rule) {
            $client->{$rule['field']} = set_value($rule['field']);
        }

        $this->data->method = 'create';
        $this->data->client = & $client;

        if ($this->form_validation->run()) {
            $id = $this->portfolio_clients_m->insert_ajax($_POST);

            if ($id > 0) {
                $message = sprintf(lang('client_add_success'), $this->input->post('title'));
            } else {
                $message = lang('client_add_error');
            }

            return $this->template->build_json(array(
                        'message' => $message,
                        'title' => $this->input->post('title'),
                        'client_id' => $id,
                        'status' => 'ok'
                    ));
        } else {
            // Render the view
            $form = $this->load->view('admin/clients/form', $this->data, TRUE);

            if ($errors = validation_errors()) {
                return $this->template->build_json(array(
                            'message' => $errors,
                            'status' => 'error',
                            'form' => $form
                        ));
            }

            echo $form;
        }
    }

}