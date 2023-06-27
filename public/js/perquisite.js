// Obténer el elemento <script> que incluye el atributo personalizado desde post_details.blade.php
const scriptElement = document.querySelector('script[data-script-id="script-perquisite"]');

// Obténer el valor del atributo personalizado
const perquisiteValue = scriptElement.getAttribute('data-valor');

//se define que existe una propina

console.log(perquisiteValue);

var perq = perquisiteValue;