<script>
function show_hide(idName) {
    if(document.getElementById(idName).style.display=='none') {
        document.getElementById(idName).style.display = '';
    } else {
        document.getElementById(idName).style.display = 'none';
    }
    return true;
}
</script>