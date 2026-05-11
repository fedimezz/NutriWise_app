// Validation pour le formulaire de menu
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="admin_add_menu"]') || document.querySelector('form[action*="admin_edit_menu"]');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupérer les valeurs
            const title = document.querySelector('input[name="title"]').value.trim();
            const userId = document.querySelector('select[name="assigned_to"]').value;
            const caloriesTarget = document.querySelector('input[name="calories_target"]').value;
            const meals = document.querySelectorAll('textarea[name^="meals"]');
            
            let errors = [];
            
            // Validation du nom du menu
            if (!title) {
                errors.push('Le nom du menu est obligatoire');
            } else if (title.length < 3) {
                errors.push('Le nom du menu doit contenir au moins 3 caractères');
            } else if (title.length > 100) {
                errors.push('Le nom du menu ne doit pas dépasser 100 caractères');
            }
            
            // Validation de l'utilisateur
            if (!userId) {
                errors.push('Veuillez sélectionner un utilisateur');
            }
            
            // Validation des calories
            if (caloriesTarget) {
                const calories = parseInt(caloriesTarget);
                if (isNaN(calories) || calories < 0) {
                    errors.push('Les calories doivent être un nombre positif');
                } else if (calories > 10000) {
                    errors.push('Les calories ne doivent pas dépasser 10000');
                }
            }
            
            // Vérifier qu'au moins une description de repas est complétée
            let mealsFilled = 0;
            meals.forEach(function(textarea) {
                if (textarea.value.trim().length > 0) {
                    mealsFilled++;
                }
            });
            
            if (mealsFilled === 0) {
                errors.push('Veuillez ajouter au moins une description de repas');
            }
            
            // Vérifier les caractères spéciaux dans les descriptions
            meals.forEach(function(textarea, index) {
                const content = textarea.value.trim();
                if (content.length > 500) {
                    errors.push(`Description au jour ${Math.floor(index / 4) + 1} trop longue (max 500 caractères)`);
                }
            });
            
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
