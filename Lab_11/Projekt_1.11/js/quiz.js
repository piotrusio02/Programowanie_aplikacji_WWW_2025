/**
* quiz.js
* Odpowiada za logikę Quizu na stronie.
*/

document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("quiz-pytania");

    if (!form) return;

    // Przycisk sprawdzający odpowiedzi.
    const sprawdzButton = document.getElementById("sprawdz");

    // Elementy okna modalnego z wynikiem
    const popupTlo = document.getElementById('popup-tlo');
    const popupTekst = document.getElementById('popup-tekst');
    const popupZamknij = document.getElementById('popup-zamknij');

    const pytania = Array.from(form.querySelectorAll(".pytanie"));

    sprawdzButton.addEventListener("click", function (event) {
        event.preventDefault();
        let punkty = 0;

        pytania.forEach(pytaniaSekcja => {
            const wybrane = pytaniaSekcja.querySelector('input[type="radio"]:checked');
            if (wybrane && wybrane.value === "true") punkty++;
        });

        const razem = pytania.length;
        let wiadomosc;

        // Komunikat końcowy na podstawie zdobytych punktów.
        if (punkty === razem) {
            wiadomosc = `Jesteś prawdziwym królem makaronów! ${punkty}/${razem} poprawnych odpowiedzi.`;
        } else if (punkty >= 5) {
            wiadomosc = `Nieźle! Masz ${punkty}/${razem} poprawnych odpowiedzi.`;
        } else {
            wiadomosc = `Mogło być lepiej... Masz ${punkty}/${razem} poprawnych odpowiedzi.`;
        }

        popupTekst.textContent = wiadomosc;
        popupTlo.style.display = 'flex';
    });

    popupZamknij.addEventListener('click', function (e) {
        e.preventDefault();
        popupTlo.style.display = 'none';

        form.reset()
    
    });
});