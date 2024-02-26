<?php
/*
Plugin Name: Image Checker
Description: Deshabilita el botón de publicar en productos de WooCommerce si no hay imágenes en la galería.
Version: 1.0
Author: Fernando Isaac Gonzalez Medina
*/
//Acción que cambia un producto publicado a borrador si no tiene imágenes
add_action('save_post', 'check_product_images', 10, 3);
function check_product_images($post_id, $post, $update) {
    if ($post->post_type == 'product' && $post->post_status == 'publish') {
        $product = wc_get_product($post_id);
        $attachment_ids = $product->get_gallery_image_ids();
        if (empty($attachment_ids)) {
            // Cambiamos el estado a 'Borrador'
            $post->post_status = 'draft';
            wp_update_post($post);
        }
    }
}
//Parte que hookea correctamente BEAR para cambiar el status en tiempo real
add_filter('woobe_new_product_status', function ($status) {
    // Verificar si la galería está vacía
    if (empty(get_post_gallery(get_the_ID(), false))) {
        // La galería está vacía, establecer el estado como 'draft'
        return 'draft';
    } else {
        // La galería no está vacía, mantener el estado actual
        return $status;
    }
});

add_action('admin_footer', 'disable_publish_button');
function disable_publish_button() {
    global $post;
    // Verifica si $post es una instancia válida de WP_Post
    if (!is_a($post, 'WP_Post')) {
        return; // Salir de la función si $post no es válido
    }
    // Ahora puedes acceder a las propiedades de $post de manera segura
    if ($post->post_type == 'product') { // Verifica si el tipo de post es 'product'
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                let gallery_images = $('li.image').length;
                let checkConditions = function() {
                    let edit_post_status_display = $('.edit-post-status.hide-if-no-js').css('display');
                    if (gallery_images == 0 && edit_post_status_display != 'none') {
                        $('#publish').prop('disabled', true);
                    } else {
                        $('#publish').prop('disabled', false);
                    }
                };
                checkConditions();
                $('body').on('DOMNodeInserted', 'li.image', function () {
                    gallery_images++;
                    checkConditions();
                });
                $('body').on('DOMNodeRemoved', 'li.image', function () {
                    gallery_images--;
                    checkConditions();
                });
                // Observar cambios en el atributo 'style' del elemento '.edit-post-status.hide-if-no-js'
                let targetNode = document.querySelector('.edit-post-status.hide-if-no-js');
                let config = { attributes: true, attributeFilter: ['style'] };
                let callback = function(mutationsList, observer) {
                    for(let mutation of mutationsList) {
                        if (mutation.type === 'attributes') {
                            checkConditions();
                        }
                    }
                };
                let observer = new MutationObserver(callback);
                observer.observe(targetNode, config);
            });
        </script>
        <?php
    }
}
//JS para ventana modal
add_action('admin_enqueue_scripts', 'enqueue_my_custom_popup_script');
function enqueue_my_custom_popup_script() {
    $screen = get_current_screen();
    if ( $screen->id == "product_page_woobe" ) {
        wp_enqueue_script('my_custom_popup_script', plugins_url('/my_custom_popup.js', __FILE__), array('jquery', 'thickbox'), false, true);
         // Enqueueing CSS file
         wp_enqueue_style('my_custom_popup_style', plugins_url('/my_custom_popup.css', __FILE__));
    }
}
add_action('admin_footer', 'my_custom_popup');
function my_custom_popup() {
    $screen = get_current_screen();
    if ( $screen->id == "product_page_woobe") {
        echo '<div id="my_custom_popup">
                <p class = "text-modal">Recuerda que si pones como "Publicado" un artículo sin imágenes en la galería, éste se cambiará automáticamente a "Borrador".</p>
              </div>';
    }
//TEST VERSION
}
?> 