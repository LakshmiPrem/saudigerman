<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'libraries/import/App_import.php');

class Import_corporate_debtors extends App_import
{
    protected $notImportableFields = [];

    private $countryFields = ['country', 'billing_country', 'shipping_country'];

    protected $requiredFields = ['debtor_title', 'client_id', 'file_no','outstanding_amount'];

    public function __construct()
    {
        $this->notImportableFields = hooks()->apply_filters('not_importable_clients_fields', ['addedby', 'id','active','default_language','default_currency','show_primary_contact','addedfrom','datecreated', 'is_notified', 'installment_receipt', 'is_verified', 'verified_date', 'verified_by', 'recovery_type','is_imported','latitude','longitude','product','status','current_status','assigned_to']);
        $this->addImportGuidelinesInfo('Default Status value must be - Submitted ');

        parent::__construct();
    }

    public function perform()
    {
        $this->initialize();

        $databaseFields      = $this->getImportableDatabaseFields();
        $totalDatabaseFields = count($databaseFields);

        foreach ($this->getRows() as $rowNumber => $row) {
            $insert    = [];
            $duplicate = false;

            for ($i = 0; $i < $totalDatabaseFields; $i++) {
                if (!isset($row[$i])) {
                    continue;
                }

                $row[$i] = $this->checkNullValueAddedByUser($row[$i]);

                if (in_array($databaseFields[$i], $this->requiredFields) &&
                    $row[$i] == '') {
                    $row[$i] = '/';
                } 

                $insert[$databaseFields[$i]] = $row[$i];
            }

            if ($duplicate) {
                continue;
            }

            $insert = $this->trimInsertValues($insert);

            if (count($insert) > 0) {
                $this->incrementImported();

                $id = null;

                if (!$this->isSimulation()) {
                    $insert['datecreated']           = date('Y-m-d H:i:s');
                  //  $insert['recovery_type'] = 'corporate';
                    $insert['is_imported'] = 1;
                    $id                   = $this->ci->corporate_recoveries_model->add($insert);

                    if ($id) {
                        /*if (isset($this->ci->input->post('reset_installments'))) {
                            
                        }*/
                        /*if (!has_permission('corporate_recovery', '', 'view')) {
                            $assign['customer_admins']   = [];
                            $assign['customer_admins'][] = get_staff_user_id();
                            $this->ci->clients_model->assign_admins($assign, $id);
                        }*/
                    }
                } else {
                    $this->simulationData[$rowNumber] = $this->formatValuesForSimulation($insert);
                }
            }

            if ($this->isSimulation() && $rowNumber >= $this->maxSimulationRows) {
                break;
            }
        }
    }

    public function formatFieldNameForHeading($field)
    {
        if (strtolower($field) == 'title') {
            return 'Position';
        }

        return parent::formatFieldNameForHeading($field);
    }

    protected function email_formatSampleData()
    {
        return uniqid() . '@example.com';
    }

    protected function failureRedirectURL()
    {
        return admin_url('clients/import');
    }

    protected function afterSampleTableHeadingText($field)
    {
        $contactFields = [
            'firstname', 'lastname', 'email', 'contact_phonenumber', 'title',
        ];

        if (in_array($field, $contactFields)) {
            return '<br /><span class="text-info">' . _l('import_contact_field') . '</span>';
        }
    }

    private function insertCustomerGroups($groups, $customer_id)
    {
        foreach ($groups as $group) {
            $this->ci->db->insert(db_prefix() . 'customer_groups', [
                                                    'customer_id' => $customer_id,
                                                    'groupid'     => $group,
                                                ]);
        }
    }

    private function shouldAddContactUnderCustomer($data)
    {
        return (isset($data['company']) && $data['company'] != '' && $data['company'] != '/')
        && (total_rows(db_prefix() . 'clients', ['company' => $data['company']]) === 1);
    }

    private function addContactUnderCustomer($data)
    {
        $contactFields = $this->getContactFields();
        $this->ci->db->where('company', $data['company']);

        $existingCompany = $this->ci->db->get(db_prefix() . 'clients')->row();
        $tmpInsert       = [];

        foreach ($data as $key => $val) {
            foreach ($contactFields as $tmpContactField) {
                if (isset($data[$tmpContactField])) {
                    $tmpInsert[$tmpContactField] = $data[$tmpContactField];
                }
            }
        }
        $tmpInsert['donotsendwelcomeemail'] = true;

        if (isset($data['contact_phonenumber'])) {
            $tmpInsert['phonenumber'] = $data['contact_phonenumber'];
        }

        $this->ci->clients_model->add_contact($tmpInsert, $existingCompany->userid, true);
    }

    private function getContactFields()
    {
        return $this->ci->db->list_fields(db_prefix() . 'contacts');
    }

    private function isDuplicateContact($email)
    {
        return total_rows(db_prefix() . 'contacts', ['email' => $email]);
    }

    private function formatValuesForSimulation($values)
    {
        // ATM only country fields
        foreach ($this->countryFields as $country_field) {
            if (array_key_exists($country_field, $values)) {
                if (!empty($values[$country_field]) && is_numeric($values[$country_field])) {
                    $country = $this->getCountry(null, $values[$country_field]);
                    if ($country) {
                        $values[$country_field] = $country->short_name;
                    }
                }
            }
        }

        return $values;
    }

    private function getCountry($search = null, $id = null)
    {
        if ($search) {
            $this->ci->db->where('iso2', $search);
            $this->ci->db->or_where('short_name', $search);
            $this->ci->db->or_where('long_name', $search);
        } else {
            $this->ci->db->where('country_id', $id);
        }

        return  $this->ci->db->get(db_prefix() . 'countries')->row();
    }

    private function countryValue($value)
    {
        if ($value != '') {
            if (!is_numeric($value)) {
                $country = $this->getCountry($value);
                $value   = $country ? $country->country_id : 0;
            }
        } else {
            $value = 0;
        }

        return $value;
    }
}
