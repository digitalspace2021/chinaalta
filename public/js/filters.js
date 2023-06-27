

const selectTable = document.getElementById("table_filter");
const selectStatus = document.getElementById("status_filter");
selectTable.addEventListener("change", filters);
selectStatus.addEventListener("change", filters);

function filters(){
if(selectTable.value !== "" && selectStatus.value == ""){
    get_recent_transactions_filter(selectTable.value,null);
}
if(selectTable.value == "" && selectStatus.value !== ""){
    get_recent_transactions_filter(null,selectStatus.value);
}
if (selectTable.value !== "" && selectStatus.value !== "") {
    get_recent_transactions_filter(selectTable.value,selectStatus.value);
}
}