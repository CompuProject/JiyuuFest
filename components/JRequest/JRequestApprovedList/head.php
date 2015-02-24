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

<style>
    .JRequestUsersListTable.approvedList {
        width: 900px;
        margin: 5px 30px 10px 30px;
        float: none;
    }
</style>