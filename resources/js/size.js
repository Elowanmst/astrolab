document.addEventListener('DOMContentLoaded', () => {
    const sizeOptions = document.querySelectorAll('.size-option');
    const sizeInput = document.getElementById('size');

    sizeOptions.forEach(option => {
        option.addEventListener('click', () => {
            // Supprime la classe "selected" de toutes les options
            sizeOptions.forEach(opt => opt.classList.remove('selected'));

            // Ajoute la classe "selected" à l'option cliquée
            option.classList.add('selected');

            // Met à jour la valeur du champ caché
            sizeInput.value = option.getAttribute('data-size');
        });
    });
});