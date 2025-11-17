
$('#case_type').change(function(){
    var type = $(this).val();
    var CaseDetailsTab  = $('#available_features option[value="case_details"]');
    var HearingsTab     = $('#available_features option[value="hearings"]');
    
    if(type == 'court_case'){ 
        CaseDetailsTab.prop('selected',true);
        HearingsTab.prop('selected',true); 
    }else if(type == 'legal_consultancy'){ 
        CaseDetailsTab.prop('selected',true);
        HearingsTab.prop('selected',false); 
    }else if(type == 'personal_law'){ 
        CaseDetailsTab.prop('selected',true);
        HearingsTab.prop('selected',false); 
    }else if(type == 'internal_matters'){ 
        CaseDetailsTab.prop('selected',false);
        HearingsTab.prop('selected',false); 
    }else{
        CaseDetailsTab.prop('selected',false);
        HearingsTab.prop('selected',false);
    }
   $('#available_features').selectpicker('refresh');
});

