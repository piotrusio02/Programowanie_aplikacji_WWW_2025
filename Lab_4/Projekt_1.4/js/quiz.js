document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("quiz-pytania");
  if (!form) return;

  let wynikiContainer = document.getElementById("quiz-wyniki");
  const sprawdzButton = document.getElementById("sprawdz");
  const pytania = Array.from(form.querySelectorAll(".pytanie"));

  sprawdzButton.addEventListener("click", function (event) {
    let punkty = 0;
    let zaznaczone = 0;

    pytania.forEach(pytaniaSekcja => {
      const wybrane = pytaniaSekcja.querySelector('input[type="radio"]:checked');
      if (wybrane) {
        zaznaczone++;

        if (wybrane.value === "true") punkty++;
      }
    });

    const razem = pytania.length;
    let wiadomosc;

    if (punkty === razem) {
      wiadomosc = `Jesteś prawdziwym królem makaronów! ${punkty}/${razem} poprawnych odpowiedzi.`;
    } else if (punkty >= 5 && punkty < razem) {
      wiadomosc = `Nieźle! Masz ${punkty}/${razem} poprawnych odpowiedzi.`;
    } else {
        wiadomosc = `Mogło być lepiej... Masz ${punkty}/${razem} poprawnych odpowiedzi.`;
    }

    const popupTlo = document.getElementById('popup-tlo');
    const popupTekst = document.getElementById('popup-tekst');
    const popupZamknij = document.getElementById('popup-zamknij');

    popupTekst.textContent = wiadomosc;
    popupTlo.style.display = 'flex';

    popupZamknij.addEventListener('click', () => {
        popupTlo.style.display = 'none';
    })
  });

});


