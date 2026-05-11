// Validation pour le formulaire de planning
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="admin_add_planning"]') || document.querySelector('form[action*="admin_edit_planning"]');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupérer les valeurs
            const name = document.querySelector('input[name="name"]').value.trim();
            const userId = document.querySelector('select[name="user_id"]').value;
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;
            const menus = document.querySelectorAll('input[name="menus[]"]:checked');
            
            let errors = [];
            
            // Validation du nom
            if (!name) {
                errors.push('Le nom du planning est obligatoire');
            } else if (name.length < 3) {
                errors.push('Le nom du planning doit contenir au moins 3 caractères');
            } else if (name.length > 100) {
                errors.push('Le nom du planning ne doit pas dépasser 100 caractères');
            }
            
            // Validation de l'utilisateur
            if (!userId) {
                errors.push('Veuillez sélectionner un utilisateur');
            }
            
            // Validation des dates
            if (!startDate) {
                errors.push('La date de début est obligatoire');
            }
            
            if (!endDate) {
                errors.push('La date de fin est obligatoire');
            }
            
            // Vérifier que la date de fin est après la date de début
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                
                if (end <= start) {
                    errors.push('La date de fin doit être après la date de début');
                }
                
                // Vérifier que les dates ne sont pas trop éloignées (max 365 jours)
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays > 365) {
                    errors.push('Le planning ne peut pas dépasser 365 jours');
                }
            }
            
            // Au moins un menu doit être sélectionné
            if (menus.length === 0) {
                errors.push('Veuillez sélectionner au moins un menu');
            }
            
            // Afficher les erreurs ou soumettre
            if (errors.length > 0) {
                alert('Veuillez corriger les erreurs suivantes:\n\n' + errors.join('\n'));
                return false;
            }
            
            // Si tout est valide, soumettre le formulaire
            form.submit();
        });
    }
});
