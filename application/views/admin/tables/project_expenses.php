<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'expenses.id',
    db_prefix() . 'expenses_categories.name as category_name',
    'amount',
	'paid_amount','last_amount','vat_amount','balance_amount',
    'expense_name',
	 '(SELECT GROUP_CONCAT(tblfiles.file_name SEPARATOR "<br>") FROM tblfiles WHERE tblfiles.rel_id=tblexpenses.id  AND rel_type = "expense" ) as file_name',
    'date',
   // 'invoiceid',
	//'tblexpense_approval_names.approval_name as reference_no',
	'paid_status',
    'reference_no',
    'approve_status',
	'tblexpenses.project_id as project_id',
	
];
$join = [
    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'expenses.clientid',
    'LEFT JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
	 'LEFT JOIN ' . db_prefix() . 'expense_approval_names ON ' . db_prefix() . 'expense_approval_names.id = ' . db_prefix() . 'expenses.approvalid',
   // 'LEFT JOIN ' . db_prefix() . 'files ON ' . db_prefix() . 'files.rel_id = ' . db_prefix() . 'expenses.id AND rel_type="expense"',
];
$custom_fields = get_custom_fields('expenses', [
    'show_on_table' => 1,
]);
$i = 0;
foreach ($custom_fields as $field) {
    array_push($aColumns, 'ctable_' . $i . '.value as cvalue_' . $i);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $i . ' ON ' . db_prefix() . 'expenses.id = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}
$where  = [];
$filter = [];
include_once(APPPATH . 'views/admin/tables/includes/expenses_filter.php');

array_push($where, 'AND tblexpenses.project_id=' . $this->ci->db->escape_str($project_id));

if (!has_permission('expenses', '', 'view')) {
    array_push($where, 'AND ' . db_prefix() . 'expenses.addedfrom=' . get_staff_user_id());
}
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'expenses';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'billable',
    'invoiceid',
    'currency',
    'tax',
    'tax2','budget_id','isbudget','tblexpenses.addedfrom as addedfrom'
]);
$output  = $result['output'];
$rResult = $result['rResult'];
$this->ci->load->model('payment_modes_model');
$this->ci->load->model('tickets_model');
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == db_prefix() . 'expenses.id') {
            $_data = '<span class="label label-default inline-block">' . $_data . '</span>';
        } elseif (strpos($aColumns[$i], 'category_name') !== false) {
            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow[db_prefix() . 'expenses.id']) . '" target="_blank">' . $aRow['category_name'] . '</a>';
            if ($aRow['billable'] == 1) {
                if ($aRow['invoiceid'] == null) {
                    $_data .= '<p class="text-danger">' . _l('expense_list_unbilled') . '</p>';
                } else {
                    if (total_rows(db_prefix() . 'invoices', [
                        'id' => $aRow['invoiceid'],
                        'status' => 2,
                    ]) > 0) {
                        $_data .= '<br /><p class="text-success">' . _l('expense_list_billed') . '</p>';
                    } else {
                        $_data .= '<p class="text-success">' . _l('expense_list_invoice') . '</p>';
                    }
                }
            }
        } elseif ($aColumns[$i] == 'amount') {
            $total     = $_data;
            $tmp_total = $total;
            if ($aRow['tax'] != 0) {
                $_tax = get_tax_by_id($aRow['tax']);
                $total += ($total / 100 * $_tax->taxrate);
            }
            if ($aRow['tax2'] != 0) {
                $_tax = get_tax_by_id($aRow['tax2']);
                $total += ($tmp_total / 100 * $_tax->taxrate);
            }
            $_data = app_format_money($total, get_currency($aRow['currency']));
        }elseif($aColumns[$i] == 'paid_amount') {
			$_data=$_data;
		}elseif($aColumns[$i] == 'last_amount') {
			$_data=$_data;
		}
		elseif($aColumns[$i] == 'vat_amount') {
			$_data=$_data;
		}
		elseif($aColumns[$i] == 'balance_amount') {
				$_data=($aRow['amount']-$aRow['paid_amount']);
		}
		elseif ($aColumns[$i] == 'approve_status') {
            $_data = '';
            /*if ($aRow['paymentmode'] != '0' && !empty($aRow['paymentmode'])) {
                $_data = $this->ci->payment_modes_model->get($aRow['paymentmode'])->name;
            }*/
            $appro_statuses = $this->ci->expenses_model-> get_expenses_status();

            $_data2 = '<select id="approve_status" onchange="change_expense_approval_status(this,'.$aRow[db_prefix() . 'expenses.id'].')" class="form-control" name="approve_status">';
                foreach($appro_statuses as $rows) { 
                    $_data2 .='<option value="'.$rows['ticketstatusid'].'"';
                            if($rows['ticketstatusid'] == $aRow['approve_status']) { $_data2 .='selected'; }$_data2 .= ' >'.$rows['name'].'</option>';
                            };
                    $_data2 .='</select>';
               $_data = $_data2;
        } elseif ($aColumns[$i] ==  'file_name') {
		  if (!empty($_data)) {
                $_data = '<a href="' . site_url('download/file/expense/' . $aRow[db_prefix() . 'expenses.id']) . '">' . $_data . '</a>';
            }
        } elseif ($aColumns[$i] == 'date') {
            $_data = _d($_data);
        }elseif ($aColumns[$i] == 'tblexpenses.project_id as project_id') {
			 $_data = '';
			$paylabel=($aRow['isbudget']=='yes')? _l('expense_set_settlement'):_l('expense_set_payment');
            $_data3 = '<a href="#" data-toggle="tooltip" data-title="'. _l('payment').'" class="btn btn-info  btn-icon " data-placement="bottom" onclick="new_expense_payment(' . $aRow[db_prefix() . 'expenses.id']. ',' . $aRow['project_id'] . '); return false;">'
          .$paylabel .
                '</a>';
			$_data=$_data3;
        }elseif ($aColumns[$i] == 'paid_status') {
            $_data = ucwords($_data);
        }/* elseif ($aColumns[$i] == 'invoiceid') {
            if ($_data) {
                $_data = '<a href="' . admin_url('invoices/list_invoices/' . $_data) . '">' . format_invoice_number($_data) . '</a>';
            } else {
                $_data = '';
            }
        } */else {
            if (startsWith($aColumns[$i], 'ctable_') && is_date($_data)) {
                $_data = _d($_data);
            }
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
