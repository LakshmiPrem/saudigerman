<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends AdminController
{
    /**
     * Codeigniter Instance
     * Expenses detailed report filters use $ci
     * @var object
     */
    private $ci;

    public function __construct()
    {
        parent::__construct();
        if (!has_permission('reports', '', 'view')) {
            access_denied('reports');
        }
        $this->ci = &get_instance();
        $this->load->model('reports_model');
    }

    /* No access on this url */
    public function index()
    {
        redirect(admin_url());
    }

    /* See knowledge base article reports*/
    public function knowledge_base_articles()
    {
        $this->load->model('knowledge_base_model');
        $data['groups'] = $this->knowledge_base_model->get_kbg();
        $data['title']  = _l('kb_reports');
        $this->load->view('admin/reports/knowledge_base_articles', $data);
    }

    /*
        public function tax_summary(){
           $this->load->model('taxes_model');
           $this->load->model('payments_model');
           $this->load->model('invoices_model');
           $data['taxes'] = $this->db->query("SELECT DISTINCT taxname,taxrate FROM ".db_prefix()."item_tax WHERE rel_type='invoice'")->result_array();
            $this->load->view('admin/reports/tax_summary',$data);
        }*/
    /* Repoert leads conversions */
    public function leads()
    {
        $type = 'leads';
        if ($this->input->get('type')) {
            $type                       = $type . '_' . $this->input->get('type');
            $data['leads_staff_report'] = json_encode($this->reports_model->leads_staff_report());
        }
        $this->load->model('leads_model');
        $data['statuses']               = $this->leads_model->get_status();
        $data['leads_this_week_report'] = json_encode($this->reports_model->leads_this_week_report());
        $data['leads_sources_report']   = json_encode($this->reports_model->leads_sources_report());
        $this->load->view('admin/reports/' . $type, $data);
    }

    /* Sales reportts */
    public function sales()
    {
        $data['mysqlVersion'] = $this->db->query('SELECT VERSION() as version')->row();
        $data['sqlMode']      = $this->db->query('SELECT @@sql_mode as mode')->row();

        if (is_using_multiple_currencies() || is_using_multiple_currencies(db_prefix() . 'creditnotes') || is_using_multiple_currencies(db_prefix() . 'estimates') || is_using_multiple_currencies(db_prefix() . 'proposals')) {
            $this->load->model('currencies_model');
            $data['currencies'] = $this->currencies_model->get();
        }
        $this->load->model('invoices_model');
        $this->load->model('estimates_model');
        $this->load->model('proposals_model');
        $this->load->model('credit_notes_model');

        $data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();
        $data['invoice_statuses']      = $this->invoices_model->get_statuses();
        $data['estimate_statuses']     = $this->estimates_model->get_statuses();
        $data['payments_years']        = $this->reports_model->get_distinct_payments_years();
        $data['estimates_sale_agents'] = $this->estimates_model->get_sale_agents();

        $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();

        $data['proposals_sale_agents'] = $this->proposals_model->get_sale_agents();
        $data['proposals_statuses']    = $this->proposals_model->get_statuses();

        $data['invoice_taxes']     = $this->distinct_taxes('invoice');
        $data['estimate_taxes']    = $this->distinct_taxes('estimate');
        $data['proposal_taxes']    = $this->distinct_taxes('proposal');
        $data['credit_note_taxes'] = $this->distinct_taxes('credit_note');


        $data['title'] = _l('sales_reports');
        $this->load->vw('admin/reports/sales', $data);
    }

    /* Customer report */
    public function customers_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $select = [
                get_sql_select_client_company(),
                '(SELECT COUNT(clientid) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
                '(SELECT SUM(subtotal) - SUM(discount_total) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
                '(SELECT SUM(total) FROM ' . db_prefix() . 'invoices WHERE ' . db_prefix() . 'invoices.clientid = ' . db_prefix() . 'clients.userid AND status != 5)',
            ];

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' ' . $custom_date_select . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
            }
            $by_currency = $this->input->post('report_currency');
            $currency    = $this->currencies_model->get_base_currency();
            if ($by_currency) {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' AND currency =' . $this->db->escape_str($by_currency) . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
                $currency = $this->currencies_model->get($by_currency);
            }
            $aColumns     = $select;
            $sIndexColumn = 'userid';
            $sTable       = db_prefix() . 'clients';
            $where        = [];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, [
                'userid',
            ]);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $x       = 0;
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 0) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } elseif ($aColumns[$i] == $select[2] || $aColumns[$i] == $select[3]) {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $_data = app_format_money($_data, $currency->name);
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            echo json_encode($output);
            die();
        }
    }

    public function payments_received()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('payment_modes_model');
            $payment_gateways = $this->payment_modes_model->get_payment_gateways(true);
            $select           = [
                db_prefix() . 'invoicepaymentrecords.id',
                db_prefix() . 'invoicepaymentrecords.date',
                'invoiceid',
                get_sql_select_client_company(),
                'paymentmode',
                'transactionid',
                'note',
                'amount',
            ];
            $where = [
                'AND status != 5',
            ];

            $custom_date_select = $this->get_where_report_period(db_prefix() . 'invoicepaymentrecords.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'invoicepaymentrecords';
            $join         = [
                'JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid',
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid',
                'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'invoicepaymentrecords.paymentmode',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'number',
                'clientid',
                db_prefix() . 'payment_modes.name',
                db_prefix() . 'payment_modes.id as paymentmodeid',
                'paymentmethod',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data['total_amount'] = 0;
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($aColumns[$i] == 'paymentmode') {
                        $_data = $aRow['name'];
                        if (is_null($aRow['paymentmodeid'])) {
                            foreach ($payment_gateways as $gateway) {
                                if ($aRow['paymentmode'] == $gateway['id']) {
                                    $_data = $gateway['name'];
                                }
                            }
                        }
                        if (!empty($aRow['paymentmethod'])) {
                            $_data .= ' - ' . $aRow['paymentmethod'];
                        }
                    } elseif ($aColumns[$i] == db_prefix() . 'invoicepaymentrecords.id') {
                        $_data = '<a href="' . admin_url('payments/payment/' . $_data) . '" target="_blank">' . $_data . '</a>';
                    } elseif ($aColumns[$i] == db_prefix() . 'invoicepaymentrecords.date') {
                        $_data = _d($_data);
                    } elseif ($aColumns[$i] == 'invoiceid') {
                        $_data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow[$aColumns[$i]]) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
                    } elseif ($i == 3) {
                        if (empty($aRow['deleted_customer_name'])) {
                            $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                        } else {
                            $row[] = $aRow['deleted_customer_name'];
                        }
                    } elseif ($aColumns[$i] == 'amount') {
                        $footer_data['total_amount'] += $_data;
                        $_data = app_format_money($_data, $currency->name);
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            $footer_data['total_amount'] = app_format_money($footer_data['total_amount'], $currency->name);
            $output['sums']              = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function proposals_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('proposals_model');

            $proposalsTaxes    = $this->distinct_taxes('proposal');
            $totalTaxesColumns = count($proposalsTaxes);

            $select = [
                'id',
                'subject',
                'proposal_to',
                'date',
                'open_till',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                'status',
            ];

            $proposalsTaxesSelect = array_reverse($proposalsTaxes);

            foreach ($proposalsTaxesSelect as $key => $tax) {
                array_splice($select, 8, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="proposal" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'proposals.id) as total_tax_single_' . $key);
            }

            $where              = [];
            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('proposal_status')) {
                $statuses  = $this->input->post('proposal_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $this->db->escape_str($status));
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('proposals_sale_agents')) {
                $agents  = $this->input->post('proposals_sale_agents');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $this->db->escape_str($agent));
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND assigned IN (' . implode(', ', $_agents) . ')');
                }
            }


            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'proposals';
            $join         = [];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'rel_id',
                'rel_type',
                'discount_percent',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'          => 0,
                'subtotal'       => 0,
                'total_tax'      => 0,
                'discount_total' => 0,
                'adjustment'     => 0,
            ];

            foreach ($proposalsTaxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('proposals/list_proposals/' . $aRow['id']) . '" target="_blank">' . format_proposal_number($aRow['id']) . '</a>';

                $row[] = '<a href="' . admin_url('proposals/list_proposals/' . $aRow['id']) . '" target="_blank">' . $aRow['subject'] . '</a>';

                if ($aRow['rel_type'] == 'lead') {
                    $row[] = '<a href="#" onclick="init_lead(' . $aRow['rel_id'] . ');return false;" target="_blank" data-toggle="tooltip" data-title="' . _l('lead') . '">' . $aRow['proposal_to'] . '</a>' . '<span class="hide">' . _l('lead') . '</span>';
                } elseif ($aRow['rel_type'] == 'customer') {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['rel_id']) . '" target="_blank" data-toggle="tooltip" data-title="' . _l('client') . '">' . $aRow['proposal_to'] . '</a>' . '<span class="hide">' . _l('client') . '</span>';
                } else {
                    $row[] = '';
                }

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['open_till']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($proposalsTaxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]), $currency->name);
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[]              = format_proposal_status($aRow['status']);
                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function estimates_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('estimates_model');

            $estimateTaxes     = $this->distinct_taxes('estimate');
            $totalTaxesColumns = count($estimateTaxes);

            $select = [
                'number',
                get_sql_select_client_company(),
                'invoiceid',
                'YEAR(date) as year',
                'date',
                'expirydate',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                'reference_no',
                'status',
            ];

            $estimatesTaxesSelect = array_reverse($estimateTaxes);

            foreach ($estimatesTaxesSelect as $key => $tax) {
                array_splice($select, 9, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="estimate" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'estimates.id) as total_tax_single_' . $key);
            }

            $where              = [];
            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('estimate_status')) {
                $statuses  = $this->input->post('estimate_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $this->db->escape_str($status));
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('sale_agent_estimates')) {
                $agents  = $this->input->post('sale_agent_estimates');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $this->db->escape_str($agent));
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'estimates';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'estimates.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'estimates.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'          => 0,
                'subtotal'       => 0,
                'total_tax'      => 0,
                'discount_total' => 0,
                'adjustment'     => 0,
            ];

            foreach ($estimateTaxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('estimates/list_estimates/' . $aRow['id']) . '" target="_blank">' . format_estimate_number($aRow['id']) . '</a>';

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                if ($aRow['invoiceid'] === null) {
                    $row[] = '';
                } else {
                    $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['invoiceid']) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
                }

                $row[] = $aRow['year'];

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['expirydate']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($estimateTaxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]), $currency->name);
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];


                $row[] = $aRow['reference_no'];

                $row[] = format_estimate_status($aRow['status']);

                $output['aaData'][] = $row;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }
            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    private function get_where_report_period($field = 'date')
    {
        $months_report      = $this->input->post('report_months');
        $custom_date_select = '';
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }

                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
            } elseif ($months_report == 'this_month') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
            } elseif ($months_report == 'this_year') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date('Y-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
            } elseif ($months_report == 'last_year') {
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date   = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'AND ' . $field . ' = "' . $this->db->escape_str($from_date) . '"';
                } else {
                    $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $this->db->escape_str($from_date) . '" AND "' . $this->db->escape_str($to_date) . '")';
                }
            }
        }

        return $custom_date_select;
    }

    public function items()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $v = $this->db->query('SELECT VERSION() as version')->row();
            // 5.6 mysql version don't have the ANY_VALUE function implemented.

            if ($v && strpos($v->version, '5.7') !== false) {
                $aColumns = [
                        'ANY_VALUE(description) as description',
                        'ANY_VALUE((SUM(' . db_prefix() . 'itemable.qty))) as quantity_sold',
                        'ANY_VALUE(SUM(rate*qty)) as rate',
                        'ANY_VALUE(AVG(rate*qty)) as avg_price',
                    ];
            } else {
                $aColumns = [
                        'description as description',
                        '(SUM(' . db_prefix() . 'itemable.qty)) as quantity_sold',
                        'SUM(rate*qty) as rate',
                        'AVG(rate*qty) as avg_price',
                    ];
            }

            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'itemable';
            $join         = ['JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'itemable.rel_id'];

            $where = ['AND rel_type="invoice"', 'AND status != 5', 'AND status=2'];

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            if ($this->input->post('sale_agent_items')) {
                $agents  = $this->input->post('sale_agent_items');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $this->db->escape_str($agent));
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [], 'GROUP by description');

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total_amount' => 0,
                'total_qty'    => 0,
            ];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = $aRow['description'];
                $row[] = $aRow['quantity_sold'];
                $row[] = app_format_money($aRow['rate'], $currency->name);
                $row[] = app_format_money($aRow['avg_price'], $currency->name);
                $footer_data['total_amount'] += $aRow['rate'];
                $footer_data['total_qty'] += $aRow['quantity_sold'];
                $output['aaData'][] = $row;
            }

            $footer_data['total_amount'] = app_format_money($footer_data['total_amount'], $currency->name);

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function credit_notes()
    {
        if ($this->input->is_ajax_request()) {
            $credit_note_taxes = $this->distinct_taxes('credit_note');
            $totalTaxesColumns = count($credit_note_taxes);

            $this->load->model('currencies_model');

            $select = [
                'number',
                'date',
                get_sql_select_client_company(),
                'reference_no',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                '(SELECT ' . db_prefix() . 'creditnotes.total - (
                  (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.credit_id=' . db_prefix() . 'creditnotes.id)
                  +
                  (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'creditnote_refunds WHERE ' . db_prefix() . 'creditnote_refunds.credit_note_id=' . db_prefix() . 'creditnotes.id)
                  )
                ) as remaining_amount',
                '(SELECT SUM(amount) FROM  ' . db_prefix() . 'creditnote_refunds WHERE credit_note_id=' . db_prefix() . 'creditnotes.id) as refund_amount',
                'status',
            ];

            $where = [];

            $credit_note_taxes_select = array_reverse($credit_note_taxes);

            foreach ($credit_note_taxes_select as $key => $tax) {
                array_splice($select, 5, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="credit_note" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'creditnotes.id) as total_tax_single_' . $key);
            }

            $custom_date_select = $this->get_where_report_period();

            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');

            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            if ($this->input->post('credit_note_status')) {
                $statuses  = $this->input->post('credit_note_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $this->db->escape_str($status));
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'creditnotes';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'creditnotes.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'creditnotes.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'            => 0,
                'subtotal'         => 0,
                'total_tax'        => 0,
                'discount_total'   => 0,
                'adjustment'       => 0,
                'remaining_amount' => 0,
                'refund_amount'    => 0,
            ];

            foreach ($credit_note_taxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }
            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('credit_notes/list_credit_notes/' . $aRow['id']) . '" target="_blank">' . format_credit_note_number($aRow['id']) . '</a>';

                $row[] = _d($aRow['date']);

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                $row[] = $aRow['reference_no'];

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($credit_note_taxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]), $currency->name);
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[] = app_format_money($aRow['remaining_amount'], $currency->name);
                $footer_data['remaining_amount'] += $aRow['remaining_amount'];

                $row[] = app_format_money($aRow['refund_amount'], $currency->name);
                $footer_data['refund_amount'] += $aRow['refund_amount'];

                $row[] = format_credit_note_status($aRow['status']);

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function invoices_report()
    {
        if ($this->input->is_ajax_request()) {
            $invoice_taxes     = $this->distinct_taxes('invoice');
            $totalTaxesColumns = count($invoice_taxes);

            $this->load->model('currencies_model');
            $this->load->model('invoices_model');

            $select = [
                'number',
                get_sql_select_client_company(),
                'YEAR(date) as year',
                'date',
                'duedate',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                '(SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id) as credits_applied',
                '(SELECT total - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid = ' . db_prefix() . 'invoices.id) - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id))',
                'status',
            ];

            $where = [
                'AND status != 5',
            ];

            $invoiceTaxesSelect = array_reverse($invoice_taxes);

            foreach ($invoiceTaxesSelect as $key => $tax) {
                array_splice($select, 8, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="invoice" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'invoices.id) as total_tax_single_' . $key);
            }

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('sale_agent_invoices')) {
                $agents  = $this->input->post('sale_agent_invoices');
                $_agents = [];
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $this->db->escape_str($agent));
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency              = $this->input->post('report_currency');
            $totalPaymentsColumnIndex = (12 + $totalTaxesColumns - 1);

            if ($by_currency) {
                $_temp = substr($select[$totalPaymentsColumnIndex], 0, -2);
                $_temp .= ' AND currency =' . $by_currency . ')) as amount_open';
                $select[$totalPaymentsColumnIndex] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
            } else {
                $currency                          = $this->currencies_model->get_base_currency();
                $select[$totalPaymentsColumnIndex] = $select[$totalPaymentsColumnIndex] .= ' as amount_open';
            }

            if ($this->input->post('invoice_status')) {
                $statuses  = $this->input->post('invoice_status');
                $_statuses = [];
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $this->db->escape_str($status));
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'invoices';
            $join         = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'invoices.clientid',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                'userid',
                'clientid',
                db_prefix() . 'invoices.id',
                'discount_percent',
                'deleted_customer_name',
            ]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = [
                'total'           => 0,
                'subtotal'        => 0,
                'total_tax'       => 0,
                'discount_total'  => 0,
                'adjustment'      => 0,
                'applied_credits' => 0,
                'amount_open'     => 0,
            ];

            foreach ($invoice_taxes as $key => $tax) {
                $footer_data['total_tax_single_' . $key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';

                if (empty($aRow['deleted_customer_name'])) {
                    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                } else {
                    $row[] = $aRow['deleted_customer_name'];
                }

                $row[] = $aRow['year'];

                $row[] = _d($aRow['date']);

                $row[] = _d($aRow['duedate']);

                $row[] = app_format_money($aRow['subtotal'], $currency->name);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'], $currency->name);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'], $currency->name);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach ($invoice_taxes as $tax) {
                    $row[] = app_format_money(($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]), $currency->name);
                    $footer_data['total_tax_single_' . $i] += ($aRow['total_tax_single_' . $t] == null ? 0 : $aRow['total_tax_single_' . $t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'], $currency->name);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'], $currency->name);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[] = app_format_money($aRow['credits_applied'], $currency->name);
                $footer_data['applied_credits'] += $aRow['credits_applied'];

                $amountOpen = $aRow['amount_open'];
                $row[]      = app_format_money($amountOpen, $currency->name);
                $footer_data['amount_open'] += $amountOpen;

                $row[] = format_invoice_status($aRow['status']);

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency->name);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function expenses($type = 'simple_report')
    {
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['currencies']    = $this->currencies_model->get();

        $data['title'] = _l('expenses_report');
        if ($type != 'simple_report') {
            $this->load->model('expenses_model');
            $data['categories'] = $this->expenses_model->get_category();
            $data['years']      = $this->expenses_model->get_expenses_years();

            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [], true);

            if ($this->input->is_ajax_request()) {
                $aColumns = [
                    db_prefix() . 'expenses.category',
                    'amount',
                    'expense_name',
                    'tax',
                    'tax2',
                    '(SELECT taxrate FROM ' . db_prefix() . 'taxes WHERE id=' . db_prefix() . 'expenses.tax)',
                    'amount as amount_with_tax',
                    'billable',
                    'date',
                    get_sql_select_client_company(),
                    'invoiceid',
                    'reference_no',
                    'paymentmode',
                ];
                $join = [
                    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'expenses.clientid',
                    'LEFT JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
                ];
                $where  = [];
                $filter = [];
                include_once(APPPATH . 'views/admin/tables/includes/expenses_filter.php');
                if (count($filter) > 0) {
                    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
                }

                $by_currency = $this->input->post('currency');
                if ($by_currency) {
                    $currency = $this->currencies_model->get($by_currency);
                    array_push($where, 'AND currency=' . $this->db->escape_str($by_currency));
                } else {
                    $currency = $this->currencies_model->get_base_currency();
                }

                $sIndexColumn = 'id';
                $sTable       = db_prefix() . 'expenses';
                $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
                    db_prefix() . 'expenses_categories.name as category_name',
                    db_prefix() . 'expenses.id',
                    db_prefix() . 'expenses.clientid',
                    'currency',
                ]);
                $output  = $result['output'];
                $rResult = $result['rResult'];
                $this->load->model('currencies_model');
                $this->load->model('payment_modes_model');

                $footer_data = [
                    'tax_1'           => 0,
                    'tax_2'           => 0,
                    'amount'          => 0,
                    'total_tax'       => 0,
                    'amount_with_tax' => 0,
                ];

                foreach ($rResult as $aRow) {
                    $row = [];
                    for ($i = 0; $i < count($aColumns); $i++) {
                        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                            $_data = $aRow[strafter($aColumns[$i], 'as ')];
                        } else {
                            $_data = $aRow[$aColumns[$i]];
                        }
                        if ($aRow['tax'] != 0) {
                            $_tax = get_tax_by_id($aRow['tax']);
                        }
                        if ($aRow['tax2'] != 0) {
                            $_tax2 = get_tax_by_id($aRow['tax2']);
                        }
                        if ($aColumns[$i] == 'category') {
                            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['category_name'] . '</a>';
                        } elseif ($aColumns[$i] == 'expense_name') {
                            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['expense_name'] . '</a>';
                        } elseif ($aColumns[$i] == 'amount' || $i == 6) {
                            $total = $_data;
                            if ($i != 6) {
                                $footer_data['amount'] += $total;
                            } else {
                                if ($aRow['tax'] != 0 && $i == 6) {
                                    $total += ($total / 100 * $_tax->taxrate);
                                }
                                if ($aRow['tax2'] != 0 && $i == 6) {
                                    $total += ($aRow['amount'] / 100 * $_tax2->taxrate);
                                }
                                $footer_data['amount_with_tax'] += $total;
                            }

                            $_data = app_format_money($total, $currency->name);
                        } elseif ($i == 9) {
                            $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                        } elseif ($aColumns[$i] == 'paymentmode') {
                            $_data = '';
                            if ($aRow['paymentmode'] != '0' && !empty($aRow['paymentmode'])) {
                                $payment_mode = $this->payment_modes_model->get($aRow['paymentmode'], [], false, true);
                                if ($payment_mode) {
                                    $_data = $payment_mode->name;
                                }
                            }
                        } elseif ($aColumns[$i] == 'date') {
                            $_data = _d($_data);
                        } elseif ($aColumns[$i] == 'tax') {
                            if ($aRow['tax'] != 0) {
                                $_data = $_tax->name . ' - ' . app_format_number($_tax->taxrate) . '%';
                            } else {
                                $_data = '';
                            }
                        } elseif ($aColumns[$i] == 'tax2') {
                            if ($aRow['tax2'] != 0) {
                                $_data = $_tax2->name . ' - ' . app_format_number($_tax2->taxrate) . '%';
                            } else {
                                $_data = '';
                            }
                        } elseif ($i == 5) {
                            if ($aRow['tax'] != 0 || $aRow['tax2'] != 0) {
                                if ($aRow['tax'] != 0) {
                                    $total = ($total / 100 * $_tax->taxrate);
                                    $footer_data['tax_1'] += $total;
                                }
                                if ($aRow['tax2'] != 0) {
                                    $total += ($aRow['amount'] / 100 * $_tax2->taxrate);
                                    $footer_data['tax_2'] += $total;
                                }
                                $_data = app_format_money($total, $currency->name);
                                $footer_data['total_tax'] += $total;
                            } else {
                                $_data = app_format_number(0);
                            }
                        } elseif ($aColumns[$i] == 'billable') {
                            if ($aRow['billable'] == 1) {
                                $_data = _l('expenses_list_billable');
                            } else {
                                $_data = _l('expense_not_billable');
                            }
                        } elseif ($aColumns[$i] == 'invoiceid') {
                            if ($_data) {
                                $_data = '<a href="' . admin_url('invoices/list_invoices/' . $_data) . '">' . format_invoice_number($_data) . '</a>';
                            } else {
                                $_data = '';
                            }
                        }
                        $row[] = $_data;
                    }
                    $output['aaData'][] = $row;
                }

                foreach ($footer_data as $key => $total) {
                    $footer_data[$key] = app_format_money($total, $currency->name);
                }

                $output['sums'] = $footer_data;
                echo json_encode($output);
                die;
            }
            $this->load->view('admin/reports/expenses_detailed', $data);
        } else {
            if (!$this->input->get('year')) {
                $data['current_year'] = date('Y');
            } else {
                $data['current_year'] = $this->input->get('year');
            }


            $data['export_not_supported'] = ($this->agent->browser() == 'Internet Explorer' || $this->agent->browser() == 'Spartan');

            $this->load->model('expenses_model');

            $data['chart_not_billable'] = json_encode($this->reports_model->get_stats_chart_data(_l('not_billable_expenses_by_categories'), [
                'billable' => 0,
            ], [
                'backgroundColor' => 'rgba(252,45,66,0.4)',
                'borderColor'     => '#fc2d42',
            ], $data['current_year']));

            $data['chart_billable'] = json_encode($this->reports_model->get_stats_chart_data(_l('billable_expenses_by_categories'), [
                'billable' => 1,
            ], [
                'backgroundColor' => 'rgba(37,155,35,0.2)',
                'borderColor'     => '#84c529',
            ], $data['current_year']));

            $data['expense_years'] = $this->expenses_model->get_expenses_years();

            if (count($data['expense_years']) > 0) {
                // Perhaps no expenses in new year?
                if (!in_array_multidimensional($data['expense_years'], 'year', date('Y'))) {
                    array_unshift($data['expense_years'], ['year' => date('Y')]);
                }
            }

            $data['categories'] = $this->expenses_model->get_category();

            $this->load->view('admin/reports/expenses', $data);
        }
    }

    public function expenses_vs_income($year = '')
    {
        $_expenses_years = [];
        $_years          = [];
        $this->load->model('expenses_model');
        $expenses_years = $this->expenses_model->get_expenses_years();
        $payments_years = $this->reports_model->get_distinct_payments_years();

        foreach ($expenses_years as $y) {
            array_push($_years, $y['year']);
        }
        foreach ($payments_years as $y) {
            array_push($_years, $y['year']);
        }

        $_years = array_map('unserialize', array_unique(array_map('serialize', $_years)));

        if (!in_array(date('Y'), $_years)) {
            $_years[] = date('Y');
        }

        rsort($_years, SORT_NUMERIC);
        $data['report_year'] = $year == '' ? date('Y') : $year;

        $data['years']                           = $_years;
        $data['chart_expenses_vs_income_values'] = json_encode($this->reports_model->get_expenses_vs_income_report($year));
        $data['base_currency']                   = get_base_currency();
        $data['title']                           = _l('als_expenses_vs_income');
        $this->load->view('admin/reports/expenses_vs_income', $data);
    }

    /* Total income report / ajax chart*/
    public function total_income_report()
    {
        echo json_encode($this->reports_model->total_income_report());
    }

    public function report_by_payment_modes()
    {
        echo json_encode($this->reports_model->report_by_payment_modes());
    }

    public function report_by_customer_groups()
    {
        echo json_encode($this->reports_model->report_by_customer_groups());
    }

    /* Leads conversion monthly report / ajax chart*/
    public function leads_monthly_report($month)
    {
        echo json_encode($this->reports_model->leads_monthly_report($month));
    }

    private function distinct_taxes($rel_type)
    {
        return $this->db->query('SELECT DISTINCT taxname,taxrate FROM ' . db_prefix() . "item_tax WHERE rel_type='" . $rel_type . "' ORDER BY taxname ASC")->result_array();
    }

     /* Matter reports */
    public function matters()
    {
        $data['mysqlVersion'] = $this->db->query('SELECT VERSION() as version')->row();
        $data['sqlMode'] = $this->db->query('SELECT @@sql_mode as mode')->row();

        $data['lawyers_arr'] = $this->staff_model->get('', ['active' => 1,'is_lawyer'=>'1','is_not_staff'=>'1']);
        $data['hearing_types'] =get_project_instances();
		$data['settle_nature']=get_settlement_nature();
        $this->load->model('projects_model');
        $data['proj_statuses'] = $this->projects_model->get_project_statuses();
        $this->load->model('casediary_model');
        $data['oppositeparty_names'] = $this->casediary_model->get_oppositeparty();
        $data['title']               = _l('als_reports_matters');
        $this->load->view('admin/reports/matters', $data);
    }

    
    public function matter_age_wise_report()
    {
        if ($this->input->is_ajax_request()) {
            $today = date('Y-m-d');
            $select = array(
                'tblprojects.name as name',
                get_sql_select_client_company(),
                'tbloppositeparty.name as opposite_party',
                'file_no',
                'status',
                'start_date',
                'DATEDIFF(CURDATE(),start_date) as age',
                //'TIMESTAMPDIFF(YEAR, start_date, CURDATE()) as age_years'
            );
            $where = array();
            $custom_date_select = $this->get_where_report_period('start_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('clientid2')) {
                $client  = $this->input->post('clientid2');
                array_push($where, 'AND tblprojects.clientid =' . $client );
                
            }
            if ($this->input->post('p_status1')) {
                $p_status  = $this->input->post('p_status1');
                array_push($where, 'AND tblprojects.status =' . $p_status );
                
            }


          
            

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblprojects';
            $join             = array(
                'INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid',
                'LEFT JOIN tbloppositeparty ON tbloppositeparty.id = tblprojects.opposite_party',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('clientid','(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members_ids','tblprojects.id as id','case_type',


            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
              
            );

            foreach ($rResult as $aRow) {
                $row = array();

                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';

                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';

                $row[] = $aRow['opposite_party'];

                $row[] = $aRow['file_no'];
                $status = get_project_status_by_id($aRow['status']);
                $row[]  = '<span class="label label inline-block project-status-' . $aRow['status'] . '" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '">' . $status['name'] . '</span>';

                $row[] = _d($aRow['start_date']);
                $row[] = $aRow['age'];
                //$row[] = $aRow['age_years'];
              
                $output['aaData'][] = $row;
            }

           /* foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }*/

            //$output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }
    public function matter_client_report()
    {
        if ($this->input->is_ajax_request()) {

            $select = array(
                'tblprojects.name as name',
                get_sql_select_client_company(),
                'tbloppositeparty.name as opposite_party',
                'file_no',
                'start_date',
                '(SELECT GROUP_CONCAT(CONCAT(REPLACE(UPPER(details_type),"_"," ")," :- ",tblcase_details.case_number) SEPARATOR "\n\n") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as case_number',
                '(SELECT GROUP_CONCAT( DISTINCT tblcourts.name SEPARATOR "\n ") FROM tblcourts LEFT JOIN tblcase_details ON tblcase_details.court_id = tblcourts.id WHERE tblcourts.id = tblcase_details.court_id AND tblcase_details.project_id=tblprojects.id ORDER BY tblcase_details.id ) as court_id',
                '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_members.staff_id WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members',
                'status', 
                '(SELECT  GROUP_CONCAT(tblcase_details.case_details SEPARATOR "\n") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as case_updates',
            );
            $where = array();
            $custom_date_select = $this->get_where_report_period('start_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('clientid2')) {
                $client  = $this->input->post('clientid2');
                array_push($where, 'AND tblprojects.clientid =' . $client );
                
            }
            if ($this->input->post('p_status')) {
                $p_status  = $this->input->post('p_status');
                array_push($where, 'AND tblprojects.status =' . $p_status );
                
            }


          
            

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblprojects';
            $join             = array(
                'INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid',
                'LEFT JOIN tbloppositeparty ON tbloppositeparty.id = tblprojects.opposite_party',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('clientid','(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members_ids','tblprojects.id as id','case_type',


            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
              
            );

            foreach ($rResult as $aRow) {
                $row = array();

                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';

                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';

                $row[] = $aRow['opposite_party'];

                $row[] = $aRow['file_no'];

                $row[] = _d($aRow['start_date']);
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['id']) . '?group=project_case_details">' . nl2br($aRow['case_number']). '</a>';
                $row[] = nl2br($aRow['court_id']);

                $membersOutput = '';

                $members       = explode(',', $aRow['members']);
                $exportMembers = '';
                foreach ($members as $key => $member) {
                    if ($member != '') {
                        $members_ids = explode(',', $aRow['members_ids']);
                        $member_id   = $members_ids[$key];
                        $membersOutput .= '<a href="' . admin_url('profile/' . $member_id) . '">' .
                        staff_profile_image($member_id, [
                            'staff-profile-image-small mright5',
                            ], 'small', [
                            'data-toggle' => 'tooltip',
                            'data-title'  => $member,
                            ]) . '</a>';
                        // For exporting
                        $exportMembers .= $member . ', ';
                    }
                }

                $membersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';
                $row[] = $membersOutput;

                $status = get_project_status_by_id($aRow['status']);
                $row[] = '<span class="label label inline-block project-status-' . $aRow['status'] . '" style="color:'.$status['color'].';border:1px solid '.$status['color'].'">' . $status['name'] . '</span>';
                $row[] = nl2br($aRow['case_updates']);
                $output['aaData'][] = $row;
            }

           /* foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }*/

            //$output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

     public function matter_lawyer_report()
    {
        if ($this->input->is_ajax_request()) {
            

            $select = array(
                'tblprojects.id as id',
                'tblprojects.name as name',
                get_sql_select_client_company(),
                '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tblprojects.id and rel_type="casediary" ORDER by tag_order ASC) as tags',
                'start_date',
                'deadline',
                '(SELECT GROUP_CONCAT( tblproject_members.staff_id  SEPARATOR ",") FROM tblproject_members JOIN tblstaff on tblstaff.staffid = tblproject_members.staff_id WHERE tblproject_members.project_id=tblprojects.id ORDER BY staff_id) as members',

                'case_type',
                'file_no',
                'case_number',
                //'tblcourts.name as court_name',
               // 'tbloppositeparty.name as opposite_party',
                'tblprojects.clientid',
                '(SELECT GROUP_CONCAT(tblcaselawyerassignees.staffid SEPARATOR ",") FROM tblcaselawyerassignees WHERE tblcaselawyerassignees.case_id=tblprojects.id ORDER BY tblcaselawyerassignees.staffid) as assignees_ids',
                'status'

            );
            $where = array();
            $custom_date_select = $this->get_where_report_period('start_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('lawyerid2')) { 
                $lawyerid  = $this->input->post('lawyerid2');
                array_push($where, ' AND tblprojects.id IN (SELECT tblproject_members.project_id FROM tblproject_members WHERE tblproject_members.staff_id=' . $lawyerid . ')');
                
            }
          
            /*if ($this->input->post('invoice_status')) {
                $statuses  = $this->input->post('invoice_status');
                $_statuses = array();
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }*/
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblprojects';
            $join             = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblprojects.clientid',
                //'LEFT JOIN tbloppositeparty ON tbloppositeparty.id = tblprojects.opposite_party',
                //'LEFT JOIN tblcourts ON tblcourts.id = tblprojects.court',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblprojects.id',
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
              
            );
 
           /* foreach($invoice_taxes as $key => $tax){
                $footer_data['total_tax_single_'.$key] = 0;
            }*/

            foreach ($rResult as $aRow) {
                $row = array();

                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';

                $row[] = '<a href="' . admin_url('casediary/view/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';


                $membersOutput = '';

                $memberss        = explode(',', $aRow['members']);
                $exportMembers = '';
                foreach ($memberss as  $member) {
                    if ($member != '') {
                        $membersOutput .= '<a href="' . admin_url('profile/' . $member) . '">' .
                        staff_profile_image($member, array(
                            'staff-profile-image-small mright5'
                            ), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => get_staff_full_name($member)
                            )) . '</a>';
                                // For exporting
                        //$exportMembers .= $member . ', ';
                    }
                }

                $row[] = $membersOutput;


               // $row[] = $aRow['opposite_party'];

                $row[] = $aRow['file_no'];

                $row[] = _d($aRow['start_date']);

                $row[] = $aRow['case_number'];

                $status = get_caseproject_status_by_id($aRow['status']);
                $row[] = '<span class="label label inline-block project-status-' . $aRow['status'] . '" style="color:'.$status['color'].';border:1px solid '.$status['color'].'">' . $status['name'] . '</span>';

                $output['aaData'][] = $row;
            }

           /* foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }*/

            //$output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }


    public function matter_hearing_report()
    {
        if ($this->input->is_ajax_request()) {

            $select = array(
                'tblhearings.id as id',
                'tblhearings.hearing_date as hearing_date',
                'tblproject_instances.instance_name as hearing_type',
                'tblhearings.court_no as hearing_no',
                'tblhearings.subject as hearing_subject',
                'tblcourts.name  as court_name',
                'tblhearings.proceedings as court_decision',
                'tblhallnumber.name as hall_number',
                'tblclients.userid as userid',
                'tblcase_details.case_number as case_number',
            );
            $where= array();
            $custom_date_select = $this->get_where_report_period('hearing_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('hearing_type')) { 
                $hearing_type  = $this->input->post('hearing_type');
                array_push($where, ' AND tblhearings.h_instance_id ="' . $hearing_type . '"');    
            }

            if ($this->input->post('case_id')) { 
                $case_id  = $this->input->post('case_id');
                array_push($where, ' AND tblhearings.project_id ='. $case_id);
            }

            if ($this->input->post('clientid')) { 
                $clientid  = $this->input->post('clientid');
                array_push($where,' AND tblclients.userid ='. $clientid);
            }
            
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblhearings';
            $join             = array(
                'INNER JOIN tblprojects ON tblprojects.id = tblhearings.project_id',
				 'LEFT JOIN tblproject_instances ON tblproject_instances.id = tblhearings.h_instance_id',
                'INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid',
                'LEFT JOIN tblcase_details ON tblcase_details.project_id = tblprojects.id',
                'LEFT JOIN tblcourts ON tblcourts.id = tblcase_details.court_id',
                'LEFT JOIN tblhallnumber ON tblhallnumber.id = tblhearings.hall_number',
                 'LEFT JOIN tbloppositeparty ON tbloppositeparty.id = tblprojects.opposite_party',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                 'tblhearings.project_id',
                'tblclients.company as company',
                'tbloppositeparty.name as opposite_party',
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
              
            );

            
            foreach ($rResult as $aRow) {
                $row = array();
                $row[] = _d($aRow['hearing_date']);
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id'].'?group=hearings&type='.$aRow['hearing_type']) . '">' . $aRow['hearing_subject']. '</a>';
                $row[] = $aRow['hearing_type'];
                $row[] = $aRow['hearing_no'];
                
                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']).'">'. $aRow['case_number']. '</a>';
                $row[] = $aRow['opposite_party'];
                

                $row[] = $aRow['hall_number'];

                $row[] = $aRow['court_name'];

                $row[] = nl2br($aRow['court_decision']);



                $output['aaData'][] = $row;
            }

           /* foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }*/

            //$output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }
	public function matter_execution_report()
    {
        if ($this->input->is_ajax_request()) {

            $select = array(
                'tblcase_details.id as id',
                'tblcase_details.dateadded as case_date',
              //  'tblproject_instances.instance_name as hearing_type',
				'tblprojects.name as case_title',
				 'tblcase_details.case_details as case_subject',
				 'tblcase_details.claiming_amount as claiming_amount',
				'CONCAT(tblstaff.firstname," ",tblstaff.lastname) as legal_cordinator',
                'tblcourts.name  as court_name',
                'tblcase_details.details_of_claim as court_decision',
                'tblclients.userid as userid',
                'tblcase_details.case_number as case_number',
				'tblcase_natures.name as case_nature',
            );
            $where= array();
            $custom_date_select = $this->get_where_report_period('case_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

           // if ($this->input->post('hearing_type')) { 
            //    $hearing_type  = $this->input->post('hearing_type');
                array_push($where, ' AND tblcase_details.instance_id =5');    
         //   }

            if ($this->input->post('case_id')) { 
                $case_id  = $this->input->post('case_id');
                array_push($where, ' AND tblcase_details.project_id ='. $case_id);
            }

            if ($this->input->post('clientid5')) { 
                $clientid  = $this->input->post('clientid5');
                array_push($where,' AND tblclients.userid ='. $clientid);
            }
            
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblcase_details';
            $join             = array(
                'INNER JOIN tblprojects ON tblprojects.id = tblcase_details.project_id',
				  'INNER JOIN tblcase_natures ON tblcase_natures.id = tblcase_details.instance_casenature',
				 'INNER JOIN tblstaff ON tblstaff.staffid = tblcase_details.legal_cordinator',
				 'LEFT JOIN tblproject_instances ON tblproject_instances.id = tblcase_details.instance_id',
                'INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid',
                'LEFT JOIN tblcourts ON tblcourts.id = tblcase_details.court_id',
              //  'LEFT JOIN tblhallnumber ON tblhallnumber.id = tblhearings.hall_number',
                 'LEFT JOIN tbloppositeparty ON tbloppositeparty.id = tblprojects.opposite_party',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                 'tblcase_details.project_id',
                'tblclients.company as company',
                'tbloppositeparty.name as opposite_party',
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
              
            );

            
            foreach ($rResult as $aRow) {
                $row = array();
				  $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']).'">'. $aRow['case_title']. '</a>';
                $row[] = _d(date('Y-m-d',strtotime($aRow['case_date'])));
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id'].'?group=project_case_details') . '">' . $aRow['case_number']. '</a>';
              
              
                
                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';
                $row[] =  $aRow['legal_cordinator'];
                $row[] = $aRow['opposite_party'];
              
                $row[] = $aRow['court_name'];
				 $row[] = $aRow['case_nature'];
				  $row[] = $aRow['claiming_amount'];
				$row[] = nl2br($aRow['case_subject']);
                $row[] = nl2br($aRow['court_decision']);



                $output['aaData'][] = $row;
            }

           /* foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }*/

            //$output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }
	  public function matter_update_report()
    {
        if ($this->input->is_ajax_request()) {
// 'tblproject_updates.rel_type as rel_type',
            $select = array(
                'tblproject_updates.id as rel_id',
               
				'tblproject_updates.dateadded as update_date',
                'tblproject_updates.content as updates',
                'tblprojects.name as matter',
                'tblcourts.name  as court_name',
                'tblclients.userid as userid',
                'tblcase_details.case_number as case_number',
            );
            $where= array();
            $custom_date_select = $this->get_where_report_period('update_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
			 if ($this->input->post('clientid4')) { 
                $clientid  = $this->input->post('clientid4');
                array_push($where,' AND tblclients.userid ='. $clientid);
            }
            if ($this->input->post('matter_id')) { 
                $pname  = $this->input->post('matter_id');
                array_push($where, ' AND tblproject_updates.rel_id ="' . $pname . '"');    
            }

            if ($this->input->post('case_id')) { 
                $case_id  = $this->input->post('case_id');
                array_push($where, ' AND tblproject_updates.rel_id ='. $case_id);
            }

           
            
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblproject_updates';
            $join             = array(
                'INNER JOIN tblprojects ON tblprojects.id = tblproject_updates.rel_id',
				// 'LEFT JOIN tblproject_instances ON tblproject_instances.id = tblhearings.h_instance_id',
                'INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid',
                'LEFT JOIN tblcase_details ON tblcase_details.project_id = tblprojects.id',
                'LEFT JOIN tblcourts ON tblcourts.id = tblcase_details.court_id',
              // 'LEFT JOIN tblhallnumber ON tblhallnumber.id = tblhearings.hall_number',
                 'LEFT JOIN tbloppositeparty ON tbloppositeparty.id = tblprojects.opposite_party',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                 'tblproject_updates.rel_id',
                'tblclients.company as company',
                'tbloppositeparty.name as opposite_party',
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
              
            );

            
            foreach ($rResult as $aRow) {
                $row = array();
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['rel_id'].'?group=project_updates') . '">' . $aRow['matter']. '</a>';
                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';
				 $row[] = $aRow['opposite_party'];
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['rel_id']).'">'. $aRow['case_number']. '</a>';
                $row[] = _d($aRow['update_date']);
                
                $row[] = $aRow['court_name'];

                $row[] = nl2br($aRow['updates']);



                $output['aaData'][] = $row;
            }

           /* foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }*/

            //$output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }
    public function matter_settlement_report()
    {
        if ($this->input->is_ajax_request()) {

            $select = array(
                'tblrecoveries_installments.id as id',
				  'tblrecoveries_installments.recovery_id as recovery_id',
                'tblrecoveries_installments.installment_date as installment_date',
                'tblrecoveries_installments.installment_amount as installment_amount',
                'tblrecoveries_installments.installment_status as installment_status',
                'tblrecoveries_installments.remarks as remarks',
                'tblprojects.name  as matter_name',
                'tblclients.company as company',
                'tblprojects.nature_of_settlement as nature_type',
                'tblclients.userid as userid',
				'tbloppositeparty.name as opposite_party', 
              
            );
            $where= array();
			
            $custom_date_select = $this->get_where_report_period('installment_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
			 array_push($where, ' AND tblrecoveries_installments.recovery_type ="project_recovery"'); 
            if ($this->input->post('nature_type')) { 
                $nature_type  = $this->input->post('nature_type');
                array_push($where, ' AND tblprojects.nature_of_settlement ="' . $nature_type . '"');    
            }

                       
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblrecoveries_installments';
            $join             = array(
                'INNER JOIN tblprojects ON tblprojects.id = tblrecoveries_installments.recovery_id',
                'INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid',
                'INNER JOIN tbloppositeparty ON tbloppositeparty.id = tblprojects.opposite_party',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                 'tblrecoveries_installments.recovery_id',
                'tblclients.company as company',
                'tbloppositeparty.name as opposite_party',
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
              
            );

            
            foreach ($rResult as $aRow) {
                $row = array();
                $row[] = _d($aRow['installment_date']);
				  $row[] = $aRow['installment_amount'];
				 if($aRow['installment_status'] == 'not_paid'){
    $status = '<div  style="color:red;"><b>'._l($aRow['installment_status']).'</b></div>';

    }else{
    $status = '<div style="color:green;"><b>'._l($aRow['installment_status']).'</b></div>';

    }
    $row[] = $status;
				 
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['recovery_id'].'?group=project_settlement') . '">' . $aRow['matter_name']. '</a>';
				  
                               
                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';
               
                $row[] = $aRow['opposite_party'];
                

                $row[] = nl2br($aRow['remarks']);



                $output['aaData'][] = $row;
            }

           /* foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }*/

            //$output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

     public function clients_bd_report()
    {
        $this->load->model('casediary_model');
        $data['clients_bd_report'] = $this->casediary_model->clients_bd_report();
        $this->load->model('leads_model');
        if($this->input->post('view_status'))
            $data['selected_statuses'] = $this->input->post('view_status');
        $data['statuses'] = $this->leads_model->get_status();
        $data['staff']  =   $this->staff_model->get('', 1);
        $data['title']                 = _l('als_clients_bd_report');       
        $this->load->view('admin/reports/clients_bd_report', $data);
    }

    public function profitability_report()
    {
        $data['view_all'] = false;
        if (is_admin() && $this->input->get('view') == 'all') {
            $data['staff_members_with_timesheets'] = $this->db->query('SELECT DISTINCT staff_id FROM tbltaskstimers WHERE staff_id !='.get_staff_user_id())->result_array();
            $data['view_all'] = true;
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('profitability_report', array('view_all'=>$data['view_all']));
        }

        if ($data['view_all'] == false) {
            unset($data['view_all']);
        }
        $data['logged_time'] = $this->staff_model->get_logged_time_data(get_staff_user_id());
        $this->load->model('casediary_model');
        $data['arr_tasks'] = $this->casediary_model->get_all_tasks();
        $data['title'] = '';
        $this->load->view('admin/reports/profitability_report', $data);
    }

    

    public function matter_detailed($case_id='')
    {
       if($case_id != ''){
        $project_id = $case_id;
        $data['case_id'] = $project_id;
        $this->load->model('projects_model');

        $project = $this->projects_model->get($project_id);
        $this->ci->load->model('hearing_model');
        $hearings = $this->hearing_model->get_hearings_by_project_id($project_id);
        $members                = $this->projects_model->get_project_members($project->id);
        $project->currency_data = $this->projects_model->get_currency($project->id);

        // Add <br /> tag and wrap over div element every image to prevent overlaping over text
        $project->description = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<br><br><div>$1</div><br><br>', $project->description);

        $data['project']    = $project;
        $data['hearings']   = $hearings;
        $data['milestones'] = $this->projects_model->get_milestones($project->id);
        $data['timesheets'] = $this->projects_model->get_timesheets($project->id);

        $data['tasks']             = $this->projects_model->get_tasks($project->id, [], false);
        $data['total_logged_time'] = seconds_to_time_format($this->projects_model->total_logged_time($project->id));
        if ($project->deadline) {
            $data['total_days'] = '';//round((human_to_unix($project->deadline . ' 00:00') - human_to_unix($project->start_date . ' 00:00')) / 3600 / 24);
        } else {
            $data['total_days'] = '/';
        }
        $data['total_members'] = count($members);
        $data['total_tickets'] = total_rows(db_prefix().'tickets', [
                'project_id' => $project->id,
            ]);
        $data['total_invoices'] = total_rows(db_prefix().'invoices', [
                'project_id' => $project->id,
            ]);

        $this->load->model('invoices_model');

        $data['invoices_total_data'] = $this->invoices_model->get_invoices_total([
                'currency'   => $project->currency_data->id,
                'project_id' => $project->id,
            ]);

        $data['total_milestones']     = count($data['milestones']);
        $data['total_files_attached'] = total_rows(db_prefix().'project_files', [
                'project_id' => $project->id,
            ]);
        $data['total_discussion'] = total_rows(db_prefix().'projectdiscussions', [
                'project_id' => $project->id,
            ]);
        $data['members'] = $members;
        $data['hearing_types'] = get_project_instances();

       } 

       $data['title'] = _l('matter_detailed');
       $cases = $this->misc_model->_search_projects('');
       $data['cases'] = $cases['result'];
       $this->load->view('admin/reports/matter_detailed', $data);
    }


      public function documents_expiry_report()
    {
        if ($this->input->is_ajax_request()) {
            
            $select = array(
                'tblproject_files.id as id',
                'expiry_date',
                'subject',
                get_sql_select_client_company(),
                'issue_date',
            );
            $where = array();
            $custom_date_select = $this->get_where_report_period('expiry_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            array_push($where,' AND expiry_date != " " AND expiry_date != "0000-00-00"');
        
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblproject_files';
            $join = array(
                'LEFT JOIN tblprojects ON tblprojects.id = tblproject_files.project_id',
                'LEFT JOIN tblclients ON tblclients.userid = tblprojects.clientid',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblprojects.clientid as clientid','project_id',

            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];
            $footer_data = array();
            foreach ($rResult as $aRow) {
                $row = array();

                $subjectOutput = $aRow['subject'];
                $row[] = $subjectOutput;
                $row[] = '<a href="'.admin_url('clients/client/'.$aRow['clientid']).'">'. $aRow['company'] . '</a>';
                $case_name =  get_project_name_by_id($aRow['project_id']);
                $row[] = '<a href="'.admin_url('projects/view/'.$aRow['project_id']).'">'. $case_name . '</a>';
                $row[] = _d($aRow['issue_date']);
                $row[] = _d($aRow['expiry_date']);

                $output['aaData'][] = $row;
            }

          
            echo json_encode($output);
            die();
        }
    }

     public function receivables_report()
    {
        if ($this->input->is_ajax_request()) {
            $invoice_taxes = $this->distinct_taxes('invoice');
            $totalTaxesColumns = count($invoice_taxes);

            $this->load->model('currencies_model');
            $this->load->model('invoices_model');

            $select = array(
                'number',
                get_sql_select_client_company(),
                'YEAR(date) as year',
                'date',
                'duedate',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                '(SELECT COALESCE(SUM(amount),0) FROM tblcredits WHERE tblcredits.invoice_id=tblinvoices.id) as credits_applied',
                '(SELECT total - (SELECT COALESCE(SUM(amount),0) FROM tblinvoicepaymentrecords WHERE invoiceid = tblinvoices.id) - (SELECT COALESCE(SUM(amount),0) FROM tblcredits WHERE tblcredits.invoice_id=tblinvoices.id))',
                'status',
                'project_id'
            );

            $where  = array(
                'AND status != 5'
            );

            $invoiceTaxesSelect = array_reverse($invoice_taxes);

            foreach ($invoiceTaxesSelect as $key => $tax) {
                array_splice($select, 8, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="invoice" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'invoices.id) as total_tax_single_' . $key);
            }

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            if ($this->input->post('client')) {
                $client  = $this->input->post('client');
                
                array_push($where, 'AND userid =' . $client );
                
            }

            if ($this->input->post('sale_agent_invoices')) {
                $agents  = $this->input->post('sale_agent_invoices');
                $_agents = array();
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency = $this->input->post('report_currency');
            $totalPaymentsColumnIndex = (12+$totalTaxesColumns-1);

            if ($by_currency) {
                $_temp = substr($select[$totalPaymentsColumnIndex], 0, -2);
                $_temp .= ' AND currency =' . $by_currency . ')) as amount_open';
                $select[$totalPaymentsColumnIndex] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
                $select[$totalPaymentsColumnIndex] = $select[$totalPaymentsColumnIndex] .= ' as amount_open';
            }

            $currency_symbol = $currency->symbol;

            if ($this->input->post('invoice_status')) {
                $statuses  = $this->input->post('invoice_status');
                $_statuses = array();
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblinvoices';
            $join         = array(
                'JOIN tblclients ON tblclients.userid = tblinvoices.clientid'
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'userid',
                'clientid',
                'tblinvoices.id',
                'discount_percent',
                'sale_agent'
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0,
                'applied_credits' => 0,
                'amount_open' => 0
            );

            foreach($invoice_taxes as $key => $tax){
                $footer_data['total_tax_single_'.$key] = 0;
            }

            foreach ($rResult as $aRow) {
                $row = array();

                $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';

                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                $row[] = $aRow['year'];
                $case_name = get_project_name_by_id($aRow['project_id']);
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . $case_name . '</a>';

                /*$membersOutput = '';

                $memberss        = $aRow['sale_agent'];
                $exportMembers = '';
                foreach ($memberss as  $member) {
                    if ($member != '' && $member != 0) {
                        $membersOutput .= '<a href="' . admin_url('profile/' . $member) . '">' .
                        staff_profile_image($member, array(
                            'staff-profile-image-small mright5'
                            ), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => get_staff_full_name($member)
                            )) . '</a>';
                                // For exporting
                        //$exportMembers .= $member . ', ';
                    }
                }*/

                $row[] = get_staff_full_name($aRow['sale_agent']);//$membersOutput;

                $row[] = _d($aRow['date']);
                $row[] = _d($aRow['duedate']);

                $row[] = app_format_money($aRow['subtotal'],$currency->name,$currency_symbol);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'],$currency->name,$currency_symbol);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'],$currency->name,$currency_symbol);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach($invoice_taxes as $tax){
                    $row[] = app_format_money(($aRow['total_tax_single_'.$t] == null ? 0 : $aRow['total_tax_single_'.$t]),$currency->name,$currency_symbol);
                    $footer_data['total_tax_single_'.$i] += ($aRow['total_tax_single_'.$t] == null ? 0 : $aRow['total_tax_single_'.$t]);
                    $t--;
                    $i++;
                }

                $row[] = app_format_money($aRow['discount_total'],$currency->name,$currency_symbol);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'],$currency->name,$currency_symbol);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[] = app_format_money($aRow['credits_applied'],$currency->name,$currency_symbol);
                $footer_data['applied_credits'] += $aRow['credits_applied'];

                $amountOpen = $aRow['amount_open'];
                $row[] = app_format_money($amountOpen,$currency->name,$currency_symbol);
                $footer_data['amount_open'] += $amountOpen;

                $row[] = format_invoice_status($aRow['status']);

                $output['aaData'][] = $row;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total, $currency_symbol);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

      public function referrals_report()
    {
        if ($this->input->is_ajax_request()) {
            $invoice_taxes = $this->distinct_taxes('invoice');
            $totalTaxesColumns = count($invoice_taxes);

            $this->load->model('currencies_model');
            $this->load->model('invoices_model');

            $select = array(
                'number',
                get_sql_select_client_company(),
                'YEAR(tblinvoices.date) as year',
                'tblinvoices.date as date',
                'duedate',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                '(SELECT COALESCE(SUM(amount),0) FROM tblcredits WHERE tblcredits.invoice_id=tblinvoices.id) as credits_applied',
                '(SELECT total - (SELECT COALESCE(SUM(amount),0) FROM tblinvoicepaymentrecords WHERE invoiceid = tblinvoices.id) - (SELECT COALESCE(SUM(amount),0) FROM tblcredits WHERE tblcredits.invoice_id=tblinvoices.id))',
                'status',
                'project_id',
                'refered_employee_id',
                'sale_agent',
                'lawyer_assigned',
            );

            $where  = array(
                'AND status != 5',
            );

           

            $invoiceTaxesSelect = array_reverse($invoice_taxes);

            foreach ($invoiceTaxesSelect as $key => $tax) {
                array_splice($select, 8, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="invoice" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'invoices.id) as total_tax_single_' . $key);
            }


            $custom_date_select = $this->get_where_report_period('tblinvoicepaymentrecords.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('sale_agent_invoices')) {
                $agents  = $this->input->post('sale_agent_invoices');
                $_agents = array();
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            if ($this->input->post('clientid')) {
                $client  = $this->input->post('clientid');
                array_push($where, 'AND tblinvoices.clientid =' . $client );
                
            }
          

            $by_currency = $this->input->post('report_currency');
            $totalPaymentsColumnIndex = (12+$totalTaxesColumns-1);

            if ($by_currency) {
                $_temp = substr($select[$totalPaymentsColumnIndex], 0, -2);
                $_temp .= ' AND currency =' . $by_currency . ')) as amount_open';
                $select[$totalPaymentsColumnIndex] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
                $select[$totalPaymentsColumnIndex] = $select[$totalPaymentsColumnIndex] .= ' as amount_open';
            }

            $currency_symbol = $currency->symbol;

            if ($this->input->post('invoice_status')) {
                $statuses  = $this->input->post('invoice_status');
                $_statuses = array();
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblinvoices';
            $join         = array(
                'JOIN tblclients ON tblclients.userid = tblinvoices.clientid',
                'LEFT JOIN tblinvoicepaymentrecords ON tblinvoicepaymentrecords.invoiceid = tblinvoices.id',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'userid',
                'clientid',
                'tblinvoices.id',
                'discount_percent',
                'tblinvoicepaymentrecords.date as collected_date'
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0,
                'applied_credits' => 0,
                'amount_open' => 0
            );

            foreach($invoice_taxes as $key => $tax){
                $footer_data['total_tax_single_'.$key] = 0;
            }

            $expense_invoices_qry =  $this->db->select('invoiceid')->from('tblexpenses')->get()->result_array();

            $expense_invoice_ids = array_column($expense_invoices_qry,'invoiceid');

            foreach ($rResult as $aRow) {
                $row = array();
                if(!in_array($aRow['id'], $expense_invoice_ids)){
                $proposed_value='';
                $row[] = '<a href="'.admin_url('staff/member/'.$aRow['refered_employee_id']).'" target="_blank">'.get_staff_full_name($aRow['refered_employee_id']).'</a>';
                
                /*$membersOutput = '';
                if($aRow['sale_agent'] != '' && $aRow['sale_agent'] != 0){
                    $members        = explode(',', $aRow['sale_agent']);
                    $exportMembers = '';
                 foreach ($members as  $member) {
                    if ($member != '') {
                        $membersOutput .= '<a href="' . admin_url('profile/' . $member) . '">' .
                        staff_profile_image($member, array(
                            'staff-profile-image-small mright5'
                            ), 'small', array(
                            'data-toggle' => 'tooltip',
                            'data-title' => get_staff_full_name($member)
                            )) . '</a>';
                                // For exporting
                        $exportMembers .= get_staff_full_name($member) . ', ';
                    }
                }
                $membersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';
                $row[] = $membersOutput;
                }else{
                    $row[]='-';/
                }*/
                if($aRow['lawyer_assigned']>0){
                    $row[] = get_staff_full_name($aRow['lawyer_assigned']);
                }else{
                   $row[]='-'; 
                }
           

                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';

                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' .get_project_name_by_id($aRow['project_id']).'</a>';
                $case_row = $this->db->get_where('tblprojects',array('id'=>$aRow['project_id']))->row();
                $row[] = _l($case_row->case_type);
                $billing_type = $case_row->billing_type;
                if($billing_type == 1){
                    $type_name = 'project_billing_type_fixed_cost';
                } else if($billing_type == 2){
                   $type_name = 'project_billing_type_project_hours';
                } else if($billing_type == 3){
                   $type_name = 'project_billing_type_project_task_hours';
                }else if($billing_type == 4) {
                   $type_name = 'retainer';
                }else if($billing_type == 5) {
                   $type_name = 'probono';
                }else if($billing_type == 6) {
                   $type_name = 'success_fee';
                }else if($billing_type == 7) {
                   $type_name = 'no_fee_arrangement_yet';
                }else{
                    $type_name = '';
                }
                $row[] = _l($type_name);
                
                $proposed_value = $this->db->get_where('tblprojects',array('id'=>$aRow['project_id']))->row()->project_cost;
                $row[] = app_format_money($proposed_value,$currency_symbol);
                $row[] = _d($aRow['date']);
                $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';


               /* $row[] = $aRow['year'];


                $row[] = _d($aRow['duedate']);
*/
                $row[] = app_format_money($aRow['subtotal'],$currency_symbol);
                $footer_data['subtotal'] += $aRow['subtotal'];

                $row[] = app_format_money($aRow['total'],$currency_symbol);
                $footer_data['total'] += $aRow['total'];

                $row[] = app_format_money($aRow['total_tax'],$currency_symbol);
                $footer_data['total_tax'] += $aRow['total_tax'];

                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach($invoice_taxes as $tax){
                    $row[] = app_format_money($aRow['total_tax_single_'.$t] == null ? 0 : $aRow['total_tax_single_'.$t],$currency_symbol);
                    $footer_data['total_tax_single_'.$i] += ($aRow['total_tax_single_'.$t] == null ? 0 : $aRow['total_tax_single_'.$t]);
                    $t--;
                    $i++;
                }
                //$collected_date = $this->db->limit(1)->get_where('tblinvoicepaymentrecords',array('invoiceid'=>$aRow['id']))->row()->date;
                /*if($collected_date){
                    $row[] = _d($collected_date);
                }else{
                    $row[] = '-'; 
                }*/
                $row[] = _d($aRow['collected_date']);
                
                $collected_amount = sum_from_table('tblinvoicepaymentrecords', array(
                        'field' => 'amount',
                        'where' => array(
                            'invoiceid' => $aRow['id'],
                        )));

                $row[] = $collected_amount;
                $footer_data['collected_amount'] += $collected_amount;
                

                $referral_amount_10 = $aRow['subtotal'] * (10/100);
                $row[] = app_format_money($referral_amount_10,$currency_symbol);
                $footer_data['referral_1'] += $referral_amount_10;


                $referral_amount_15 = $aRow['subtotal'] * (15/100);
                $row[] = app_format_money($referral_amount_15,$currency_symbol);
                $footer_data['referral_2'] += $referral_amount_15;


                // Total Referral

                $total_referal = $referral_amount_10 + $referral_amount_15;
                $row[] = app_format_money($total_referal,$currency_symbol);
                $footer_data['total_referral'] += $total_referal;
####################
                $referral_amount_10_a = $collected_amount * (10/100);
                $row[] = app_format_money($referral_amount_10_a,$currency_symbol);
                $footer_data['referral_3'] += $referral_amount_10_a;


                $referral_amount_15_a = $collected_amount * (15/100);
                $row[] = app_format_money($referral_amount_15_a,$currency_symbol);
                $footer_data['referral_4'] += $referral_amount_15_a;

                
                $row[] = app_format_money($aRow['discount_total'],$currency_symbol);
                $footer_data['discount_total'] += $aRow['discount_total'];

                $row[] = app_format_money($aRow['adjustment'],$currency_symbol);
                $footer_data['adjustment'] += $aRow['adjustment'];

                $row[] = app_format_money($aRow['credits_applied'],$currency_symbol);
                $footer_data['applied_credits'] += $aRow['credits_applied'];

                $amountOpen = $aRow['amount_open'];
                $row[] = app_format_money($amountOpen,$currency_symbol);
                $footer_data['amount_open'] += $amountOpen;

               
                $row[] = format_invoice_status($aRow['status']);

                $output['aaData'][] = $row;
            }
            }


            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total,$currency_symbol);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }


     public function pb_report()
    {
        $data['mysqlVersion'] = $this->db->query('SELECT VERSION() as version')->row();
        $data['sqlMode'] = $this->db->query('SELECT @@sql_mode as mode')->row();

        if (is_using_multiple_currencies() || is_using_multiple_currencies('tblcreditnotes') || is_using_multiple_currencies('tblestimates') || is_using_multiple_currencies('tblproposals')) {
            $this->load->model('currencies_model');
            $data['currencies'] = $this->currencies_model->get();
        }
        $this->load->model('invoices_model');
        $this->load->model('estimates_model');
        $this->load->model('proposals_model');
        $this->load->model('credit_notes_model');

        $data['credit_notes_statuses']      = $this->credit_notes_model->get_statuses();
        $data['invoice_statuses']      = $this->invoices_model->get_statuses();
        $data['estimate_statuses']     = $this->estimates_model->get_statuses();
        $data['payments_years']        = $this->reports_model->get_distinct_payments_years();
        $data['estimates_sale_agents'] = $this->estimates_model->get_sale_agents();

        $data['invoices_sale_agents']  = $this->invoices_model->get_sale_agents();

        $data['proposals_sale_agents']  = $this->proposals_model->get_sale_agents();
        $agn = array_column($data['proposals_sale_agents'],'sale_agent');


        $simple_array = array(); //simple array

          foreach( $data['proposals_sale_agents'] as $d)
          {
                $simple_array[]= explode(',',$d['sale_agent']);   
          }
        $arraySingle = call_user_func_array('array_merge', $simple_array);
        $s = array_unique($arraySingle);
        //print_r($s);
        $data['proposals_sale_agents'] = $s;
        $data['proposals_statuses'] = $this->proposals_model->get_statuses();

        $data['invoice_taxes'] = $this->distinct_taxes('invoice');
        $data['estimate_taxes'] = $this->distinct_taxes('estimate');
        $data['proposal_taxes'] = $this->distinct_taxes('proposal');
        $data['credit_note_taxes'] = $this->distinct_taxes('credit_note');
        $where = array('designation'=>3);
        $data['staff_admins']  = $this->staff_model->get('',1,$where);

        $data['title']                 = _l('pb_report');
        $this->load->view('admin/reports/pb_report', $data);
    }

    public function pb()
    {
        if ($this->input->is_ajax_request()) {
            $invoice_taxes = $this->distinct_taxes('invoice');
            $totalTaxesColumns = count($invoice_taxes);

            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('staff_model');

            $where = array('designation'=>3);
            $staff_admins = $this->staff_model->get('',1,$where);
            $total_salary = 0;
            foreach ($staff_admins as $admins) {
                $total_salary = $total_salary + $admins['salary'];
            }
            $select = array(
                'number',
                get_sql_select_client_company(),
                'YEAR(tblinvoices.date) as year',
                'tblinvoices.date as date',
                'duedate',
                'subtotal',
                'total',
                'total_tax',
                'discount_total',
                'adjustment',
                '(SELECT COALESCE(SUM(amount),0) FROM tblcredits WHERE tblcredits.invoice_id=tblinvoices.id) as credits_applied',
                '(SELECT total - (SELECT COALESCE(SUM(amount),0) FROM tblinvoicepaymentrecords WHERE invoiceid = tblinvoices.id) - (SELECT COALESCE(SUM(amount),0) FROM tblcredits WHERE tblcredits.invoice_id=tblinvoices.id))',
                'tblinvoices.status',
                'project_id'
            );

            $where  = array(
                'AND tblinvoices.status != 5'
            );

            $invoiceTaxesSelect = array_reverse($invoice_taxes);

            foreach ($invoiceTaxesSelect as $key => $tax) {
                array_splice($select, 8, 0, '(
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * discount_percent/100)),' . get_decimal_places() . ')
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*' . db_prefix() . 'item_tax.taxrate) - (qty*rate/100*' . db_prefix() . 'item_tax.taxrate * (discount_total/subtotal*100) / 100)),' . get_decimal_places() . ')
                    ELSE ROUND(SUM(qty*rate/100*' . db_prefix() . 'item_tax.taxrate),' . get_decimal_places() . ')
                    END
                    FROM ' . db_prefix() . 'itemable
                    INNER JOIN ' . db_prefix() . 'item_tax ON ' . db_prefix() . 'item_tax.itemid=' . db_prefix() . 'itemable.id
                    WHERE ' . db_prefix() . 'itemable.rel_type="invoice" AND taxname="' . $tax['taxname'] . '" AND taxrate="' . $tax['taxrate'] . '" AND ' . db_prefix() . 'itemable.rel_id=' . db_prefix() . 'invoices.id) as total_tax_single_' . $key);
            }

            $custom_date_select = $this->get_where_report_period('tblinvoicepaymentrecords.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('sale_agent_invoices')) {
                $agents  = $this->input->post('sale_agent_invoices');
                $_agents = array();
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            if ($this->input->post('case_type')) {
                $case_type  = $this->input->post('case_type');
                array_push($where, 'AND tblprojects.case_type ="' . $case_type.'"' );
                
            }
          

            $by_currency = $this->input->post('report_currency');
            $totalPaymentsColumnIndex = (12+$totalTaxesColumns-1);

            if ($by_currency) {
                $_temp = substr($select[$totalPaymentsColumnIndex], 0, -2);
                $_temp .= ' AND currency =' . $by_currency . ')) as amount_open';
                $select[$totalPaymentsColumnIndex] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
                $select[$totalPaymentsColumnIndex] = $select[$totalPaymentsColumnIndex] .= ' as amount_open';
            }

            $currency_symbol = $currency->symbol;

            if ($this->input->post('invoice_status')) {
                $statuses  = $this->input->post('invoice_status');
                $_statuses = array();
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND tblinvoices.status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblinvoices';
            $join         = array(
                'JOIN tblclients ON tblclients.userid = tblinvoices.clientid',
                'LEFT JOIN tblprojects ON tblprojects.id = tblinvoices.project_id',
                'LEFT JOIN tblinvoicepaymentrecords ON tblinvoicepaymentrecords.invoiceid = tblinvoices.id',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'userid',
                'tblinvoices.clientid',
                'tblinvoices.id',
                'discount_percent'
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0,
                'applied_credits' => 0,
                'amount_open' => 0
            );

            foreach($invoice_taxes as $key => $tax){
                $footer_data['total_tax_single_'.$key] = 0;
            }
            $row[]='';
            $row[]='';
            $row[]='';
            $row[]='';
            $row[]='';
            $row[]='';
            $row[]='';
            $row[]='';
            $row[]='';
            $row[]=_l('cc');;
            $row[]=_l('lc');
            $row[]=_l('total');

            $row[]=_l('cc');

            $row[]=_l('cc');
            $row[]=_l('lc');
            $row[]=_l('total');

            $row[]=_l('cc');
            $row[]=_l('lc');
            $row[]=_l('total');
            foreach ($staff_admins as $admins) {
                $row[] = $admins['full_name'];
            }           
            $row[]=_l('total');
        
            $output['aaData'][] = $row;
            foreach ($rResult as $aRow) {

                $case_type = $this->db->get_where('tblprojects',array('id'=>$aRow['project_id']))->row()->case_type;
                if($case_type == 'court_case' || $case_type == 'legal_consultancy'){
                $row = array();
                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' .get_project_name_by_id($aRow['project_id']).'</a>';
                $billing_type = $this->db->get_where('tblprojects',array('id'=>$aRow['project_id']))->row()->billing_type;
                if($billing_type == 1){
                    $type_name = 'project_billing_type_fixed_cost';
                } else if($billing_type == 2){
                   $type_name = 'project_billing_type_project_hours';
                } else if($billing_type == 3){
                   $type_name = 'project_billing_type_project_task_hours';
                }else if($billing_type == 4) {
                   $type_name = 'retainer';
                }else if($billing_type == 5) {
                   $type_name = 'probono';
                }else if($billing_type == 6) {
                   $type_name = 'success_fee';
                }else if($billing_type == 7) {
                   $type_name = 'no_fee_arrangement_yet';
                }else{
                    $type_name = '';
                }
                $row[] = _l($type_name);
                
                $row[] = _l($case_type);
                $row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';
                $proposed_value=0;
                $proposed_value = $this->db->get_where('tblprojects',array('id'=>$aRow['project_id']))->row()->project_cost;
                $row[] = app_format_money($proposed_value,$currency_symbol);
                $footer_data['total_fees'] += $proposed_value;

                
                $row[] = app_format_money($aRow['subtotal'],$currency_symbol);
                $footer_data['subtotal'] += $aRow['subtotal'];
                $pb_cc = $pb_lc = $sk_cc = $sk_lc = $sk_total = $kfcc = $admin_share =
                $total_amdin_share =  $lawyer_share_cc = $lawyer_share_lc = $total_lawyer_share = 0;
                if($case_type == 'court_case'){
                    $pb_cc = round($aRow['subtotal'] * (9/100)); // 9 % of Collection mount
                    $row[] = app_format_money($pb_cc,$currency_symbol);
                    $row[] = '-';
                    $footer_data['pb_cc'] += $pb_cc;

                    ######## SK Share ##########
                    $sk_cc = round($aRow['subtotal'] * (1/100)); // 1 % of Collection mount
                    
                    $sk_total = $sk_lc + $sk_cc;

                    $row[] = app_format_money($sk_cc,$currency_symbol);
                    $row[] = '-';
                    $row[] = app_format_money($sk_total,$currency_symbol);
                    $footer_data['sk_cc'] += $sk_cc;
                    $footer_data['sk_total'] += $sk_total;

                    ########KF Share##############
                    $kfcc  = round($aRow['subtotal'] * (3/100)); // 3 % of Collection mount
                    $row[] = app_format_money($kfcc,$currency_symbol);
                    $footer_data['kf_share'] += $kfcc;

                    ######## ADMIN share ###############

                    $admin_share  = round($aRow['subtotal'] * (2/100)); // 2 % of Collection mount
                    $row[] = app_format_money($admin_share,$currency_symbol);
                    $row[] = '-';
                    $total_amdin_share =  $admin_share;
                    $row[] = app_format_money($total_amdin_share,$currency_symbol);
                    
                    $footer_data['admin_cc'] += $admin_share;
                    $footer_data['admin_total'] += $total_amdin_share;

                    ##########LAwyers Share#################
                    $lawyer_share_cc  = round($aRow['subtotal'] * (3/100)); // 3 % of Collection mount
                    
                    $row[] = app_format_money($lawyer_share_cc ,$currency_symbol);
                    $row[] = '-';
                    
                    $total_lawyer_share = $lawyer_share_cc ;
                    $row[] = app_format_money($total_lawyer_share,$currency_symbol);

                    $footer_data['lawyer_cc']    += $lawyer_share_cc;
                    $footer_data['lawyer_total'] += $total_lawyer_share;

                }else{
                    $row[] = '-';
                    $pb_lc = round($aRow['subtotal'] * (10/100));// 10 % of Collection mount
                    $row[] = app_format_money($pb_lc,$currency_symbol);
                    $footer_data['pb_lc'] += $pb_lc;

                    ######## SK share ###############
                    $sk_lc = round($aRow['subtotal'] * (2/100)); // 2 % of Collection mount
                    $sk_total = $sk_lc + $sk_cc;
                    $row[] = '-';
                    $row[] = app_format_money($sk_lc,$currency_symbol);
                    $row[] = app_format_money($sk_total,$currency_symbol); 
                    $footer_data['sk_lc'] += $sk_lc;
                    ########KF Share################
                    $row[]  = '-';
                    ######## ADMIN share ###############
                    $admin_share  = round($aRow['subtotal'] * (2/100)); // 2 % of Collection mount
                    $row[] = '-';
                    $row[] = app_format_money($admin_share,$currency_symbol);
                    $total_amdin_share =  $admin_share;
                    $row[] = app_format_money($total_amdin_share,$currency_symbol);
                    
                    $footer_data['admin_lc'] += $admin_share;
                    $footer_data['admin_total'] += $total_amdin_share;
                     ##########LAwyers Share#################
                    
                    $lawyer_share_lc  = round($aRow['subtotal'] * (6/100)); // 6 % of Collection mount
                    $row[] = '-';
                    $row[] = app_format_money($lawyer_share_lc,$currency_symbol);
                    
                    $total_lawyer_share = $lawyer_share_lc;
                    $row[] = app_format_money($total_lawyer_share,$currency_symbol);

                    $footer_data['lawyer_lc']    += $lawyer_share_lc;
                    $footer_data['lawyer_total'] += $total_lawyer_share;

                }
            
                $t = $totalTaxesColumns - 1;
                $i = 0;
                foreach($invoice_taxes as $tax){
                    //$row[] = app_format_money(($aRow['total_tax_single_'.$t] == null ? 0 : $aRow['total_tax_single_'.$t]),$currency_symbol);
                    $footer_data['total_tax_single_'.$i] += ($aRow['total_tax_single_'.$t] == null ? 0 : $aRow['total_tax_single_'.$t]);
                    $t--;
                    $i++;
                }
                $collected_amount = sum_from_table('tblinvoicepaymentrecords', array(
                        'field' => 'amount',
                        'where' => array(
                            'invoiceid' => $aRow['id'],
                        )));
                //$row[] = $collected_amount;
                $footer_data['collected_amount'] += $collected_amount;
                

                $t=0;
                foreach ($staff_admins as $admins) { 
                    $admin_u_share = 0;
                    $percent       = ($admins['salary']/$total_salary)*100;
                    $admin_u_share = round($total_amdin_share * ($percent/100)); 
                    $row[]         = app_format_money($admin_u_share,$currency_symbol);
                    //$admin_u_share = app_format_money($admin_u_share,$currency_symbol);
                    $footer_data['staff_share'][$t] += $admin_u_share;
                    $t++;
                }
                $row[] = app_format_money($total_amdin_share,$currency_symbol);
                

                $output['aaData'][] = $row;
                }
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = app_format_money($total,$currency_symbol);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function matter_litigation_report()
    {
        if ($this->input->is_ajax_request()) {

            $select = array(
                'tblprojects.id as id',
                 get_sql_select_client_company(),
                'opposite_party',
                '(SELECT tblcase_details.court_id  FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  AND details_type = "first_instance" ) as court_id',
                'tblprojects.name as name',
                '(SELECT GROUP_CONCAT(CONCAT(details_type," ^ ",tblcase_details.case_number) SEPARATOR "~") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as case_number',
                '(SELECT  GROUP_CONCAT(tblcase_details.case_details SEPARATOR "   ") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as case_updates',
                'start_date',
                'claiming_amount',
                'referred_by',
                '(SELECT  GROUP_CONCAT( DISTINCT tblcase_details.lawyer_id SEPARATOR " ") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as lawyer_ids',
            );
            $where = array();
            $custom_date_select = $this->get_where_report_period('start_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('clientid3')) {
                $client  = $this->input->post('clientid3');
                array_push($where, 'AND tblprojects.clientid =' . $client );
            }
            if ($this->input->post('p_status')) {
                $p_status  = $this->input->post('p_status');
                array_push($where, 'AND tblprojects.status =' . $p_status );
                
            }

            if ($this->input->post('opposite_party')) {
                $opposite_party  = $this->input->post('opposite_party');
                array_push($where, 'AND tblprojects.opposite_party =' . $opposite_party );
                
            }
          
            array_push($where, 'AND tblprojects.case_type ="court_case"'  );

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblprojects';
            $join             = array(
                'INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('clientid',
                'case_type',));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
              
            );

            
            $j=1;
           
            foreach ($rResult as $aRow) {
                $row = array();

                $row[] = $j++;
                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                $row[] = get_opposite_party_name($aRow['opposite_party']);
                $row[] = get_court_name_by_id($aRow['court_id']);
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';

                $casenum = '';
                $explode_casenumber = explode('~',$aRow['case_number']);
                foreach ($explode_casenumber as $cs) {                    
                    $exp = explode('^',$cs);
                    if(isset($exp[0]) && isset($exp[1]))
                    $casenum .= _l($exp[0]).' - '.$exp[1].'<br><br><br>';
                }
                $row[] = $casenum;

                $row[] = get_casedetails_complete_update($aRow['id']);


                $row[] = get_hearing_latest_date($aRow['id']);

                $row[] = $aRow['claiming_amount'];
                $row[] = $aRow['referred_by'];

                $membersOutput = '';

                $members        = explode(',', $aRow['lawyer_ids']);
                $exportMembers = '';
                foreach ($members as  $member) {
                    if ($member != '') {
                        $membersOutput .= get_staff_full_name($member);
                    }
                }
                
                $row[] = $membersOutput;
                $row[] = isset($aRow['lawyer_id']) ? get_staff_full_name($aRow['lawyer_id']) : '';
                
                
                $output['aaData'][] = $row;
            }

           /* foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }*/

            //$output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function matter_others_report()
    {
        if ($this->input->is_ajax_request()) {

            $select = array(
                'tblprojects.id as id',
                'tblprojects.name as name',
                'content',
                 get_sql_select_client_company(),
                'referred_by',
                '(SELECT  GROUP_CONCAT( DISTINCT tblcase_details.lawyer_id SEPARATOR ",") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as lawyer_ids',
            );
            $where = array();
            $custom_date_select = $this->get_where_report_period('start_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('clientid2')) {
                $client  = $this->input->post('clientid2');
                array_push($where, 'AND tblprojects.clientid =' . $client );
                
            }
          
            array_push($where, 'AND tblprojects.case_type !="court_case"'  );

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblprojects';
            $join             = array(
                'INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid',
                'LEFT JOIN tblproject_notes ON tblproject_notes.project_id = tblprojects.id',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('clientid',
                'case_type', 

            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
              
            );

            
            $j=1;
            foreach ($rResult as $aRow) {
                $row = array();

                $row[] = $j++;
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';
                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';

                $row[] = get_casedetails_complete_update($aRow['id']);
                $row[] = $aRow['referred_by'];
                $membersOutput = '';

                $members        = explode(',', $aRow['lawyer_ids']);
                $exportMembers = '';
                foreach ($members as  $member) {
                    if ($member != '') {
                        $membersOutput .= get_staff_full_name($member);
                    }
                }
                
                $row[] = $membersOutput;
                
                
                $output['aaData'][] = $row;
            }

           /* foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }*/

            //$output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }


    public function agreements_report(){

        if ($this->input->is_ajax_request()) {
            $base_currency = get_base_currency();

            $aColumns = [
                db_prefix() . 'contracts.id as id',
                db_prefix() . 'contracts_types.name as type_name',
                get_sql_select_client_company(),
                'subject',
                //db_prefix() . 'contracts.description as description',
                '(SELECT  GROUP_CONCAT(tblnotes.description SEPARATOR "\n") FROM tblnotes WHERE tblnotes.rel_id=tblcontracts.id AND rel_type="contract" ORDER BY tblnotes.id) as description',
                db_prefix() . 'contracts.addedfrom as addedfrom', 
                /*'datestart',
                'dateend',
                db_prefix() . 'projects.name as project_name',
                'signature',*/
                ];

            $sIndexColumn = 'id';
            $sTable       = db_prefix() . 'contracts';

            $join = [
                'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'contracts.client',
                'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'contracts.project_id',
                'LEFT JOIN ' . db_prefix() . 'contracts_types ON ' . db_prefix() . 'contracts_types.id = ' . db_prefix() . 'contracts.contract_type',
            ];


            $where  = [];
            $filter = [];

            $custom_date_select = $this->get_where_report_period('datestart');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if (!has_permission('contracts', '', 'view')) {
                array_push($where, 'AND ' . db_prefix() . 'contracts.addedfrom=' . get_staff_user_id());
            }



            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'contracts.id', 'trash', 'client', 'hash', 'marked_as_signed', 'project_id','other_party']);

            $output  = $result['output'];
            $rResult = $result['rResult'];
            $j= 1 ;
            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = $aRow['id'];

                $row[] = $aRow['type_name'];

                $row[] = $aRow['other_party'].' Vs  <a href="' . admin_url('clients/client/' . $aRow['client']) . '">' . $aRow['company'] . '</a>';

                $subjectOutput = '<a href="' . admin_url('contracts/contract/' . $aRow['id']) . '"' . ' target="_blank"'  . '>' . $aRow['subject'] . '</a>';
                if ($aRow['trash'] == 1) {
                    $subjectOutput .= '<span class="label label-danger pull-right">' . _l('contract_trash') . '</span>';
                }

                $subjectOutput .= '<div class="row-options">';

                $subjectOutput .= '<a href="' . site_url('contract/' . $aRow['id'] . '/' . $aRow['hash']) . '" target="_blank">' . _l('view') . '</a>';

                if (has_permission('contracts', '', 'edit')) {
                    $subjectOutput .= ' | <a href="' . admin_url('contracts/contract/' . $aRow['id']) . '">' . _l('edit') . '</a>';
                }

                if (has_permission('contracts', '', 'delete')) {
                    $subjectOutput .= ' | <a href="' . admin_url('contracts/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                }

                $subjectOutput .= '</div>';
                $row[] = $subjectOutput;

                $row[] = get_contracts_complete_update($aRow['id']);
                $row[] = get_staff_full_name($aRow['addedfrom']);

                
/*
                $row[] = app_format_money($aRow['contract_value'], $base_currency);

                $row[] = _d($aRow['datestart']);

                $row[] = _d($aRow['dateend']);

                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . $aRow['project_name'] . '</a>';

                if ($aRow['marked_as_signed'] == 1) {
                    $row[] = '<span class="text-success">' . _l('marked_as_signed') . '</span>';
                } elseif (!empty($aRow['signature'])) {
                    $row[] = '<span class="text-success">' . _l('is_signed') . '</span>';
                } else {
                    $row[] = '<span class="text-muted">' . _l('is_not_signed') . '</span>';
                }*/

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();

        }
    }

    public function matter_cheque_bounce_report()
    {
        if ($this->input->is_ajax_request()) {

            $select = array(
                'tblprojects.id as id',
                'tblprojects.name as name',
                'file_no',
                get_sql_select_client_company(),
                'cheque_date',
                'cheque_no',
                'cheque_issue_date',
                'cheque_due_date',
                'cheque_amount',
                'approval_status',
                'cheque_status',
                'remarks'
            );
            $where = array();
            $custom_date_select = $this->get_where_report_period('start_date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('clientid2')) {
                $client  = $this->input->post('clientid2');
                array_push($where, 'AND tblprojects.clientid =' . $client );
                
            }
          
            array_push($where, 'AND tblprojects.case_type = "chequebounce"'  );

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblprojects';
            $join             = array(
                'INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('clientid',
                'case_type', 

            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data = array(
              
            );

            
            $j=1;
            foreach ($rResult as $aRow) {
                $row = array();

                $row[] = $j++;
                $row[] = '<a href="' . admin_url('projects/view/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';
                $row[] = $aRow['file_no'];
                $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                $row[] = $aRow['cheque_date'];
                $row[] = $aRow['cheque_no'];
                $row[] = $aRow['cheque_issue_date'];
                $row[] = $aRow['cheque_due_date'];
                $row[] = $aRow['cheque_amount'];
                $row[] = _l($aRow['approval_status']);
                $row[] = $aRow['cheque_status'];
                $row[] = $aRow['remarks'];
                $output['aaData'][] = $row;
            }

           /* foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }*/

            //$output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    
}
