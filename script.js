function showNextStep() {
    var currentStepIndex = -1;
    var steps = document.getElementsByClassName('pik-step');
    for (var i = 0; i < steps.length; i++) {
        if (steps[i].style.display === 'block') {
            currentStepIndex = i;
            break;
        }
    }
    
    var inputs = steps[currentStepIndex].querySelectorAll('input[required], select[required], textarea[required]');
    for (var i = 0; i < inputs.length; i++) {
        if (!inputs[i].checkValidity()) {
            alert('Proszę poprawnie wypełnić wszystkie wymagane pola.');
            return; // Zatrzymaj przejście do następnego kroku
        }
    }

    

    // Jeśli jesteśmy już w ostatnim kroku, nie rób nic
    if (currentStepIndex >= steps.length - 1) {
        return;
    }

    // Ukryj wszystkie kroki
    for (var i = 0; i < steps.length; i++) {
        steps[i].style.display = 'none';
    }

    // Pokaż następny krok
    var nextStep = steps[currentStepIndex + 1];
    nextStep.style.display = 'block';

    // Resetuj opację dla animacji "fade-in"
    nextStep.style.opacity = 0;

    // Animacja "fade-in"
    var last = +new Date();
    var tick = function() {
        nextStep.style.opacity = +nextStep.style.opacity + (new Date() - last) / 400; // Dostosuj czas trwania animacji zgodnie z potrzebami
        last = +new Date();

        if (+nextStep.style.opacity < 1) {
            (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
        }
    };

    tick();
}


    
    
function showPreviousStep() {
    var currentStepIndex = -1;
    var steps = document.getElementsByClassName('pik-step');
    for (var i = 0; i < steps.length; i++) {
        if (steps[i].style.display === 'block') {
            currentStepIndex = i;
            break;
        }
    }

    // Jeśli jesteśmy już w pierwszym kroku, nie rób nic
    if (currentStepIndex <= 0) {
        return;
    }

    // Ukryj wszystkie kroki
    for (var i = 0; i < steps.length; i++) {
        steps[i].style.display = 'none';
    }

    // Pokaż poprzedni krok
    var previousStep = steps[currentStepIndex - 1];
    previousStep.style.display = 'block';

       // Resetuj opację dla animacji "fade-in"
    previousStep.style.opacity = 0;

    // Animacja "fade-in"
    var last = +new Date();
    var tick = function() {
        previousStep.style.opacity = +previousStep.style.opacity + (new Date() - last) / 400; // Dostosuj czas trwania animacji zgodnie z potrzebami
        last = +new Date();

        if (+previousStep.style.opacity < 1) {
            (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
        }
    };

    tick();
}




function showCitySuggestions(value) {
  // Pobierz element, w którym będą wyświetlane sugestie
  var suggestionsContainer = document.getElementById('city-suggestions');

  // Czyść sugestie, jeśli długość wartości jest mniejsza niż 3
  if (value.length < 3) {
    suggestionsContainer.innerHTML = '';
    return;
  }

  // Wykonaj zapytanie AJAX do form.php
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      if (xhr.status == 200) {
        // W przypadku sukcesu, wyświetl odpowiedź
        suggestionsContainer.innerHTML = xhr.responseText;
      } else {
        // W przypadku błędu, wyczyść sugestie
        suggestionsContainer.innerHTML = '';
      }
    }
  };
  
  // Określ typ żądania, URL i czy ma być asynchroniczne
  xhr.open('GET', 'https://www.pikevents.com/wp-content/plugins/Archiwum/form.php?action=suggest&city=' + encodeURIComponent(value), true);
  // Wyślij żądanie
  xhr.send();
}




function selectCity(cityWithProvince) {
    // Find the city input text field and set its value to the full city name with province
    var cityInput = document.getElementById('city-input');
    cityInput.value = cityWithProvince;

    // Optionally, clear the suggestions container
    var suggestionsContainer = document.getElementById('city-suggestions');
    suggestionsContainer.innerHTML = '';
}
   

// Function to disable blocked dates in the date picker
function setBlockedDates(dateInput, blockedDates) {
    dateInput.setAttribute('onkeydown', 'return false'); // Prevent manual edit
    dateInput.onfocus = function() {
        this.oldValue = this.value;
    };
    dateInput.onblur = function() {
        var date = this.value;
        var isBlocked = blockedDates.includes(date);
        if (isBlocked) {
            alert('Niestety, nie możemy zagrać dla Ciebie imprezy w podanym terminie.');
            this.value = this.oldValue;
        }
    };
}

// Get the date input and call setBlockedDates on it
var dateInput = document.getElementById('termin');
if (dateInput && blockedDates) {
    setBlockedDates(dateInput, blockedDates);
}

function calculateEventPrice(eventType) {
     var eventPrices = {
        'osiemnastka': 1600,
        'urodziny': 1600,
        'wesele': 2300,
        'studniowka': 3500,
        'impreza_firmowa': 2500,
        'polmetek': 1800,
        'poprawiny': 1800,
        'rocznica': 1600,
        'domowka': 500,
        'inna_impreza': 2200,
        'sylwester': 5000,
        'festyn': 2000,
        'bal': 2500,
        'andrzejki': 2300
    };
    return eventPrices[eventType] || 0;
}

function calculateDistancePrice(location) {
      var distanceMatch = location.match(/\[(\d+) km\]/);
    var distance = distanceMatch ? parseInt(distanceMatch[1]) : 0;

    // Pricing logic based on distance
    if (distance <= 10) {
        return 0;
    } else if (distance <= 25) {
        return 100;
    }
    else if (distance <= 50) {
        return 200;
    } else if (distance <= 85) {
        return 300;
    } else if (distance <= 120) {
        return 600;
    } else if (distance <= 160) {
        return 1000;
    } else if (distance <= 250) {
        return 2200;
    } else {
        return 4000;
    }
}

function calculateHoursPrice(totalHours) {
     var hours = parseInt(totalHours);
    
    // Pricing logic based on total hours
    if (hours <= 6) {
        return 0;
    } else if (hours <= 8) {
        return 100;
    }
    else if (hours <= 9) {
        return 300;
    }
     else if (hours <= 10) {
        return 600;
    } else if (hours <= 12) {
        return 1200;
    } else if (hours <= 14) {
        return 1800;
    } else {
        return 3000;
    }
}

function calculateGuestsPrice(guestNumber) {
    var guests = parseInt(guestNumber);
    
    // Pricing logic based on number of guests
    if (guests <= 25) {
        return 0;
    } else if (guests <= 40) {
        return 150;
    } else if (guests <= 60) {
        return 300;
    } else if (guests <= 80) {
        return 500;
    } else if (guests <= 100) {
        return 800;
    } else if (guests <= 150) {
        return 1000;
    } else if (guests <= 200) {
        return 1400;
    } else if (guests <= 250) {
        return 1800;
    } else if (guests <= 300) {
        return 2600;
    } else if (guests <= 400) {
        return 4000;
    } else if (guests <= 500) {
        return 5000;
    } else {
        return 8000;
    }
}

var lastSubmitTime = null;

function canSubmit() {
    var lastSubmitTime = localStorage.getItem('lastSubmitTime');
    var currentTime = new Date().getTime();

    if (lastSubmitTime && currentTime - parseInt(lastSubmitTime) < 600000) {
        return false;
    }

    localStorage.setItem('lastSubmitTime', currentTime.toString());
    return true;
}


function handleSubmit() {
    

    // Zbierz dane z formularza
    var formData = {
        name: document.getElementById('imie').value,
        eventType: document.getElementById('typ').value,
        eventDate: document.getElementById('termin').value,
        location: document.getElementById('city-input').value,
        startTime: document.getElementById('start_hour').value,
        endTime: document.getElementById('finish_hour').value,
        totalHours: document.getElementById('total_hours').value,
        guestNumber: document.getElementById('liczba_gosci').value,
        email: document.getElementById('email').value
        
    };

     var price = 0;
    price += calculateEventPrice(formData.eventType);
    price += calculateDistancePrice(formData.location);
    price += calculateHoursPrice(formData.totalHours);
    price += calculateGuestsPrice(formData.guestNumber);
    discountPrice = price - 200;

    // Add calculated price to formData
    formData.discountPrice = discountPrice;
    formData.price = price;
    formData.priceType = calculateEventPrice(formData.eventType);
    formData.priceDistance = calculateDistancePrice(formData.location);
    formData.priceHours = calculateHoursPrice(formData.totalHours);
    formData.priceGuests = calculateGuestsPrice(formData.guestNumber);

    
    
    // Wyświetl zebrane dane
    displayFormData(formData);
}

function displayFormData(data) {
    var displayDiv = document.getElementById('form-data-display');
    displayDiv.innerHTML = `
        <div id="wycenionaImpreza">Witaj, <b>${data.name}</b>.</br></br>Dziękujemy za zainteresowanie naszą ofertą. Oto Twoja impreza:</br>Rodzaj: <b>${data.eventType}</b></br>Lokalizacja: <b>${data.location}</b></br>Termin: <b>${data.eventDate}</b>, od <b>${data.startTime}</b> do <b>${data.endTime}</b></br>Czas trwania: <b>${data.totalHours} godzin</b></br>Liczba gości: <b>${data.guestNumber}</b></br></br>Proponujemy cenę <b>${data.price}</b> zł i cena ta obejmuje:<b></br>- Poprowadznie imprezy przez dwóch DJ-ów/konfernasjerów</br>- Pełne nagłośnienie i oświetlenie sali</br>- Efekty dymne</br>- Nagranie i zmontowanie filmiku z imprezy w 4K.</br>- System zamawiania utworów za pomocą kodu QR.</b></br></br>Jeżeli jednak Ty i Twoi goście zgodzą się na to, abyśmy udostępnili ten filmik na naszych social mediach w celach promocyjnych, to wtedy cena zmniejeszy się do:</br></div>
        <div id="cenaWycenionejImprezy">${data.discountPrice} zł!</br></div>
        <div id="wycenionaImpreza">Nie czekaj! Zarezerwuj termin już teraz!</br></div>
        <button type="button" id="rezerwacja">ZAREZERWUJ</button>
        <div id="wycenionaImpreza">Po kliknięciu "ZAREZERWUJ" otrzymasz wiadomość e-mail z dalszymi instrukcjami.</div>
    `;
    document.getElementById('rezerwacja').addEventListener('click', function() {
    var formData = {
        name: document.getElementById('imie').value,
        eventType: document.getElementById('typ').value,
        eventDate: document.getElementById('termin').value,
        location: document.getElementById('city-input').value,
        startTime: document.getElementById('start_hour').value,
        endTime: document.getElementById('finish_hour').value,
        totalHours: document.getElementById('total_hours').value,
        guestNumber: document.getElementById('liczba_gosci').value,
        email: document.getElementById('email').value
       
    };
    
      var price = 0;
    price += calculateEventPrice(formData.eventType);
    price += calculateDistancePrice(formData.location);
    price += calculateHoursPrice(formData.totalHours);
    price += calculateGuestsPrice(formData.guestNumber);
    discountPrice = price - 200;

    // Add calculated price to formData
    formData.discountPrice = discountPrice;
    formData.price = price;

    // Wysyłanie danych formularza do save_data.php
fetch('https://www.pikevents.com/wp-content/plugins/Archiwum/save_data.php', {
    method: 'POST',
    body: JSON.stringify(formData),
    headers: {'Content-Type': 'application/json'}
}).then(response => {
    if (response.ok) {
        return response.text();
    } else {
        throw new Error('Serwer zwrócił błąd');
    }
}).then(data => {
    console.log(data);
    if (data === 'Dane zostaly zapisane') {
        window.location.href = 'https://www.pikevents.com/zamowienie-wyslane';
    } else {
        throw new Error('Nieoczekiwana odpowiedź serwera');
    }
}).catch(error => {
    console.error('Wystąpił błąd przy przesyłaniu danych: ', error);
});

    
});
    
}



// Dołącz funkcję handleSubmit do przycisku wysyłania formularza
document.getElementById('submit').addEventListener('click', handleSubmit);

