/**
* quis.js
* Odpowiada za logikę Quizu na stronie.
*/

// Uruchania kod dopiero po pełnym załadowaniu strony.
document.addEventListener("DOMContentLoaded", function () {

    // Główny formularz quizu.
    const form = document.getElementById("quiz-pytania");

    // Jeżeli formularz nie istnieje, skryp przywa działanie.
    if (!form) return;

    // Przycisk sprawdzający odpowiedzi.
    const sprawdzButton = document.getElementById("sprawdz");

    // Elementy okna modalnego z wynikiem
    const popupTlo = document.getElementById('popup-tlo');
    const popupTekst = document.getElementById('popup-tekst');
    const popupZamknij = document.getElementById('popup-zamknij');

    // Lista odpowiedzi konwertowana na tablicę.
    const pytania = Array.from(form.querySelectorAll(".pytanie"));

    sprawdzButton.addEventListener("click", function (event) {
        event.preventDefault();
        let punkty = 0;

        // Pętla iterująca przez każde pytanie.
        // Sprawdza poprawność zaznaczonych odpowiedzi.
        pytania.forEach(pytaniaSekcja => {

            // Wyszukiwanie zaznaczonych 'radi button' w bieżącej sesji.
            const wybrane = pytaniaSekcja.querySelector('input[type="radio"]:checked');
            if (wybrane && wybrane.value === "true") punkty++;
        });

        // Łączna liczba pytań dla komunikatu końcowego.
        const razem = pytania.length;
        let wiadomosc;

        // Komunikat końcowy na podstawie zdobytych punktów.
        if (punkty === razem) {
            wiadomosc = `Jesteś prawdziwym królem makaronów! ${punkty}/${razem} poprawnych odpowiedzi.`;
        } else if (punkty >= 5) { // Próh zależy od liczby pytań.
            wiadomosc = `Nieźle! Masz ${punkty}/${razem} poprawnych odpowiedzi.`;
        } else {
            wiadomosc = `Mogło być lepiej... Masz ${punkty}/${razem} poprawnych odpowiedzi.`;
        }

        // Wyświetlenie wyniku w oknie modalnym.
        popupTekst.textContent = wiadomosc;
        popupTlo.style.display = 'flex';
    });

    popupZamknij.addEventListener('click', function (e) {
        e.preventDefault();

        // Ukrycie oknba modalnego.
        popupTlo.style.display = 'none';

        // Resetowanie formularza, aby wyczyścić zaznaczone odpowiedzi.
        form.reset()
    
    });
});