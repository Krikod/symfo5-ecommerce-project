window.onload = () => {
    // gestion des boutons "supprimer"
    let links = document.querySelectorAll("[data-delete]")

    // on boucle sur links
    for(link of links){ // équivalent du foreach de PHP
        // on écoute le clic
        link.addEventListener("click", function(e) {
            // on empêche la navigation (éviter une navigation au moment du clic sur le lien avant confirmation)
            e.preventDefault()
// todo faire en modale ?
            // on demande confirmation
            if(confirm("Voulez-vous supprimer cette image ? Attention, elle sera supprimée même si vous n'éditez pas le produit !")) {

                // Ier bloc: REQUETE (fetch)
                // on envoie une requête Ajax vers le href du lien avec la méthode DELETE
                fetch(this.getAttribute("href"), { // fetch fonctionne comme une promesse (attendre d'avoir réponse), puis .then
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(   // 2e bloc: récupération de la réponse (promesse aussi): on la traite en JSON
                    response => response.json()
                ).then(data => {   // 3e bloc: à la réponse, on stocke les données dans data, puis gestion succès etc.
                    if(data.success)
                        this.parentElement.remove()
                else
                    alert(data.error)
                }).catch(e => alert(e))   // Si la promesse n'est pas tenue

            }
        })
    }
}

