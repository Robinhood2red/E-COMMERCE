# E-COMMERCE

POUR CRéE TOUTES MES BASES DE DONNéES
php bin/console doctrine:database:create

POUR RéCUP TOUTES MES BASES DE DONNéES
php bin/console make:migration
php bin/console doctrine:migrations:migrate

CRéE ENTITY
php bin/console make:entity 'name'

CRéE CRUD
php bin/console make:crud 'NameEntity'

VOIR TOUT MES DOCS
php bin/console debug:router

POUR UTILISER LE MAILER 
php bin/console messenger:consume async -vv

php -S localhost:8000 -t public

Ma sous catégorie = alpha_camp

https://www.udemy.com/course/python-pour-la-data-le-cours-ultime/

Pour le TD :
https://www.youtube.com/shorts/izwW9mYZjtY



{% block javascripts %}
    {{ parent() }} {#! Importe les scripts de base.html.twig pour garder les couleurs du css #}
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        //! ON RÉCUPÈRE LES ÉLÉMENTS HTML
        const citySelect = document.getElementById('order_city'); // Le menu déroulant généré par OrderType
        const shippingDisplay = document.getElementById('shipping-cost'); // La zone "Frais logistiques"
        const totalDisplay = document.querySelector('.fs-4.text-primary-alpha'); // La zone "Total estimé"
        
        //! ON RÉCUPÈRE LE PRIX DES ARTICLES (Injecté par Twig au chargement)
        // On utilise |default(0) pour éviter une erreur JS si le panier est vide
        const baseTotal = {{ total_items|default(0) }};

        // ON ÉCOUTE LE CHANGEMENT DE VILLE
        if (citySelect) {
            citySelect.addEventListener('change', function() {
                const cityId = this.value; // L'ID de la ville sélectionnée

                // Si l'utilisateur choisit le "Retrait magasin" (vide)
                if (!cityId) {
                    shippingDisplay.innerText = "0,00 €";
                    updateTotal(0); // Mise à jour du total avec 0€ de frais
                    return;
                }

                // APPEL AJAX (FETCH) AU CONTRÔLEUR
                // On demande au serveur : "Quel est le prix pour la ville ID X ?"
                fetch('/order/get-shipping-cost/' + cityId)
                    .then(response => response.json()) // On transforme la réponse en objet JS
                    .then(data => {
                        const cost = parseFloat(data.cost); // On récupère le nombre
                        
                        // MISE À JOUR VISUELLE DES FRAIS
                        // toLocaleString permet d'afficher "15,00 €" au lieu de "15"
                        shippingDisplay.innerText = cost.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + " €";
                        
                        // CALCUL DU NOUVEAU TOTAL
                        updateTotal(cost);
                    })
                    .catch(error => console.error('Erreur logistique Alpha:', error));
            });
        }

        // Fonction pour additionner le Panier + la Livraison
        function updateTotal(shippingCost) {
            const finalTotal = baseTotal + shippingCost;
            totalDisplay.innerText = finalTotal.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + " €";
        }
    });
    </script>
{% endblock %}