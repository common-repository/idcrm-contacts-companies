<?php

namespace idcrm\includes\lib;

function delete_crm_roles() {
    // remove_role( 'crm_manager' );
    remove_role( 'lead' );
    remove_role( 'crm_support' );
}

?>
