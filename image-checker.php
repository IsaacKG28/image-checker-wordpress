<?php
/*
Plugin Name: Image Checker
Description: Plugin que verifica la existencia de imágenes en la galería del producto para que se pueda habilitar el botón de publicar.
Version: 1.0
Author: Fernando Isaac Gonzalez Medina
*/
//AQUI EMPIEZA LOGICA PARA PRODUCTOS 1 POR 1 PARA WOOCOMMERCE
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
?> 
