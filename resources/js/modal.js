document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('contactModal');
    const openModalButton = document.getElementById('openModal');
    const closeModalButton = document.querySelector('.screen-header-right .close');
    const modalOverlay = document.querySelector('.modal-overlay');

    // Ouvrir la modale
    openModalButton.addEventListener('click', () => {
        modal.style.display = 'block';
    });

    // Fermer la modale
    closeModalButton.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Fermer la modale en cliquant en dehors du contenu
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    modalOverlay.addEventListener('click', (event) => {
        if (event.target === modalOverlay) {
            modal.style.display = 'none';
        }
    });
});