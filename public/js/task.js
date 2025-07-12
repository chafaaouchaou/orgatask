// Gestion des filtres de tâches
document.addEventListener('DOMContentLoaded', function() {
    // Fonction utilitaire pour gérer les filtres utilisateur
    function setupUserFilter(searchId, dropdownId, paramName) {
        const searchInput = document.getElementById(searchId);
        const dropdown = document.getElementById(dropdownId);
        
        if (!searchInput || !dropdown) return;
        
        searchInput.addEventListener('focus', function() {
            dropdown.style.display = 'block';
        });
        
        searchInput.addEventListener('blur', function() {
            setTimeout(() => {
                dropdown.style.display = 'none';
            }, 200);
        });
        
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const userItems = dropdown.querySelectorAll('.user-item');
            
            userItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(query) || item.dataset.userId === '-1') {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Gestion de la sélection d'utilisateur
        dropdown.addEventListener('click', function(e) {
            if (e.target.classList.contains('user-item')) {
                const userId = e.target.dataset.userId;
                const userName = e.target.textContent.trim();
                
                // Mettre à jour le champ de recherche
                if (userId && userId !== '-1') {
                    searchInput.value = userName;
                } else {
                    searchInput.value = '';
                }
                
                // Rediriger avec le filtre
                const currentParams = new URLSearchParams(window.location.search);
                if (userId && userId !== '-1') {
                    currentParams.set(paramName, userId);
                } else {
                    currentParams.delete(paramName);
                }
                currentParams.delete('page'); // Reset pagination
                
                window.location.search = currentParams.toString();
            }
        });
    }
    
    // Configurer les filtres utilisateur
    setupUserFilter('created-by-search', 'created-by-dropdown', 'created_by');
    setupUserFilter('assigned-to-search', 'assigned-to-dropdown', 'assigned_to');
    
    // Gestion du tri
    const orderSelect = document.getElementById('order-select');
    
    if (orderSelect) {
        orderSelect.addEventListener('change', function() {
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.set('order', this.value);
            currentParams.delete('page'); // Reset pagination
            
            window.location.search = currentParams.toString();
        });
    }
    
    // Animation des cartes de tâches
    function animateCards() {
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }
    
    // Lancer l'animation au chargement
    animateCards();
    
    // Utilitaires pour le feedback utilisateur
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container');
        container.insertBefore(notification, container.firstChild);
        
        // Auto-dismiss après 5 secondes
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
    
    // Exposer les utilitaires globalement
    window.TaskManager = {
        showNotification: showNotification
    };
});
