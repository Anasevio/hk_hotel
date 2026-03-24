// ======================
// POPUP TAMBAH TIMER
// ======================

const popup = document.getElementById("popup");
const addBtn = document.getElementById("addCustom");
const closePopup = document.getElementById("closePopup");
const saveRoom = document.getElementById("saveRoom");

addBtn.onclick = () => {
popup.style.display = "flex";
};

closePopup.onclick = () => {
popup.style.display = "none";
};


// ======================
// TAMBAH DATA KAMAR
// ======================

saveRoom.onclick = () => {

const room = document.getElementById("roomNumber").value;
const type = document.getElementById("roomType").value;
const duration = document.getElementById("roomDuration").value;

if(room === "" || duration === ""){
alert("Data belum lengkap");
return;
}

const table = document.querySelector("#customTable tbody");

const row = table.insertRow();

row.innerHTML = `
<td>${room}</td>
<td>${type}</td>
<td>${duration} menit</td>
<td class="status custom">Custom</td>
`;

popup.style.display = "none";

};


// ======================
// POPUP EDIT TIPE KAMAR
// ======================

const editPopup = document.getElementById("editPopup");
const editBtns = document.querySelectorAll(".editBtn");
const closeEdit = document.getElementById("closeEdit");
const saveEdit = document.getElementById("saveEdit");

let currentRow = null;

editBtns.forEach(btn => {

btn.onclick = () => {

currentRow = btn.closest("tr");

const type = currentRow.children[0].innerText;
const duration = currentRow.children[1].innerText.replace(" menit","");

document.getElementById("editType").value = type;
document.getElementById("editDuration").value = duration;

editPopup.style.display = "flex";

};

});


// ======================
// CLOSE POPUP EDIT
// ======================

closeEdit.onclick = () => {
editPopup.style.display = "none";
};


// ======================
// SIMPAN EDIT
// ======================

saveEdit.onclick = () => {

const newDuration = document.getElementById("editDuration").value;

currentRow.children[1].innerText = newDuration + " menit";

editPopup.style.display = "none";

};