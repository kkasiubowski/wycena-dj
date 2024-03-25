<?php

// Read blocked dates from the JSON file
$blocked_dates_json = file_get_contents(__DIR__ . '/blocked_dates.json');
$blocked_dates = json_decode($blocked_dates_json, true);
?>
<script type="text/javascript">
// Store blocked dates in a JavaScript variable
var blockedDates = <?php echo json_encode($blocked_dates['blocked_dates']); ?>;
</script>
<?php
header('Access-Control-Allow-Origin: https://pikevents.com/strona-glowna');
// Sprawdź, czy to zapytanie AJAX do autouzupełniania
if (isset($_GET['action']) && $_GET['action'] == 'suggest' && isset($_GET['city'])) {
    // Pobierz parametr miasta
    $city = $_GET['city'];
    
    // Załaduj dane z pliku miasta.json
    $json_data = file_get_contents('miasta.json');
    $cities = json_decode($json_data, true);
    
    // Przeszukaj dane i znajdź pasujące miasta
    $suggestions = array_filter($cities, function ($item) use ($city) {
        // Użyj stristr, jeśli chcesz wyszukiwanie bez uwzględniania wielkości liter
        return strpos(mb_strtolower($item['n']), mb_strtolower($city)) === 0;
    });

    // Sortuj sugestie według wartości "o"
    usort($suggestions, function ($item1, $item2) {
        return $item1['o'] - $item2['o'];
    });

    // Wyświetl posortowane sugestie
    echo '<ul>';
    foreach ($suggestions as $suggestion) {
        $city_name = htmlspecialchars($suggestion['n']);
        $distance = htmlspecialchars($suggestion['o']);
        $full_name = $city_name . ' [' . $distance . ' km]';
        echo "<li onclick='selectCity(\"" . addslashes($full_name) . "\")'>" . $full_name . "</li>";
    }
    echo '</ul>';
    
    // Zakończ skrypt, nie wyświetlaj reszty strony
    exit;
}

?>


<form>
    

      <div class="pik-step"  style="display:block;" id="pik-step-intro" style="text-align: center;">
        <label id="title1">Wycenimy Twoją imprezę w pół minuty!</br></label>
        <label id="title2">Wypełnij nasz krótki i prosty formularz</br></br></label>
        <button type="button" class="btn-next" onclick="showNextStep('pik-step-name')">Zaczynamy!</button>
    </div>

    <div class="pik-step" style="display:none;" id="pik-step-name">
        <label id="title2">Poznajmy się!</br></label>
        <label id="title1">Podaj proszę swoje imię:</br></label>
        <input type="text" id="imie" name="imie" minlength="2" maxlength="30" required>
        
        <button type="button"  class="btn-next" onclick="showNextStep('pik-step-type')">Dalej</button>
    </div>

    <div class="pik-step" style="display:none;" id="pik-step-type" style="display:none;">
        <label for="typ">Jaką imprezę organizujesz?</br></label>
        <select id="typ" name="typ" required>
            <option value="osiemnastka">Osiemnastka</option>
            <option value="urodziny">Urodziny</option>
            <option value="wesele">Wesele</option>
            <option value="studniowka">Studniówka</option>
            <option value="impreza_firmowa">Impreza firmowa</option>
            <option value="polmetek">Półmetek</option>
            <option value="sylwester">Sylwester</option>
            <option value="andrzejki">Andrzejki</option>
            <option value="bal">Bal</option>
            <option value="festyn">Festyn</option>
            <option value="poprawiny">Poprawiny</option>
            <option value="rocznica">Rocznica</option>
            <option value="domowka">Domówka</option>
            <option value="inna_impreza">Inna impreza</option>
        </select>

        <button type="button"   class="btn-next" onclick="showNextStep('pik-step-date')">Dalej</button>
<button type="button" class="btn-back" onclick="showPreviousStep()">Wstecz</button>
    </div>

    
    

    <div class="pik-step" style="display:none;" id="pik-step-date" style="display:none;">
        <label for="termin">Kiedy odbywać się będzie Twoja impreza?</br></label>
<label id="title2">Kliknij na kalendarz i wybierz datę (najwcześniej za 7 dni) by sprawdzić, czy mamy wolny termin.</br></label>
        <input type="date" id="termin" name="termin" required>
         <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            var today = new Date();
            today.setDate(today.getDate() + 7); // Dodaje 7 dni do aktualnej daty
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); // Styczeń to 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            document.getElementById('termin').setAttribute('min', today);
        });
    </script>
        

        <button type="button"  class="btn-next" onclick="showNextStep('pik-step-location')">Dalej</button>
<button type="button" class="btn-back" onclick="showPreviousStep()">Wstecz</button>
    </div>


   <div class="pik-step" style="display:none;" id="pik-step-location">
    <label id="title3">Podaj lokalizację Twojej imprezy</br></label>
    <label id="title4">Wpisz nazwę miejscowości i <b><u>wybierz z listy podpowiedzi.</u></b></br> W nawiasie podana jest odległość miejscowości od Legnicy.</br></label>
    <input type="text" id="city-input" placeholder="Wpisz miasto" onkeyup="showCitySuggestions(this.value)" minlength="2" maxlength="50" required pattern="^[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ]+(\s[A-Za-ząćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)*\s\[\d+\s?km\]$">
    <div id="city-suggestions" style="position: relative;"></div>

    <button type="button" class="btn-next" onclick="showNextStep('pik-step-duration')">Dalej</button>
<button type="button" class="btn-back" onclick="showPreviousStep()">Wstecz</button>
</div>



   <div class="pik-step" style="display:none;" id="pik-step-duration">
    <label for="start_hour">O której godzinie zacznie się impreza?</br></label>
    <input type="time" id="start_hour" name="start_hour" required>
    </br>
    <label for="finish_hour">O której godzinie skończy się impreza?</br></label>
    <input type="time" id="finish_hour" name="finish_hour" required>
    
    <input type="number" id="total_hours" name="total_hours" readonly>


    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ustaw domyślne godziny
        document.getElementById('start_hour').value = '18:00';
        document.getElementById('finish_hour').value = '02:00';
        calculateEventDuration(); // Oblicz czas trwania na starcie
    });

    function setFullHour(time) {
        var timeParts = time.split(':');
        return timeParts[0] + ':00';
    }

    function calculateEventDuration() {
        // Pobieramy godziny rozpoczęcia i zakończenia
        var startTime = document.getElementById('start_hour').value;
        var endTime = document.getElementById('finish_hour').value;

        // Ustawiamy pełne godziny, jeśli potrzeba
        if (!checkIfFullHour(startTime)) {
            document.getElementById('start_hour').value = setFullHour(startTime);
            startTime = document.getElementById('start_hour').value;
        }
        if (!checkIfFullHour(endTime)) {
            document.getElementById('finish_hour').value = setFullHour(endTime);
            endTime = document.getElementById('finish_hour').value;
        }

        // Reszta kodu pozostaje bez zmian
        var start = new Date('1970-01-01T' + startTime);
        var end = new Date('1970-01-01T' + endTime);
        if (end < start) {
            end.setDate(end.getDate() + 1);
        }
        var duration = (end - start) / (1000 * 60 * 60);

        var totalHoursInput = document.getElementById('total_hours');
        totalHoursInput.value = parseInt(duration.toFixed(0), 10);
    }

    document.getElementById('start_hour').addEventListener('change', calculateEventDuration);
    document.getElementById('finish_hour').addEventListener('change', calculateEventDuration);

    function checkIfFullHour(time) {
        var timeParts = time.split(':');
        return timeParts[1] === '00';
    }

    </script>

    <button type="button" class="btn-next" onclick="showNextStep('pik-step-guests')">Dalej</button>
<button type="button" class="btn-back" onclick="showPreviousStep()">Wstecz</button>
</div>


<div class="pik-step" style="display:none;" id="pik-step-guests">
    <label for="liczba_gosci">Ile osób planujesz zaprosić?</br></label>
    <label id="title4">Wpisz liczbę.</br></label>
    <!-- Zmieniony input na typ 'number' z min=20 i max=1000 -->
    <input type="number" id="liczba_gosci" name="liczba_gosci" min="20" max="1000" value="25" oninput="updateGuestNumber(this.value)" required>
    <button type="button"  class="btn-next" onclick="showNextStep('pik-step-final')">Dalej</button>
    <button type="button" class="btn-back" onclick="showPreviousStep()">Wstecz</button>
</div>

<div class="pik-step" style="display:none;" id="pik-step-final">
    <label for="email">Już prawie!</br></label>
    <label id="title2">Podaj proszę adres e-mail:</br></label>
    <input type="email" id="email" name="email" minlength="5" maxlength="50" required></br>
    <button type="button"  class="btn-next" onclick="showNextStep('pik-step-wycena')">Dalej</button>
    <button type="button" class="btn-back" onclick="showPreviousStep()">Wstecz</button>
        
</div>

<div class="pik-step" style="display:none;" id="pik-step-wycena">
    <label for="email">Wyceniliśmy Twoją imprezę :)</br></label>
    <button type="button" class="btn-next" id="submit" onclick="submitForm()">Pokaż wycenę</button>
        <div id="form-data-display"></div>
</div>



</form>

