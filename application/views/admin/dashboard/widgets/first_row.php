<?php 
 
//$boxOrder=[1,2,4,3,5,6,9,7,10,8,11,12,13];

echo '<div class="row"><div class="col-md-12">';
foreach ($boxOrder as $key=>$value) { 
        // Hardcoded content for each box
        $boxContent = getBoxContent($value['id']);
        $boxData = $boxContent;
        echo $boxData;
    }
echo '</div></div>';


function getBoxContent($boxId) {
        switch ($boxId) {
            case 1:   
              return first_box();
            case 2:
              return second_box();
            case 3:
              return third_box();
            case 4:
              return fourth_box();
            case 5:
              return fifth_box();
            case 6:
              return sixth_box();
            case 7:
              return seventh_box();
            case 8:
              return eighth_box();
            case 9:
              return ninth_box();
            case 10:
              return tenth_box();
            case 11:
              return leventh_box();
            case 12:
              return twelth_box();
            case 13:
              return thurtinth_box();
            case 14:
              return fourteenth_box();
            case 15:
              return ip_trademark_box();
            case 16:
              return ip_patent_box();
            case 17:
              return ip_domain_box();
              case 18:
                return litigation_cases_box();
              case 19:
                return non_litigation_cases_box();
              
        }
    }

  ?>
