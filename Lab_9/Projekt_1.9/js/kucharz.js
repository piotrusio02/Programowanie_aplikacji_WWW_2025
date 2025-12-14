/**
* kucharz.js
* Odpowiada za logikę interaktywnych elementów na stronie głównej.
*/

// Uruchania kod dopiero po pełnym załadowaniu strony.
$(document).ready(function ()
{    

    // Tablica zawierająca porady kulinarne wyświetlane po każdym kliknięciu w kucharza.
    const porady = [
        "Nie warto płukać makaronu po ugotowaniu. Wtedy straci swój smak i skrobię!",
        "Zawsze dodawaj do sosu trochę wody z gotowania. To sekret najlepszego smaku!",
        "Nigdy nie łam spaghetti na pół! Chyba, że chcesz trafić na czarną listę włoskich kucharzy.",
        "Dodaj odrobinę oliwy do wody podczas gotowania makaronu, aby nie kleił się po ugotowaniu.",
        "Włoskie powiedzenie mówi, że miękki makaron to smutek na talerzu. Zawsze celuj w al dente."
    ];

    // Zmienna śledząca aktualnie wyświetlaną poradę.
    let index = 0;

    // Obsługa zmiany porady po kliknięciu.
    $(".chef-img").click(function () {

        // Zwiększenie indeksu, aby przejść do następnej porady.
        index++;

        // Sprawdzenie, czy indeks przekroczył rozmiar tablicy.
        // Jeśli tak, resetuje do 0.
        if (index >= porady.length) index = 0;

        // Zmiana wartości po przez animacje ukrycia i ponowne pokazanie.
        $("#porada-tekst").fadeOut(300, function () {
            $(this).text(porady[index]).fadeIn(300);
        });
    });

    // Efekt powiększania szefa kuchni po najechaniu myszką.
    $(".chef-img").hover(
        
        // Funkcja powiększania po najechaniu myszką.
        function () {
            $(this).stop().animate({ width: "140px" }, 300);
        },
    
        // Funkcja pomniejszania po odjechaniu myszką.
        function() {
            (this).stop().animate({ width: "120px" }, 300);
        }
    );

});