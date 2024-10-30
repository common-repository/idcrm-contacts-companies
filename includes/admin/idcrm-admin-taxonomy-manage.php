<?php

namespace idcrm\admin;

use idcrm\includes\actions\idCRMActionLanguage;

if ( ! class_exists( '\idcrm\includes\api\idCRMAdminTaxonomyManage' ) ) {
    class idCRMAdminTaxonomyManage {
        public static function register()
        {
            $handler = new self();
            add_action( 'contact_events_add_form_fields', array($handler, 'taxonomyAddNewMetaField'), 10, 2 );
            add_action( 'contact_events_edit_form_fields', array($handler, 'taxonomyEditMetaField'), 10, 2 );
            add_action( 'edited_contact_events', array($handler, 'taxonomySaveCustomMeta'), 10, 2 );
            add_action( 'create_contact_events', array($handler, 'taxonomySaveCustomMeta'), 10, 2 );
        }
        /*
        * {$taxonomy}_add_form_fields
        * Админ Форма Термин
        * Класс idCRMAdminTaxonomyManage
        */
        /* добавляет поле custom_icon_type в форму добавления термина */
        /* taxonomy contact_events */
        /* admin ui custom taxonomy contact_events add form custom fiels */
        function taxonomyAddNewMetaField() {
            ?>
                <div class="form-field">
                    <label for="custom_icon_type"><?php _e( 'Event icon', idCRMActionLanguage::TEXTDOMAIN ); ?></label>
                    <input type="text" name="custom_icon_type" id="custom_icon_type" value="">
                    <p class="description"><?php _e( 'Enter an icon name, example: people. Icon list: https://simplelineicons.github.io',idCRMActionLanguage::TEXTDOMAIN ); ?></p>
                </div>
            <?php
        }
        /*
        * {$taxonomy}_edit_form_fields
        * Админ Форма Термин
        * Класс idCRMAdminTaxonomyManage
        */
        /* добавляет поле custom_icon_type в форму редактирования термина */
        /* taxonomy contact_events */
        /* admin ui custom taxonomy contact_events edit form custom fiels */
        function taxonomyEditMetaField($term) { ?>
            <tr class="form-field">
            <th scope="row" valign="top"><label for="custom_icon_type"><?php _e( 'Event icon', idCRMActionLanguage::TEXTDOMAIN ); ?></label></th>
                <td>
                    <input type="text" name="custom_icon_type" id="custom_icon_type" value="<?php echo esc_attr( get_term_meta($term->term_id,'custom_icon_type', true) ) ? esc_attr( get_term_meta($term->term_id,'custom_icon_type', true) ) : ''; ?>">
                    <p class="description"><?php _e( 'Enter an icon name, example: people. Icon list: https://simplelineicons.github.io',idCRMActionLanguage::TEXTDOMAIN ); ?></p>
                </td>
            </tr>
        <?php
        }
        /*
        * edited_(taxonomy)
        * create_{$taxonomy}
        * Админ Форма Термин
        * Класс idCRMAdminTaxonomyManage
        */
        /* обновление мета иконки после формы добавления или редактирования термина*/
        /* taxonomy contact_events */
        /* admin ui custom taxonomy contact_events after add or edit form meta update */
        function taxonomySaveCustomMeta( $term_id ) {
            if ( array_key_exists('custom_icon_type', $_POST) ) {
                update_term_meta($term_id, 'custom_icon_type', $_POST['custom_icon_type']);
            }
        }
    }
}

?>