<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Contract_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
                [
                    'name'      => 'Contract ID',
                    'key'       => '{contract_id}',
                    'available' => [
                        'contract',
                    ],
                ],
                [
                    'name'      => 'Contract Subject',
                    'key'       => '{subject}',
                    'available' => [
                        'contract',
                    ],
                ],
                [
                    'name'      => 'Contract Description',
                    'key'       => '{contract_description}',
                    'available' => [
                        'contract',
                    ],
                ],
                [
                    'name'      => 'Contract Date Start',
                    'key'       => '{contract_datestart}',
                    'available' => [
                        'contract',
                    ],
                ],
                [
                    'name'      => 'Contract Date End',
                    'key'       => '{contract_dateend}',
                    'available' => [
                        'contract',
                    ],
                ],
                [
                    'name'      => 'Contract Value',
                    'key'       => '{contract_contract_value}',
                    'available' => [
                        'contract',
                    ],
                ],
			[
                    'name'      => 'Contract Value In Words',
                    'key'       => '{contract_amount_in_words}',
                    'available' => [
                        'contract',
                    ],
                ],
                [
                    'name'      => 'Contract Link',
                    'key'       => '{contract_link}',
                    'available' => [
                        'contract',
                    ],
                ],
                  [
                    'name'      => 'Otherparty Link',
                    'key'       => '{otherparty_link}',
                    'available' => [
                        'contract',
                    ],
                ],
                
                
                
                
                [
                    'name'      => 'Contract Type',
                    'key'       => '{contract_type}',
                    'available' => [
                        'contract',
                    ],
                ],
                [
                    'name'      => 'Project name',
                    'key'       => '{project_name}',
                    'available' => [
                        'contract',
                    ],
                ],
			    [
                    'name'      => 'Other Party',
                    'key'       => '{other_party}',
                    'available' => [
                        'contract',
                    ],
                ],
			 [
                    'name'      => 'Other Party Adress',
                    'key'       => '{other_party_address}',
                    'available' => [
                        'contract',
                    ],
                ],
			[
                    'name'      => 'No Of Installment',
                    'key'       => '{no_of_installment}',
                    'available' => [
                        'contract',
                    ],
                ],
			[
                    'name'      => 'Installment Amount',
                    'key'       => '{installment_amount}',
                    'available' => [
                        'contract',
                    ],
                ],
			[
                    'name'      => 'Installment Amount In Words',
                    'key'       => '{installment_amount_in_words}',
                    'available' => [
                        'contract',
                    ],
                ],
			 [
                    'name'      => "Today's Date ",
                    'key'       => '{todays_date}',
                    'available' => [
                        'contract',
                    ],
                ],
				[
        'name' => "Customer Signature",
        'key' => '{customer_signature}',
        'available' => [
			'contract',
        ],
      ],
	  [
        'name' => "Party Signature",
        'key' => '{party_signature}',
        'available' => [
			'contract',
        ],
      ],
            ];
    }

    /**
     * Merge field for contracts
     * @param  mixed $contract_id contract id
     * @return array
     */
    public function format($contract_id)
    {
        $fields = [];
        $this->ci->db->select(db_prefix() . 'contracts.id as id, subject, description, datestart,other_party, dateend,client,contract_value, hash,otherparty_hash, project_id, ' . db_prefix() . 'contracts_types.name as type_name,no_of_installment,installment_amount,party_signature,signature');
        $this->ci->db->where('contracts.id', $contract_id);
        $this->ci->db->join(db_prefix() . 'contracts_types', '' . db_prefix() . 'contracts_types.id = ' . db_prefix() . 'contracts.contract_type', 'left');
        $contract = $this->ci->db->get(db_prefix() . 'contracts')->row();

        if (!$contract) {
            return $fields;
        }

        $currency = get_base_currency();

        $fields['{contract_id}']             = $contract->id;
        $fields['{contract_subject}']        = $contract->subject;
        $fields['{contract_type}']           = $contract->type_name;
        $fields['{contract_description}']    = nl2br($contract->description);
        $fields['{contract_datestart}']      = _d($contract->datestart);
        $fields['{contract_dateend}']        = _d($contract->dateend);
        $fields['{contract_contract_value}'] = app_format_money($contract->contract_value, $currency);
		$this->ci->load->library('app_number_to_word', [ 'clientid' => $contract->client  ], 'numberword');
		$fields['{contract_amount_in_words}'] =  $this->ci->numberword->convert($contract->contract_value, $currency->name);
        $fields['{contract_link}']      = site_url('contract/' . $contract->id . '/' . $contract->hash);
        //$fields['{otherparty_link}']      = site_url('contract/' . $contract->id . '/' . $contract->otherparty_hash);
         $fields['{otherparty_link}']      = site_url('files/index/' . $contract->otherparty_hash);
        $fields['{project_name}']       = get_project_name_by_id($contract->project_id);
		$fields['{other_party}']       = get_opposite_party_name($contract->other_party);
		$fields['{other_party_address}'] = get_buyerinfo($contract->other_party);
        $fields['{contract_short_url}'] = get_contract_shortlink($contract);
		 $fields['{no_of_installment}']        = $contract->no_of_installment;
		 $fields['{installment_amount}']        = $contract->installment_amount;
		$fields['{todays_date}']             =date('d F, Y');// _d(date('Y-m-d'));
		$fields['{installment_amount_in_words}'] =  $this->ci->numberword->convert($contract->installment_amount, $currency->name);
		$customerSignature = '';
		$partySignature = '';
		if (!empty($contract->signature)) {
            $customerSignaturePath = get_upload_path_by_type('contract') . $contract->id . '/' . $contract->signature;
           
			 if (!empty($customerSignaturePath) && file_exists($customerSignaturePath)) {
            
            // $imageData = base64_encode(file_get_contents($customerSignaturePath));
            // $customerSignature .= str_repeat('<br />', hooks()->apply_filters('pdf_signature_break_lines', 1)) . '<img width=200 height=100 src="@' . $imageData . '">';

            $imageData = $customerSignaturePath;

            $customerSignature .= str_repeat('<br />', hooks()->apply_filters('pdf_signature_break_lines', 1)) . '<img width=200 height=100 src="' . $imageData . '">';
			 }
			
        }
		$fields[ '{customer_signature}' ] =$customerSignature;
		if (!empty($contract->party_signature)) {
            $partySignaturePath = get_upload_path_by_type('contract') . $contract->id . '/' . $contract->party_signature;
           
			 if (!empty($partySignaturePath) && file_exists($partySignaturePath)) {
            
            // $imageData = base64_encode(file_get_contents($customerSignaturePath));
            // $customerSignature .= str_repeat('<br />', hooks()->apply_filters('pdf_signature_break_lines', 1)) . '<img width=200 height=100 src="@' . $imageData . '">';

            $imageData1 = $partySignaturePath;

            $partySignature .= str_repeat('<br />', hooks()->apply_filters('pdf_signature_break_lines', 1)) . '<img width=200 height=100 src="' . $imageData1 . '">';
			 }
			
        }
		
		$fields[ '{party_signature}' ] =$partySignature;
        $custom_fields = get_custom_fields('contracts');
        foreach ($custom_fields as $field) {
            $fields['{' . $field['slug'] . '}'] = get_custom_field_value($contract_id, $field['id'], 'contracts');
        }

        return hooks()->apply_filters('contract_merge_fields', $fields, [
        'id'       => $contract_id,
        'contract' => $contract,
     ]);
    }
}
