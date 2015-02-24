<script type="text/javascript">
    
function ShowMessageInDeveloping() {
    var message = "Данный функционал находистя на\n\
стадии разработки и тестирвоания.\n\n\
Скорее всего он появится\n\с ближайшим обнволением сайта\n\n\
Мы приносим свои извинения\n\за доставленные неудобства.";
    alert(message);
}

function SelectDefileType() {
    if($("#defileType").length) {
        var type = $('#defileType').val();
        if(arrTypeData[type]['fendom'] == '1') {
//            if($("#fendomRow").length) {
//                $('#fendomRow').show();
//            }
            if($("#fendom").length) {
                $("#fendom").prop("required", true);
            }
        } else {
//            if($("#fendomRow").length) {
//                $('#fendomRow').hide();
//            }
            if($("#fendom").length) {
                $("#fendom").prop("required", false);
            }
        }
        if(arrTypeData[type]['collage'] == '1') {
//            if($("#collageRow").length) {
//                $('#collageRow').show();
//            }
            if($("#collage").length) {
                $("#collage").prop("required", true);
            }
        } else {
//            if($("#collageRow").length) {
//                $('#collageRow').hide();
//            }
            if($("#collage").length) {
                $("#collage").prop("required", false);
            }
        }
    }
}

$( document ).ready(function() {
    SelectDefileType();
});
</script>