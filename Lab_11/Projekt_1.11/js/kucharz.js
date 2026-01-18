/**
* kucharz.js
* Odpowiada za logikę interaktynego elementu na stronie głównej.
*/

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

    let index = 0;

    // Efekt powiększania szefa kuchni po najechaniu myszką.
    $(".chef-img").hover(

        function () {
            $(this).stop().animate({ width: "130px" }, 300);
        },

        function() {
            $(this).stop().animate({ width: "120px" }, 300);
        }
    );

    // Obsługa zmiany porady kulinarnej po kliknięciu w kucharza.
    $(".chef-img").click(function () {

        index++;

        if (index >= porady.length) index = 0;

        $("#porada-tekst").fadeOut(300, function () {
            $(this).text(porady[index]).fadeIn(300);
        });
    });

});