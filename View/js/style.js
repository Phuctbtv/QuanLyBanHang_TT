function SelectChange() {
        const x = document.getElementById('slt-month');
        const y = document.getElementById('slt-year');
        if (x.value !== "" || y.value !== "") {
            document.getElementById('btn-send').click();
        }
    }
function deleteConfirm() {
    const confirmDeletion = confirm("Bạn chắc chắn xóa không ?");
    if (!confirmDeletion) {
        return false;
    }
    return true;
}
