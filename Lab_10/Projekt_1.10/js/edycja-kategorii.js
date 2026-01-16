/**
* edycja-kategorii.js
* Obsługuje interfejs okien modalnych dla edycji kategorii.
*/


/**
 * otworzEdycje(id, nazwa, matka)
 * Otwiera popup edycji i wypełnia go danymi wybranej kategorii
 */
function otworzEdycje(id, nazwa, matka) {
    const editId = document.getElementById("edit_id");
    const editName = document.getElementById("edit_name");
    const editMother = document.getElementById("edit_mother");
    const modal = document.getElementById("editModal");

    if (editId && editName && editMother && modal) {
        // Wstawienie danych do pól formularza
        editId.value = id;
        editName.value = nazwa;
        editMother.value = matka;

        modal.style.display = "block";
    }
}

/**
 * Zamyka okno modalne
 */
function zamknijEdycje() {
    const modal = document.getElementById("editModal");
    if (modal) {
        modal.style.display = "none";
    }
}
