jQuery(document).ready(function($) {
    // Asegúrate de que Thickbox esté cargado antes de ejecutar tb_show
    if (typeof tb_show === 'function') {
        tb_show("Recordatorio", "#TB_inline?width=600&height=550&inlineId=my_custom_popup");
    } else {
        console.error('Thickbox no está cargado correctamente.');
    }
});